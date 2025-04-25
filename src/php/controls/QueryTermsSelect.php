<?php

namespace Arts\QueryControl\Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use \Elementor\Core\Editor\Editor;
use \Arts\QueryControl\Base\QueryControl;

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
	 * AJAX handler for retrieving terms.
	 *
	 * Handles the AJAX request to fetch taxonomy terms based on the post type.
	 * Returns an array of term IDs and names.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param array $data Request data.
	 * @return array|WP_Error Array of terms or WP_Error if request is invalid.
	 */
	public static function ajax_action_get( $data ) {
		if ( ! current_user_can( Editor::EDITING_CAPABILITY ) ) {
			return new \WP_Error( 'access_denied', esc_html__( 'Access denied.', 'arts-query-control-for-elementor' ) );
		}

		if ( ! isset( $data['get_titles'] ) || empty( $data['get_titles'] ) ) {
			return new \WP_Error( 'ArtsQueryControlGetTitles', esc_html__( 'Empty or incomplete data', 'arts-query-control-for-elementor' ) );
		}

		$query = $data['get_titles']['query'];

		if ( empty( $query['post_type'] ) ) {
			$query['post_type'] = 'any';
		}

		$taxonomies = get_object_taxonomies( $query['post_type'], 'objects' );

		$terms = array();

		foreach ( $taxonomies as $taxonomy ) {
			// retrieve all available terms, including those not yet used
			$taxonomy_terms = get_terms(
				array(
					'taxonomy'   => $taxonomy->name,
					'hide_empty' => false,
				)
			);

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
	 * @param array $data Request data.
	 * @return array|WP_Error Array of terms in Select2 format or WP_Error if request is invalid.
	 */
	public static function ajax_action_autocomplete( $data ) {
		if ( ! current_user_can( Editor::EDITING_CAPABILITY ) ) {
			return new \WP_Error( 'access_denied', esc_html__( 'Access denied.', 'arts-query-control-for-elementor' ) );
		}

		$results    = array();
		$query_data = self::autocomplete_query_data( $data );

		if ( is_wp_error( $query_data ) ) {
			return $query_data;
		}

		$include_taxonomies = isset( $query_data['query']['include'] ) ? $query_data['query']['include'] : array();
		$include_taxonomies = array_map( 'sanitize_text_field', $include_taxonomies );
		$exclude_taxonomies = isset( $query_data['query']['exclude'] ) ? $query_data['query']['exclude'] : array();
		$exclude_taxonomies = array_map( 'sanitize_text_field', $exclude_taxonomies );
		$taxonomies         = get_object_taxonomies( $query_data['query']['post_type'], 'objects' );
		$terms              = array();

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

			$arr = array(
				'text'     => $taxonomy->label,
				'children' => array(),
			);

			foreach ( $taxonomy_terms as $term ) {
				if ( ! empty( $data['search'] ) ) {
					if ( strpos( $term->name, $data['search'] ) !== false ) {
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
