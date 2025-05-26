<?php

namespace Cool_FormKit;

use DateTime;

if (! defined('ABSPATH')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}


class Review_notice
{

	private $plugin_url     = CFL_PLUGIN_URL;
	private $plugin_name    = 'Cool Formkit Lite';

	private $review_option = 'cfl_review_notice_dismiss';
	private $plugin_slug    = 'cfl';

	private $installation_date_option = 'eef-installDate';

	private $review_link = 'https://wordpress.org/support/plugin/extensions-for-elementor-form/reviews/#new-post';
	// private $feedback_url   = 'http://feedback.coolplugins.net/wp-json/coolplugins-feedback/v1/feedback';

	private $plugin_logo = 'assets/images/cool-formkit-lite-logo.gif';
	public function __construct()
	{

		\add_action('elementor/element/cool-form/section_form_options/after_section_end', array($this, 'add_review_notice'), 10, 2);

		\add_action('elementor/editor/before_enqueue_styles', array($this, 'editor_assets'));

		\add_action('admin_notices', array($this, 'cfl_admin_notice_for_review'));

		// \add_action('wp_ajax_cfl_elementor_review_notice', array($this, 'cfl_elementor_review_notice'));

		\add_action('wp_ajax_' . \sanitize_key($this->plugin_slug) . '_dismiss_notice', array($this, 'cfl_review_notice'));
	}

	public function add_review_notice($widget)
	{
		if (! \get_option('cfl_review_notice_dismiss')) {
			// Create nonce for security
			$review_nonce = \wp_create_nonce('cfl_elementor_review');
			$url          = \admin_url('admin-ajax.php');

			// HTML for the review notice

			$html         = '<div class="cfl_elementor_review_wrapper">';
			$html        .= '<div id="cfl_elementor_review_dismiss" data-url="' . \esc_url($url) . '" data-nonce="' . \esc_attr($review_nonce) . '">Close Notice X</div>
								<div class="cfl_elementor_review_msg">' . \__('Hope this addon solved your problem!', 'cfl') . '<br><a href="https://wordpress.org/support/plugin/extensions-for-elementor-form/reviews/#new-post" target="_blank"">Share the love with a ⭐⭐⭐⭐⭐ rating.</a><br><br></div>
								<div class="cfl_elementor_demo_btn"><a href="https://wordpress.org/support/plugin/extensions-for-elementor-form/reviews/#new-post" target="_blank">Submit Review</a></div>
								</div>'; // Close main wrapper 

			$widget->start_controls_section(
				'cfl_review_notice_section',
				array(
					'label' => \__('Review Notice', 'cfl'),
				)
			);

			// Add review notice field control
			$widget->add_control(
				'cfl_review_notice',
				array(
					'label' => \__('Review Notice', 'cfl'),
					'type' => \Elementor\Controls_Manager::RAW_HTML,
					'raw' => $html,
					'content_classes' => 'cfl_elementor_review_notice',
					'tab' => 'content',
				)
			);

			$widget->end_controls_section();
		}
	}

	public function cfl_admin_notice_for_review()
	{

		if (! \current_user_can('update_plugins')) {
			return;
		}

		// get installation dates and rated settings
		$installation_date = \get_option($this->installation_date_option);
		$alreadyRated      = \get_option($this->review_option) != false ? \get_option($this->review_option) : 'no';

		// check user already rated
		if ($alreadyRated == 'yes') {
			return;
		}

		// grab plugin installation date and compare it with current date
		$display_date = date('Y-m-d h:i:s');
		$install_date = new DateTime($installation_date);
		$current_date = new DateTime($display_date);
		$difference   = $install_date->diff($current_date);
		$diff_days    = $difference->days;


		// check if installation days is greator then week
		if (isset($diff_days) && $diff_days >= 3) {
			echo $this->cfl_create_notice_content();
		}
	}


