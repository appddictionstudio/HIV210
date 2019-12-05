<?php
/**
 * Plugin Name: WP Quiz
 * Plugin URI:  https://mythemeshop.com/plugins/wp-quiz/
 * Description: WP Quiz lets you easily add polished, responsive and modern quizzes to your site or blog! Increase engagement and shares while building your mailing list! WP Quiz makes it easy!
 * Version:     2.0.3
 * Author:      MyThemeShop
 * Author URI:  https://mythemeshop.com/
 *
 * Text Domain: wp-quiz
 * Domain Path: /languages/
 *
 * @package WPQuiz
 */

if ( defined( 'WP_QUIZ_FILE' ) ) {
	return;
}

define( 'WP_QUIZ_FILE', __FILE__ );

/*
 * Main plugin files and dependencies.
 */
require_once dirname( __FILE__ ) . '/vendor/autoload.php';

if ( ! function_exists( 'wp_quiz' ) ) {
	/**
	 * Gets plugin instance.
	 *
	 * @return \WPQuiz\WPQuiz
	 */
	function wp_quiz() {
		return \WPQuiz\WPQuiz::get_instance();
	}

	// phpcs:disable
	class WP_Quiz_Plugin {
		public static function activate_plugin() {}
	} // For compatibility.
	// phpcs:enable
}

$wp_quiz = wp_quiz();

register_activation_hook( __FILE__, array( $wp_quiz, 'activate' ) );
register_deactivation_hook( __FILE__, array( $wp_quiz, 'deactivate' ) );

/**
 * Shows deactivate plugin notice.
 */
function wp_quiz_deactivate_plugin_notice() {
	if ( ! has_action( 'plugins_loaded', 'wp_quiz' ) ) {
		return;
	}
	?>
	<div class="error">
		<p><?php esc_html_e( 'Please deactivate WP Quiz FREE plugin first to use the Premium features!', 'wp-quiz' ); ?></p>
	</div>
	<?php
}
add_action( 'admin_notices', 'wp_quiz_deactivate_plugin_notice' );
