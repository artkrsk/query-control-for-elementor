<?php

namespace Arts\QueryControl;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \Arts\ElementorExtension\Plugins\BasePlugin;
use \Arts\Utilities\Utilities;

/**
 * Main plugin class
 *
 * @since 1.0.0
 */
class Plugin extends BasePlugin {
	/**
	 * Get default configuration for the plugin.
	 *
	 * Overrides the parent method to provide specific configuration for this plugin.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return array Empty array as default configuration.
	 */
	protected function get_default_config() {
		return array();
	}

	/**
	 * Get default strings for the plugin.
	 *
	 * Overrides the parent method to provide specific strings for this plugin.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return array Empty array as default strings.
	 */
	protected function get_default_strings() {
		return array();
	}

	/**
	 * Get default WordPress action to run the plugin.
	 *
	 * Defines at which WordPress action hook the plugin should be initialized.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return string WordPress action name.
	 */
	protected function get_default_run_action() {
		return 'init';
	}

	/**
	 * Get manager classes for the plugin.
	 *
	 * Defines which manager classes should be instantiated
	 * and made available through the $this->managers object.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return array Associative array of manager keys and their corresponding class names.
	 */
	protected function get_managers_classes() {
		return array(
			'controls'      => Managers\Controls::class,
			'compatibility' => Managers\Compatibility::class,
		);
	}

