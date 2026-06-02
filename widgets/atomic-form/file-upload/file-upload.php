<?php
namespace Cool_FormKit\Widgets\AtomicForm\File_Upload;

use Cool_FormKit\Widgets\AtomicForm\Input\Conditional_Input_Definition;
use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Attachment_Type_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Number_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Switch_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Text_Control;
use Elementor\Modules\AtomicWidgets\Elements\Base\Has_Template;
use Elementor\Modules\AtomicWidgets\PropTypes\Attributes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Classes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Boolean_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropDependencies\Manager as Dependency_Manager;
use Elementor\Modules\Components\PropTypes\Overridable_Prop_Type;
use ElementorPro\Modules\AtomicForm\Default_Id_Provider;
use ElementorPro\Modules\AtomicForm\File_Upload\File_Upload as AtomicFormFileUpload;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once CFL_PLUGIN_PATH . 'widgets/atomic-form/field-controls-definition/conditional-input-definition.php';

class File_Upload extends AtomicFormFileUpload {
	use Has_Template;

	public const DEFAULT_MAX_FILE_SIZE_MB = 5;
	public const DEFAULT_FILE_TYPES = 'jpg, png, pdf, zip';

	public static $widget_description = 'Display a file upload input with configurable allowed types, size limit, multiple-file support, and required flag.';

	public static function get_element_type(): string {
		return 'e-form-file-upload';
	}

	public function get_title(): string {
		return esc_html__( 'File Upload', 'elementor-pro' );
	}

	public function get_icon(): string {
		return 'eicon-atomic-file-upload';
	}

	public function get_categories(): array {
		return [ 'atomic-form' ];
	}

	public function get_keywords() {
		return [ 'atomic', 'form', 'file', 'upload' ];
	}

	protected static function define_props_schema(): array {
		$max_files_dependencies = Dependency_Manager::make()
			->where( [
				'operator' => 'eq',
				'path' => [ 'multiple' ],
				'value' => true,
				'effect' => 'hide',
			] )
			->get();

		$schema = [
			'classes' => Classes_Prop_Type::make()
				->default( [] ),
			'attachment-type' => String_Prop_Type::make()
				->default( 'link' )
				->enum( [ 'link', 'attach', 'both' ] ),
			'max-file-size' => Number_Prop_Type::make()
				->default( self::DEFAULT_MAX_FILE_SIZE_MB )
				->meta( 'suffix', 'MB' ),
			'file-types' => String_Prop_Type::make()
				->default( self::DEFAULT_FILE_TYPES ),
			'multiple' => Boolean_Prop_Type::make()
				->default( false ),
			'max-files' => Number_Prop_Type::make()
				->default( 1 )
				->set_dependencies( $max_files_dependencies ),
			'required' => Boolean_Prop_Type::make()
				->default( false ),
			'attributes' => Attributes_Prop_Type::make()->meta( Overridable_Prop_Type::ignore() ),
			'_cssid' => Default_Id_Provider::get_default_id_prop( self::get_element_type() ),
		];

		if ( Conditional_Input_Definition::is_conditional_logic_enabled() ) {
			$schema = array_merge( $schema, Conditional_Input_Definition::props_schema() );
		}

		return $schema;
	}

	protected function define_atomic_controls(): array {
		$sections = [
			Section::make()
				->set_label( __( 'Content', 'elementor-pro' ) )
				->set_items( [
					Attachment_Type_Control::bind_to( 'attachment-type' )
						->set_label( __( 'Send file', 'elementor-pro' ) )
						->set_options( [
							[
								'label' => __( 'Email with link', 'elementor-pro' ),
								'value' => 'link',
							],
							[
								'label' => __( 'Email with attachment', 'elementor-pro' ),
								'value' => 'attach',
							],
							[
								'label' => __( 'Email with both', 'elementor-pro' ),
								'value' => 'both',
							],
						] ),
					Number_Control::bind_to( 'max-file-size' )
						->set_label( __( 'Max file size', 'elementor-pro' ) )
						->set_min( 1 ),
					Text_Control::bind_to( 'file-types' )
						->set_label( __( 'Allowed file types', 'elementor-pro' ) )
						->set_placeholder( 'pdf, docx, doc, jpeg, jpg…' ),
					Switch_Control::bind_to( 'multiple' )
						->set_label( __( 'Multiple files', 'elementor-pro' ) ),
					Number_Control::bind_to( 'max-files' )
						->set_label( __( 'Max files', 'elementor-pro' ) )
						->set_placeholder( 'specify number' )
						->set_min( 1 ),
					Switch_Control::bind_to( 'required' )
						->set_label( __( 'Required', 'elementor-pro' ) ),
				] ),
			Section::make()
				->set_label( __( 'Settings', 'elementor-pro' ) )
				->set_id( 'settings' )
				->set_items( $this->get_settings_controls() ),
		];

		if ( Conditional_Input_Definition::is_conditional_logic_enabled() ) {
			$sections[] = Conditional_Input_Definition::conditions_section();
		}

		return $sections;
	}


	protected function get_templates(): array {
		return [
			'file_upload' => __DIR__ . '/file-upload.html.twig',
		];
	}
}
