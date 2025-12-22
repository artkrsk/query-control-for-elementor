<?php

namespace Arts\QueryControl\Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Arts\Utilities\Utilities;
use Arts\QueryControl\Controls\QueryPostsSelect;
use Arts\QueryControl\Controls\QueryPostTypesSelect;
use Arts\QueryControl\Controls\QueryTermsSelect;
use Elementor\Group_Control_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Repeater;

/**
 * QueryGroup Control Class
 *
 * Provides a comprehensive group control for query-related fields in Elementor.
 * Allows switching between dynamic (WordPress posts) and static (custom) content,
 * with various filtering and sorting options for dynamic content.
 *
 * @since 1.0.0
 */
class QueryGroup extends Group_Control_Base {
	/**
	 * The instance of this class.
	 *
	 * Stores the singleton instance of this control.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var static|null
	 */
	protected static $instance;

	/**
	 * The fields of this class.
	 *
	 * Stores the initialized fields for this control.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array<string, mixed>|null
	 */
	protected static $fields;

	/**
	 * Get the instance of this class.
	 *
	 * Implements the singleton pattern to ensure only one instance
	 * of this control exists.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return static The instance of this class.
	 */
	public static function instance(): static {
		if ( is_null( self::$instance ) ) {
			$instance = new self();
			/** @var static $instance */
			self::$instance = $instance;
		}

		return self::$instance;
	}

	/**
	 * Get group control type.
	 *
	 * Returns the unique identifier for this group control.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return string The group control type.
	 */
	public static function get_type(): string {
		return 'arts-query-control-for-elementor-group-control';
	}

	/**
	 * Get child default arguments.
	 *
	 * Retrieves the default arguments for all the child controls for a specific group
	 * control.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @return array<string, mixed> Default arguments for all the child controls.
	 */
	protected function get_child_default_args(): array {
		return array(
			'name'    => 'data',
			'exclude' => array(),
		);
	}

	/**
	 * Init fields.
	 *
	 * Initialize group control fields by creating the base source selector
	 * and merging in both dynamic and static field controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @return array<string, mixed> Group control fields.
	 */
	protected function init_fields(): array {
		$fields = array();

		$fields['source'] = array(
			'label'       => sprintf(
				'<strong>%1$s</strong>',
				esc_html__( 'Data Source', 'arts-query-control-for-elementor' )
			),
			'type'        => Controls_Manager::SELECT,
			'options'     => array(
				'dynamic' => esc_html__( 'Dynamic Posts', 'arts-query-control-for-elementor' ),
				'static'  => esc_html__( 'Custom Content', 'arts-query-control-for-elementor' ),
			),
			'default'     => 'dynamic',
			'label_block' => true,
		);

		$fields = array_merge(
			$fields,
			$this->get_dynamic_fields_controls(),
			$this->get_static_fields_controls()
		);

		return $fields;
	}

	/**
	 * Get default options.
	 *
	 * Retrieve the default options of the group control. Used to return the
	 * default options while initializing the group control.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @return array<string, mixed> Default group control options.
	 */
	protected function get_default_options(): array {
		return array(
			'popover' => false,
		);
	}

	/**
	 * Retrieves the dynamic fields controls.
	 *
	 * Generates an array of dynamic fields controls based on the default set of controls.
	 * Each control is added only if it exists in the dynamic fields set.
	 * Controls include post type selection, query filtering, ordering, and media preferences.
	 *
	 * @since 1.0.0
	 * @access private
	 * @return array<string, mixed> The array of dynamic fields controls.
	 */
	private function get_dynamic_fields_controls(): array {
		$group_name = $this->get_controls_prefix();
		$group_name = apply_filters( 'arts/query_control/group_control/dynamic_fields_controls/group_name', $group_name, $this );

		$is_archive = Utilities::is_archive();
		$is_archive = apply_filters( 'arts/query_control/group_control/dynamic_fields_controls/condition_is_archive', $is_archive, $this );

		$fields_set = $this->get_dynamic_fields_controls_default_set();

		$fields['is_archive'] = array(
			'type'    => Controls_Manager::HIDDEN,
			'default' => $is_archive ? 'yes' : '',
		);

		if ( array_key_exists( 'post_type', $fields_set ) ) {
			$fields['post_type'] = array(
				'label'       => $fields_set['post_type'],
				'type'        => QueryPostTypesSelect::TYPE,
				'condition'   => array(
					'source' => 'dynamic',
				),
				'group'       => $group_name,
				'label_block' => true,
			);
		}

		if ( array_key_exists( 'posts_query', $fields_set ) ) {
			$fields['posts_query'] = array(
				'label'       => $fields_set['posts_query'],
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'all'     => esc_html__( 'All', 'arts-query-control-for-elementor' ),
					'include' => esc_html__( 'Include', 'arts-query-control-for-elementor' ),
					'exclude' => esc_html__( 'Exclude', 'arts-query-control-for-elementor' ),
				),
				'condition'   => array(
					'source' => 'dynamic',
				),
				'group'       => $group_name,
				'default'     => 'all',
				'label_block' => true,
			);
		}

