<?php

namespace Arts\QueryControl\Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use \Elementor\Core\Editor\Editor;
use \Arts\QueryControl\Base\QueryControl;

/**
 * QueryPostsSelect Class
 *
 * Elementor control for selecting posts with advanced query capabilities.
 * Provides both standard selection and autocomplete functionality.
 *
 * @since 1.0.0
 */
class QueryPostsSelect extends QueryControl {
	/**
	 * Control type identifier.
	 *
	 * @since 1.0.0
	 */
	public const TYPE = 'arts-query-control-for-elementor-posts-select';

	/**
	 * AJAX action for retrieving posts.
	 *
	 * @since 1.0.0
	 */
	public const ACTION_GET = 'arts_query_control_for_elementor_get_posts';

	/**
	 * AJAX action for autocomplete functionality.
	 *
	 * @since 1.0.0
	 */
	public const ACTION_AUTOCOMPLETE = 'arts_query_control_for_elementor_get_posts_autocomplete';

	/**
	 * Get default settings.
	 *
	 * Returns an array of default settings for this control,
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
				'multiple'            => false,
				'sortable'            => false,
				'label_block'         => true,
			)
		);
	}

	/**
	 * Handles AJAX request to get posts for the control.
	 *
	 * Performs security check and retrieves posts based on the query data.
	 * Returns an array of post IDs and formatted titles.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param array $data The request data.
	 * @return array|WP_Error The formatted posts data or WP_Error if access denied.
	 */
	public static function ajax_action_get( $data ) {
		if ( ! current_user_can( Editor::EDITING_CAPABILITY ) ) {
			return new \WP_Error( 'access_denied', esc_html__( 'Access denied.', 'arts-query-control-for-elementor' ) );
		}

		$query_data = self::get_titles_query_data( $data );

		if ( is_wp_error( $query_data ) ) {
			return $query_data;
		}

		$query_args                  = $query_data['query'];
		$query_args['no_found_rows'] = true;

		$result = array();

		$loop = new \WP_Query( $query_args );

		if ( $loop->have_posts() ) {
			foreach ( $loop->posts as $post ) {
				$result[ $post->ID ] = self::get_formatted_post_to_display( $post );
			}

			wp_reset_postdata();
		}

		return $result;
	}

	/**
	 * Handles AJAX request for post autocomplete.
	 *
	 * Processes autocomplete queries and returns posts in a format
	 * compatible with Select2, grouped by post type.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param array $data The request data.
	 * @return array|WP_Error The autocomplete results in Select2 format or WP_Error if access denied.
	 */
	public static function ajax_action_autocomplete( $data ) {
		if ( ! current_user_can( Editor::EDITING_CAPABILITY ) ) {
			return new \WP_Error( 'access_denied', esc_html__( 'Access denied.', 'arts-query-control-for-elementor' ) );
		}

		$query_data = self::autocomplete_query_data( $data );

		if ( is_wp_error( $query_data ) ) {
			return $query_data;
		}

		$query_args                  = $query_data['query'];
		$query_args['no_found_rows'] = true;
		$post_type_obj               = get_post_type_object( $query_args['post_type'] );
		$results                     = array(
			'text'     => $post_type_obj->labels->name,
			'children' => array(),
		);

		$loop = new \WP_Query( $query_args );

		if ( $loop->have_posts() ) {
			foreach ( $loop->posts as $post ) {
				$text = self::get_formatted_post_to_display( $post );

				$results['children'][] = array(
					'id'   => $post->ID,
					'text' => $text,
				);
			}

			wp_reset_postdata();
		}

		return array(
			'results' => array( $results ),
		);
	}

	/**
	 * Retrieve the query data for post titles.
	 *
	 * Processes and validates the data for a titles query.
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 *
	 * @param array $data The request data.
	 * @return array|WP_Error The query data or error object if invalid.
	 */
	private static function get_titles_query_data( $data ) {
		if ( ! isset( $data['get_titles'] ) || empty( $data['get_titles'] ) ) {
			return new \WP_Error( 'ArtsQueryControlGetTitles', esc_html__( 'Empty or incomplete data', 'arts-query-control-for-elementor' ) );
		}

		$get_titles = $data['get_titles'];

		if ( empty( $get_titles['query'] ) ) {
			$get_titles['query'] = array();
		}

		$query = self::get_titles_query_for_post( $data );

		if ( is_wp_error( $query ) ) {
			return $query;
		}

		$get_titles['query'] = $query;

		return $get_titles;
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
	 * Retrieve the query for post titles.
	 *
	 * Prepares a WP_Query compatible array for fetching posts.
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 *
	 * @param array $data The request data.
	 * @return array The prepared query arguments.
	 */
	private static function get_titles_query_for_post( $data ) {
		$query = $data['get_titles']['query'];

		if ( empty( $query['post_type'] ) ) {
			$query['post_type'] = 'any';
		}

		$query['posts_per_page'] = -1;

		return $query;
	}
}
