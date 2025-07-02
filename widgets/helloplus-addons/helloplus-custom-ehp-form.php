<?php
namespace Cool_FormKit\Widgets\HelloPlusAddons;

use HelloPlus\Modules\Forms\Widgets\Ehp_Form as Base_Form;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Custom_Ehp_Form extends Base_Form {

    public function __construct( $data = [], $args = null ) {
        parent::__construct( $data, $args );

        // instantiate handlers so they hook into render_field actions
        static $custom_fields_loaded = false;

        if ( ! $custom_fields_loaded ) {
            $custom_fields_loaded = true;
        }
    }

    protected function add_content_form_fields_section(): void {
        parent::add_content_form_fields_section();
        
        $stack_id = $this->get_unique_name();
        $control  = \Elementor\Plugin::instance()
            ->controls_manager
            ->get_control_from_stack( $stack_id, 'form_fields' );

        if ( is_wp_error( $control ) || empty( $control['fields'] ) || ! is_array( $control['fields'] ) ) {
            return;
        }

        $fields = $control['fields'];

        // ensure options textarea shows up for radio & checkbox
        $vals = &$fields['field_options']['conditions']['terms'][0]['value'];
        foreach ( [ 'radio', 'checkbox' ] as $type ) {
            if ( ! in_array( $type, $vals, true ) ) {
                $vals[] = $type;
            }
        }

        $this->update_control( 'form_fields', [ 'fields' => $fields ] );
    }

    public function get_name() {
        return 'ehp-form';
    }

    public function get_title() {
        return esc_html__( 'Form Lite', 'cool-formkit-for-elementor-forms' );
    }

    public function get_icon() {
        return 'eicon-ehp-forms';
    }
}
