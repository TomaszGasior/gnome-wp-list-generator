use std::path::Path;
use std::slice::Iter;
use walkdir::WalkDir;

#[derive(Debug)]
pub struct ImageCollection {
    images: Vec<String>,
}

impl ImageCollection {
    pub fn new(directory: &Path, recursive: bool) -> Self {
        // TODO: change to constant
        // Taken from https://gitlab.gnome.org/GNOME/gnome-control-center/blob/2b95f957/panels/background/bg-pictures-source.c#L58
        let supported_mime_types = [
            "image/png",
            "image/jp2",
            "image/jpeg",
            "image/bmp",
            "image/svg+xml",
            "image/x-portable-anymap",
            "image/png",
        ];

        let mut instance = Self{images: Vec::new()};

        let directory = match recursive {
            true => WalkDir::new(directory),
            false => WalkDir::new(directory).max_depth(1),
        };
        let directory = directory.into_iter();

        for file in directory {
            let file = file.unwrap();
            let mime_type = tree_magic::from_filepath(file.path());

            if supported_mime_types.contains(&mime_type.as_str()) {
                let file_path = file.path().to_str().unwrap().to_string();
                instance.images.push(file_path);
            }
        }

        instance.images.sort();

        instance
    }

    pub fn iter(&self) -> Iter<String> {
        self.images.iter()
    }
}