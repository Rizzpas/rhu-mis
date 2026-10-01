<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

/**
 * Secure image upload validation rule.
 *
 * Validates uploaded image files by checking:
 * 1. File extension (case-insensitive) — only jpg, jpeg, png, webp
 * 2. MIME type — only image/jpeg, image/png, image/webp
 * 3. Magic bytes — verifies actual file content matches expected image format
 *
 * Rejects: GIF, BMP, SVG, TIFF, HEIC, PDF, files without extensions,
 * and double-extension attacks (e.g. image.png.exe).
 */
class SecureImage implements ValidationRule
{
    /**
     * The error message for rejected files.
     */
    protected const ERROR_MESSAGE = 'Invalid file type. Only JPEG, JPG, PNG, and WebP images are accepted.';

    /**
     * Allowed file extensions (lowercase).
     */
    protected const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * Allowed MIME types.
     */
    protected const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/webp'];

    /**
     * Magic byte signatures for allowed image formats.
     *
     * JPEG: FF D8 FF
     * PNG:  89 50 4E 47 0D 0A 1A 0A
     * WebP: RIFF....WEBP (bytes 0-3 = RIFF, bytes 8-11 = WEBP)
     */
    protected const MAGIC_BYTES = [
        'jpeg' => ["\xFF\xD8\xFF"],
        'png'  => ["\x89\x50\x4E\x47\x0D\x0A\x1A\x0A"],
        'webp' => ['RIFF'],  // Additional check for 'WEBP' at offset 8
    ];

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile || !$value->isValid()) {
            $fail(self::ERROR_MESSAGE);
            return;
        }

        // 1. Validate file extension (rejects double extensions, missing extensions, and non-whitelisted formats)
        if (!$this->hasValidExtension($value)) {
            $fail(self::ERROR_MESSAGE);
            return;
        }

        // 2. Validate MIME type detected server-side by PHP's finfo
        if (!$this->hasValidMimeType($value)) {
            $fail(self::ERROR_MESSAGE);
            return;
        }

        // 3. Validate magic bytes (actual file content) and format consistency
        if (!$this->hasValidMagicBytes($value)) {
            $fail(self::ERROR_MESSAGE);
            return;
        }
    }

    /**
     * Check the file extension is allowed, case-insensitive.
     * Also rejects files without extensions and double extensions (e.g. image.png.exe, shell.php.jpg).
     */
    protected function hasValidExtension(UploadedFile $file): bool
    {
        $originalName = $file->getClientOriginalName();

        // Reject files without a name or extension
        if (empty($originalName)) {
            return false;
        }

        // Check for double extensions (e.g. image.png.exe, file.jpg.php, shell.php.jpg)
        // Count the dots — a valid filename should have exactly one dot before the extension
        $basename = pathinfo($originalName, PATHINFO_FILENAME);
        if (str_contains($basename, '.')) {
            return false;
        }

        $extension = strtolower($file->getClientOriginalExtension());

        // Reject empty extensions (files without extensions)
        if (empty($extension)) {
            return false;
        }

        return in_array($extension, self::ALLOWED_EXTENSIONS, true);
    }

    /**
     * Check the MIME type determined by PHP (server-side detection), not the client header.
     * Also verifies that the MIME type is consistent with the file extension.
     */
    protected function hasValidMimeType(UploadedFile $file): bool
    {
        // Use getMimeType() which uses PHP's finfo (magic bytes via fileinfo extension)
        $mimeType = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($mimeType, self::ALLOWED_MIMES, true)) {
            return false;
        }

        // Ensure extension corresponds to MIME type
        return match ($extension) {
            'jpg', 'jpeg' => $mimeType === 'image/jpeg',
            'png' => $mimeType === 'image/png',
            'webp' => $mimeType === 'image/webp',
            default => false,
        };
    }

    /**
     * Read the first bytes of the file and verify they match known image format signatures,
     * and match the file extension.
     */
    protected function hasValidMagicBytes(UploadedFile $file): bool
    {
        $path = $file->getRealPath();

        if (!$path || !file_exists($path)) {
            return false;
        }

        $handle = @fopen($path, 'rb');
        if (!$handle) {
            return false;
        }

        $header = fread($handle, 12);
        fclose($handle);

        if ($header === false || strlen($header) < 3) {
            return false;
        }

        $extension = strtolower($file->getClientOriginalExtension());

        // Check JPEG: starts with FF D8 FF
        if (in_array($extension, ['jpg', 'jpeg'], true)) {
            return str_starts_with($header, "\xFF\xD8\xFF");
        }

        // Check PNG: starts with 89 50 4E 47 0D 0A 1A 0A
        if ($extension === 'png') {
            return str_starts_with($header, "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A");
        }

        // Check WebP: starts with RIFF and has WEBP at offset 8
        if ($extension === 'webp') {
            return strlen($header) >= 12
                && str_starts_with($header, 'RIFF')
                && substr($header, 8, 4) === 'WEBP';
        }

        return false;
    }

    /**
     * Static helper to validate a single file and return true/false.
     * Useful for controllers that need to check files manually.
     */
    public static function isValid(UploadedFile $file): bool
    {
        $rule = new self();
        $isValid = true;

        $rule->validate('file', $file, function () use (&$isValid) {
            $isValid = false;
        });

        return $isValid;
    }

    /**
     * Get the error message constant for use in controllers/responses.
     */
    public static function errorMessage(): string
    {
        return self::ERROR_MESSAGE;
    }
}
