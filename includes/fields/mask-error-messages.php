<?php
/**
 * Shared mask validation error messages for FME / Atomic Form localize.
 *
 * @package Cool_FormKit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string, string>
 */
function cfl_get_mask_error_messages() {
	return array(
		'mask-cnpj'  => __( 'Invalid CNPJ.', 'extensions-for-elementor-form' ),
		'mask-cpf'   => __( 'Invalid CPF.', 'extensions-for-elementor-form' ),
		'mask-cep'   => __( 'Invalid CEP (XXXXX-XXX).', 'extensions-for-elementor-form' ),
		'mask-phus'  => __( 'Invalid number: (123) 456-7890', 'extensions-for-elementor-form' ),
		'mask-ph8'   => __( 'Invalid number: 1234-5678', 'extensions-for-elementor-form' ),
		'mask-ddd8'  => __( 'Invalid number: (DDD) 1234-5678', 'extensions-for-elementor-form' ),
		'mask-ddd9'  => __( 'Invalid number: (DDD) 91234-5678', 'extensions-for-elementor-form' ),
		'mask-dmy'   => __( 'Invalid date: dd/mm/yyyy', 'extensions-for-elementor-form' ),
		'mask-mdy'   => __( 'Invalid date: mm/dd/yyyy', 'extensions-for-elementor-form' ),
		'mask-hms'   => __( 'Invalid time: hh:mm:ss', 'extensions-for-elementor-form' ),
		'mask-hm'    => __( 'Invalid time: hh:mm', 'extensions-for-elementor-form' ),
		'mask-dmyhm' => __( 'Invalid date: dd/mm/yyyy hh:mm', 'extensions-for-elementor-form' ),
		'mask-mdyhm' => __( 'Invalid date: mm/dd/yyyy hh:mm', 'extensions-for-elementor-form' ),
		'mask-my'    => __( 'Invalid date: mm/yyyy', 'extensions-for-elementor-form' ),
		'mask-ccs'   => __( 'Invalid credit card number.', 'extensions-for-elementor-form' ),
		'mask-cch'   => __( 'Invalid credit card number.', 'extensions-for-elementor-form' ),
		'mask-ccmy'  => __( 'Invalid date.', 'extensions-for-elementor-form' ),
		'mask-ccmyy' => __( 'Invalid date.', 'extensions-for-elementor-form' ),
		'mask-ipv4'  => __( 'Invalid IPv4 address.', 'extensions-for-elementor-form' ),
	);
}
