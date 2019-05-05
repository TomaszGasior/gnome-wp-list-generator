mod collection;
mod xml;

use crate::collection::ImageCollection;
use crate::xml::GccXmlFile;
use getopts::Options;
use std::env;
use std::path::PathBuf;
use xdg::BaseDirectories;

const XML_FILE_NAME: &str = "custom-backgrounds.xml";

fn main() {
    let args: Vec<String> = env::args().collect();
    let mut options = Options::new();

    options.optopt("d", "directory", &format!(
        "Set path to directory with your wallpapers. Defaults to \"{}\" or if system-wide: \"{}\".",
        get_default_wallpapers_directory(false).to_str().unwrap(),
        get_default_wallpapers_directory(true).to_str().unwrap()
    )[..], "DIR");
    options.optflag("r", "recursive", "Look for images recursively.");
    options.optflag("", "system", "Install wallpapers system-wide for all users instead for current user only. Root privileges required!");
    options.optopt("", "name", &format!(
        "Name of XML file for gnome-control-center. Defaults to \"{}\". File will be placed in \"{}\" or if system-wide: \"{}\".",
        XML_FILE_NAME,
        get_gcc_config_directory(false).to_str().unwrap(),
        get_gcc_config_directory(true).to_str().unwrap()
    )[..], "NAME.xml");
    // TODO: add option for wallpaper mode

    let options = match options.parse(&args[1..]) {
        Ok(options) => options,
        Err(_) => {
            print!("{}", options.usage("This application allows you to add your own wallpapers to background panel\nof GNOME Settings. It looks for images in your directory, generates XML file for\ngnome-control-center and saves it in proper user-wide or system-wide directory.\n\nhttps://github.com/TomaszGasior/gnome-wp-list-generator"));
            return;
        }
    };

    let (images_count, directory) = do_the_job(
        options.opt_str("directory"),
        options.opt_present("recursive"),
        options.opt_present("system"),
        options.opt_str("name"),
    );
    println!("Found {} images in \"{}\".", images_count, directory);
}

fn do_the_job(images_directory: Option<String>, recursive: bool, system_wide: bool,
              xml_file_name: Option<String>) -> (usize, String) {
    let images_directory_path = match images_directory {
        Some(images_directory) => {
            let mut path = PathBuf::new();
            path.push(images_directory);
            path
        },
        None => get_default_wallpapers_directory(system_wide),
    };

    let mut xml_file_path = get_gcc_config_directory(system_wide);
    xml_file_path.push(match xml_file_name {
        Some(xml_file_name) => xml_file_name,
        None => XML_FILE_NAME.to_string(),
    });

    let images = ImageCollection::new(&images_directory_path, recursive);
    let images_count = images.iter().count();

    let xml_file = GccXmlFile::new(images);
    xml_file.generate(&xml_file_path);

    let directory = images_directory_path.to_str().unwrap().to_string();
    (images_count, directory)
}

fn get_default_wallpapers_directory(system_wide: bool) -> PathBuf {
    let mut path = match system_wide {
        true => BaseDirectories::new().unwrap().get_data_dirs().remove(0),
        false => BaseDirectories::new().unwrap().get_data_home(),
    };
    match system_wide {
        true => { path.push("backgrounds"); path.push("custom") },
        false => path.push("backgrounds"),
    };

    path
}

fn get_gcc_config_directory(system_wide: bool) -> PathBuf {
    let mut path = match system_wide {
        true => BaseDirectories::new().unwrap().get_data_dirs().remove(0),
        false => BaseDirectories::new().unwrap().get_data_home(),
    };
    path.push("gnome-background-properties");

    path
}