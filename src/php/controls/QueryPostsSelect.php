<?php

namespace Arts\QueryControl\Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Core\Editor\Editor;
use Arts\QueryControl\Base\QueryControl;

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
	 * @return array<string, mixed> Control default settings.
	 */
	protected function get_default_settings(): array {
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
	 * @param array<string, mixed> $data The request data.
	 * @return array<int|string, mixed>|\WP_Error The formatted posts data or WP_Error if access denied.
	 */
	public static function ajax_action_get( array $data ): array|\WP_Error {
		if ( ! current_user_can( Editor::EDITING_CAPABILITY ) ) {
			return new \WP_Error( 'access_denied', esc_html__( 'Access denied.', 'arts-query-control-for-elementor' ) );
		}

		$query_data = self::get_titles_query_data( $data );

		if ( is_wp_error( $query_data ) ) {
			return $query_data;
		}

		if ( ! isset( $query_data['query'] ) || ! is_array( $query_data['query'] ) ) {
			return new \WP_Error( 'invalid_query', esc_html__( 'Invalid query data.', 'arts-query-control-for-elementor' ) );
		}

		$query_args                  = $query_data['query'];
		$query_args['no_found_rows'] = true;

		$result = array();

		$loop = new \WP_Query( $query_args );

		if ( $loop->have_posts() ) {
			foreach ( $loop->posts as $post ) {
				if ( ! $post instanceof \WP_Post ) {
					continue;
				}

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
	 * @param array<string, mixed> $data The request data.
	 * @return array<string, mixed>|\WP_Error The autocomplete results in Select2 format or WP_Error if access denied.
	 */
	public static function ajax_action_autocomplete( array $data ): array|\WP_Error {
		if ( ! current_user_can( Editor::EDITING_CAPABILITY ) ) {
			return new \WP_Error( 'access_denied', esc_html__( 'Access denied.', 'arts-query-control-for-elementor' ) );
		}

		$query_data = self::autocomplete_query_data( $data );

		if ( is_wp_error( $query_data ) ) {
			return $query_data;
		}

		if ( ! isset( $query_data['query'] ) || ! is_array( $query_data['query'] ) ) {
			return new \WP_Error( 'invalid_query', esc_html__( 'Invalid query data.', 'arts-query-control-for-elementor' ) );
		}

		$query_args                  = $query_data['query'];
		$query_args['no_found_rows'] = true;

		$post_type     = isset( $query_args['post_type'] ) && is_string( $query_args['post_type'] ) ? $query_args['post_type'] : 'post';
		$post_type_obj = get_post_type_object( $post_type );

		if ( ! $post_type_obj ) {
			return new \WP_Error( 'invalid_post_type', esc_html__( 'Invalid post type.', 'arts-query-control-for-elementor' ) );
		}

		$results = array(
			'text'     => $post_type_obj->labels->name,
			'children' => array(),
		);

		$loop = new \WP_Query( $query_args );

		if ( $loop->have_posts() ) {
			foreach ( $loop->posts as $post ) {
				if ( ! $post instanceof \WP_Post ) {
					continue;
				}

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
	 * @param array<string, mixed> $data The request data.
	 * @return array<string, mixed>|\WP_Error The query data or error object if invalid.
	 * @phpstan-return array<string, mixed>|\WP_Error
	 */
	private static function get_titles_query_data( array $data ): array|\WP_Error {
		if ( ! isset( $data['get_titles'] ) || empty( $data['get_titles'] ) ) {
			return new \WP_Error( 'ArtsQueryControlGetTitles', esc_html__( 'Empty or incomplete data', 'arts-query-control-for-elementor' ) );
		}

		$get_titles = $data['get_titles'];

		if ( ! is_array( $get_titles ) ) {
			return new \WP_Error( 'ArtsQueryControlGetTitles', esc_html__( 'Empty or incomplete data', 'arts-query-control-for-elementor' ) );
		}

		if ( empty( $get_titles['query'] ) ) {
			$get_titles['query'] = array();
		}

		$query = self::get_titles_query_for_post( $data );

		$get_titles['query'] = $query;

		/** @var array<string, mixed> */
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
	private static function get_formatted_post_to_display( \WP_Post $post ): string {
		$post_type_obj = get_post_type_object( $post->post_type );

		if ( ! $post_type_obj ) {
			return esc_html( $post->post_title );
		}

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
	 * @param array<string, mixed> $data The request data.
	 * @return array<string, mixed> The prepared query arguments.
	 * @phpstan-return array<string, mixed>
	 */
	private static function get_titles_query_for_post( array $data ): array {
		$get_titles = isset( $data['get_titles'] ) && is_array( $data['get_titles'] ) ? $data['get_titles'] : array();
		$query      = isset( $get_titles['query'] ) && is_array( $get_titles['query'] ) ? $get_titles['query'] : array();

		if ( empty( $query['post_type'] ) ) {
			$query['post_type'] = 'any';
		}

		$query['posts_per_page'] = -1;

		/** @var array<string, mixed> */
		return $query;
	}
}
