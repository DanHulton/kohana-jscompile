<?php defined('SYSPATH') OR die('No direct access allowed.');

$jscompile_cache_path = DOCROOT . Kohana::$config->load('jscompile.path');

if ( ! is_writable($jscompile_cache_path)) {
    throw new Kohana_Exception('Directory :dir must be writable',
        array(':dir' => Debug::path($jscompile_cache_path)));
}

require_once 'vendor/jsmin.php';