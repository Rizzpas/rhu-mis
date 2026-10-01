<?php

namespace Tests\Unit;

use App\Rules\SecureImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class SecureImageTest extends TestCase
{
    /**
     * 1x1 valid PNG base64.
     */
    protected const VALID_PNG_BASE64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    /**
     * 1x1 valid JPEG base64.
     */
    protected const VALID_JPEG_BASE64 = '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=';

    /**
     * 1x1 valid WebP base64.
     */
    protected const VALID_WEBP_BASE64 = 'UklGRkAAAABXRUJQVlA4WAoAAAAQAAAAAAAAAAAAQUxQSAwAAAARBxAR/Q9ERP8DAABWUDggGAAAADABAJ0BKgEAAQABAAAAbwAA/v60AAAA';

    /**
     * 1x1 valid GIF base64.
     */
    protected const VALID_GIF_BASE64 = 'R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

    /**
     * 1x1 valid BMP base64.
     */
    protected const VALID_BMP_BASE64 = 'Qk06AAAAAAAAADYAAAAoAAAAAQAAAAEAAAABACAAAAAAAAYAAAASCwAAEgsAAAAAAAAAAAAA';

    /**
     * Create an UploadedFile with raw binary content.
     */
    protected function createFile(string $filename, string $content): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($filename, $content);
    }

    /**
     * Helper to run validation with SecureImage rule.
     */
    protected function validateFile(UploadedFile $file): \Illuminate\Validation\Validator
    {
        return Validator::make(
            ['image' => $file],
            ['image' => [new SecureImage()]]
        );
    }

    public function test_valid_jpeg_passes_validation(): void
    {
        $file = $this->createFile('photo.jpg', base64_decode(self::VALID_JPEG_BASE64));
        $validator = $this->validateFile($file);
        $this->assertTrue($validator->passes(), 'Valid JPEG should pass validation.');

        $fileJpeg = $this->createFile('photo.jpeg', base64_decode(self::VALID_JPEG_BASE64));
        $validatorJpeg = $this->validateFile($fileJpeg);
        $this->assertTrue($validatorJpeg->passes(), 'Valid JPEG (.jpeg) should pass validation.');
    }

    public function test_valid_png_passes_validation(): void
    {
        $file = $this->createFile('graphic.png', base64_decode(self::VALID_PNG_BASE64));
        $validator = $this->validateFile($file);
        $this->assertTrue($validator->passes(), 'Valid PNG should pass validation.');
    }

    public function test_valid_webp_passes_validation(): void
    {
        $file = $this->createFile('illustration.webp', base64_decode(self::VALID_WEBP_BASE64));
        $validator = $this->validateFile($file);
        $this->assertTrue($validator->passes(), 'Valid WebP should pass validation.');
    }

    public function test_case_insensitive_extensions_pass(): void
    {
        $fileJpgUpper = $this->createFile('IMAGE.JPG', base64_decode(self::VALID_JPEG_BASE64));
        $this->assertTrue($this->validateFile($fileJpgUpper)->passes());

        $filePngUpper = $this->createFile('IMAGE.PNG', base64_decode(self::VALID_PNG_BASE64));
        $this->assertTrue($this->validateFile($filePngUpper)->passes());

        $fileWebpMixed = $this->createFile('IMAGE.WebP', base64_decode(self::VALID_WEBP_BASE64));
        $this->assertTrue($this->validateFile($fileWebpMixed)->passes());
    }

    public function test_gif_is_rejected_with_exact_message(): void
    {
        $file = $this->createFile('animation.gif', base64_decode(self::VALID_GIF_BASE64));
        $validator = $this->validateFile($file);

        $this->assertTrue($validator->fails());
        $this->assertEquals(
            'Invalid file type. Only JPEG, JPG, PNG, and WebP images are accepted.',
            $validator->errors()->first('image')
        );
    }

    public function test_bmp_is_rejected(): void
    {
        $file = $this->createFile('bitmap.bmp', base64_decode(self::VALID_BMP_BASE64));
        $validator = $this->validateFile($file);

        $this->assertTrue($validator->fails());
        $this->assertEquals(
            'Invalid file type. Only JPEG, JPG, PNG, and WebP images are accepted.',
            $validator->errors()->first('image')
        );
    }

    public function test_svg_is_rejected(): void
    {
        $svgContent = '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"><rect width="10" height="10"/></svg>';
        $file = $this->createFile('vector.svg', $svgContent);
        $validator = $this->validateFile($file);

        $this->assertTrue($validator->fails());
        $this->assertEquals(
            'Invalid file type. Only JPEG, JPG, PNG, and WebP images are accepted.',
            $validator->errors()->first('image')
        );
    }

    public function test_tiff_is_rejected(): void
    {
        // TIFF Little-endian signature: II*\0
        $tiffContent = "II*\x00\x08\x00\x00\x00";
        $file = $this->createFile('document.tiff', $tiffContent);
        $validator = $this->validateFile($file);

        $this->assertTrue($validator->fails());
        $this->assertEquals(
            'Invalid file type. Only JPEG, JPG, PNG, and WebP images are accepted.',
            $validator->errors()->first('image')
        );
    }

    public function test_heic_is_rejected(): void
    {
        // HEIC signature: ftypheic
        $heicContent = "\x00\x00\x00\x18ftypheic\x00\x00\x00\x00mif1heic";
        $file = $this->createFile('photo.heic', $heicContent);
        $validator = $this->validateFile($file);

        $this->assertTrue($validator->fails());
        $this->assertEquals(
            'Invalid file type. Only JPEG, JPG, PNG, and WebP images are accepted.',
            $validator->errors()->first('image')
        );
    }

    public function test_pdf_is_rejected(): void
    {
        $pdfContent = "%PDF-1.4\n1 0 obj\n<<>>\nendobj\ntrailer\n<<>>\n%%EOF";
        $file = $this->createFile('report.pdf', $pdfContent);
        $validator = $this->validateFile($file);

        $this->assertTrue($validator->fails());
        $this->assertEquals(
            'Invalid file type. Only JPEG, JPG, PNG, and WebP images are accepted.',
            $validator->errors()->first('image')
        );
    }

    public function test_files_without_extensions_are_rejected(): void
    {
        $file1 = $this->createFile('avatar', base64_decode(self::VALID_PNG_BASE64));
        $validator1 = $this->validateFile($file1);
        $this->assertTrue($validator1->fails());
        $this->assertEquals(
            'Invalid file type. Only JPEG, JPG, PNG, and WebP images are accepted.',
            $validator1->errors()->first('image')
        );

        $file2 = $this->createFile('no_extension_file', 'some random content');
        $validator2 = $this->validateFile($file2);
        $this->assertTrue($validator2->fails());
    }

    public function test_double_extensions_are_rejected(): void
    {
        // image.png.exe (executable hidden as image)
        $file1 = $this->createFile('image.png.exe', "MZ\x90\x00");
        $validator1 = $this->validateFile($file1);
        $this->assertTrue($validator1->fails(), 'image.png.exe should be rejected');
        $this->assertEquals(
            'Invalid file type. Only JPEG, JPG, PNG, and WebP images are accepted.',
            $validator1->errors()->first('image')
        );

        // shell.php.jpg (PHP script hidden as JPG)
        $file2 = $this->createFile('shell.php.jpg', base64_decode(self::VALID_JPEG_BASE64));
        $validator2 = $this->validateFile($file2);
        $this->assertTrue($validator2->fails(), 'shell.php.jpg should be rejected due to double extension');

        // test.jpg.png (double image extension)
        $file3 = $this->createFile('test.jpg.png', base64_decode(self::VALID_PNG_BASE64));
        $validator3 = $this->validateFile($file3);
        $this->assertTrue($validator3->fails(), 'test.jpg.png should be rejected due to double extension');

        // exploit.phtml.webp
        $file4 = $this->createFile('exploit.phtml.webp', base64_decode(self::VALID_WEBP_BASE64));
        $validator4 = $this->validateFile($file4);
        $this->assertTrue($validator4->fails(), 'exploit.phtml.webp should be rejected due to double extension');
    }

    public function test_content_spoofing_mismatched_magic_bytes_are_rejected(): void
    {
        // PDF content disguised with .jpg extension
        $fakeJpg = $this->createFile('fake.jpg', "%PDF-1.4\n1 0 obj<<>>endobj");
        $validator1 = $this->validateFile($fakeJpg);
        $this->assertTrue($validator1->fails());
        $this->assertEquals(
            'Invalid file type. Only JPEG, JPG, PNG, and WebP images are accepted.',
            $validator1->errors()->first('image')
        );

        // Text content disguised with .png extension
        $fakePng = $this->createFile('fake.png', 'Plain text content that is not a PNG file');
        $validator2 = $this->validateFile($fakePng);
        $this->assertTrue($validator2->fails());

        // GIF content disguised with .webp extension
        $fakeWebp = $this->createFile('fake.webp', base64_decode(self::VALID_GIF_BASE64));
        $validator3 = $this->validateFile($fakeWebp);
        $this->assertTrue($validator3->fails());

        // Valid PNG content named with .jpg extension (MIME & magic bytes mismatch extension)
        $mismatched = $this->createFile('mismatched.jpg', base64_decode(self::VALID_PNG_BASE64));
        $validator4 = $this->validateFile($mismatched);
        $this->assertTrue($validator4->fails());
    }

    public function test_static_is_valid_helper(): void
    {
        $validFile = $this->createFile('photo.png', base64_decode(self::VALID_PNG_BASE64));
        $this->assertTrue(SecureImage::isValid($validFile));

        $invalidFile = $this->createFile('bad.pdf', '%PDF-1.4');
        $this->assertFalse(SecureImage::isValid($invalidFile));
    }

    public function test_static_error_message_helper(): void
    {
        $this->assertSame(
            'Invalid file type. Only JPEG, JPG, PNG, and WebP images are accepted.',
            SecureImage::errorMessage()
        );
    }
}
