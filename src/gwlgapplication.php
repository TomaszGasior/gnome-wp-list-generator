<?php

/**
 * Gnome Wallpaper List Generator.
 */
class GwlgApplication
{
    public function __construct(array $argv)
    {
        $options = getopt('h', ['help', 'directory::', 'name::', 'system']);

        if (isset($options['h']) || isset($options['help'])) {
            $this->helpAction();
        }
        else {
            $this->workAction(
                $options['directory'] ?? null,
                $options['name'] ?? null,
                isset($options['system']),
            );
        }
    }

    private function helpAction(): void
    {
        echo <<< HELP_TEXT
GNOME Wallpaper List Generator

This script generates XML file for gnome-control-center's background panel
with your own wallpapers and saves it in proper user-wide or system-wide directory.

Optional options:

    --system
        Boolean. If true, wallpapers will be installed system-wide for all users
        of this machine, otherwise wallpapers will be installed for current user.
        It's required to run this script as root to use --system option.

    --directory
        Path to directory with your wallpapers. Defaults to this path:
        "{$this->getDefaultWallpapersDirectory(false)}"
        or to this path if --system is specified:
        "{$this->getDefaultWallpapersDirectory(true)}".

    --name
        Name of XML file for gnome-control-center.
        Defaults to "{$this->getDefaultGccFile()}".
        XML file will be saved automatically in this directory:
        "{$this->getGccDirectory(false)}"
        or in this directory if --system is specified:
        "{$this->getGccDirectory(true)}".

HELP_TEXT;
    }

    private function workAction($wallpapersDirectory = null, $xmlFileName = null, $systemWide = false): void
    {
        if (null === $wallpapersDirectory) {
            $wallpapersDirectory = $this->getDefaultWallpapersDirectory($systemWide);
        }

        if (null === $xmlFileName) {
            $xmlFileName = $this->getDefaultGccFile();
        }
        $xmlFileDirectory = $this->getGccDirectory($systemWide);

        $wallpapersCollection = new ImagesCollection($wallpapersDirectory);
        $xmlGenerator = new GccXmlGenerator($wallpapersCollection);

        if (false === file_exists($xmlFileDirectory)) {
            mkdir($xmlFileDirectory, 0755, true);
        }

        file_put_contents(
            $xmlFileDirectory . '/' . $xmlFileName,
            $xmlGenerator->generate()
        );
    }

    private function findDataDirectory(string $subdirectory, bool $systemWide): string
    {
        $directory = '/usr/share';

        if (false === $systemWide) {
            $directory = getenv('XDG_DATA_HOME')
                         ? getenv('XDG_DATA_HOME')
                         : getenv('HOME') . '/.local/share';
        }

        return $directory . '/' . $subdirectory;
    }

    private function getDefaultWallpapersDirectory(bool $systemWide): string
    {
        return $this->findDataDirectory(
            $systemWide ? 'backgrounds/custom' : 'backgrounds',
            $systemWide
        );
    }

    private function getGccDirectory(bool $systemWide): string
    {
        return $this->findDataDirectory('gnome-background-properties', $systemWide);
    }

    private function getDefaultGccFile(): string
    {
        return 'custom-backgrounds.xml';
    }
}