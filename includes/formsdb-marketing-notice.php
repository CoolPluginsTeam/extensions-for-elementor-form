<?php
/**
 * Shared FormsDB marketing notice HTML for Elementor / Hello Plus sheet actions.
 *
 * @package Cool_FormKit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return string
 */
function cfl_formsdb_marketing_raw_html() {
	return '<div class="elementor-control-raw-html cool-form-wrp"><div class="elementor-control-notice elementor-control-notice-type-info">
		<div class="elementor-control-notice-icon"><img class="cfl-highlight-icon" src="' . esc_url( CFL_PLUGIN_URL . 'assets/images/cfl-highlight-icon.svg' ) . '" width="250" alt="Highlight Icon" /></div>
		<div class="elementor-control-notice-main">
			<div class="elementor-control-notice-main-content">Save Form Submissions to Google Sheets.</div>
			<div class="elementor-control-notice-main-actions">
			<button type="button" class="elementor-button e-btn e-info e-btn-1 cfl-install-plugin" data-plugin="form-db" data-nonce="' . esc_attr( wp_create_nonce( 'cfl_install_nonce' ) ) . '">Install FormsDB</button>
		</div></div>
		</div></div>';
}
