<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Compiles multiple JS files into one, optionally minifying them.
 */
class JsCompile_Core {
	// The formats that compile can return.
	const FORMAT_TAG 		= 'tag';
	const FORMAT_FILENAME	= 'filename';

	/**
	 * Compiles multiple JS files into one.
	 *
	 * @param array  $files  The files to compile.
	 * @param string $format The format to return the compiled JS files in.
	 *
	 * @return string
	 */
	public static function compile($files = array(), $format = JsCompile::FORMAT_TAG) {
		// Compiled contents of file
		$compiled = "";

        // Load config file
        $config = Kohana::$config->load('jscompile');

        // If no files to compile, no tag necessary
        if (empty($files)) {
            return "";
        }

		// Get filename to save compiled files to
		$compiled_filename = self::get_filename($files, $config['path']);

		// If file doesn't exist already, files have changed, recompile them
		if ( ! file_exists($compiled_filename)) {

            // Clear compiled folder?
            if ($config['clear_first']) {
                self::clear_folder($config['path']);
            }

			// Loop through all files
			foreach ($files as $file) {
				// If file doesn't exist, log the fact and skip
				if ( ! file_exists($file)) {
					Kohana::$log->add("ERROR", "Could not find JS file: $file");
					continue;
				}

				// Get contents of file
				$contents = file_get_contents($file);

				// Compress if allowed
				if ($config['compress']) {
					$contents = JSMin::minify($contents);
				}

				// Append
				$compiled .= "\n$contents";
			}

			// Write new file
			file_put_contents($compiled_filename, $compiled);
		}

        switch ($format) {
			case JsCompile::FORMAT_TAG:
				$result = html::script($compiled_filename);
			break;

			case JsCompile::FORMAT_FILENAME:
				$result = $compiled_filename;
			break;

			default:
				throw new Exception("Unknown format: $format.");
		}

		return $result;
    }

	/**
	 * Gets the filename that will be used to save these files.
	 *
	 * @param array  $files The files to be compiled.
	 * @param string $path  The path to save the compiled file to.
	 *
	 * @return string
	 */
	private static function get_filename($files, $path) {
        // Most recently modified file
        $last_modified = 0;

		foreach($files as $file) {
            // Check if this file was the most recently modified
            $last_modified = max(filemtime($file), $last_modified);
		}

		return $path . md5(implode("|", $files)) . "-$last_modified.js";
	}

    /**
     * Delete all files from a provided folder.
     *
     * @param string $path The path to clear.
     *
     * @return void
     */
    private static function clear_folder($path) {
        $files = glob("$path*");
        foreach ($files as $file){
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}