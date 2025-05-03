<?php

namespace Tests\Services\FileReader;

use PHPUnit\Framework\TestCase;
use App\Services\FileReader\FileReader;
use Exception;

class FileReaderTest extends TestCase
{
    private string $testFilePath;

    protected function setUp(): void
    {
        // Создаем временный файл
        $this->testFilePath = tempnam(sys_get_temp_dir(), 'test_file_reader_');
        file_put_contents($this->testFilePath, "Line 1\nLine 2\nLine 3");
    }

    protected function tearDown(): void
    {
        // Удаляем файл
        if (file_exists($this->testFilePath)) {
            unlink($this->testFilePath);
        }
    }

    public function testReadLines(): void
    {
        $reader = new FileReader($this->testFilePath);

        $this->assertEquals("Line 1\n", $reader->readLine());
        $this->assertEquals("Line 2\n", $reader->readLine());
        $this->assertEquals("Line 3", $reader->readLine());
        $this->assertNull($reader->readLine()); // конец файла
    }

    public function testFileNotFoundThrowsException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to read the file');

        @new FileReader('/path/to/nonexistent/file.txt');
    }
}
