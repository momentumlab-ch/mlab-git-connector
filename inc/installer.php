<?php
/**
 * Downloads a plugin ZIP from a GitHub release and installs it through WordPress.
 *
 * @package MlabGitConnector
 */

defined( 'ABSPATH' ) || exit;

/**
 * Turns a GitHub repository URL into its owner and repository name.
 *
 * @param string $url Repository URL, e.g. https://github.com/owner/repo.
 * @return array{owner: string, repo: string}|WP_Error
 */
function mlab_git_connector_parse_repo_url( $url ) {
	$parts = wp_parse_url( trim( (string) $url ) );
	$host  = isset( $parts['host'] ) ? strtolower( $parts['host'] ) : '';

	if ( empty( $parts['scheme'] ) || 'https' !== $parts['scheme'] || 'github.com' !== $host ) {
		return new WP_Error( 'mlab_git_connector_host', __( 'Please enter an https GitHub address, for example https://github.com/owner/repo.', 'mlab-git-connector' ) );
	}

	$segments = array_values( array_filter( explode( '/', isset( $parts['path'] ) ? $parts['path'] : '' ) ) );
	if ( count( $segments ) < 2 ) {
		return new WP_Error( 'mlab_git_connector_path', __( 'The address must contain the owner and the repository name.', 'mlab-git-connector' ) );
	}

	$owner = $segments[0];
	$repo  = preg_replace( '/\.git$/', '', $segments[1] );

	if ( ! preg_match( '/^[A-Za-z0-9-]+$/', $owner ) || ! preg_match( '/^[A-Za-z0-9._-]+$/', $repo ) ) {
		return new WP_Error( 'mlab_git_connector_path', __( 'The owner or repository name contains invalid characters.', 'mlab-git-connector' ) );
	}

	return array(
		'owner' => $owner,
		'repo'  => $repo,
	);
}

/**
 * Builds the request arguments for GitHub. Adds the token only if one is defined.
 *
 * @param string $accept Value for the Accept header.
 * @return array<string, mixed>
 */
function mlab_git_connector_request_args( $accept ) {
	$headers = array(
		'Accept'     => $accept,
		'User-Agent' => 'MLab-Git-Connector/' . MLAB_GIT_CONNECTOR_VERSION,
	);

	if ( defined( 'MLAB_GIT_CONNECTOR_GITHUB_TOKEN' ) && MLAB_GIT_CONNECTOR_GITHUB_TOKEN ) {
		$headers['Authorization'] = 'Bearer ' . MLAB_GIT_CONNECTOR_GITHUB_TOKEN;
	}

	return array(
		'headers' => $headers,
		'timeout' => 60,
	);
}

/**
 * Finds the ZIP asset of the latest GitHub release.
 *
 * @param string $owner Repository owner.
 * @param string $repo  Repository name.
 * @return array{name: string, url: string}|WP_Error
 */
function mlab_git_connector_find_release_asset( $owner, $repo ) {
	$api_url  = sprintf( 'https://api.github.com/repos/%s/%s/releases/latest', rawurlencode( $owner ), rawurlencode( $repo ) );
	$response = wp_remote_get( $api_url, mlab_git_connector_request_args( 'application/vnd.github+json' ) );

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$code = wp_remote_retrieve_response_code( $response );
	if ( 200 !== $code ) {
		return new WP_Error( 'mlab_git_connector_release', mlab_git_connector_release_error_message( $code ) );
	}

	$release = json_decode( wp_remote_retrieve_body( $response ), true );
	foreach ( isset( $release['assets'] ) && is_array( $release['assets'] ) ? $release['assets'] : array() as $asset ) {
		if ( isset( $asset['name'], $asset['url'] ) && '.zip' === substr( $asset['name'], -4 ) ) {
			return array(
				'name' => $asset['name'],
				'url'  => $asset['url'],
			);
		}
	}

	return new WP_Error( 'mlab_git_connector_asset', __( 'The latest release has no ZIP file attached. Attach the plugin ZIP to the release and try again.', 'mlab-git-connector' ) );
}

/**
 * Maps a GitHub API status code to a message that tells the user what to do.
 *
 * @param int $code HTTP status code.
 * @return string
 */
function mlab_git_connector_release_error_message( $code ) {
	if ( 404 === $code ) {
		return __( 'No release found. Create a release with the plugin ZIP attached, or check the address. For a private repository, the token must be defined in wp-config.php.', 'mlab-git-connector' );
	}

	if ( 401 === $code || 403 === $code ) {
		return __( 'GitHub denied access. For a private repository, check the MLAB_GIT_CONNECTOR_GITHUB_TOKEN constant in wp-config.php.', 'mlab-git-connector' );
	}

	/* translators: %d: HTTP status code returned by GitHub. */
	return sprintf( __( 'GitHub answered with status %d. Please try again later.', 'mlab-git-connector' ), (int) $code );
}

/**
 * Downloads a release asset to a temporary file.
 *
 * @param string $asset_url API URL of the asset.
 * @return string|WP_Error Path of the temporary file.
 */
function mlab_git_connector_download_asset( $asset_url ) {
	$tmp_file = wp_tempnam( 'mlab-git-connector.zip' );
	$args     = mlab_git_connector_request_args( 'application/octet-stream' );

	$args['stream']   = true;
	$args['filename'] = $tmp_file;

	$response = wp_remote_get( $asset_url, $args );
	$code     = is_wp_error( $response ) ? 0 : wp_remote_retrieve_response_code( $response );

	if ( 200 !== $code ) {
		wp_delete_file( $tmp_file );
		return is_wp_error( $response ) ? $response : new WP_Error( 'mlab_git_connector_download', __( 'The plugin ZIP could not be downloaded from GitHub.', 'mlab-git-connector' ) );
	}

	return $tmp_file;
}

/**
 * Installs a plugin from a GitHub release through the standard WordPress installer.
 *
 * @param string $url Repository URL entered by the user.
 * @return string|WP_Error Plugin basename on success.
 */
function mlab_git_connector_install( $url ) {
	$repo = mlab_git_connector_parse_repo_url( $url );
	if ( is_wp_error( $repo ) ) {
		return $repo;
	}

	$asset = mlab_git_connector_find_release_asset( $repo['owner'], $repo['repo'] );
	if ( is_wp_error( $asset ) ) {
		return $asset;
	}

	$zip_path = mlab_git_connector_download_asset( $asset['url'] );
	if ( is_wp_error( $zip_path ) ) {
		return $zip_path;
	}

	return mlab_git_connector_run_installer( $zip_path );
}

/**
 * Runs the WordPress plugin upgrader on a local ZIP file and removes the file afterwards.
 *
 * @param string $zip_path Path of the downloaded ZIP.
 * @return string|WP_Error Plugin basename on success.
 */
function mlab_git_connector_run_installer( $zip_path ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once ABSPATH . 'wp-admin/includes/class-automatic-upgrader-skin.php';
	require_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';

	$upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
	$result   = $upgrader->install( $zip_path, array( 'overwrite_package' => true ) );

	wp_delete_file( $zip_path );

	if ( is_wp_error( $result ) ) {
		return $result;
	}

	if ( true !== $result ) {
		return new WP_Error( 'mlab_git_connector_install', __( 'WordPress could not install the plugin. Check the ZIP file and the file permissions.', 'mlab-git-connector' ) );
	}

	return (string) $upgrader->plugin_info();
}
