<?php

namespace Arts\QueryControl\Managers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Arts\ElementorExtension\Plugins\BaseManager;

/**
 * Compatibility Class
 *
 * Manages compatibility with Elementor editor by handling script
 * and style enqueuing, as well as providing localized strings.
 *
 * @since 1.0.0
 */
class Compatibility extends BaseManager {
	/**
	 * Script and style handle.
	 *
	 * Used as the handle parameter for wp_enqueue_script() and wp_enqueue_style().
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string
	 */
	private $handle = 'arts-query-control-for-elementor-editor';

	/**
	 * Enqueue the editor scripts.
	 *
	 * Registers and enqueues the necessary JavaScript files for the editor.
	 * Adds translation strings via wp_localize_script().
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function elementor_enqueue_editor_scripts() {
		wp_enqueue_script(
			$this->handle,
			esc_url( untrailingslashit( $this->plugin_dir_url ) . '/libraries/arts-query-control-for-elementor/index.umd.js' ),
			array(),
			false,
			true
		);
	}

	/**
	 * Enqueue the editor styles.
	 *
	 * Registers and enqueues the necessary CSS files for the editor.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function elementor_enqueue_editor_styles() {
		wp_enqueue_style(
			$this->handle,
			esc_url( untrailingslashit( $this->plugin_dir_url ) . '/libraries/arts-query-control-for-elementor/index.css' ),
			array(),
			false
		);
	}
}
