<?php
use Database\MySQLWrapper;

echo "Starting database setup...\n";
$mysqli = new MySQLWrapper();

try {
    // トランザクション開始
    $mysqli->begin_transaction();

    echo "Attempting to create tables...\n";
    $sqlFiles = glob(__DIR__ . '/Examples/*.sql');
    foreach ($sqlFiles as $sqlFile) {
        $filename = basename($sqlFile);
        echo "Attempting to execute $filename...\n";
        $sql = file_get_contents($sqlFile);
        $result = $mysqli->query($sql);
        if ($result === false) {
            throw new Exception('Could not execute query: ' . $mysqli->error);
        } else {
            echo "Successfully executed $filename\n";
        }
    }

    // トランザクションのコミット
    $mysqli->commit();
    echo "All queries executed successfully. Changes committed.\n";

} catch (Exception $e) {
    // エラーが発生した場合、トランザクションをロールバック
    $mysqli->rollback();
    echo "An error occurred. All changes have been rolled back: " . $e->getMessage() . "\n";
} finally {
    // $mysqliオブジェクトをクローズしない
    // データベース接続は init-app.php で閉じる
}