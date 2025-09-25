<?php

namespace App\Tests\Services;

use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileUploaderTest extends WebTestCase
{
    public function testUploadMovesFileAndReturnsFilename(): void
    {
        $file = $this->createMock(UploadedFile::class);
        $file->expects($this->once())
            ->method('guessExtension')
            ->willReturn('jpg');

        $file->expects($this->once())
            ->method('move')
            ->with(
                $this->equalTo('/tmp'),
                $this->matchesRegularExpression('/^[a-z0-9]+\.[a-z0-9]+$/')
            );

        $filesystem = $this->createMock(Filesystem::class);
        $uploader = new FileUploader('/tmp', $filesystem);

        $filename = $uploader->upload($file);

        $this->assertMatchesRegularExpression('/^[a-z0-9]+\.[a-z0-9]+$/', $filename);
    }

    public function testUploadHandlesFileExceptionAndStillReturnsFilename(): void
    {
        $file = $this->createMock(UploadedFile::class);
        $file->method('guessExtension')->willReturn('jpg');
        $file->method('move')->willThrowException(new FileException('Move failed'));

        $filesystem = $this->createMock(Filesystem::class);
        $uploader = new FileUploader('/tmp', $filesystem);

        $filename = $uploader->upload($file);

        $this->assertMatchesRegularExpression('/^[a-z0-9]+\.[a-z0-9]+$/', $filename);
    }

    public function testDeleteRemovesFileIfItExists(): void
    {
        $filesystem = $this->createMock(Filesystem::class);

        $filesystem->expects($this->once())
            ->method('exists')
            ->with('/tmp/test.jpg')
            ->willReturn(true);

        $filesystem->expects($this->once())
            ->method('remove')
            ->with('/tmp/test.jpg');

        $uploader = new FileUploader('/tmp', $filesystem);
        $uploader->delete('test.jpg');
    }

    public function testDeleteDoesNothingIfFileDoesNotExist(): void
    {
        $filesystem = $this->createMock(Filesystem::class);

        $filesystem->expects($this->once())
            ->method('exists')
            ->with('/tmp/test.jpg')
            ->willReturn(false);

        $filesystem->expects($this->never())
            ->method('remove');

        $uploader = new FileUploader('/tmp', $filesystem);
        $uploader->delete('test.jpg');
    }

    public function testDeleteDoesNothingIfFilenameIsNull(): void
    {
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects($this->never())->method('exists');
        $filesystem->expects($this->never())->method('remove');

        $uploader = new FileUploader('/tmp', $filesystem);
        $uploader->delete(null);
    }




}