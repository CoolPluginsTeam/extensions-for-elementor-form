<?php
/**
 * Shared FME (Form Mask Elementor) plugin bootstrap for Elementor Pro, Cool Form, and Hello Plus.
 *
 * Concrete classes are final singletons. Call init_fme_plugin() from the private constructor.
 *
 * @package Cool_FormKit
 */

namespace Cool_FormKit\Includes\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait FME_Plugin_Trait {

	/**
	 * @return string
	 */
	abstract protected function get_custom_mask_script_handle(): string;

	/**
	 * @return string
	 */
	abstract protected function get_frontend_style_handle(): string;

	/**
	 * @return string
	 */
	abstract protected function get_input_mask_script_handle(): string;

	/**
	 * @return string Absolute URL to the platform new-input-mask script.
	 */
	abstract protected function get_input_mask_script_src(): string;

	/**
	 * @return string
	 */
	abstract protected function get_editor_template_script_handle(): string;

	/**
	 * @return string Absolute URL to the editor template script.
	 */
	abstract protected function get_editor_template_script_src(): string;

	/**
	 * Action fired after mask attributes are applied.
	 *
	 * @return string
	 */
	abstract protected function get_after_mask_attribute_action(): string;

	/**
	 * Shared constructor body. Call from the concrete class private constructor.
	 */
	protected function init_fme_plugin(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'my_enqueue_scripts' ) );
		add_action( 'elementor/preview/init', array( $this, 'editor_inline_JS' ) );
		add_action( 'init', array( $this, 'init' ), 10 );
		add_action( $this->get_after_mask_attribute_action(), array( $this, 'add_frontend_assets_conditionally' ), 10, 3 );
	}

	/**
	 * @param array $field        Field settings.
	 * @param mixed $field_index  Field index.
	 * @param mixed $form_widget  Form widget.
	 * @return void
	 */
	public function add_frontend_assets_conditionally( $field, $field_index, $form_widget ) {
		if ( ! empty( $field['fme_mask_control'] ) && $field['fme_mask_control'] !== 'mask' && $field['field_type'] === 'text' ) {
			$this->handle_dynamic_assests_loading( false );
		}
	}

	public function my_enqueue_scripts() {
		$custom_handle = $this->get_custom_mask_script_handle();
		$style_handle  = $this->get_frontend_style_handle();
		$input_handle  = $this->get_input_mask_script_handle();

		wp_register_script(
			'cfkef-mask-validators',
			CFL_PLUGIN_URL . 'assets/js/shared/mask-validators.js',
			array(),
			CFL_VERSION,
			true
		);

		wp_register_script( $custom_handle, CFL_PLUGIN_URL . 'assets/js/inputmask/custom-mask-script.js', array( 'jquery', 'cfkef-mask-validators' ), CFL_VERSION, true );

		wp_register_style( $style_handle, CFL_PLUGIN_URL . 'assets/css/inputmask/mask-frontend.css', array(), CFL_VERSION, 'all' );

		wp_register_script(
			'cfkef-shared-mask-ui',
			CFL_PLUGIN_URL . 'assets/js/shared/mask-ui.js',
			array( 'jquery' ),
			CFL_VERSION,
			true
		);

		wp_register_script(
			'cfkef-shared-input-mask',
			CFL_PLUGIN_URL . 'assets/js/shared/input-mask.js',
			array( 'elementor-frontend', 'jquery', 'cfkef-shared-mask-ui' ),
			CFL_VERSION,
			true
		);

		wp_register_script( $input_handle, $this->get_input_mask_script_src(), array( 'elementor-frontend', 'jquery', 'cfkef-shared-input-mask' ), CFL_VERSION, true );

		if ( ! function_exists( 'cfl_get_mask_error_messages' ) ) {
			require_once CFL_PLUGIN_PATH . 'includes/fields/mask-error-messages.php';
		}

		wp_localize_script(
			$custom_handle,
			'fmeData',
			array(
				'pluginUrl'     => CFL_PLUGIN_URL,
				'errorMessages' => cfl_get_mask_error_messages(),
			)
		);
	}

	public function editor_inline_JS() {
		wp_register_script(
			'cfkef-shared-mask-editor-classes',
			CFL_PLUGIN_URL . 'assets/js/shared/mask-editor-classes.js',
			array(),
			CFL_VERSION,
			true
		);
		wp_enqueue_script(
			$this->get_editor_template_script_handle(),
			$this->get_editor_template_script_src(),
			array( 'cfkef-shared-mask-editor-classes' ),
			CFL_VERSION,
			true
		);

		$this->handle_dynamic_assests_loading( true );
	}

	/**
	 * @param bool $editor Whether loading for the editor preview.
	 * @return void
	 */
	public function handle_dynamic_assests_loading( $editor ) {
		$custom_handle = $this->get_custom_mask_script_handle();

		if ( $editor ) {
			if ( ! wp_script_is( 'cfkef-mask-validators', 'registered' ) ) {
				wp_register_script(
					'cfkef-mask-validators',
					CFL_PLUGIN_URL . 'assets/js/shared/mask-validators.js',
					array(),
					CFL_VERSION,
					true
				);
			}

			wp_register_script(
				$custom_handle,
				CFL_PLUGIN_URL . 'assets/js/inputmask/custom-mask-script.js',
				array( 'jquery', 'cfkef-mask-validators' ),
				CFL_VERSION,
				true
			);
		}

		wp_enqueue_script( 'cfkef-mask-validators' );
		wp_enqueue_script( $custom_handle );
		wp_enqueue_script( $this->get_input_mask_script_handle() );
		wp_enqueue_style( $this->get_frontend_style_handle() );
	}
}
