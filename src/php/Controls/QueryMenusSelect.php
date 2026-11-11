<?php

namespace Arts\QueryControl\Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Core\Editor\Editor;
use Arts\QueryControl\Base\QueryControl;

/**
 * QueryMenusSelect Control Class
 *
 * Provides a specialized Elementor control for selecting WordPress navigation menus.
 * Allows for selection of registered menus within the Elementor interface.
 *
 * @since 1.0.0
 */
class QueryMenusSelect extends QueryControl {
	/**
	 * Control type identifier.
	 *
	 * @since 1.0.0
	 */
	public const TYPE = 'arts-query-control-for-elementor-menus-select';

	/**
	 * AJAX action for retrieving menus.
	 *
	 * @since 1.0.0
	 */
	public const ACTION_GET = 'arts_query_control_for_elementor_get_menus';

	/**
	 * AJAX action for autocomplete functionality.
	 *
	 * @since 1.0.0
	 */
	public const ACTION_AUTOCOMPLETE = 'arts_query_control_for_elementor_get_menus_autocomplete';

	/**
	 * Get default settings.
	 *
	 * Returns the default settings for this control,
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
				'action_autocomplete' => self::ACTION_AUTOCOMPLETE, // Add autocomplete action
				'options'             => self::get_menus(),
				'autocomplete'        => true,
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
	 * Handles AJAX request to get menus for the control.
	 *
	 * Performs security check and returns available navigation menus.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param array<string, mixed> $data The request data.
	 * @return array<string, string>|\WP_Error List of navigation menus or WP_Error if access denied.
	 */
	public static function ajax_action_get( array $data ): array|\WP_Error {
		if ( ! current_user_can( Editor::EDITING_CAPABILITY ) ) {
			return new \WP_Error( 'access_denied', esc_html__( 'Access denied.', 'arts-query-control-for-elementor' ) );
		}

		return self::get_menus();
	}

	/**
	 * AJAX handler for menus autocomplete.
	 *
	 * Handles the AJAX request for the autocomplete feature.
	 * Returns menus matching the search term in a format compatible with Select2.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param array<string, mixed> $data Request data.
	 * @return array<string, mixed>|\WP_Error Array of menus in Select2 format or WP_Error if request is invalid.
	 */
	public static function ajax_action_autocomplete( array $data ): array|\WP_Error {
		if ( ! current_user_can( Editor::EDITING_CAPABILITY ) ) {
			return new \WP_Error( 'access_denied', esc_html__( 'Access denied.', 'arts-query-control-for-elementor' ) );
		}

		$search = isset( $data['search'] ) && is_string( $data['search'] ) ? sanitize_text_field( $data['search'] ) : '';

		$menus = wp_get_nav_menus();

		$results = array();

		foreach ( $menus as $menu ) {
			if ( empty( $search ) || false !== stripos( $menu->name, $search ) ) {
				$results[] = array(
					'id'   => $menu->slug,
					'text' => $menu->name,
				);
			}
		}

		return array(
			'results' => $results,
		);
	}

	/**
	 * Retrieves an array of navigation menus.
	 *
	 * Fetches all the navigation menus registered in WordPress
	 * and returns an associative array where the keys are the menu slugs
	 * and the values are the menu names.
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 * @return array<string, string> An associative array of menu slugs and names.
	 */
	private static function get_menus(): array {
		$menus = wp_get_nav_menus();

		$result = array();

		foreach ( $menus as $menu ) {
			$result[ $menu->slug ] = $menu->name;
		}

		return $result;
	}
}
