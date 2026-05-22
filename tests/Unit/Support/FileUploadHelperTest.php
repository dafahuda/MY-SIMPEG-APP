<?php

namespace Tests\Unit\Support;

use App\Support\FileUploadHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class FileUploadHelperTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_helper_rejects_dangerous_mime_type_with_default_field_key()
    {
        $file = UploadedFile::fake()->create('dangerous.php', 100, 'application/x-php');

        try {
            FileUploadHelper::validateAndStore(
                $file,
                ['application/pdf', 'image/jpeg', 'image/png'],
                2048 * 1024,
                'public',
                'document'
            );
            $this->fail('Expected ValidationException was not thrown.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('file', $e->errors());
            $this->assertSame('File type not allowed.', $e->errors()['file'][0]);
        }
    }

    public function test_helper_rejects_dangerous_mime_type_with_custom_field_key()
    {
        $file = UploadedFile::fake()->create('dangerous.php', 100, 'application/x-php');

        try {
            FileUploadHelper::validateAndStore(
                $file,
                ['application/pdf', 'image/jpeg', 'image/png'],
                2048 * 1024,
                'public',
                'document',
                'file_sertifikat_diklat'
            );
            $this->fail('Expected ValidationException was not thrown.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('file_sertifikat_diklat', $e->errors());
        }
    }

    public function test_helper_rejects_oversize_file_with_custom_field_key()
    {
        $file = UploadedFile::fake()->create('oversize.pdf', 2049, 'application/pdf');

        try {
            FileUploadHelper::validateAndStore(
                $file,
                ['application/pdf', 'image/jpeg', 'image/png'],
                2048 * 1024,
                'public',
                'document',
                'file_sertifikat_diklat'
            );
            $this->fail('Expected ValidationException was not thrown.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('file_sertifikat_diklat', $e->errors());
            $this->assertSame('File exceeds the maximum allowed size.', $e->errors()['file_sertifikat_diklat'][0]);
        }
    }

    public function test_helper_accepts_valid_pdf_and_returns_uuid_file_path()
    {
        $file = UploadedFile::fake()->create('valid.pdf', 500, 'application/pdf');

        $result = FileUploadHelper::validateAndStore(
            $file,
            ['application/pdf', 'image/jpeg', 'image/png'],
            2048 * 1024,
            'public',
            'document'
        );

        $this->assertArrayHasKey('file_path', $result);
        $this->assertArrayHasKey('original_name', $result);
        $this->assertSame('valid.pdf', $result['original_name']);

        $path = parse_url($result['file_path'], PHP_URL_PATH) ?: $result['file_path'];
        $relativePath = preg_replace('#^/?storage/#', '', $path);

        $this->assertMatchesRegularExpression(
            '#^document/[0-9a-f\-]{36}\.pdf$#',
            $relativePath
        );

        Storage::disk('public')->assertExists($relativePath);
    }

    public function test_helper_uses_mime_based_extension_for_unicode_double_extension_filename()
    {
        $file = UploadedFile::fake()->create('résumé.final.pdf.php', 300, 'application/pdf');

        $result = FileUploadHelper::validateAndStore(
            $file,
            ['application/pdf'],
            2048 * 1024,
            'public',
            'document'
        );

        $path = parse_url($result['file_path'], PHP_URL_PATH) ?: $result['file_path'];
        $relativePath = preg_replace('#^/?storage/#', '', $path);

        $this->assertSame('résumé.final.pdf.php', $result['original_name']);
        $this->assertStringEndsWith('.pdf', $relativePath);
        $this->assertStringNotContainsString('résumé.final.pdf.php', $relativePath);
        Storage::disk('public')->assertExists($relativePath);
    }

    public function test_helper_delete_supports_full_public_url_and_storage_prefixed_path()
    {
        Storage::disk('public')->put('document/delete-url.pdf', 'dummy');
        Storage::disk('public')->put('document/delete-prefixed.pdf', 'dummy');

        $publicUrl = Storage::disk('public')->url('document/delete-url.pdf');

        $this->assertTrue(FileUploadHelper::delete($publicUrl, 'public'));
        Storage::disk('public')->assertMissing('document/delete-url.pdf');

        $this->assertTrue(FileUploadHelper::delete('/storage/document/delete-prefixed.pdf', 'public'));
        Storage::disk('public')->assertMissing('document/delete-prefixed.pdf');
    }

    public function test_helper_delete_is_idempotent_for_same_relative_path()
    {
        Storage::disk('public')->put('document/idempotent-delete.pdf', 'dummy');

        $this->assertTrue(FileUploadHelper::delete('document/idempotent-delete.pdf', 'public'));
        $this->assertTrue(FileUploadHelper::delete('document/idempotent-delete.pdf', 'public'));
        Storage::disk('public')->assertMissing('document/idempotent-delete.pdf');
        $this->assertFalse(FileUploadHelper::delete(null, 'public'));
    }
}
