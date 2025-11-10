<?php
/**
 * Plugin Name:       RUNA Workshop Suite
 * Plugin URI:        https://example.com
 * Description:       Foundation for the Runa workshop suite including custom post type scaffolding.
 * Version:           0.1.0
 * Author:            Runa
 * Author URI:        https://example.com
 * Text Domain:       runa-mod-workshop-suite
 *
 * @package RunaModWorkshopSuite
 */

declare(strict_types=1);

namespace Runa\WorkshopSuite;

if (! defined('ABSPATH')) {
	exit;
}

if (! defined('RUNA_WSS_PLUGIN_FILE')) {
	define('RUNA_WSS_PLUGIN_FILE', __FILE__);
}

spl_autoload_register(
	static function (string $class): void {
		if (strpos($class, __NAMESPACE__ . '\\') !== 0) {
			return;
		}

		$relative = substr($class, strlen(__NAMESPACE__) + 1);
		$relative = str_replace('\\', DIRECTORY_SEPARATOR, $relative);
		$file     = __DIR__ . '/src/' . $relative . '.php';

		if (file_exists($file)) {
			require_once $file;
		}
	}
);

require_once __DIR__ . '/src/Plugin.php';

/**
 * Returns the plugin singleton.
 */
function plugin(): Plugin {
	return Plugin::instance();
}

plugin()->boot();
