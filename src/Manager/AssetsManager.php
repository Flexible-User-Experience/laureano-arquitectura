<?php

namespace App\Manager;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class AssetsManager
{
    public const MIME_APPLICATION_PDF_TYPE = 'application/pdf';
    public const MIME_APPLICATION_PDF_X_TYPE = 'application/x-pdf';
    public const MIME_IMAGE_JPG_TYPE = 'image/jpg';
    public const MIME_IMAGE_JPEG_TYPE = 'image/jpeg';
    public const MIME_IMAGE_PNG_TYPE = 'image/png';
    public const MIME_IMAGE_GIF_TYPE = 'image/gif';

    private Filesystem $filesystem;
    private string $projectRootPathDir;
    private string $publicPathDir;

    public function __construct(string $kpd)
    {
        $this->filesystem = new Filesystem();
        $this->projectRootPathDir = $kpd.DIRECTORY_SEPARATOR;
        $this->publicPathDir = $this->projectRootPathDir.'public';
    }

    public function getProjectRootDir(): string
    {
        return $this->projectRootPathDir;
    }

    public function getLocalPublicPath(string $path): string
    {
        $publicPath = Path::makeAbsolute($path, $this->publicPathDir);
        if (!$this->filesystem->exists($publicPath)) {
            throw new FileNotFoundException();
        }

        return $publicPath;
    }

    public function fileExists(string $filepath): bool
    {
        return $this->filesystem->exists($filepath);
    }
}
