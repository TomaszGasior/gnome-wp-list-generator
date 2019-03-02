#!/usr/bin/php
<?php

const SCRIPT_NAME = 'gnome-wp-list-generator';

file_put_contents(SCRIPT_NAME, '#!/usr/bin/php' . PHP_EOL . '<?php' . PHP_EOL);

foreach (glob('src/*.php') as $file) {
    $content = str_replace(['<?php', '?>'], '', php_strip_whitespace($file));
    file_put_contents(SCRIPT_NAME, $content, FILE_APPEND);
}

chmod(SCRIPT_NAME, 0755);