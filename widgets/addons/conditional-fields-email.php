<?php
namespace Cool_FormKit;

/**
 * Class Cfkef_Conditional_Email_Action_Pro
 */
if ( ! defined( 'ABSPATH' ) ){
    exit;
} 

use Elementor\Controls_Manager;
use ElementorPro\Core\Utils;
use Cool_FormKit\Modules\Forms\Components\Ajax_Handler;
use Cool_FormKit\Modules\Forms\Actions\Email;
use Cool_FormKit\Modules\Forms\Classes\Form_Record;

/**
 * Class Cfkef_Conditional_Email_Action_Pro
 */
if(!class_exists('Cfkef_Conditional_Email_Action_Pro')) {
class Cfkef_Conditional_Email_Action_Pro extends Email {

	/**
     * Get action name.
     *
     * Retrieve Cfkef_Conditional_Email_Action_Pro action name.
     *
     * @access public
     * @return string
     */
	public function get_name() {
		return 'email_conditional_action';
	}

	/**
     * Get action label.
     *
     * Retrieve Cfkef_Conditional_Email_Action_Pro action label.
     *
     * @access public
     * @return string
     */
	public function get_label() {
		return esc_html__( 'Email Conditionally', 'cool-formkit' );
	}

	/**
     * Get action Controler ID.
     *
     * Retrieve Cfkef_Conditional_Email_Action_Pro Controler ID.
     *
     * @access protected
     * @param string $control_id Control ID.
     * @return string
     */
	protected function controler_id_email( $control_id ) {
		return $control_id . '_cfefp_email_action';
	}

	/**
     * Get reply-to email address.
     *
     * @access protected
     * @param Form_Record $record Form record object.
     * @param array $fields Email fields.
     * @return string
     */
	protected function get_reply_to( $record, $fields ) {
		return isset( $fields['email_reply_to'] ) ? sanitize_email($fields['email_reply_to']) : '';
	}

	/**
     * Register action controls.
     *
     * Method to register action controls.
     *
     * @access public
     * @param \Elementor\Widget_Base $widget Elementor widget object.
     */
	public function register_settings_section( $widget ) {
		$widget->start_controls_section(
			$this->controler_id_email( 'section_email' ),
			array(
				'label' => $this->get_label(),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => array(
					'submit_actions' => $this->get_name(),
				),
			)
		);

		$widget->add_control(
			$this->controler_id_email( 'email_to' ),
			array(
				'label' => esc_html__( 'To', 'cool-formkit' ),
				'type' => Controls_Manager::TEXT,
				'default' => get_option( 'admin_email' ),
				'placeholder' => get_option( 'admin_email' ),
				'label_block' => true,
				'title' => esc_html__( 'Separate emails with commas', 'cool-formkit' ),
				'render_type' => 'none',
				'ai'          => array(
					'active' => false,
				),
				'dynamic' => array(
					'active' => true, 
				),
			)
		);

		/* translators: %s: Site title. */
		$default_message = sprintf( esc_html__( 'New message from "%s"', 'cool-formkit' ), get_option( 'blogname' ) );

		$widget->add_control(
			$this->controler_id_email( 'email_subject' ),
			array(
				'label' => esc_html__( 'Subject', 'cool-formkit' ),
				'type' => Controls_Manager::TEXT,
				'default' => $default_message,
				'placeholder' => $default_message,
				'label_block' => true,
				'render_type' => 'none',
				'ai'          => array(
					'active' => false,
				),
				'dynamic' => array(
					'active' => true, 
				),
			)
		);

		$widget->add_control(
			$this->controler_id_email( 'email_content' ),
			array(
				'label' => esc_html__( 'Message', 'cool-formkit' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => '[all-fields]',
				'placeholder' => '[all-fields]',
				'description' => sprintf( esc_html__( 'By default, all form fields are sent via %s shortcode. To customize sent fields, copy the shortcode that appears inside each field and paste it above.', 'cool-formkit' ), '<code>[all-fields]</code>' ),
				'render_type' => 'none',
				'ai'          => array(
					'active' => false,
				),
				'dynamic' => array(
					'active' => true, 
				),
			)
		);

		$site_domain = Utils::get_site_domain();

		$widget->add_control(
			$this->controler_id_email( 'email_from' ),
			array(
				'label' => esc_html__( 'From Email', 'cool-formkit' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'email@' . $site_domain,
				'render_type' => 'none',
				'ai'          => array(
					'active' => false,
				),
				'dynamic' => array(
					'active' => true, 
				),
			)
		);

		$widget->add_control(
			$this->controler_id_email( 'email_from_name' ),
			array(
				'label' => esc_html__( 'From Name', 'cool-formkit' ),
				'type' => Controls_Manager::TEXT,
				'default' => get_bloginfo( 'name' ),
				'render_type' => 'none',
				'ai'          => array(
					'active' => false,
				),
				'dynamic' => array(
					'active' => true, 
				),
			)
		);

		$admin_email_address = get_option( 'admin_email' );
		$widget->add_control(
			$this->controler_id_email( 'email_reply_to' ),
			array(
				'label' => esc_html__( 'Reply-To', 'cool-formkit' ),
				'type' => Controls_Manager::TEXT,
				'default' => $admin_email_address,
				'render_type' => 'none',
				'ai'          => array(
					'active' => false,
				),
			)
		);

		$widget->add_control(
			$this->controler_id_email( 'email_to_cc' ),
			array(
				'label' => esc_html__( 'Cc', 'cool-formkit' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'title' => esc_html__( 'Separate emails with commas', 'cool-formkit' ),
				'render_type' => 'none',
				'ai'          => array(
					'active' => false,
				),
				'dynamic' => array(
					'active' => true, 
				),
			)
		);

		$widget->add_control(
			$this->controler_id_email( 'email_to_bcc' ),
			array(
				'label' => esc_html__( 'Bcc', 'cool-formkit' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'title' => esc_html__( 'Separate emails with commas', 'cool-formkit' ),
				'render_type' => 'none',
				'ai'          => array(
					'active' => false,
				),
				'dynamic' => array(
					'active' => true, 
				),
			)
		);

		$widget->add_control(
			$this->controler_id_email( 'form_metadata' ),
			array(
				'label' => esc_html__( 'Meta Data', 'cool-formkit' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'label_block' => true,
				'separator' => 'before',
				'default' => array(
					'date',
					'time',
					'page_url',
					'user_agent',
					'remote_ip',
					'credit',
				),
				'options' => array(
					'date' => esc_html__( 'Date', 'cool-formkit' ),
					'time' => esc_html__( 'Time', 'cool-formkit' ),
					'page_url' => esc_html__( 'Page URL', 'cool-formkit' ),
					'user_agent' => esc_html__( 'User Agent', 'cool-formkit' ),
					'remote_ip' => esc_html__( 'Remote IP', 'cool-formkit' ),
					'credit' => esc_html__( 'Credit', 'cool-formkit' ),
				),
				'render_type' => 'none',
				'ai'          => array(
					'active' => false,
				),
			)
		);

		$widget->add_control(
			$this->controler_id_email( 'email_content_type' ),
			array(
				'label' => esc_html__( 'Send As', 'cool-formkit' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'html',
				'render_type' => 'none',
				'options' => array(
					'html' => esc_html__( 'HTML', 'cool-formkit' ),
					'plain' => esc_html__( 'Plain', 'cool-formkit' ),
				),
			)
		);
		$cfef_conditional_logic_id = $this->controler_id_email( 'email_conditional_logic' );
		$widget->add_control(
			$cfef_conditional_logic_id,
			array(
				'label' => esc_html__( 'Enable Conditions', 'cool-formkit' ),
				'render_type' => 'none',
				'type' => Controls_Manager::SWITCHER,
			)
		);
		$widget->add_control(
			$this->controler_id_email( 'email_conditional_field_display' ),
			array(
				'label' => esc_html__( 'Send mode', 'cool-formkit' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => array(
                    'show' => array(
                        'title' => esc_html__( 'Send if', 'cool-formkit' ),
                        'icon' => 'fa fa-paper-plane',
					),
                    'hide' => array(
                        'title' => esc_html__( 'Don not send', 'cool-formkit' ),
                        'icon' => 'fa fa-times',
					),
				),
                'default' => 'show',
                'condition' => array(
                    $cfef_conditional_logic_id => 'yes'
				),
			)
		);
		$widget->add_control(
			$this->controler_id_email( 'email_conditional_field_trigger' ),
			array(
				'label' => esc_html__( 'Conditions Trigger', 'cool-formkit' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    "All"=> esc_html__('All - AND Conditions','cool-formkit'),
					"Any"=> esc_html__('Any - OR Conditions','cool-formkit')
				),
                'default' => 'All',
                'condition' => array(
                    $cfef_conditional_logic_id => 'yes'
				),
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'conditional_logic_id',
			array(
				'label' => esc_html__( 'Field ID', 'cool-formkit' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => '',
				'ai' => array(
					'active' => false,
				),
			)
		);

		$repeater->add_control(
			'conditional_logic_operator',
			array(
				'label' => esc_html__( 'Operator', 'cool-formkit' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'options' => array(
					'==' => esc_html__( 'is equal ( == )', 'cool-formkit' ),
					'!=' => esc_html__( 'is not equal (!=)', 'cool-formkit' ),
					'>'  => esc_html__( 'greater than (>)', 'cool-formkit' ),
					'<'  => esc_html__( 'less than (<)', 'cool-formkit' ),
					'>=' => esc_html__( 'greater than equal (>=)', 'cool-formkit' ),
					'<=' => esc_html__( 'less than equal (<=)', 'cool-formkit' ),
					'e'  => esc_html__( "empty ('')", 'cool-formkit' ),
					'!e' => esc_html__( 'not empty', 'cool-formkit' ),
					'c'  => esc_html__( 'contains', 'cool-formkit' ),
					'!c' => esc_html__( 'does not contain', 'cool-formkit' ),
					'^'  => esc_html__( 'starts with', 'cool-formkit' ),
					'~'  => esc_html__( 'ends with', 'cool-formkit' ),
				),
				'default' => '==',
				'ai' => array(
					'active' => false,
				),
			)
		);

		$repeater->add_control(
			'conditional_logic_value',
			array(
				'label' => esc_html__( 'Value to compare', 'cool-formkit' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => '',
				'ai' => array(
					'active' => false,
				),
			)
		);

		$widget->add_control(
			$this->controler_id_email( 'email_conditional_fields_datas' ),
			array(
				'name' => 'email_conditional_fields_datas',
				'label' => esc_html__( 'Send if / Do not Send', 'cool-formkit' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'condition' => array(
					$cfef_conditional_logic_id => 'yes'
				),
				'style_transfer' => false,
				'title_field' => '{{{ conditional_logic_id }}} {{{ conditional_logic_operator }}} {{{ conditional_logic_value }}}',
				'default' => array(
					array(
						'conditional_logic_id' => '',
						'conditional_logic_operator' => '==',
						'conditional_logic_value' => '',
					),
				),
			)
		);


		$widget->end_controls_section();
	}

	/**
     * Run action.
     *
     * Method for Cfkef_Conditional_Email_Action_Pro after form submission.
     *
     * @access public
     * @param Form_Record $record Form record object.
     * @param Ajax_Handler $ajax_handler Ajax handler object.
     * @throws Exception If email sending fails.
     */
	public function run( $record, $ajax_handler ) { 
		$settings = $record->get( 'form_settings' );
		
		$send_status = true;
		if( $settings[$this->controler_id_email( 'email_conditional_logic' )] == "yes" ){
			$display_mode = $settings[$this->controler_id_email( 'email_conditional_field_display' )];
            $fire_action = $settings[$this->controler_id_email( 'email_conditional_field_trigger' )];
            $repeater_data = $settings[$this->controler_id_email( 'email_conditional_fields_datas' )];
            $condition_pass_fail = array();
            $form_fields = $record->get("fields");
            foreach ( $repeater_data as $logic_key => $logic_values ) {
                if(isset($form_fields[$logic_values["conditional_logic_id"]])){
                    $value_id = $form_fields[$logic_values["conditional_logic_id"]]["value"];
                    if( is_array($value_id) ){
                        $value_id = implode(", ",$value_id);
                    }
                }
				else{
                //    $value_id = $logic_values["conditional_logic_id"];
					$value_id = "";
                }
                $operator = $logic_values["conditional_logic_operator"];
                $value = $logic_values["conditional_logic_value"];
                $condition_pass_fail[] = $this->cfefp_check_email_action_logic($value_id,$operator,$value);
            }
            $check_rs = false;
			if ($fire_action == "All") {
				$check_rs = true;
				foreach ($condition_pass_fail as $fvalue) {
					if (!$fvalue) {
						$check_rs = false;
						break;
					}
				}
			} else {
				foreach ($condition_pass_fail as $fvalue) {
					if ($fvalue) {
						$check_rs = true;
						break;
					}
				}
			}
			if ($display_mode == "show") {
				$send_status = $check_rs;
			} else {
				$send_status = !$check_rs;
			}			

		}
		if( $send_status ==  true ){
			
			$send_html = 'plain' !== $settings[ $this->controler_id_email( 'email_content_type' ) ];
			$line_break = $send_html ? '<br>' : "\n";

			$fields = [
				'email_to' => get_option( 'admin_email' ),
				/* translators: %s: Site title. */
				'email_subject' => sprintf( esc_html__( 'New message from "%s"', 'cool-formkit' ), get_bloginfo( 'name' ) ),
				'email_content' => '[all-fields]',
				'email_from_name' => get_bloginfo( 'name' ),
				'email_from' => get_bloginfo( 'admin_email' ),
				'email_reply_to' => 'noreply@' . Utils::get_site_domain(),
				'email_to_cc' => '',
				'email_to_bcc' => '',
			];

			foreach ( $fields as $key => $default ) {
				$setting = trim( $settings[ $this->controler_id_email( $key ) ] );
				$setting = $record->replace_setting_shortcodes( $setting );
				if ( ! empty( $setting ) ) {
					$fields[ $key ] = $setting;
				}
			}

			$email_reply_to = $this->get_reply_to( $record, $fields );

			$fields['email_content'] = $this->replace_content_shortcodes( $fields['email_content'], $record, $line_break );
			$email_meta = '';

			$form_metadata_settings = $settings[ $this->controler_id_email( 'form_metadata' ) ];

			foreach ( $record->get( 'meta' ) as $id => $field ) {
				if ( in_array( $id, $form_metadata_settings ) ) {
					$email_meta .= $this->cfef_field_formatted( $field ) . $line_break;
				}
			}

			if ( ! empty( $email_meta ) ) {
				$fields['email_content'] .= $line_break . '---' . $line_break . $line_break . $email_meta;
			}

			$headers = sprintf( 'From: %s <%s>' . "\r\n", $fields['email_from_name'], $fields['email_from'] );
			$headers .= sprintf( 'Reply-To: %s' . "\r\n", $email_reply_to );

			if ( $send_html ) {
				$headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
			}

			$cc_header = '';
			if ( ! empty( $fields['email_to_cc'] ) ) {
				$cc_header = 'Cc: ' . $fields['email_to_cc'] . "\r\n";
			}
			$headers = apply_filters( 'elementor_pro/forms/wp_mail_headers', $headers );

			$fields['email_content'] = apply_filters( 'elementor_pro/forms/wp_mail_message', $fields['email_content'] );

			$email_sent = wp_mail( $fields['email_to'], $fields['email_subject'], $fields['email_content'], $headers . $cc_header );
			if ( ! empty( $fields['email_to_bcc'] ) ) {
				$bcc_emails = explode( ',', $fields['email_to_bcc'] );
				foreach ( $bcc_emails as $bcc_email ) {
					wp_mail( trim( $bcc_email ), $fields['email_subject'], $fields['email_content'], $headers );
				}
			}

			do_action( 'elementor_pro/forms/mail_sent', $settings, $record );

			if ( ! $email_sent ) {
				$message = Ajax_Handler::get_default_message( Ajax_Handler::SERVER_ERROR, $settings );

				$ajax_handler->add_error_message( $message );

				throw new \Exception( $message );
			}
		}
	}

	/**
	 * Replace content shortcodes.
	 *
	 * Replaces content shortcodes with actual field values in the email content.
	 *
	 * @access private
	 * @param string $email_content Email content with shortcodes.
	 * @param Form_Record $record Form record object.
	 * @param string $line_break Line break character.
	 * @return string Email content with replaced shortcodes.
 	*/
	private function replace_content_shortcodes( $email_content, $record, $line_break ) {
		$email_content = do_shortcode( $email_content );
		$all_fields_shortcode = '[all-fields]';

		if ( false !== strpos( $email_content, $all_fields_shortcode ) ) {
			$text = '';
			foreach ( $record->get( 'fields' ) as $field ) {
				$formatted = $this->cfef_field_formatted( $field );
				if ( ( 'textarea' === $field['type'] ) && ( '<br>' === $line_break ) ) {
					$formatted = str_replace( [ "\r\n", "\n", "\r" ], '<br />', $formatted );
				}
				$text .= $formatted . $line_break;
			}

			$email_content = str_replace( $all_fields_shortcode, $text, $email_content );

		}

		return $email_content;
	}

	/**
	 * Format field for email.
	 *
	 * Formats a field for display in the email content.
	 *
	 * @access private
	 * @param array $field Field array.
	 * @return string Formatted field.
	 */
	private function cfef_field_formatted( $field ) {
		$formatted = '';
		if ( ! empty( $field['title'] ) ) {
			$formatted = sprintf( '%s: %s', $field['title'], $field['value'] );
		} elseif ( ! empty( $field['value'] ) ) {
			$formatted = sprintf( '%s', $field['value'] );
		}

		return $formatted;
	}

	/**
	 * Check email action logic.
	 *
	 * Checks if the conditions for sending the email are met.
	 *
	 * @param mixed $value_id Field value.
	 * @param string $operator Comparison operator.
	 * @param mixed $value Value to compare against.
	 * @return boolean Whether the conditions are met for sending the email.
	 */
	function cfefp_check_email_action_logic($value_id,$operator,$value){
        
		// Sanitize and escape dynamic values.
		$value_id = esc_html( $value_id );
		$value    = trim( $value );
		$value    = esc_html( $value );

        switch($operator) {
            case "==":
                return $value_id == $value;
            case "!=":
                return $value_id != $value;
            case "e":
                return $value_id == "";
            case "!e":
                return $value_id != "";
            case "c":
                return str_contains($value_id, $value);
            case "!c":
                return !str_contains($value_id, $value);
            case "^":
                return str_starts_with($value_id, $value);
            case "~":
                return str_ends_with($value_id, $value);
            case ">":
                return $value_id > $value;
            case "<":
                return $value_id < $value;
            case "<=":
                return $value_id <= $value;
            case ">=":
                return $value_id >= $value;
            default:
                return false; // Default case if operator is not recognized
			}
    }
}

/**
 * Class Cfkef_Conditional_Email_Action_Two_Pro
 */
class Cfkef_Conditional_Email_Action_Two_Pro extends Cfkef_Conditional_Email_Action_Pro {
	public function get_name() {
		return 'email_conditional_action_2';
	}

	public function get_label() {
		return esc_html__( 'Email Conditionally 2', 'cool-formkit' );
		
	}
	protected function controler_id_email( $control_id ) {
		return $control_id . '_cfefp_email_action_two';
	}
}

/**
 * Class Cfkef_Conditional_Email_Action_Three_Pro
 */
class Cfkef_Conditional_Email_Action_Three_Pro extends Cfkef_Conditional_Email_Action_Pro {
	public function get_name() {
		return 'email_conditional_action_3';
	}

	public function get_label() {
		return esc_html__( 'Email Conditionally 3', 'cool-formkit' );
		
	}
	protected function controler_id_email( $control_id ) {
		return $control_id . '_cfefp_email_action_three';
	}
}

/**
 * Class Cfkef_Conditional_Email_Action_Four_Pro
 */
class Cfkef_Conditional_Email_Action_Four_Pro extends Cfkef_Conditional_Email_Action_Pro {
	public function get_name() {
		return 'email_conditional_action_4';
	}

	public function get_label() {
		return esc_html__( 'Email Conditionally 4', 'cool-formkit' );
		
	}
	protected function controler_id_email( $control_id ) {
		return $control_id . '_cfefp_email_action_four';
	}
}

/**
 * Get the email conditional key value and loop the rest of the based on that. 
 */

$email_conditionally = get_option('cfefp_email_conditionally', '');

for ($i = 5; $i <= $email_conditionally; $i++) {
    $class_name = sprintf('Cfkef_Conditional_Email_Action_%s_Pro', $i);
    if (!class_exists($class_name)) {
        eval("
            class $class_name extends Cfkef_Conditional_Email_Action_Pro {
                public function get_name() {
                    return 'email_conditional_action_{$i}';
                }

                public function get_label() {
                    return esc_html__('Email Conditionally {$i}', 'cool-formkit');
                }

                public function controler_id_email(\$control_id) {
                    return \$control_id . '_cfefp_email_action_{$i}';
                }
            }
        ");
    }
}
}
