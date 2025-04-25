<?php

namespace Arts\QueryControl\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use \Elementor\Control_Select2;
use \Elementor\Core\Editor\Editor;
use \Elementor\Core\Common\Modules\Ajax\Module as AJAXManager;

/**
 * Abstract Query Control base class
 *
 * Extends Elementor's Select2 control to provide enhanced query capabilities.
 * This abstract class serves as the foundation for all query controls in this plugin.
 *
 * @since 1.0.0
 */
abstract class QueryControl extends Control_Select2 {
	/**
	 * Instances of the class.
	 *
	 * Stores singleton instances of derived control classes.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array
	 */
	protected static $instances = array();

	/**
	 * Control type identifier.
	 *
	 * Must be overridden in extending classes with a unique identifier.
	 *
	 * @since 1.0.0
	 */
	public const TYPE = '';

	/**
	 * AJAX action for retrieving data.
	 *
	 * Must be overridden in extending classes with a unique action name.
	 *
	 * @since 1.0.0
	 */
	public const ACTION_GET = '';

	/**
	 * AJAX action for autocomplete functionality.
	 *
	 * Must be overridden in extending classes with a unique action name.
	 *
	 * @since 1.0.0
	 */
	public const ACTION_AUTOCOMPLETE = '';

	/**
	 * Get the instance of this class.
	 *
	 * Implements the singleton pattern to ensure only one instance
	 * of each control class exists.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return object The instance of this class.
	 */
	public static function instance() {
		$cls = static::class;

		if ( ! isset( self::$instances[ $cls ] ) ) {
			self::$instances[ $cls ] = new static();
		}

		return self::$instances[ $cls ];
	}

	/**
	 * Get control type.
	 *
	 * Returns the control type identifier defined in extending classes.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Control type.
	 */
	public function get_type() {
		return static::TYPE;
	}

	/**
	 * Get the AJAX action for retrieving data.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string AJAX action name.
	 */
	public function get_action() {
		return static::ACTION_GET;
	}

	/**
	 * Get the AJAX action for autocomplete functionality.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string AJAX action name for autocomplete.
	 */
	public function get_action_autocomplete() {
		return static::ACTION_AUTOCOMPLETE;
	}

	/**
	 * Register AJAX actions for this control.
	 *
	 * Registers the get and autocomplete AJAX actions for use in the Elementor editor.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param AJAXManager $ajax_manager The Elementor AJAX manager.
	 * @return void
	 */
	public function register_ajax_action( AJAXManager $ajax_manager ) {
		if ( static::ACTION_GET ) {
			$ajax_manager->register_ajax_action( static::ACTION_GET, array( static::class, 'ajax_action_get' ) );
		}

		if ( static::ACTION_AUTOCOMPLETE ) {
			$ajax_manager->register_ajax_action( static::ACTION_AUTOCOMPLETE, array( static::class, 'ajax_action_autocomplete' ) );
		}
	}

	/**
	 * AJAX handler for retrieving data.
	 *
	 * Must be implemented in extending classes to handle the AJAX request
	 * for retrieving data for the control.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param array $data Request data.
	 * @return array|WP_Error Response data or WP_Error on failure.
	 */
	public static function ajax_action_get( $data ) {
		if ( ! current_user_can( Editor::EDITING_CAPABILITY ) ) {
			return new \WP_Error( 'access_denied', esc_html__( 'Access denied.', 'arts-query-control-for-elementor' ) );
		}

		// Implement the logic to get the data
		return array();
	}

	/**
	 * AJAX handler for autocomplete functionality.
	 *
	 * Must be implemented in extending classes to handle the AJAX request
	 * for autocomplete functionality.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param array $data Request data.
	 * @return array|WP_Error Response data in the format expected by Select2 or WP_Error on failure.
	 */
	public static function ajax_action_autocomplete( $data ) {
		if ( ! current_user_can( Editor::EDITING_CAPABILITY ) ) {
			return new \WP_Error( 'access_denied', esc_html__( 'Access denied.', 'arts-query-control-for-elementor' ) );
		}

		// Implement the logic to get the data
		return array(
			'results' => array(),
		);
	}

	/**
	 * Retrieve the autocomplete query data.
	 *
	 * Processes and formats the data received from an autocomplete AJAX request.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @static
	 *
	 * @param array $data The request data.
	 * @return array|\WP_Error The processed query data or error object.
	 */
	protected static function autocomplete_query_data( $data ) {
		if ( ! isset( $data['autocomplete'] ) || empty( $data['autocomplete'] ) ) {
			return new \WP_Error( 'ArtsQueryControlAutocomplete', esc_html__( 'Empty or incomplete data', 'arts-query-control-for-elementor' ) );
		}

		$autocomplete = $data['autocomplete'];

		$query = $data['autocomplete']['query'];

		if ( empty( $query['post_type'] ) ) {
			$query['post_type'] = 'any';
		}

		$query['posts_per_page'] = -1;
		$query['s']              = isset( $data['search'] ) ? $data['search'] : '';

		if ( is_wp_error( $query ) ) {
			return $query;
		}

		$autocomplete['query'] = $query;

		return $autocomplete;
	}

	/**
	 * Retrieve the post name with parents.
	 *
	 * Formats a post's title to include its parent posts' titles in a hierarchical format.
	 * Useful for displaying hierarchical post types like pages in a more readable format.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @static
	 *
	 * @param \WP_Post $post The post object.
	 * @param int      $max  The maximum number of parents to display. Default 3.
	 * @return string The formatted post name with parents.
	 */
	protected static function get_post_name_with_parents( $post, $max = 3 ) {
		if ( $post->post_parent === 0 ) {
			return $post->post_title;
		}

		$separator = is_rtl() ? ' < ' : ' > ';
		$test_post = $post;
		$names     = array();

		while ( $test_post->post_parent > 0 ) {
			$test_post = get_post( $test_post->post_parent );

			if ( ! $test_post ) {
				break;
			}

			$names[] = $test_post->post_title;
		}

		$names = array_reverse( $names );

		if ( count( $names ) < ( $max ) ) {
			return implode( $separator, $names ) . $separator . $post->post_title;
		}

		$name_string = '';

		for ( $i = 0; $i < ( $max - 1 ); $i++ ) {
			$name_string .= $names[ $i ] . $separator;
		}

		return $name_string . '...' . $separator . $post->post_title;
	}

	/**
	 * Get default settings.
	 *
	 * Retrieves the default settings for this control.
	 * Can be overridden by child classes to add/modify settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return array_merge(
			parent::get_default_settings(),
			array(
				'multiple' => false,
				'sortable' => false,
			)
		);
	}
}
