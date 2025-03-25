<?php
namespace Cool_FormKit\feedback;

use DateTime;

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

class cfef_feedback {

		private $plugin_url     = CFL_PLUGIN_URL;
		private $plugin_version = CFL_VERSION;
		private $plugin_name    = 'Cool Formkit Lite';

		private $review_option = 'cfl_review_notice_dismiss';
		private $plugin_slug    = 'eef';

		private $installation_date_option = 'eef-installDate';

		private $review_link = 'https://wordpress.org/support/plugin/extensions-for-elementor-form/reviews/#new-post';
		// private $feedback_url   = 'http://feedback.coolplugins.net/wp-json/coolplugins-feedback/v1/feedback';

		private $plugin_logo = 'assets/images/cool-formkit-lite-logo.gif';


		

	/*
	|-----------------------------------------------------------------|
	|   Use this constructor to fire all actions and filters          |
	|-----------------------------------------------------------------|
	*/
	public function __construct() {
		// $this->plugin_url = plugin_dir_url( $this->plugin_url );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_feedback_scripts' ) );
		add_action( 'admin_head', array( $this, 'show_deactivate_feedback_popup' ) );
		add_action( 'wp_ajax_' . $this->plugin_slug . '_submit_deactivation_response', array( $this, 'submit_deactivation_response' ) );
		add_action( 'admin_notices', array( $this, 'cfl_admin_notice_for_review' ) );
		add_action( 'wp_ajax_' . $this->plugin_slug . '_dismiss_notice', array( $this, 'cfl_dismiss_review_notice' ) );

	}

	public function cfl_dismiss_review_notice(){
		$rs = update_option( $this->review_option, 'yes' );
		echo json_encode( array( 'success' => 'true' ) );
		exit;
	}



	public function cfl_admin_notice_for_review(){

		// new DateTime();

		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		// get installation dates and rated settings
		$installation_date = get_option( $this->installation_date_option );
		$alreadyRated      = get_option( $this->review_option ) != false ? get_option( $this->review_option ) : 'no';

		// check user already rated
		if ( $alreadyRated == 'yes' ) {
			return;
		}

		// grab plugin installation date and compare it with current date
		$display_date = date( 'Y-m-d h:i:s' );
		$install_date = new DateTime( $installation_date );
		$current_date = new DateTime( $display_date );
		$difference   = $install_date->diff( $current_date );
		$diff_days    = $difference->days;


		// check if installation days is greator then week
		if ( isset( $diff_days ) && $diff_days >= 3 ) {
			echo $this->cfl_create_notice_content();
		}
	}

	function cfl_create_notice_content() {
		// $plugin_buy_button = '';
		// if ( $this->buy_link != '' ) {
		// 	$plugin_buy_button = '<li><a href="' . $this->buy_link . '" target="_blank" class="buy-pro-btn button button-secondary" title="Buy Pro">Buy Pro</a></li>';
		// }

		var_dump($this->plugin_slug . '_dismiss_notice');

		$html = '
		<div data-ajax-url="' . admin_url( 'admin-ajax.php' ) . '" data-ajax-callback="' . $this->plugin_slug . '_dismiss_notice" class="' . $this->plugin_slug . '-review-notice-wrapper notice">
			<div class="logo_container">
				<a href="' . esc_url( $this->review_link ) . '" target="_blank"><img src="' . $this->plugin_url . $this->plugin_logo . '" alt="' . $this->plugin_name . '"></a>
			</div>
			<div class="message_container">
				<p>Thanks for using <b>' . $this->plugin_name . '</b> WordPress plugin. We hope it meets your expectations!<br/>Please give us a quick rating, it works as a boost for us to keep working on more <a href="https://coolplugins.net" target="_blank"><strong>Cool Plugins</strong></a>!</p>
				<ul>
					<li><a href="' . esc_url( $this->review_link ) . '" class="rate-it-btn button button-primary" target="_blank" title="Submit A Review...">Rate Now! ★★★★★</a></li>
					<li><a href="javascript:void(0);" class="already-rated-btn button button-secondary ' . $this->plugin_slug . '_dismiss_notice" title="Already Rated - Close This Notice!">Already Rated</a></li>
					<li><a href="javascript:void(0);" class="already-rated-btn button button-secondary ' . $this->plugin_slug . '_dismiss_notice" title="Not Interested - Close This Notice!">Not Interested</a></li>
					' . $plugin_buy_button . '
				</ul>
			</div>
		</div>
		';

		// css styles
		$style = '
		<style>
		#wpbody .' . $this->plugin_slug . '-review-notice-wrapper.notice {
			padding: 5px;
			margin: 5px 0;
			display: table;
			max-width: 820px;
			border-radius: 5px;
			border: 1px solid #ced3d6;
			box-sizing: border-box;
			box-shadow: 2px 4px 8px -2px rgba(0, 0, 0, 0.1)
		}
		.' . $this->plugin_slug . '-review-notice-wrapper .logo_container {
			width: 80px;
			display: table-cell;
			padding: 5px;
			vertical-align: middle;
		}
		.' . $this->plugin_slug . '-review-notice-wrapper .logo_container a,
		.' . $this->plugin_slug . '-review-notice-wrapper .logo_container img {
			width:80px;
			height:auto;
			display:inline-block;
		}
		.' . $this->plugin_slug . '-review-notice-wrapper .message_container {
			display: table-cell;
			padding: 5px;
			vertical-align: middle;
		}
		.' . $this->plugin_slug . '-review-notice-wrapper p,
		.' . $this->plugin_slug . '-review-notice-wrapper ul {
			padding: 0;
			margin: 0;
			line-height: 1.25em;
			display: flow-root;
		}
		.' . $this->plugin_slug . '-review-notice-wrapper ul {
			margin-top: 10px;
		}
		.' . $this->plugin_slug . '-review-notice-wrapper ul li {
			float: left;
			margin: 0px 10px 0 0;
		}
		.' . $this->plugin_slug . '-review-notice-wrapper ul li .button-primary {
			background: #772ec9;
			text-shadow: none;
			border-color: #a69516;
			box-shadow: none;
			color: #fff;
		}
		.' . $this->plugin_slug . '-review-notice-wrapper ul li .button-secondary {
			background: #fff;
			background-color: #fff;
			border: 1px solid #757575;
			color: #757575;
		}
		.' . $this->plugin_slug . '-review-notice-wrapper ul li .button-secondary.already-rated-btn:after {
			color: #f12945;
			content: "\f153";
			display: inline-block;
			vertical-align: middle;
			margin: -1px 0 0 5px;
			font-size: 14px;
			line-height: 14px;
			font-family: dashicons;
		}
		.' . $this->plugin_slug . '-review-notice-wrapper ul li .button-primary:hover {
			background: #222;
			border-color: #000;
		}
		@media screen and (max-width: 660px) {
			.' . $this->plugin_slug . '-review-notice-wrapper .logo_container{
				display:none;
			}
			.' . $this->plugin_slug . '-review-notice-wrapper .message_container {
				display: flow-root;
			}
		}
		</style>
		';