		if ( array_key_exists( 'include_terms', $fields_set ) ) {
			$fields['include_terms'] = array(
				'label'       => $fields_set['include_terms'],
				'type'        => QueryTermsSelect::TYPE,
				'condition'   => array(
					'source'      => 'dynamic',
					'posts_query' => 'include',
				),
				'default'     => array(),
				'group'       => $group_name,
				'label_block' => true,
				'multiple'    => true,
			);
		}

		if ( array_key_exists( 'exclude_terms', $fields_set ) ) {
			$fields['exclude_terms'] = array(
				'label'       => $fields_set['exclude_terms'],
				'type'        => QueryTermsSelect::TYPE,
				'condition'   => array(
					'source'      => 'dynamic',
					'posts_query' => 'exclude',
				),
				'default'     => array(),
				'group'       => $group_name,
				'label_block' => true,
				'multiple'    => true,
			);
		}

		if ( array_key_exists( 'include_ids', $fields_set ) ) {
			$fields['include_ids'] = array(
				'label'       => $fields_set['include_ids'],
				'type'        => QueryPostsSelect::TYPE,
				'condition'   => array(
					'source'      => 'dynamic',
					'posts_query' => 'include',
				),
				'default'     => array(),
				'group'       => $group_name,
				'label_block' => true,
				'multiple'    => true,
				'sortable'    => true,
			);
		}

		if ( array_key_exists( 'exclude_ids', $fields_set ) ) {
			$fields['exclude_ids'] = array(
				'label'       => $fields_set['exclude_ids'],
				'type'        => QueryPostsSelect::TYPE,
				'condition'   => array(
					'source'      => 'dynamic',
					'posts_query' => 'exclude',
				),
				'default'     => array(),
				'group'       => $group_name,
				'label_block' => true,
				'multiple'    => true,
				'sortable'    => true,
			);
		}

		if ( array_key_exists( 'order_by', $fields_set ) ) {
			$fields['order_by'] = array(
				'label'       => $fields_set['order_by'],
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => array(
					''           => esc_html__( 'Auto', 'arts-query-control-for-elementor' ),
					'post_date'  => esc_html__( 'Date', 'arts-query-control-for-elementor' ),
					'post_title' => esc_html__( 'Title', 'arts-query-control-for-elementor' ),
					'rand'       => esc_html__( 'Random', 'arts-query-control-for-elementor' ),
				),
				'group'       => $group_name,
				'condition'   => array(
					'source' => 'dynamic',
				),
				'label_block' => true,
			);
		}

