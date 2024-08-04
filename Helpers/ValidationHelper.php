<?php
namespace Helpers;

class ValidationHelper
{
    public static function integer($value, float $min = -INF, float $max = INF): int
    {
        // PHPには、データを検証する組み込み関数があります。詳細は
        // https://www.php.net/manual/en/filter.filters.validate.php を参照ください。
        $value = filter_var($value, FILTER_VALIDATE_INT, ["min_range" => (int) $min, "max_range"=>(int) $max]);
        // 結果がfalseの場合、フィルターは失敗したことになります。
        if ($value === false) throw new \InvalidArgumentException("The provided value is not a valid integer.");
        // 値がすべてのチェックをパスしたら、そのまま返します。
        return $value;
    }

    public static function validateImage($file)
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['valid' => false, 'error' => 'No file uploaded'];
        }

        if (!in_array($file['type'], $allowedTypes)) {
            return ['valid' => false, 'error' => 'Invalid file type. Allowed types are JPEG, PNG, and GIF'];
        }

        if ($file['size'] > $maxSize) {
            return ['valid' => false, 'error' => 'File too large. Maximum size is 5MB'];
        }

        // 画像の内容を確認
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return ['valid' => false, 'error' => 'Invalid image file'];
        }

        // 追加のセキュリティチェック（例：画像の寸法制限）を行うことができます
        $maxWidth = 5000;
        $maxHeight = 5000;
        if ($imageInfo[0] > $maxWidth || $imageInfo[1] > $maxHeight) {
            return ['valid' => false, 'error' => "Image dimensions are too large. Maximum dimensions are {$maxWidth}x{$maxHeight} pixels"];
        }

        return ['valid' => true];
    }

    public static function sanitizeFilename($filename)
    {
        // ファイル名から危険な文字を取り除く
        $filename = preg_replace("/[^a-zA-Z0-9.-]/", "_", $filename);
        // ファイル名の先頭のドットを取り除く（隠しファイル対策）
        $filename = ltrim($filename, '.');
        return $filename;
    }

    public static function validateString($value, $minLength = 1, $maxLength = INF)
    {
        $length = mb_strlen($value);
        if ($length < $minLength) {
            throw new \InvalidArgumentException("String is too short. Minimum length is {$minLength}");
        }
        if ($length > $maxLength) {
            throw new \InvalidArgumentException("String is too long. Maximum length is {$maxLength}");
        }
        return $value;
    }

    public static function validateEmail($email)
    {
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if ($email === false) {
            throw new \InvalidArgumentException("Invalid email address");
        }
        return $email;
    }
}