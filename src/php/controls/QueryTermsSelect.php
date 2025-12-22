<?php

namespace Arts\QueryControl\Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Core\Editor\Editor;
use Arts\QueryControl\Base\QueryControl;

/**
 * QueryTermsSelect Class
 *
 * Elementor control that allows selecting taxonomy terms dynamically.
 * Supports autocomplete functionality and multiple selection.
 *
 * @since 1.0.0
 */
class QueryTermsSelect extends QueryControl {
	/**
	 * Control type.
	 *
	 * @since 1.0.0
	 */
	public const TYPE = 'arts-query-control-for-elementor-terms-select';

	/**
	 * AJAX action for retrieving terms.
	 *
	 * @since 1.0.0
	 */
	public const ACTION_GET = 'arts_query_control_for_elementor_get_terms';

	/**
	 * AJAX action for autocomplete functionality.
	 *
	 * @since 1.0.0
	 */
	public const ACTION_AUTOCOMPLETE = 'arts_query_control_for_elementor_get_terms_autocomplete';

	/**
	 * Get default settings for the control.
	 *
	 * Returns an array of default settings, which gets combined
	 * with parent default settings.
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
	 * AJAX handler for retrieving terms.
	 *
	 * Handles the AJAX request to fetch taxonomy terms based on the post type.
	 * Returns an array of term IDs and names.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param array<string, mixed> $data Request data.
	 * @return array<int|string, mixed>|\WP_Error Array of terms or WP_Error if request is invalid.
	 */
	public static function ajax_action_get( array $data ): array|\WP_Error {
		if ( ! current_user_can( Editor::EDITING_CAPABILITY ) ) {
			return new \WP_Error( 'access_denied', esc_html__( 'Access denied.', 'arts-query-control-for-elementor' ) );
		}

		if ( ! isset( $data['get_titles'] ) || empty( $data['get_titles'] ) ) {
			return new \WP_Error( 'ArtsQueryControlGetTitles', esc_html__( 'Empty or incomplete data', 'arts-query-control-for-elementor' ) );
		}

		if ( ! is_array( $data['get_titles'] ) || ! isset( $data['get_titles']['query'] ) ) {
			return new \WP_Error( 'ArtsQueryControlGetTitles', esc_html__( 'Empty or incomplete data', 'arts-query-control-for-elementor' ) );
		}

		$query = $data['get_titles']['query'];

		if ( ! is_array( $query ) ) {
			$query = array();
		}

		if ( empty( $query['post_type'] ) ) {
			$query['post_type'] = 'any';
		}

		$post_type_value = isset( $query['post_type'] ) ? $query['post_type'] : 'any';
		$post_type       = is_string( $post_type_value ) ? $post_type_value : 'any';
		$taxonomies      = get_object_taxonomies( $post_type, 'objects' );

		if ( ! is_array( $taxonomies ) ) {
			return array();
		}

		$terms = array();

		foreach ( $taxonomies as $taxonomy ) {
			// retrieve all available terms, including those not yet used
			$taxonomy_terms = get_terms(
				array(
					'taxonomy'   => $taxonomy->name,
					'hide_empty' => false,
				)
			);

			if ( is_wp_error( $taxonomy_terms ) || ! is_array( $taxonomy_terms ) ) {
				continue;
			}

			foreach ( $taxonomy_terms as $term ) {
				$terms[ $term->term_id ] = esc_html( $term->name );
			}
		}

		return $terms;
	}

	/**
	 * AJAX handler for terms autocomplete.
	 *
	 * Handles the AJAX request for the autocomplete feature.
	 * Returns terms organized by taxonomy in a format compatible with Select2.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param array<string, mixed> $data Request data.
	 * @return array<string, mixed>|\WP_Error Array of terms in Select2 format or WP_Error if request is invalid.
	 */
	public static function ajax_action_autocomplete( array $data ): array|\WP_Error {
		if ( ! current_user_can( Editor::EDITING_CAPABILITY ) ) {
			return new \WP_Error( 'access_denied', esc_html__( 'Access denied.', 'arts-query-control-for-elementor' ) );
		}

		$results    = array();
		$query_data = self::autocomplete_query_data( $data );

		if ( is_wp_error( $query_data ) ) {
			return $query_data;
		}

		if ( ! isset( $query_data['query'] ) || ! is_array( $query_data['query'] ) ) {
			return new \WP_Error( 'invalid_query', esc_html__( 'Invalid query data.', 'arts-query-control-for-elementor' ) );
		}

		$include_taxonomies = isset( $query_data['query']['include'] ) && is_array( $query_data['query']['include'] ) ? $query_data['query']['include'] : array();
		$include_taxonomies = array_map(
			static function ( $item ): string {
				return sanitize_text_field( is_scalar( $item ) ? (string) $item : '' );
			},
			$include_taxonomies
		);
		$exclude_taxonomies = isset( $query_data['query']['exclude'] ) && is_array( $query_data['query']['exclude'] ) ? $query_data['query']['exclude'] : array();
		$exclude_taxonomies = array_map(
			static function ( $item ): string {
				return sanitize_text_field( is_scalar( $item ) ? (string) $item : '' );
			},
			$exclude_taxonomies
		);

		$post_type_value = isset( $query_data['query']['post_type'] ) ? $query_data['query']['post_type'] : 'any';
		$post_type       = is_string( $post_type_value ) ? $post_type_value : 'any';
		$taxonomies      = get_object_taxonomies( $post_type, 'objects' );

		if ( ! is_array( $taxonomies ) ) {
			return array( 'results' => array() );
		}

		$terms = array();

		// Loop over your taxonomies
		foreach ( $taxonomies as $taxonomy ) {
			// Skip this taxonomy if it's in the exclude list
			if ( in_array( $taxonomy->name, $exclude_taxonomies, true ) ) {
				continue;
			}

			// Skip this taxonomy if include list is not empty and taxonomy is not in it
			if ( ! empty( $include_taxonomies ) && ! in_array( $taxonomy->name, $include_taxonomies, true ) ) {
				continue;
			}

			// Retrieve all available terms, including those not yet used
			$taxonomy_terms = get_terms(
				array(
					'taxonomy'   => $taxonomy->name,
					'hide_empty' => false,
				)
			);

			if ( is_wp_error( $taxonomy_terms ) || ! is_array( $taxonomy_terms ) ) {
				continue;
			}

			$arr = array(
				'text'     => $taxonomy->label,
				'children' => array(),
			);

			$search = isset( $data['search'] ) && is_string( $data['search'] ) ? $data['search'] : '';

			foreach ( $taxonomy_terms as $term ) {
				if ( ! empty( $search ) ) {
					if ( strpos( $term->name, $search ) !== false ) {
						$arr['children'][] = array(
							'id'   => $term->term_id,
							'text' => $term->name,
						);
					}
				} else {
					$arr['children'][] = array(
						'id'   => $term->term_id,
						'text' => $term->name,
					);
				}
			}

			array_push( $terms, $arr );
		}

		$results = $terms;

		return array(
			'results' => $results,
		);
	}
}