	/**
	 * Register WordPress actions for the plugin.
	 *
	 * Hooks into WordPress and Elementor actions to register controls,
	 * AJAX actions, scripts, and styles.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return Plugin Current plugin instance.
	 */
	protected function add_actions() {
		// Register query controls
		add_action( 'elementor/controls/register', array( $this->managers->controls, 'register_controls' ) );

		// Register AJAX actions for the query controls
		add_action( 'elementor/ajax/register_actions', array( $this->managers->controls, 'register_ajax_actions' ) );

		// Add necessary scripts to Elementor editor
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this->managers->compatibility, 'elementor_enqueue_editor_scripts' ) );

		// Add custom styles to Elementor editor
		add_action( 'elementor/editor/after_enqueue_styles', array( $this->managers->compatibility, 'elementor_enqueue_editor_styles' ) );

		return $this;
	}

	/**
	 * Retrieves an array of queried posts based on the provided settings.
	 *
	 * Executes a `WP_Query` based on the provided settings and data control prefix,
	 * and returns an array of posts with various details such as ID, title, link, date, featured image,
	 * taxonomies, and ACF fields.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array  $settings            The Elementor settings array containing query parameters.
	 * @param string $data_control_prefix The prefix for the data control in the settings array.
	 *
	 * @return array An array of queried posts with detailed information.
	 */
	public static function get_queried_posts( $settings, $data_control_prefix = '' ) {
		$post_type = self::get_setting( $settings, $data_control_prefix . 'post_type' );
		$posts     = array();

		if ( ! $post_type ) {
			return $posts;
		}

		$counter = 0;

		$query_args = self::get_posts_query_args( $settings, $data_control_prefix );

		// Execute the query with the modified arguments
		$loop = new \WP_Query( $query_args );

		if ( $loop->have_posts() ) {
			$taxonomies = get_object_taxonomies( $query_args['post_type'], 'objects' );

			while ( $loop->have_posts() ) {
				$loop->the_post();

				$post_id = get_the_ID();

				// Standard WP fields
				$posts[ $counter ]['id']    = $posts[ $counter ]['ID'] = $post_id;
				$posts[ $counter ]['title'] = get_the_title();
				$posts[ $counter ]['link']  = array(
					'url'         => get_the_permalink(),
					'is_external' => false,
					'nofollow'    => false,
				);
				$posts[ $counter ]['date']  = get_the_date( '', $post_id );

				// Featured image
				$posts[ $counter ]['image'] = array(
					'id'  => get_post_thumbnail_id(),
					'url' => get_the_post_thumbnail_url(),
				);

				// Post taxonomies and terms
				$posts[ $counter ]['taxonomies'] = Utilities::get_post_terms( $taxonomies, $post_id );

				// ACF registered fields
				$posts[ $counter ]['acf_fields'] = Utilities::acf_get_post_fields( $post_id );

				$counter++;
			}

			wp_reset_postdata();
		}

		return $posts;
	}

	/**
	 * Retrieves the query arguments for fetching posts.
	 *
	 * Builds WP_Query arguments based on widget settings, handling different query modes
	 * (include/exclude) and pagination settings.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array  $settings            The Elementor settings array containing query parameters.
	 * @param string $data_control_prefix The prefix for the data control in the settings array.
	 *
	 * @return array The query arguments for fetching posts.
	 */
	public static function get_posts_query_args( $settings = array(), $data_control_prefix = '' ) {
		$is_archive = Utilities::is_archive();

		$post_type     = self::get_setting( $settings, $data_control_prefix . 'post_type', '' );
		$posts_amount  = self::get_setting( $settings, $data_control_prefix . 'posts_amount', array() );
		$posts_query   = self::get_setting( $settings, $data_control_prefix . 'posts_query', '' );
		$include_terms = self::get_setting( $settings, $data_control_prefix . 'include_terms', array() );
		$exclude_terms = self::get_setting( $settings, $data_control_prefix . 'exclude_terms', array() );
		$include_ids   = self::get_setting( $settings, $data_control_prefix . 'include_ids', array() );
		$exclude_ids   = self::get_setting( $settings, $data_control_prefix . 'exclude_ids', array() );
		$order_by      = self::get_setting( $settings, $data_control_prefix . 'order_by', '' );
		$order         = self::get_setting( $settings, $data_control_prefix . 'order', '' );

		$query_args = array(
			'post_type'     => $post_type,
			'no_found_rows' => true,
			'lang'          => '',
			'orderby'       => $order_by,
			'order'         => $order,
		);

		if ( $is_archive ) {
			$sync_global_query_vars = array(
				'paged',
				'cat',
				's',
			);

			foreach ( $sync_global_query_vars as $var ) {
				$value = get_query_var( $var );

				if ( $value ) {
					$query_args[ $var ] = $value;
				}
			}
		} else {
			$query_args['posts_per_page'] = -1;
		}

		if ( ! $is_archive && $posts_query === 'all' && $posts_amount && $posts_amount['size'] > 0 ) {
			$query_args['posts_per_page'] = $posts_amount['size'];
		}

		if ( $posts_query === 'include' ) {
			// Include posts by chosen taxonomy terms
			if ( is_array( $include_terms ) && ! empty( $include_terms ) ) {
				 $query_args['tax_query'] = Utilities::get_tax_query( $include_terms, 'IN' );
			}

			// Include posts IDs
			if ( is_array( $include_ids ) && ! empty( $include_ids ) ) {
				$query_args['post__in'] = $include_ids;
				$query_args['orderby']  = 'post__in';
			}
		}

		if ( $posts_query === 'exclude' ) {
			// Exclude posts by chosen taxonomy terms
			if ( is_array( $exclude_terms ) && ! empty( $exclude_terms ) ) {
				$query_args['tax_query'] = Utilities::get_tax_query( $exclude_terms, 'NOT IN' );
			}

			// Exclude posts IDs
			if ( is_array( $exclude_ids ) && ! empty( $exclude_ids ) ) {
				$query_args['post__not_in'] = $exclude_ids;
			}
		}

		/**
		 * Filter the query arguments for fetching posts.
		 *
		 * This filter allows modification of the query arguments used to fetch posts.
		 * It affects all theme widgets that use this control.
		 *
		 * @since 1.0.0
		 *
		 * @param array $query_args The query arguments for fetching posts.
		 */
		$query_args = apply_filters( 'arts/query_control_for_elementor/query_args', $query_args );

		return $query_args;
	}

	/**
	 * Retrieves a setting value with a default.
	 *
	 * Helper method to safely access settings array values with fallback defaults.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param array  $settings The settings array to retrieve from.
	 * @param string $key      The key to retrieve from the settings.
	 * @param mixed  $default  The default value if the key is not set.
	 *
	 * @return mixed The setting value or the default.
	 */
	private static function get_setting( $settings, $key, $default = null ) {
		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
	}
}
