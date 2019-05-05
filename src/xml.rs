use crate::collection::ImageCollection;
use std::fs;
use std::fs::File;
use std::path::Path;
use std::io::prelude::*;
use treexml::{Document, Element, ElementBuilder};

#[derive(Debug)]
pub enum WallpaperOption {
    Wallpaper,
    Centered,
    Scaled,
    Stretched,
    Zoom,
    Spanned,
}

#[derive(Debug)]
pub struct GccXmlFile {
    images: ImageCollection,
    pub wallpaper_option: WallpaperOption,
}

impl GccXmlFile {
    pub fn new(images: ImageCollection) -> Self {
        let instance = Self {
            images,

            // Taken from https://gitlab.gnome.org/GNOME/gsettings-desktop-schemas/blob/71492d38/schemas/org.gnome.desktop.background.gschema.xml.in#L5
            wallpaper_option: WallpaperOption::Zoom,
        };

        instance
    }

    pub fn generate(&self, xml_file_path: &Path) {
        let document = self.prepare_document();

        let directory = xml_file_path.parent().unwrap();
        if !directory.exists() {
            let directory = directory.to_str().unwrap().to_string();
            fs::create_dir_all(directory);
        }

        let mut xml_file = File::create(xml_file_path).unwrap();
        xml_file.write_all(document.to_string().as_bytes());
    }

    fn prepare_document(&self) -> Document {
        let mut root = Element::new("wallpapers");

        for image_path in self.images.iter() {
            root.children.push(
                ElementBuilder::new("wallpaper")
                    .attr("deleted", "false")
                    .children(vec![
                        ElementBuilder::new("name")
                            // TODO: can I do it better?
                            .text(Path::new(image_path).file_name().unwrap().to_str().unwrap()),
                        ElementBuilder::new("filename")
                            .text(image_path),
                        ElementBuilder::new("options")
                            .text(self.get_wallpaper_option()),
                        ElementBuilder::new("pcolor")
                            // TODO: somehow calculate this value from the image
                            .text("#000000"),
                    ])
                    .element()
            );
        }

        let mut document = Document::new();
        document.root = Some(root);

        document
    }

    fn get_wallpaper_option(&self) -> &str {
        return match self.wallpaper_option {
            // Taken from https://gitlab.gnome.org/GNOME/gsettings-desktop-schemas/blob/9a7e6d33/headers/gdesktop-enums.h#L47
            WallpaperOption::Wallpaper => "wallpaper",
            WallpaperOption::Centered => "centered",
            WallpaperOption::Scaled => "scaled",
            WallpaperOption::Stretched => "stretched",
            WallpaperOption::Zoom => "zoom",
            WallpaperOption::Spanned => "spanned",
        }
    }
}