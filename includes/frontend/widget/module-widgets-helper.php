<?php
/**
 * Language list helper for form field controls.
 *
 * @package Cool_FormKit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Provides language options for country/language selectors.
 */
if ( ! class_exists( 'Module_widgets_Helper', false ) ) {
class Module_widgets_Helper {

	/**
	 * Language code => label map.
	 *
	 * @var array<string, string>
	 */
	public static $countries = array(
		'ar' => 'Arabic',
		'bg' => 'Bulgarian',
		'bn' => 'Bengali',
		'bs' => 'Bosnian',
		'ca' => 'Catalan',
		'cs' => 'Czech',
		'da' => 'Danish',
		'de' => 'German',
		'ee' => 'Estonian',
		'el' => 'Greek',
		'es' => 'Spanish',
		'fa' => 'Persian',
		'fi' => 'Finnish',
		'fr' => 'French',
		'hi' => 'Hindi',
		'hr' => 'Croatian',
		'hu' => 'Hungarian',
		'id' => 'Indonesian',
		'it' => 'Italian',
		'ja' => 'Japanese',
		'ko' => 'Korean',
		'mr' => 'Marathi',
		'nl' => 'Dutch',
		'no' => 'Norwegian',
		'pl' => 'Polish',
		'pt' => 'Portuguese',
		'ro' => 'Romanian',
		'ru' => 'Russian',
		'sk' => 'Slovak',
		'sv' => 'Swedish',
		'te' => 'Telugu',
		'th' => 'Thai',
		'tr' => 'Turkish',
		'uk' => 'Ukrainian',
		'ur' => 'Urdu',
		'vi' => 'Vietnamese',
		'zh' => 'Chinese',
		'en' => 'English',
	);

	/**
	 * Language options for select controls.
	 *
	 * @return array<string, string>
	 */
	public static function get_countries_list() {
		return self::$countries;
	}
}
}
