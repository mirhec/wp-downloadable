<?php
/*
Plugin Name: Downloadable
Plugin URI: http://none
Description: ...
Version: 1.0
Author: Mirko Hecky
Author URI: none
License: GPL2
*/

add_action('init', 'downloadable_register_shortcodes');

function downloadable_register_shortcodes() {
    // register shortcode [downloadable path="path/to/your/ftp/folder" depth=2 files_per_folder=10]
    add_shortcode('downloadable', 'downloadable_download');
}

function downloadable_handleDir($path, $dir, $depth, $max_files_in_dir) {
    if($depth < 0) return '';

    $s = '<h2>' . $dir . '</h2><ul>';

    // handle all files in this dir
    $p = $path . '/' . $dir;
    $arr = scandir($p, 1);
    $max = $max_files_in_dir;
    foreach($arr as $file) {
        if ($file == "." || $file == "..")
            continue;

        if(is_dir($p . '/' . $file))
            $s = $s . downloadable_handleDir($p, $file, $depth);
        else
        {
            if($max == 0) continue;
            $s = $s . downloadable_handleFile($p, $file);
            $max -= 1;
        }
    }

    $s = $s . '</ul>';

    return $s;
}

function downloadable_handleFile($path, $file) {
    // check the extension
    $plugin_dir_path = dirname(__FILE__);
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $extpath = $plugin_dir_path . '/img/extensions/' . $ext . '.png';
    $img = '';
    if(file_exists($extpath))
        $img = '<img width="24" src="/wp-content/plugins/downloadable/img/extensions/' . $ext . '.png"></img>';
    return '<li>' . $img . ' <a href="/' . $path . '/' . $file . '">' . $file . '</a></li>';
}

function downloadable_download($args, $content) {
    if(isset($args['path']))
        $path = $args['path'];
    else
        return '';

    if(isset($args['depth']))
        $depth = $args['depth'];
    else
        $depth = 1;

    if(isset($args['max_files_in_dir']))
        $max_files_in_dir = $args['max_files_in_dir'];
    else
        $max_files_in_dir = 1;

    $files = '';

    $arr = scandir($path, 1);
    $max = $max_files_in_dir;
    foreach($arr as $file) {
        if ($file == "." || $file == "..")
            continue;

        if(is_dir($path . '/' . $file))
            $files = $files . downloadable_handleDir($path, $file, $depth, $max_files_in_dir);
        else
        {
            if($max == 0) continue;
            $files = $files . downloadable_handleFile($file);
            $max -= 1;
        }
    }

    return $files;
}

?>