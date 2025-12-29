<?php

namespace Arts\QueryControl\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Control_Select2;
use Elementor\Core\Editor\Editor;
use Elementor\Core\Common\Modules\Ajax\Module as AJAXManager;

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
	 * @var array<class-string, static>
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
	 * @return static The instance of this class.
	 */
	public static function instance(): static {
		$cls = static::class;

		if ( ! isset( self::$instances[ $cls ] ) ) {
			// PHPStan cannot verify static instantiation returns correct type at analysis time
			$instance                = new static(); // @phpstan-ignore new.static
			self::$instances[ $cls ] = $instance;
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
	public function get_type(): string {
		$type = static::TYPE;

		return is_string( $type ) ? $type : '';
	}

	/**
	 * Get the AJAX action for retrieving data.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string AJAX action name.
	 */
	public function get_action(): string {
		$action = static::ACTION_GET;

		return is_string( $action ) ? $action : '';
	}

	/**
	 * Get the AJAX action for autocomplete functionality.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string AJAX action name for autocomplete.
	 */
	public function get_action_autocomplete(): string {
		$action = static::ACTION_AUTOCOMPLETE;

		return is_string( $action ) ? $action : '';
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
	public function register_ajax_action( AJAXManager $ajax_manager ): void {
		$action_get          = static::ACTION_GET;
		$action_autocomplete = static::ACTION_AUTOCOMPLETE;

		if ( $action_get && is_string( $action_get ) ) {
			$ajax_manager->register_ajax_action( $action_get, array( static::class, 'ajax_action_get' ) );
		}

		if ( $action_autocomplete && is_string( $action_autocomplete ) ) {
			$ajax_manager->register_ajax_action( $action_autocomplete, array( static::class, 'ajax_action_autocomplete' ) );
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
	 * @param array<string, mixed> $data Request data.
	 * @return array<int|string, mixed>|\WP_Error Response data or WP_Error on failure.
	 */
	public static function ajax_action_get( array $data ): array|\WP_Error {
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
	 * @param array<string, mixed> $data Request data.
	 * @return array<string, mixed>|\WP_Error Response data in the format expected by Select2 or WP_Error on failure.
	 */
	public static function ajax_action_autocomplete( array $data ): array|\WP_Error {
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
	 * @param array<string, mixed> $data The request data.
	 * @return array<string, mixed> The processed query data.
	 * @phpstan-return array<string, mixed>
	 */
	protected static function autocomplete_query_data( array $data ): array {
		$autocomplete = isset( $data['autocomplete'] ) && is_array( $data['autocomplete'] ) && ! empty( $data['autocomplete'] ) ? $data['autocomplete'] : array( 'query' => array() );
		$query        = isset( $autocomplete['query'] ) && is_array( $autocomplete['query'] ) ? $autocomplete['query'] : array();

		if ( empty( $query['post_type'] ) ) {
			$query['post_type'] = 'any';
		}

		$query['posts_per_page'] = -1;
		$query['s']              = isset( $data['search'] ) ? $data['search'] : '';

		$autocomplete['query'] = $query;

		/** @var array<string, mixed> */
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
	protected static function get_post_name_with_parents( \WP_Post $post, int $max = 3 ): string {
		if ( $post->post_parent === 0 ) {
			return $post->post_title;
		}

		$separator = is_rtl() ? ' < ' : ' > ';
		$test_post = $post;
		$names     = array();

		while ( $test_post->post_parent > 0 ) {
			$test_post = get_post( $test_post->post_parent );

			if ( ! $test_post instanceof \WP_Post ) {
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
	 * @return array<string, mixed> Control default settings.
	 * @phpstan-return array<string, mixed>
	 */
	protected function get_default_settings(): array {
		/** @var array<string, mixed> */
		return array_merge(
			parent::get_default_settings(),
			array(
				'multiple' => false,
				'sortable' => false,
			)
		);
	}
}
