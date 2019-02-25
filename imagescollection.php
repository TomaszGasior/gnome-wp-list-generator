<?php

/**
 * Represents collection of images for XML file generator.
 */
class ImagesCollection implements IteratorAggregate
{
    // Taken from https://gitlab.gnome.org/GNOME/gnome-control-center/blob/2b95f957/panels/background/bg-pictures-source.c#L58
    private const SUPPORTED_MIME_TYPES = [
        'image/png',
        'image/jp2',
        'image/jpeg',
        'image/bmp',
        'image/svg+xml',
        'image/x-portable-anymap',
        'image/png',
    ];

    private $images = [];

    public function __construct(string $directory)
    {
        $this->collectImages($directory);
    }

    private function collectImages(string $directory): void
    {
        $isFileImageType = function(string $filePath): bool
        {
            $mimeType = mime_content_type($filePath);

            return in_array($mimeType, self::SUPPORTED_MIME_TYPES);
        };

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $directory,
                FilesystemIterator::SKIP_DOTS | FilesystemIterator::CURRENT_AS_PATHNAME
            ),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $filePath) {
            if ($isFileImageType($filePath)) {
                $this->images[] = $filePath;
            }
        }
    }

    public function getIterator(): iterable
    {
        foreach ($this->images as $imagePath) {
            yield $imagePath;
        }
    }
}