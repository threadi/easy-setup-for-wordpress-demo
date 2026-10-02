<?php
/**
 * Tasks to run during uninstallation of this plugin.
 *
 * @package easy-setup-for-wordpress-demo
 */

// prevent direct access.
defined( 'ABSPATH' ) || exit;

// if uninstall.php is not called by WordPress, die.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// embed the composer packages.
require __DIR__ . '/vendor/autoload.php';
