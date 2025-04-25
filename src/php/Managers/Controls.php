<?php

namespace Arts\QueryControl\Managers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use \Arts\ElementorExtension\Plugins\BaseManager;
use \Elementor\Controls_Manager;
use \Arts\QueryControl\Controls\QueryPostTypesSelect;
use \Arts\QueryControl\Controls\QueryPostsSelect;
use \Arts\QueryControl\Controls\QueryTermsSelect;
use \Arts\QueryControl\Controls\QueryMenusSelect;
use \Arts\QueryControl\Controls\QueryGroup;
use \Elementor\Core\Common\Modules\Ajax\Module as AJAX_Manager;

/**
 * Controls Manager Class
 *
 * Handles the registration of custom controls with Elementor
 * and manages AJAX actions for these controls.
 *
 * @since 1.0.0
 */
class Controls extends BaseManager {
	/**
	 * Register custom controls with Elementor.
	 *
	 * Registers all the query control types with the Elementor
	 * controls manager for use in widgets.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param Controls_Manager $controls_manager The Elementor controls manager instance.
	 * @return void
	 */
	public function register_controls( Controls_Manager $controls_manager ) {
		$controls_manager->register( QueryPostTypesSelect::instance() );
		$controls_manager->register( QueryPostsSelect::instance() );
		$controls_manager->register( QueryTermsSelect::instance() );
		$controls_manager->register( QueryMenusSelect::instance() );
		$controls_manager->add_group_control( QueryGroup::get_type(), QueryGroup::instance() );
	}

	/**
	 * Registers AJAX handlers for query controls.
	 *
	 * Registers AJAX actions for each query control type, allowing
	 * dynamic data retrieval in the Elementor editor.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param AJAX_Manager $ajax_manager The AJAX manager instance.
	 * @return void
	 */
	public function register_ajax_actions( AJAX_Manager $ajax_manager ) {
		QueryPostTypesSelect::instance()->register_ajax_action( $ajax_manager );
		QueryPostsSelect::instance()->register_ajax_action( $ajax_manager );
		QueryTermsSelect::instance()->register_ajax_action( $ajax_manager );
		QueryMenusSelect::instance()->register_ajax_action( $ajax_manager );
	}
}
