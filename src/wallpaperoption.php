<?php

/**
 * Enumeration of wallpaper options supported by gnome-shell/gnome-flashback.
 */
class WallpaperOption extends EnumClass
{
    // Taken from https://gitlab.gnome.org/GNOME/gsettings-desktop-schemas/blob/9a7e6d33/headers/gdesktop-enums.h#L47
    public const WALLPAPER = 'wallpaper';
    public const CENTERED = 'centered';
    public const SCALED = 'scaled';
    public const STRETCHED = 'stretched';
    public const ZOOM = 'zoom';
    public const SPANNED = 'spanned';

    // Taken from https://gitlab.gnome.org/GNOME/gsettings-desktop-schemas/blob/71492d38/schemas/org.gnome.desktop.background.gschema.xml.in#L5
    protected const __default = self::ZOOM;
}