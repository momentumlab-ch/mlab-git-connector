<?php
/**
 * Plugin Name:       MLab Git Connector
 * Plugin URI:        https://github.com/momentumlab-ch/mlab-git-connector
 * Description:       Install WordPress plugins directly from a GitHub repository.
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP:      8.0
 * Author:            MomentumLab
 * License:           GPL-2.0-or-later
 * Text Domain:       mlab-git-connector
 * Update URI:        https://github.com/momentumlab-ch/mlab-git-connector
 */

defined( 'ABSPATH' ) || exit;

define( 'MLAB_GIT_CONNECTOR_VERSION', '1.0.0' );
define( 'MLAB_GIT_CONNECTOR_DIR', plugin_dir_path( __FILE__ ) );

require_once MLAB_GIT_CONNECTOR_DIR . 'inc/updater.php';
require_once MLAB_GIT_CONNECTOR_DIR . 'inc/installer.php';
require_once MLAB_GIT_CONNECTOR_DIR . 'inc/admin-page.php';

add_action( 'admin_menu', 'mlab_git_connector_register_page' );
add_action( 'admin_post_mlab_git_connector_install', 'mlab_git_connector_handle_install' );
