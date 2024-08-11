<?php
namespace Helpers;
use Types\ValueType;

class ValidationHelper
{
    private const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/gif'];
    private const MAX_IMAGE_SIZE = 5 * 1024 * 1024; // 5MB
    private const MAX_IMAGE_WIDTH = 5000;
    private const MAX_IMAGE_HEIGHT = 5000;

    public static function integer($value, float $min = -INF, float $max = INF): int
    {
        $value = filter_var($value, FILTER_VALIDATE_INT, ["min_range" => (int) $min, "max_range"=>(int) $max]);
        if ($value === false) throw new \InvalidArgumentException("The provided value is not a valid integer.");
        return $value;
    }

    public static function validateImage($file)
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['valid' => false, 'error' => 'No file uploaded'];
        }

        if (!in_array($file['type'], self::ALLOWED_IMAGE_TYPES)) {
            return ['valid' => false, 'error' => 'Invalid file type. Allowed types are JPEG, PNG, and GIF'];
        }

        if ($file['size'] > self::MAX_IMAGE_SIZE) {
            return ['valid' => false, 'error' => 'File too large. Maximum size is 5MB'];
        }

        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return ['valid' => false, 'error' => 'Invalid image file'];
        }

        if ($imageInfo[0] > self::MAX_IMAGE_WIDTH || $imageInfo[1] > self::MAX_IMAGE_HEIGHT) {
            return ['valid' => false, 'error' => "Image dimensions are too large. Maximum dimensions are " . self::MAX_IMAGE_WIDTH . "x" . self::MAX_IMAGE_HEIGHT . " pixels"];
        }

        return ['valid' => true];
    }

    public static function sanitizeFilename($filename)
    {
        $filename = preg_replace("/[^a-zA-Z0-9.-]/", "_", $filename);
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

    public static function validateDate(string $date, string $format = 'Y-m-d'): string
    {
        $d = \DateTime::createFromFormat($format, $date);
        if ($d && $d->format($format) === $date) {
            return $date;
        }

        throw new \InvalidArgumentException(sprintf("Invalid date format for %s. Required format: %s", $date, $format));
    }

    public static function validateFields(array $fields, array $data): array
    {
        $validatedData = [];

        foreach ($fields as $field => $type) {
            if (!isset($data[$field]) || ($data)[$field] === '') {
                throw new \InvalidArgumentException("Missing field: $field");
            }

            $value = $data[$field];

            $validatedValue = match ($type) {
                ValueType::STRING => is_string($value) ? $value : throw new \InvalidArgumentException("The provided value is not a valid string."),
                ValueType::INT => self::integer($value),
                ValueType::FLOAT => filter_var($value, FILTER_VALIDATE_FLOAT),
                ValueType::DATE => self::validateDate($value),
                default => throw new \InvalidArgumentException(sprintf("Invalid type for field: %s, with type %s", $field, $type)),
            };

            if ($validatedValue === false) {
                throw new \InvalidArgumentException(sprintf("Invalid value for field: %s", $field));
            }
            $validatedData[$field] = $validatedValue;
        }

        return $validatedData;
    }

    public static function validateAndSaveImage(array $file): ?string
    {
        $validationResult = self::validateImage($file);
        if (!$validationResult['valid']) {
            throw new \Exception($validationResult['error']);
        }

        $uploadDir = __DIR__ . '/../uploads/';
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                throw new \Exception('Failed to create upload directory.');
            }
        }

        $fileName = md5(uniqid()) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $filePath = $uploadDir . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            throw new \Exception('Failed to move uploaded file.');
        }

        return 'uploads/' . $fileName;
    }

    public static function resizeImage(string $filePath, int $maxWidth = 800, int $maxHeight = 600): bool
    {
        $fullFilePath = __DIR__ . '/../' . $filePath;
        
        if (!file_exists($fullFilePath)) {
            throw new \Exception('Image file not found: ' . $fullFilePath);
        }

        list($width, $height, $type) = getimagesize($fullFilePath);

        if ($width <= $maxWidth && $height <= $maxHeight) {
            return true; // No need to resize
        }

        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = round($width * $ratio);
        $newHeight = round($height * $ratio);

        $srcImage = imagecreatefromstring(file_get_contents($fullFilePath));
        $dstImage = imagecreatetruecolor($newWidth, $newHeight);

        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($dstImage, $fullFilePath, 90);
                break;
            case IMAGETYPE_PNG:
                imagepng($dstImage, $fullFilePath, 9);
                break;
            case IMAGETYPE_GIF:
                imagegif($dstImage, $fullFilePath);
                break;
            default:
                return false;
        }

        imagedestroy($srcImage);
        imagedestroy($dstImage);

        return true;
    }
}