		if ( array_key_exists( 'order_by_notice', $fields_set ) ) {
			$fields['order_by_notice'] = array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => $fields_set['order_by_notice'],
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'group'           => $group_name,
				'conditions'      => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => 'source',
							'operator' => '===',
							'value'    => 'dynamic',
						),
						array(
							'name'     => 'posts_query',
							'operator' => '===',
							'value'    => 'include',
						),
						array(
							'name'     => 'include_ids',
							'operator' => '!==',
							'value'    => array(),
						),
					),
				),
			);
		}

		if ( array_key_exists( 'order', $fields_set ) ) {
			$fields['order'] = array(
				'label'       => $fields_set['order'],
				'type'        => Controls_Manager::SELECT,
				'default'     => 'desc',
				'options'     => array(
					'asc'  => esc_html__( 'Ascending', 'arts-query-control-for-elementor' ),
					'desc' => esc_html__( 'Descending', 'arts-query-control-for-elementor' ),
				),
				'group'       => $group_name,
				'conditions'  => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => 'source',
							'operator' => '===',
							'value'    => 'dynamic',
						),
						array(
							'name'     => 'order_by',
							'operator' => '!in',
							'value'    => array( '', 'rand' ),
						),
					),
				),
				'label_block' => true,
			);
		}

		if ( array_key_exists( 'posts_amount', $fields_set ) ) {
			$fields['posts_amount'] = array(
				'label'     => $fields_set['posts_amount'],
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'number' => array(
						'min'  => 0,
						'max'  => 16,
						'step' => 1,
					),
				),
				'default'   => array(
					'unit' => 'number',
					'size' => 0,
				),
				'group'     => $group_name,
				'condition' => array(
					'source'      => 'dynamic',
					'posts_query' => 'all',
					'is_archive'  => '',
				),
			);
		}

		if ( array_key_exists( 'prefer_media', $fields_set ) ) {
			$fields['prefer_media'] = array(
				'label'       => $fields_set['prefer_media'],
				'type'        => Controls_Manager::SELECT,
				'default'     => 'default',
				'options'     => array(
					'default'   => esc_html__( 'Prefer Default Featured Image', 'arts-query-control-for-elementor' ),
					'secondary' => esc_html__( 'Prefer Secondary Featured Image', 'arts-query-control-for-elementor' ),
				),
				'group'       => $group_name,
				'condition'   => array(
					'source' => 'dynamic',
				),
				'label_block' => true,
				'separator'   => 'before',
			);
		}

		if ( array_key_exists( 'use_video_enabled', $fields_set ) ) {
			$fields['use_video_enabled'] = array(
				'label'     => $fields_set['use_video_enabled'],
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'group'     => $group_name,
				'condition' => array(
					'source' => 'dynamic',
				),
			);
		}

		return $fields;
	}

	/**
	 * Retrieves the static fields controls.
	 *
	 * Creates a repeater control for custom content items when 'static' source is selected.
	 * The repeater fields are defined in get_static_fields_repeater_controls().
	 *
	 * @since 1.0.0
	 * @access private
	 * @return array<string, mixed> The array of static fields controls.
	 */
	private function get_static_fields_controls(): array {
		$repeater_fields = $this->get_static_fields_repeater_controls();
		$title_field     = array_key_exists( 'title', $repeater_fields ) ? '{{{ title }}}' : '';

		$fields['posts'] = array(
			'type'          => Controls_Manager::REPEATER,
			'fields'        => $repeater_fields,
			'label'         => esc_html__( 'Items', 'arts-query-control-for-elementor' ),
			'title_field'   => $title_field,
			'prevent_empty' => false,
			'condition'     => array(
				'source' => 'static',
			),
		);

		return $fields;
	}

	/**
	 * Retrieves the static fields of the repeater control.
	 *
	 * Initializes an Elementor Repeater and adds various controls to it
	 * based on the static fields set. Each control is added
	 * only if it exists in the static fields set.
	 * Controls include title, category, year, description, link, images and video.
	 *
	 * @since 1.0.0
	 * @access private
	 * @return array<string, mixed> The controls added to the repeater.
	 */
	private function get_static_fields_repeater_controls(): array {
		$repeater = new Repeater();

		$fields_set = $this->get_static_fields_controls_default_set();

		if ( array_key_exists( 'title', $fields_set ) ) {
			$repeater->add_control(
				'title',
				array(
					'label'       => $fields_set['title'],
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( 'Item...', 'arts-query-control-for-elementor' ),
					'label_block' => true,
				)
			);
		}

		if ( array_key_exists( 'category', $fields_set ) ) {
			$repeater->add_control(
				'category',
				array(
					'label'       => $fields_set['category'],
					'type'        => Controls_Manager::TEXT,
					'default'     => '',
					'label_block' => true,
				)
			);
		}

		if ( array_key_exists( 'year', $fields_set ) ) {
			$repeater->add_control(
				'year',
				array(
					'label'       => $fields_set['year'],
					'type'        => Controls_Manager::TEXT,
					'default'     => '',
					'label_block' => true,
				)
			);
		}

		if ( array_key_exists( 'description', $fields_set ) ) {
			$repeater->add_control(
				'description',
				array(
					'label'       => $fields_set['description'],
					'type'        => Controls_Manager::TEXTAREA,
					'default'     => '',
					'label_block' => true,
				)
			);
		}

		if ( array_key_exists( 'link', $fields_set ) ) {
			$repeater->add_control(
				'link',
				array(
					'label'         => $fields_set['link'],
					'type'          => Controls_Manager::URL,
					'placeholder'   => 'https://...',
					'show_external' => false,
					'default'       => array(
						'url'         => '#',
						'is_external' => false,
						'nofollow'    => false,
					),
					'label_block'   => true,
					'dynamic'       => array(
						'active' => true,
					),
				)
			);
		}

		if ( array_key_exists( 'image', $fields_set ) ) {
			$repeater->add_control(
				'image',
				array(
					'label'       => $fields_set['image'],
					'type'        => Controls_Manager::MEDIA,
					'default'     => array(
						'url' => Utils::get_placeholder_image_src(),
					),
					'label_block' => true,
				)
			);
		}

		if ( array_key_exists( 'secondary_image', $fields_set ) ) {
			$repeater->add_control(
				'secondary_image',
				array(
					'label'       => $fields_set['secondary_image'],
					'type'        => Controls_Manager::MEDIA,
					'default'     => array(
						'url' => Utils::get_placeholder_image_src(),
					),
					'label_block' => true,
				)
			);
		}

		if ( array_key_exists( 'video', $fields_set ) ) {
			$repeater->add_control(
				'video',
				array(
					'label'       => $fields_set['video'],
					'type'        => Controls_Manager::MEDIA,
					'media_type'  => 'video',
					'label_block' => true,
					'conditions'  => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'name'     => 'image',
								'operator' => '!==',
								'value'    => array(
									'id'  => '',
									'url' => '',
								),
							),
							array(
								'name'     => 'image',
								'operator' => '!==',
								'value'    => array(
									'id'  => '',
									'url' => Utils::get_placeholder_image_src(),
								),
							),
						),
					),
				)
			);
		}

		/**
		 * Filter for adding additional controls to the static fields repeater.
		 *
		 * Allows third-party developers to add custom controls to the repeater used
		 * for static content items.
		 *
		 * @since 1.0.0
		 *
		 * @param Repeater   $repeater The Elementor repeater instance.
		 * @param QueryGroup $instance The current QueryGroup instance.
		 */
		do_action( 'arts/query_control/group_control/static_fields_controls/repeater', $repeater, $this );

		/** @var array<string, mixed> */
		$controls = $repeater->get_controls();

		return $controls;
	}

	/**
	 * Retrieves the default set of static field controls.
	 *
	 * Defines the standard set of fields available in the static content repeater,
	 * with a filter hook to allow modification.
	 *
	 * @since 1.0.0
	 * @access private
	 * @return array<string, string> The default set of static field controls.
	 * @phpstan-return array<string, string>
	 */
	private function get_static_fields_controls_default_set(): array {
		$fields_set = array(
			'title' => esc_html__( 'Title', 'arts-query-control-for-elementor' ),
			'link'  => esc_html__( 'Link', 'arts-query-control-for-elementor' ),
			'image' => esc_html__( 'Image', 'arts-query-control-for-elementor' ),
		);

		/**
		 * Filter the default set of static field controls.
		 *
		 * Allows modifying the default fields available in the static content repeater.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string, string> $fields_set The default set of static field controls.
		 */
		$fields_set = apply_filters( 'arts/query_control/group_control/static_fields_controls/default_set', $fields_set );

		/** @var array<string, string> */
		return $fields_set;
	}

	/**
	 * Retrieves the default set of dynamic fields controls.
	 *
	 * Defines the standard set of fields available for dynamic content selection,
	 * with a filter hook to allow modification.
	 *
	 * @since 1.0.0
	 * @access private
	 * @return array<string, string> The default set of dynamic fields controls.
	 * @phpstan-return array<string, string>
	 */
	private function get_dynamic_fields_controls_default_set(): array {
		$fields_set = array(
			'post_type'       => esc_html__( 'Post Type', 'arts-query-control-for-elementor' ),
			'posts_query'     => esc_html__( 'Query Posts', 'arts-query-control-for-elementor' ),
			'include_terms'   => sprintf(
				'<strong>%1$s</strong> %2$s',
				esc_html__( 'Include', 'arts-query-control-for-elementor' ),
				esc_html__( 'by Terms', 'arts-query-control-for-elementor' ),
			),
			'exclude_terms'   => sprintf(
				'<strong>%1$s</strong> %2$s',
				esc_html__( 'Exclude', 'arts-query-control-for-elementor' ),
				esc_html__( 'by Terms', 'arts-query-control-for-elementor' ),
			),
			'include_ids'     => sprintf(
				'<strong>%1$s</strong> %2$s',
				esc_html__( 'Include', 'arts-query-control-for-elementor' ),
				esc_html__( 'Posts', 'arts-query-control-for-elementor' ),
			),
			'exclude_ids'     => sprintf(
				'<strong>%1$s</strong> %2$s',
				esc_html__( 'Exclude', 'arts-query-control-for-elementor' ),
				esc_html__( 'Posts', 'arts-query-control-for-elementor' ),
			),
			'order_by'        => esc_html__( 'Order By', 'arts-query-control-for-elementor' ),
			'order_by_notice' => esc_html__( 'Please note that the "Order By" option is ignored once you select individual posts to include. Use drag & drop sorting in the "Include Posts" field instead.', 'arts-query-control-for-elementor' ),
			'order'           => esc_html__( 'Order', 'arts-query-control-for-elementor' ),
			'posts_amount'    => esc_html__( 'Limit Posts for Display (0 for no limit)', 'arts-query-control-for-elementor' ),
		);

		/**
		 * Filter the default set of dynamic field controls.
		 *
		 * Allows modifying the default fields available for dynamic content selection.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string, string> $fields_set The default set of dynamic field controls.
		 */
		$fields_set = apply_filters( 'arts/query_control/group_control/dynamic_fields_controls/default_set', $fields_set );

		/** @var array<string, string> */
		return $fields_set;
	}
}