	function cfl_create_notice_content()
	{
		$html = '
		<div data-ajax-url="' . \admin_url('admin-ajax.php') . '" data-nonce="' . \wp_create_nonce('cfl_elementor_review') . '" data-ajax-callback="' . \esc_attr($this->plugin_slug) . '_dismiss_notice" class="' . \esc_attr($this->plugin_slug) . '-review-notice-wrapper notice">
			<div class="logo_container">
				<a href="' . \esc_url($this->review_link) . '" target="_blank"><img src="' . $this->plugin_url . $this->plugin_logo . '" alt="' . \esc_attr($this->plugin_name) . '"></a>
			</div>
			<div class="message_container">
				<p>Thanks for using <b>' . \esc_html($this->plugin_name) . '</b> WordPress plugin. We hope it meets your expectations!<br/>Please give us a quick rating, it works as a boost for us to keep working on more <a href="https://coolplugins.net" target="_blank"><strong>Cool Plugins</strong></a>!</p>
				<ul>
					<li><a href="' . \esc_url($this->review_link) . '" class="rate-it-btn button button-primary" target="_blank" title="Submit A Review...">Rate Now! ★★★★★</a></li>
					<li><a href="javascript:void(0);" class="already-rated-btn button button-secondary ' . \esc_attr($this->plugin_slug) . '_dismiss_notice" title="Already Rated - Close This Notice!">Already Rated</a></li>
					<li><a href="javascript:void(0);" class="already-rated-btn button button-secondary ' . \esc_attr($this->plugin_slug) . '_dismiss_notice" title="Not Interested - Close This Notice!">Not Interested</a></li>
				</ul>
			</div>
		</div>
		';

		// css styles
		$style = '
		<style>
		#wpbody .' . \esc_attr($this->plugin_slug) . '-review-notice-wrapper.notice {
			padding: 5px;
			margin: 5px 0;
			display: table;
			max-width: 820px;
			border-radius: 5px;
			border: 1px solid #ced3d6;
			box-sizing: border-box;
			box-shadow: 2px 4px 8px -2px rgba(0, 0, 0, 0.1)
		}
		.' . \esc_attr($this->plugin_slug) . '-review-notice-wrapper .logo_container {
			width: 80px;
			display: table-cell;
			padding: 5px;
			vertical-align: middle;
		}
		.' . \esc_attr($this->plugin_slug) . '-review-notice-wrapper .logo_container a,
		.' . \esc_attr($this->plugin_slug) . '-review-notice-wrapper .logo_container img {
			width:80px;
			height:auto;
			display:inline-block;
		}
		.' . \esc_attr($this->plugin_slug) . '-review-notice-wrapper .message_container {
			display: table-cell;
			padding: 5px;
			vertical-align: middle;
		}
		.' . \esc_attr($this->plugin_slug) . '-review-notice-wrapper p,
		.' . \esc_attr($this->plugin_slug) . '-review-notice-wrapper ul {
			padding: 0;
			margin: 0;
			line-height: 1.25em;
			display: flow-root;
		}
		.' . \esc_attr($this->plugin_slug) . '-review-notice-wrapper ul {
			margin-top: 10px;
		}
		.' . \esc_attr($this->plugin_slug) . '-review-notice-wrapper ul li {
			float: left;
			margin: 0px 10px 0 0;
		}
		.' . \esc_attr($this->plugin_slug) . '-review-notice-wrapper ul li .button-primary {
			background: #772ec9;
			text-shadow: none;
			border-color: #a69516;
			box-shadow: none;
			color: #fff;
		}
		.' . \esc_attr($this->plugin_slug) . '-review-notice-wrapper ul li .button-secondary {
			background: #fff;
			background-color: #fff;
			border: 1px solid #757575;
			color: #757575;
		}
		.' . \esc_attr($this->plugin_slug) . '-review-notice-wrapper ul li .button-secondary.already-rated-btn:after {
			color: #f12945;
			content: "\f153";
			display: inline-block;
			vertical-align: middle;
			margin: -1px 0 0 5px;
			font-size: 14px;
			line-height: 14px;
			font-family: dashicons;
		}
		.' . \esc_attr($this->plugin_slug) . '-review-notice-wrapper ul li .button-primary:hover {
			background: #222;
			border-color: #000;
		}
		@media screen and (max-width: 660px) {
			.' . \esc_attr($this->plugin_slug) . '-review-notice-wrapper .logo_container{
				display:none;
			}
			.' . \esc_attr($this->plugin_slug) . '-review-notice-wrapper .message_container {
				display: flow-root;
			}
		}
		</style>
		';

		// close notice script
		$script = '
		<script>
		jQuery(document).ready(function ($) {
			$(".' . \esc_js($this->plugin_slug) . '_dismiss_notice").on("click", function (event) {
				var $this = $(this);
				var wrapper=$this.parents(".' . \esc_js($this->plugin_slug) . '-review-notice-wrapper");
				var ajaxURL=wrapper.data("ajax-url");
				var nonce = wrapper.data("nonce");
				var ajaxCallback=wrapper.data("ajax-callback");         
				$.post(ajaxURL, { "action":ajaxCallback, cfl_notice_dismiss: true, nonce: nonce }, function( data ) {
					console.log("hello");
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


	public function editor_assets()
	{
		\wp_register_script('cfl_logic_editor', CFL_PLUGIN_URL . 'assets/js/cfl_editor.min.js', array('jquery'), CFL_VERSION, true);
		\wp_enqueue_style('cfl_logic_editor', CFL_PLUGIN_URL . 'assets/css/cfl_editor.min.css', null, CFL_VERSION);
		\wp_enqueue_script('cfl_logic_editor');
	}

	public function cfl_review_notice()
	{


		if (! \check_ajax_referer('cfl_elementor_review', 'nonce', false)) {
			\wp_send_json_error(\__('Invalid security token sent.', 'cfl'));
			\wp_die('0', 400);
		}

		if (isset($_POST['cfl_notice_dismiss']) && 'true' === \sanitize_text_field($_POST['cfl_notice_dismiss'])) {
			\update_option('cfl_review_notice_dismiss', 'yes');
			echo json_encode(array('success' => 'true'));
			exit;
		}
	}
}