		// close notice script
		$script = '
		<script>
		jQuery(document).ready(function ($) {
			$(".' . $this->plugin_slug . '_dismiss_notice").on("click", function (event) {
				var $this = $(this);
				var wrapper=$this.parents(".' . $this->plugin_slug . '-review-notice-wrapper");
				var ajaxURL=wrapper.data("ajax-url");
				var ajaxCallback=wrapper.data("ajax-callback");         
				$.post(ajaxURL, { "action":ajaxCallback }, function( data ) {
					wrapper.slideUp("fast");
				}, "json");
			});
		});
		</script>
		';

		$html .= '
		' . $style . '
		' . $script;

		return $html;
	}
	/*
	|-----------------------------------------------------------------|
	|   Enqueue all scripts and styles to required page only          |
	|-----------------------------------------------------------------|
	*/
	function enqueue_feedback_scripts() {
		$screen = get_current_screen();
		if ( isset( $screen ) && $screen->id == 'plugins' ) {
			wp_enqueue_script( __NAMESPACE__ . 'feedback-script', $this->plugin_url . 'admin/feedback/js/admin-feedback.js', array( 'jquery' ), $this->plugin_version );
			wp_enqueue_style( 'cool-plugins-feedback-css', $this->plugin_url . 'admin/feedback/css/admin-feedback.css', null, $this->plugin_version );
		}
	}

	/*
	|-----------------------------------------------------------------|
	|   HTML for creating feedback popup form                         |
	|-----------------------------------------------------------------|
	*/
	public function show_deactivate_feedback_popup() {
		$screen = get_current_screen();
		if ( ! isset( $screen ) || $screen->id != 'plugins' ) {
			return;
		}
		$deactivate_reasons = array(
			'didnt_work_as_expected'         => array(
				'title'             => __( 'The plugin didn\'t work as expected.', 'cfef' ),
				'input_placeholder' => 'What did you expect?',
			),
			'found_a_better_plugin'          => array(
				'title'             => __( 'I found a better plugin.', 'cfef' ),
				'input_placeholder' => __( 'Please share which plugin.', 'cfef' ),
			),
			'couldnt_get_the_plugin_to_work' => array(
				'title'             => __( 'The plugin is not working.', 'cfef' ),
				'input_placeholder' => 'Please share your issue. So we can fix that for other users.',
			),
			'temporary_deactivation'         => array(
				'title'             => __( 'It\'s a temporary deactivation.', 'cfef' ),
				'input_placeholder' => '',
			),
			'other'                          => array(
				'title'             => __( 'Other reason.', 'cfef' ),
				'input_placeholder' => __( 'Please share the reason.', 'cfef' ),
			),
		);

		?>
		<div id="cool-plugins-feedback-<?php echo esc_attr( $this->plugin_slug ); ?>" class="hide-feedback-popup">
						
			<div class="cp-feedback-wrapper">

			<div class="cp-feedback-header">
				<div class="cp-feedback-title"><?php echo esc_html__( 'Quick Feedback', 'cfef' ); ?></div>
				<div class="cp-feedback-title-link">A plugin by <a href="https://coolplugins.net/?utm_source=<?php echo esc_attr( $this->plugin_slug ); ?>_plugin&utm_medium=inside&utm_campaign=coolplugins&utm_content=deactivation_feedback" target="_blank">CoolPlugins.net</a></div>
			</div>

			<div class="cp-feedback-loader">
				<img src="<?php echo esc_url( $this->plugin_url ); ?>admin/feedback/images/cool-plugins-preloader.gif">
			</div>

			<div class="cp-feedback-form-wrapper">
				<div class="cp-feedback-form-title"><?php echo esc_html__( 'If you have a moment, please share the reason for deactivating this plugin.', 'cfef' ); ?></div>
				<form class="cp-feedback-form" method="post">
					<?php
					wp_nonce_field( '_cool-plugins_deactivate_feedback_nonce' );
					?>
					<input type="hidden" name="action" value="cool-plugins_deactivate_feedback" />
					
					<?php foreach ( $deactivate_reasons as $reason_key => $reason ) : ?>
						<div class="cp-feedback-input-wrapper">
							<input id="cp-feedback-reason-<?php echo esc_attr( $reason_key ); ?>" class="cp-feedback-input" type="radio" name="reason_key" value="<?php echo esc_attr( $reason_key ); ?>" />
							<label for="cp-feedback-reason-<?php echo esc_attr( $reason_key ); ?>" class="cp-feedback-reason-label"><?php echo esc_html( $reason['title'] ); ?></label>
							<?php if ( ! empty( $reason['input_placeholder'] ) ) : ?>
								<textarea class="cp-feedback-text" type="textarea" name="reason_<?php echo esc_attr( $reason_key ); ?>" placeholder="<?php echo esc_attr( $reason['input_placeholder'] ); ?>"></textarea>
							<?php endif; ?>
							<?php if ( ! empty( $reason['alert'] ) ) : ?>
								<div class="cp-feedback-text"><?php echo esc_html( $reason['alert'] ); ?></div>
							<?php endif; ?>	
						</div>
					<?php endforeach; ?>
					
					<div class="cp-feedback-terms">
					<input class="cp-feedback-terms-input" id="cp-feedback-terms-input" type="checkbox"><label for="cp-feedback-terms-input"><?php echo esc_html__( 'I agree to share my feedback with Cool Plugins, including site URL and admin email, to enable them to address my inquiry.', 'cfef' ); ?></label>
					</div>

					<div class="cp-feedback-button-wrapper">
						<a class="cp-feedback-button cp-submit" id="cool-plugin-submitNdeactivate">Submit and Deactivate</a>
						<a class="cp-feedback-button cp-skip" id="cool-plugin-skipNdeactivate">Skip and Deactivate</a>
					</div>
				</form>
			</div>


		   </div>
		</div>
		<?php
	}


	function submit_deactivation_response() {
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['_wpnonce'] ), '_cool-plugins_deactivate_feedback_nonce' ) ) {
			wp_send_json_error();
		} else {
			$reason             = isset( $_POST['reason'] ) ? sanitize_text_field( $_POST['reason'] ) : '';
			$deactivate_reasons = array(
				'didnt_work_as_expected'         => array(
					'title'             => __( 'The plugin didn\'t work as expected', 'cfef' ),
					'input_placeholder' => 'What did you expect?',
				),
				'found_a_better_plugin'          => array(
					'title'             => __( 'I found a better plugin', 'cfef' ),
					'input_placeholder' => __( 'Please share which plugin.', 'cfef' ),
				),
				'couldnt_get_the_plugin_to_work' => array(
					'title'             => __( 'The plugin is not working', 'cfef' ),
					'input_placeholder' => 'Please share your issue. So we can fix that for other users.',
				),
				'temporary_deactivation'         => array(
					'title'             => __( 'It\'s a temporary deactivation.', 'cfef' ),
					'input_placeholder' => '',
				),
				'other'                          => array(
					'title'             => __( 'Other', 'cool-plugins' ),
					'input_placeholder' => __( 'Please share the reason.', 'cfef' ),
				),
			);

			$deativation_reason = array_key_exists( $reason, $deactivate_reasons ) ? $reason : 'other';

			$deativation_reason = esc_html($deativation_reason);
			$sanitized_message = empty( $_POST['message'] ) || sanitize_text_field( $_POST['message'] ) == '' ? 'N/A' : sanitize_text_field( $_POST['message'] );
			$admin_email       = sanitize_email( get_option( 'admin_email' ) );
			$site_url          = esc_url( site_url() );
			$feedback_url      = esc_url( 'http://feedback.coolplugins.net/wp-json/coolplugins-feedback/v1/feedback' );
			$response          = wp_remote_post(
				$feedback_url,
				array(
					'timeout' => 30,
					'body'    => array(
						'plugin_version' => $this->plugin_version,
						'plugin_name'    => $this->plugin_name,
						'reason'         => $deativation_reason,
						'review'         => $sanitized_message,
						'email'          => $admin_email,
						'domain'         => $site_url,
					),
				)
			);

			die( json_encode( array( 'response' => $response ) ) );
		}

	}
}
new cfef_feedback();
