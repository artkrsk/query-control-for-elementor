<?php

namespace Arts\QueryControl\Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use \Elementor\Core\Editor\Editor;
use \Arts\QueryControl\Base\QueryControl;

/**
 * QueryPostTypesSelect Control Class
 *
 * Provides a specialized Elementor control for selecting post types,
 * with both standard and autocomplete functionality.
 *
 * @since 1.0.0
 */
class QueryPostTypesSelect extends QueryControl {
	/**
	 * Cached post types array.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @var array
	 */
	public static $post_types = array();

	/**
	 * Control type identifier.
	 *
	 * @since 1.0.0
	 */
	public const TYPE = 'arts-query-control-for-elementor-post-types-select';

	/**
	 * AJAX action for retrieving post types.
	 *
	 * @since 1.0.0
	 */
	public const ACTION_GET = 'arts_query_control_for_elementor_get_post_types';

		/**
		 * AJAX action for autocomplete functionality.
		 *
		 * @since 1.0.0
		 */
	public const ACTION_AUTOCOMPLETE = 'arts_query_control_for_elementor_get_post_types_autocomplete';

	/**
	 * Get singleton instance of this class.
	 *
	 * Ensures that post types are initialized upon instance creation.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return QueryPostTypesSelect The singleton instance.
	 */
	public static function instance() {
		$cls = static::class;

		if ( ! isset( self::$instances[ $cls ] ) ) {
			self::$instances[ $cls ] = new static();
		}

		if ( is_null( self::$post_types ) ) {
			self::$post_types = self::get_post_types();
		}

		return self::$instances[ $cls ];
	}

	/**
	 * Get default settings.
	 *
	 * Returns the default settings for this control,
	 * merged with parent default settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return array_merge(
			parent::get_default_settings(),
			array(
				'action'              => self::ACTION_GET,
				'action_autocomplete' => self::ACTION_AUTOCOMPLETE,
				'autocomplete'        => true,
				'options'             => self::get_post_types(),
				'select2options'      => array(
					'allowClear'    => false,
					'closeOnSelect' => true,
				),
				'multiple'            => false,
				'sortable'            => false,
				'label_block'         => true,
			)
		);
	}

	/**
	 * Handles AJAX request to get post types for the control.
	 *
	 * Performs security check and returns available post types.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param array $data The request data.
	 * @return array|WP_Error List of post types or WP_Error on access denied.
	 */
	public static function ajax_action_get( $data ) {
		if ( ! current_user_can( Editor::EDITING_CAPABILITY ) ) {
			return new \WP_Error( 'access_denied', esc_html__( 'Access denied.', 'arts-query-control-for-elementor' ) );
		}

		return self::get_post_types();
	}

	/**
	 * Filter the post types for the autocomplete control.
	 *
	 * Handles autocomplete AJAX requests, returning post types matching the search term
	 * in a format compatible with Select2.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param array $data The request data.
	 * @return array|WP_Error The filtered post types for Select2 or WP_Error on failure.
	 */
	public static function ajax_action_autocomplete( $data ) {
		if ( ! current_user_can( Editor::EDITING_CAPABILITY ) ) {
			return new \WP_Error( 'access_denied', esc_html__( 'Access denied.', 'arts-query-control-for-elementor' ) );
		}

		$search = isset( $data['search'] ) ? sanitize_text_field( $data['search'] ) : '';

		$all_post_types = self::get_post_types();
		$results        = array();

		foreach ( $all_post_types as $slug => $name ) {
			// Check if search term matches slug or name (case-insensitive)
			if ( empty( $search ) || false !== stripos( $name, $search ) || false !== stripos( $slug, $search ) ) {
				$results[] = array(
					'id'   => $slug,
					'text' => $name,
				);
			}
		}

		return array(
			'results' => $results,
		);
	}

	/**
	 * Format post for display.
	 *
	 * Creates a human-readable post title, including parent hierarchy for hierarchical post types.
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 *
	 * @param \WP_Post $post The post object.
	 * @return string The formatted post title.
	 */
	private static function get_formatted_post_to_display( $post ) {
		$post_type_obj = get_post_type_object( $post->post_type );

		$text = ( $post_type_obj->hierarchical ) ? self::get_post_name_with_parents( $post ) : $post->post_title;

		return esc_html( $text );
	}

	/**
	 * Retrieves an array of post types.
	 *
	 * Fetches all the post types registered in WordPress based on configured criteria
	 * and returns an associative array where the keys are the post type slugs
	 * and the values are the post type names. Includes filter hooks to customize
	 * the query args, excluded types, and included types.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return array An associative array of post type slugs and names.
	 */
	public static function get_post_types() {
		if ( ! empty( self::$post_types ) ) {
			return self::$post_types;
		}

		$args = apply_filters(
			'arts/query_control/post_types/query_args',
			array(
				'public'   => true,
				'_builtin' => false,
			)
		);

		$exclude_types = apply_filters(
			'arts/query_control/post_types/exclude',
			array(
				'e-landing-page',
				'elementor_library',
				'elementor-hf',
				'e-floating-buttons',
			)
		);

		$include_types = apply_filters(
			'arts/query_control/post_types/include',
			array(
				'page',
				'post',
			)
		);

		$result = array();

		$output   = 'objects'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'

		$post_types = get_post_types( $args, $output, $operator );

		// Check if get_post_types returned a valid result
		if ( is_wp_error( $post_types ) || empty( $post_types ) ) {
			return array();
		}

		// Add available post types
		foreach ( $post_types  as $object_post_type ) {
			if ( ! in_array( $object_post_type->name, $exclude_types ) ) {
				$result[ $object_post_type->name ] = $object_post_type->labels->name;
			}
		}

		// Include additional post types
		if ( ! empty( $include_types ) ) {
			foreach ( $include_types as $post_type ) {
				$object_post_type = get_post_type_object( $post_type );

				if ( $object_post_type ) {
					$result[ $object_post_type->name ] = $object_post_type->labels->name;
				}
			}
		}

		return array_unique( $result );
	}
}
