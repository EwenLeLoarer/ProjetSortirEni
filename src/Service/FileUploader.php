<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
class FileUploader
{
    public function __construct(
        private readonly string     $uploadDir,
        private readonly Filesystem $filesystem
    )
    {
    }

    public function upload(UploadedFile $file): string
    {
        $filename = uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($this->getUploadDir(), $filename);
        } catch (FileException) {
            // TODO
        }

        return $filename;
    }

    public function getUploadDir(): string
    {
        return $this->uploadDir;
    }

    public function delete(string $filename): void
    {
        if (null != $filename) {
            $filePath = $this->getUploadDir() . '/' . $filename;
            if ($this->filesystem->exists($filePath)) {
                $this->filesystem->remove($filePath);
            }
        }
    }
}