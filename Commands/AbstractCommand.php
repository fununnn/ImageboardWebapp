<?php

namespace Commands;

use Exception;

abstract class AbstractCommand implements Command{
    protected ?string $value;
    protected array $argsMap = [];
    protected static ?string $alias = null;
    protected static bool $requiredCommandValue = false;
    public function __construct(){
        $this->setUpArgsMap();
    }
private function setUpArgsMap(): void
{
    $args = $GLOBALS['argv'];
    $startIndex = array_search($this->getAlias(), $args);
    if ($startIndex === false) {
        throw new Exception(sprintf("Could not find alias %s", $this->getAlias()));
    }
    $startIndex++;

    $shellArgs = [];
    $commandValue = null;

    // コマンド値の処理
    if (isset($args[$startIndex]) && $args[$startIndex][0] !== '-') {
        $commandValue = $args[$startIndex];
        $startIndex++;
    } elseif ($this->isCommandValueRequired()) {
        throw new Exception(sprintf("%s's value is required.", $this->getAlias()));
    }

    // 引数の処理
    for ($i = $startIndex; $i < count($args); $i++) {
        $arg = $args[$i];
        if (strpos($arg, '--') === 0) {
            $key = substr($arg, 2);
        } elseif ($arg[0] === '-') {
            $key = substr($arg, 1);
        } else {
            throw new Exception('Option must start with - or --');
        }

        if (isset($args[$i + 1]) && $args[$i + 1][0] !== '-') {
            $shellArgs[$key] = $args[$i + 1];
            $i++;
        } else {
            $shellArgs[$key] = true;
        }
    }

    // コマンド値の設定
    if ($commandValue !== null) {
        $this->argsMap[$this->getAlias()] = $commandValue;
    }

    // 引数の設定
    foreach ($this->getArguments() as $argument) {
        $argString = $argument->getArgument();
        $value = null;

        if ($argument->isShortAllowed() && isset($shellArgs[$argString[0]])) {
            $value = $shellArgs[$argString[0]];
        } elseif (isset($shellArgs[$argString])) {
            $value = $shellArgs[$argString];
        }

        if ($value === null) {
            if ($argument->isRequired()) {
                throw new Exception(sprintf('Could not find the required argument %s', $argString));
            } else {
                $this->argsMap[$argString] = false;
            }
        } else {
            $this->argsMap[$argString] = $value;
        }
    }

    $this->log(json_encode($this->argsMap));
}

public static function getHelp(): string
{
    $helpString = "Command: " . static::getAlias() . 
(static::isCommandValueRequired()?" {value}":"") . PHP_EOL;

    $arguments = static::getArguments();
    if(empty($arguments)) return $helpString;

    $helpString .= "Arguments:" . PHP_EOL;

    foreach ($arguments as $argument) {
        $helpString .= "  --" . $argument->getArgument();  // long argument name
        if ($argument->isShortAllowed()) {
            $helpString .= " (-" . $argument->getArgument()[0] . ")";  // short argument name
        }
        $helpString .= ": " . $argument->getDescription();
        $helpString .= $argument->isRequired() ? " (Required)" : " (Optional)";
        $helpString .= PHP_EOL;
    }
    return $helpString;
}

public static function getAlias(): string
{
    // staticはselfと比べて遅延バインディングを行い、子クラスが$aliasをオーバーライドするときの値を使用します。
    // selfは常にこのクラスの値($alias = null)を使用します。
    return static::$alias !== null ? static::$alias : static::class;
}

public static function isCommandValueRequired(): bool{
    return static::$requiredCommandValue;
}

public function getCommandValue(): string{
    return $this->argsMap[static::getAlias()]??"";
}

// 引数の値の文字列を返し、存在するが値が設定されていない場合はtrue、存在しない場合はfalseを返します。
public function getArgumentValue(string $arg): bool|string
{
    return $this->argsMap[$arg];
}

// コマンドにログを取る方法を提供します。
protected function log(string $info): void
{
    fwrite(STDOUT, $info . PHP_EOL);
}

/** @return Argument[] */
public abstract static function getArguments(): array;

public abstract function execute(): int;
}