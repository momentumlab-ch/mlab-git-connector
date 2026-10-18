<?php
/**
 * Central place for the plugin's fixed values.
 *
 * @package MlabGitConnector
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns the plugin settings. Filterable so a site can adjust them without editing the plugin.
 *
 * @return array<string, int|string>
 */
function mlab_git_connector_options(): array {
	$defaults = array(
		'lookup_timeout'   => 20,  // Seconds to wait for the GitHub release lookup.
		'download_timeout' => 60,  // Seconds to wait for the ZIP download.
		'time_limit'       => 300, // PHP time limit for one install run.
		'notice_ttl'       => MINUTE_IN_SECONDS,
		'asset_name'       => '.zip',
	);

	return wp_parse_args( apply_filters( 'mlab_git_connector_options', array() ), $defaults );
}

/**
 * Returns one setting.
 *
 * @param string $key Setting key from mlab_git_connector_options().
 * @return int|string
 */
function mlab_git_connector_option( string $key ) {
	$options = mlab_git_connector_options();

	return $options[ $key ] ?? '';
}
