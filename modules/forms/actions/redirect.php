<?php
namespace Cool_FormKit\Modules\Forms\Actions;

use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Cool_FormKit\Modules\Forms\Classes\Action_Base;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Redirect extends Action_Base {

	/**
	 * Legacy Hello Plus–prefixed slug stored in older Cool Form submit_actions.
	 */
	const LEGACY_NAME = 'ehp-redirect';

	public function get_name(): string {
		return 'cool_redirect';
	}

	public function get_label(): string {
		return esc_html__( 'Redirect', 'extensions-for-elementor-form' );
	}

	/**
	 * Map legacy ehp-redirect entries to cool_redirect for runtime matching.
	 *
	 * @param array $submit_actions Stored submit action slugs.
	 * @return array
	 */
	public static function normalize_submit_actions( $submit_actions ) {
		if ( ! is_array( $submit_actions ) ) {
			return [];
		}

		return array_map(
			static function ( $name ) {
				return self::LEGACY_NAME === $name ? 'cool_redirect' : $name;
			},
			$submit_actions
		);
	}

	public function register_settings_section( $widget ) {
		$widget->start_controls_section(
			'section_redirect',
			[
				'label' => esc_html__( 'Redirect', 'extensions-for-elementor-form' ),
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'submit_actions',
							'operator' => 'contains',
							'value' => $this->get_name(),
						],
						[
							'name' => 'submit_actions',
							'operator' => 'contains',
							'value' => self::LEGACY_NAME,
						],
					],
				],
			]
		);

		$widget->add_control(
			'should_redirect',
			[
				'label' => esc_html__( 'Redirect To Thank You Page', 'extensions-for-elementor-form' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'extensions-for-elementor-form' ),
				'label_off' => esc_html__( 'No', 'extensions-for-elementor-form' ),
				'return_value' => 'true',
				'default' => '',
			]
		);
		$widget->add_control(
			'redirect_to',
			[
				'label' => esc_html__( 'Redirect To', 'extensions-for-elementor-form' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'https://your-link.com', 'extensions-for-elementor-form' ),
				'ai' => [
					'active' => false,
				],
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::TEXT_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'label_block' => true,
				'render_type' => 'none',
				'classes' => 'elementor-control-direction-ltr',
				'condition' => [
					'should_redirect' => 'true',
				],
			]
		);

		$widget->end_controls_section();
	}

	public function on_export( $element ) {
		unset(
			$element['settings']['redirect_to']
		);

		return $element;
	}

	public function run( $record, $ajax_handler ) {
		if ( 'true' !== $record->get_form_settings( 'should_redirect' ) ) {
			return;
		}

		// Keep the admin-authored template (with its [field] placeholders) so we
		// can tell the admin-intended host apart from any submitted field value.
		$configured_template = $record->get_form_settings( 'redirect_to' );

		$redirect_to = $record->replace_setting_shortcodes( $configured_template, true );

		$redirect_to = esc_url_raw( $redirect_to );

		if ( empty( $redirect_to ) || ! filter_var( $redirect_to, FILTER_VALIDATE_URL ) ) {
			return;
		}

		// Defense-in-depth: a submitted field value must not be able to change
		// the redirect host. We only block when the final host differs from both
		// the site and the admin-configured host; the URL itself is never
		// rewritten, so every legitimately configured redirect is preserved
		// byte-for-byte (important across site/domain migrations).
		if ( ! $this->is_redirect_target_allowed( $redirect_to, $configured_template ) ) {
			return;
		}

		$ajax_handler->add_response_data( 'redirect_url', $redirect_to );
	}

	/**
	 * Whether the final redirect host is one the admin intended.
	 *
	 * Allows same-site and host-less (relative) targets, plus the exact host the
	 * admin typed into the redirect setting. Returns false only when the host
	 * could have come from submitted data.
	 *
	 * @param string $redirect_to         Final, escaped redirect URL.
	 * @param string $configured_template Raw admin setting before field substitution.
	 * @return bool
	 */
	private function is_redirect_target_allowed( $redirect_to, $configured_template ) {
		$target_host = wp_parse_url( $redirect_to, PHP_URL_HOST );

		// Relative / host-less target: same-site, always allowed.
		if ( empty( $target_host ) ) {
			return true;
		}

		$site_host = wp_parse_url( home_url(), PHP_URL_HOST );
		if ( ! empty( $site_host ) && strtolower( $target_host ) === strtolower( $site_host ) ) {
			return true;
		}

		$configured_host = wp_parse_url( (string) $configured_template, PHP_URL_HOST );
		if ( ! empty( $configured_host ) && strtolower( $target_host ) === strtolower( $configured_host ) ) {
			return true;
		}

		return false;
	}
}
