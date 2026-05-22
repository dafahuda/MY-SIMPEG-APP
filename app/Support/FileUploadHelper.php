<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FileUploadHelper
{
    public static function validateAndStore(
        UploadedFile $file,
        array $allowedMimes,
        int $maxSize,
        string $disk = 'public',
        string $directory = 'uploads',
        string $fieldName = 'file'
    ): array {
        if (!in_array($file->getMimeType(), $allowedMimes, true)) {
            throw ValidationException::withMessages([
                $fieldName => 'File type not allowed.'
            ]);
        }

        if ($file->getSize() > $maxSize) {
            throw ValidationException::withMessages([
                $fieldName => 'File exceeds the maximum allowed size.'
            ]);
        }

        $uuid = (string) Str::uuid();
        $extension = strtolower($file->extension() ?: $file->getClientOriginalExtension() ?: 'bin');
        $safeFileName = $uuid . '.' . $extension;

        $filePath = $file->storeAs($directory, $safeFileName, $disk);

        return [
            'file_path' => Storage::disk($disk)->url($filePath),
            'original_name' => $file->getClientOriginalName(),
        ];
    }

    public static function delete(?string $filePath, string $disk = 'public'): bool
    {
        if (!$filePath) {
            return false;
        }

        $normalizedPath = trim($filePath);

        if (filter_var($normalizedPath, FILTER_VALIDATE_URL)) {
            $normalizedPath = (string) parse_url($normalizedPath, PHP_URL_PATH);
        }

        $normalizedPath = str_replace('\\', '/', $normalizedPath);
        $normalizedPath = ltrim($normalizedPath, '/');

        $publicPrefix = trim((string) parse_url(Storage::disk($disk)->url('/'), PHP_URL_PATH), '/');

        if ($publicPrefix !== '' && Str::startsWith($normalizedPath, $publicPrefix . '/')) {
            $normalizedPath = substr($normalizedPath, strlen($publicPrefix) + 1);
        }

        if ($normalizedPath === '') {
            return false;
        }

        return Storage::disk($disk)->delete($normalizedPath);
    }
}