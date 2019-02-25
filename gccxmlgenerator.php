<?php

/**
 * Generates XML file for gnome-control-center's background panel.
 */
class GccXmlGenerator
{
    private $collection;
    private $wallpaperOption;

    public function __construct(ImagesCollection $collection)
    {
        $this->collection = $collection;
        $this->wallpaperOption = new WallpaperOption;
    }

    public function setWallpaperOption(WallpaperOption $wallpaperOption): void
    {
        $this->wallpaperOption = $wallpaperOption;
    }

    public function generate(): string
    {
        $implementation = new DOMImplementation();
        $document = $implementation->createDocument(
            '', '',
            $implementation->createDocumentType('wallpapers', '', 'gnome-wp-list.dtd')
        );
        $document->formatOutput = true;
        $document->version = '1.0';

        $wallpapers = $document->createElement('wallpapers');
        $document->appendChild($wallpapers);

        foreach ($this->collection as $imagePath) {
            $wallpaper = $document->createElement('wallpaper');

            $wallpaper->setAttribute('deleted', 'false');
            $wallpaper->appendChild(
                // TODO: humanize file name
                $document->createElement('name', basename($imagePath))
            );
            $wallpaper->appendChild(
                $document->createElement('filename', $imagePath)
            );
            $wallpaper->appendChild(
                $document->createElement('options', $this->wallpaperOption)
            );
            $wallpaper->appendChild(
                // TODO: don't hardcode this color, calculate it from image
                $document->createElement('pcolor', '#000000')
            );

            $wallpapers->appendChild($wallpaper);
        }

        return $document->saveXML();
    }
}