GNOME Wallpaper List Generator
===

This simple PHP script generates XML files for background panel of GNOME Control Center. It allows you to add your own images to GNOME wallpapers gallery.

Installation
---

Clone this repository, then run `build_application.php` script to build convenient executable PHP file and move generated script to your `$PATH`. This script requires PHP 7.1 or newer.

```
git clone https://github.com/TomaszGasior/gnome-wp-list-generator.git
cd gnome-wp-list-generator
php build_application.php
mv gnome-wp-list-generator ~/.local/bin
```

Usage
---

Move your beautiful wallpapers to `~/.local/share/backgrounds` directory and run `gnome-wp-list-generator`. Now, you can choose your wallpaper in background section of GNOME Control Center.

It's possible to set its own images directory with `--directory` option and install wallpapers configuration system-wide by `--system` switch.

TODO
---

- Ability to set wallpaper mode (zoom, centered, scaled, etc.).
- Optional (not default) recursive searching over specified directory.
- Automatically calculated fallback solid color for each wallpaper.
- Support for timed backgrounds.
- Unit tests.
