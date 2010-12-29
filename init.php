<?php defined('SYSPATH') OR die('No direct access allowed.');

$jscompile_cache_path = DOCROOT . Kohana::config('jscompile.path');

if ( ! is_writable($jscompile_cache_path)) {
    throw new Kohana_Exception('Directory :dir must be writable',
        array(':dir' => Kohana::debug_path($jscompile_cache_path)));
}

require_once 'vendor/jsmin.php';