<?php
/**
 * Admin screen under Plugins that takes a GitHub URL and installs the plugin.
 *
 * @package MlabGitConnector
 */

defined( 'ABSPATH' ) || exit;

/**
 * Adds the screen under Plugins.
 *
 * @return void
 */
function mlab_git_connector_register_page() {
	add_plugins_page(
		__( 'Install from GitHub', 'mlab-git-connector' ),
		__( 'Install from GitHub', 'mlab-git-connector' ),
		'install_plugins',
		'mlab-git-connector',
		'mlab_git_connector_render_page'
	);
}

/**
 * Key of the per-user transient that carries the result of the last install.
 *
 * @return string
 */
function mlab_git_connector_notice_key() {
	return 'mlab_git_connector_notice_' . get_current_user_id();
}

/**
 * Handles the form submit. Installs the plugin and redirects back with the result.
 *
 * @return void
 */
function mlab_git_connector_handle_install() {
	check_admin_referer( 'mlab_git_connector_install' );

	if ( ! current_user_can( 'install_plugins' ) ) {
		wp_die( esc_html__( 'You are not allowed to install plugins.', 'mlab-git-connector' ) );
	}

	$url    = isset( $_POST['repo_url'] ) ? esc_url_raw( wp_unslash( $_POST['repo_url'] ) ) : '';
	$result = mlab_git_connector_install( $url );

	if ( is_wp_error( $result ) ) {
		$notice = array(
			'type'    => 'error',
			'message' => $result->get_error_message(),
		);
	} else {
		$notice = array(
			'type'    => 'success',
			/* translators: %s: plugin file, for example my-plugin/my-plugin.php. */
			'message' => sprintf( __( 'Plugin installed: %s. You can activate it on the Plugins page.', 'mlab-git-connector' ), $result ),
		);
	}

	set_transient( mlab_git_connector_notice_key(), $notice, (int) mlab_git_connector_option( 'notice_ttl' ) );
	wp_safe_redirect( admin_url( 'plugins.php?page=mlab-git-connector' ) );
	exit;
}

/**
 * Reads and clears the notice left by the last install.
 *
 * @return array{type: string, message: string}|null
 */
function mlab_git_connector_pop_notice() {
	$notice = get_transient( mlab_git_connector_notice_key() );
	delete_transient( mlab_git_connector_notice_key() );

	return is_array( $notice ) ? $notice : null;
}

/**
 * Renders the screen.
 *
 * @return void
 */
function mlab_git_connector_render_page() {
	if ( ! current_user_can( 'install_plugins' ) ) {
		wp_die( esc_html__( 'You are not allowed to install plugins.', 'mlab-git-connector' ) );
	}

	$notice = mlab_git_connector_pop_notice();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Install from GitHub', 'mlab-git-connector' ); ?></h1>

		<?php if ( $notice ) : ?>
			<div class="notice notice-<?php echo esc_attr( $notice['type'] ); ?> is-dismissible">
				<p><?php echo esc_html( $notice['message'] ); ?></p>
			</div>
		<?php endif; ?>

		<p><?php esc_html_e( 'Enter the GitHub address of the repository. The plugin ZIP attached to its latest release is installed.', 'mlab-git-connector' ); ?></p>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="mlab_git_connector_install">
			<?php wp_nonce_field( 'mlab_git_connector_install' ); ?>

			<label for="mlab-git-connector-url"><?php esc_html_e( 'GitHub address', 'mlab-git-connector' ); ?></label>
			<input type="url" id="mlab-git-connector-url" name="repo_url" class="regular-text" placeholder="https://github.com/owner/repo" required>

			<?php submit_button( __( 'Install', 'mlab-git-connector' ) ); ?>
		</form>

		<p class="description"><?php esc_html_e( 'Large plugins can take a while. If your server shows a timeout error such as 504, wait a minute and reload this page. The installation may still finish and the result appears here.', 'mlab-git-connector' ); ?></p>
	</div>
	<?php
}
