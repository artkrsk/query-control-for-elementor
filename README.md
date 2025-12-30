# Arts Query Control for Elementor

![License](https://img.shields.io/badge/license-MIT-blue)
![WordPress](https://img.shields.io/badge/wordpress-6.0%2B-green)
![PHP](https://img.shields.io/badge/php-8.0%2B-purple)
![Elementor](https://img.shields.io/badge/elementor-compatible-red)
[![Buy Me A Coffee](https://img.shields.io/badge/Buy%20Me%20a%20Coffee-ffdd00?style=flat&logo=buy-me-a-coffee&logoColor=black)](https://buymeacoffee.com/artemsemkin)

A development extension providing advanced query controls for Elementor page builder widgets.

## Setup

Install using Composer:

```bash
composer require arts/query-control-for-elementor
```

Initialize the plugin in your project to register all necessary controls:

```php
<?php

// Load composer
require_once __DIR__ . '/vendor/autoload.php';

// No need to use hooks
Arts\QueryControl\Plugin::instance();
```

## Usage

These controls can be used within your Elementor widget's `register_controls` method.

### Standalone Query Controls

These controls allow selecting specific WordPress objects like posts, terms, post types, or menus.

**Example:**

```php
<?php

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Arts\QueryControl\Controls\QueryPostsSelect;
use \Arts\QueryControl\Controls\QueryTermsSelect;
use \Arts\QueryControl\Controls\QueryPostTypesSelect;
use \Arts\QueryControl\Controls\QueryMenusSelect;

class My_Elementor_Widget extends Widget_Base {
  // ... other widget methods ...

  protected function register_controls() {
    $this->start_controls_section(
      'content_section',
      array(
        'label' => esc_html__( 'Content', 'my-text-domain' ),
        'tab'   => Controls_Manager::TAB_CONTENT,
      )
    );

    // Simple Posts Select Control with predefined post type
    $this->add_control(
      'page',
      array(
        'label'          => esc_html__( 'Select a Page', 'trigger' ),
        'type'           => \Arts\QueryControl\Controls\QueryPostsSelect::TYPE,
        'query'          => array(
          'post_type' => array( 'page' ),
        ),
        'multiple'       => false,
        'select2options' => array(
          'allowClear'    => false,
          'closeOnSelect' => true,
        ),
      )
    );

    // Multiple Posts Select Control with predefined post type
    $this->add_control(
      'multiple_posts',
      array(
        'label'          => esc_html__( 'Select Multiple Posts', 'trigger' ),
        'type'           => \Arts\QueryControl\Controls\QueryPostsSelect::TYPE,
        'query'          => array(
          'post_type' => array( 'post' ),
        ),
        'multiple'       => true,
        'select2options' => array(
          'allowClear'    => false,
          'closeOnSelect' => true,
        ),
      )
    );

    // Multiple Sortable Posts Select Control with predefined post type
    $this->add_control(
      'multiple_sortable_posts',
      array(
        'label'    => esc_html__( 'Select & Reorder Portfolio Items', 'trigger' ),
        'type'     => \Arts\QueryControl\Controls\QueryPostsSelect::TYPE,
        'query'    => array(
          'post_type' => array( 'arts_portfolio_item' ),
        ),
        'multiple' => true,
        'sortable' => true,
      )
    );

    // Simple WP Menu Select Control
    $this->add_control(
      'menu',
      array(
        'label'          => esc_html__( 'Select a WP Menu', 'trigger' ),
        'type'           => \Arts\QueryControl\Controls\QueryMenusSelect::TYPE,
        'multiple'       => false,
        'select2options' => array(
          'allowClear'    => true,
          'closeOnSelect' => true,
        ),
      )
    );

    // Post Type Select Control
    $this->add_control(
      'post_type',
      array(
        'label'          => esc_html__( 'Select a Post Type', 'trigger' ),
        'type'           => \Arts\QueryControl\Controls\QueryPostTypesSelect::TYPE,
        'multiple'       => false,
        'default'        => 'post',
        'select2options' => array(
          'allowClear'    => true,
          'closeOnSelect' => true,
        ),
      )
    );

    // Terms Select Control with predefined taxonomy
    $this->add_control(
      'categories',
      array(
        'label'          => esc_html__( 'Select Posts Categories', 'trigger' ),
        'type'           => \Arts\QueryControl\Controls\QueryTermsSelect::TYPE,
        'multiple'       => true,
        'sortable'       => false,
        'query'          => array(
          'post_type' => array( 'post' ),
          'include'   => array( 'category' ),
        ),
        'select2options' => array(
          'allowClear'    => true,
          'closeOnSelect' => false,
        ),
      )
    );

    // Terms Select Control with predefined taxonomy and sortable option
    $this->add_control(
      'tags',
      array(
        'label'          => esc_html__( 'Select & Reoder Posts Tags', 'trigger' ),
        'type'           => \Arts\QueryControl\Controls\QueryTermsSelect::TYPE,
        'multiple'       => true,
        'sortable'       => true,
        'query'          => array(
          'post_type' => array( 'post' ),
          'include'   => array( 'post_tag' ),
        ),
        'select2options' => array(
          'allowClear'    => true,
          'closeOnSelect' => false,
        ),
      )
    );

    $this->end_controls_section();
  }

  // ... other widget methods ...
}

```

### Query Group Control

This group control provides a comprehensive UI for building complex post queries, allowing users to select between dynamic post fetching (based on criteria like post type, terms, specific posts) or defining static content items.

**Example:**

```php
<?php

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Arts\QueryControl\Controls\QueryGroup;

class My_Advanced_Widget extends Widget_Base {

  // ... other widget methods ...

  protected function register_controls() {

    $this->start_controls_section(
      'query_section',
      array(
        'label' => esc_html__( 'Query', 'my-text-domain' ),
        'tab'   => Controls_Manager::TAB_CONTENT,
      )
    );

    $this->add_group_control(
      QueryGroup::get_type(),
      array(
        'fields_options' => array( 'title', 'link', 'image' ),
      )
    );

    $this->end_controls_section();

  }

  // ... other widget methods ...
}

```

## WordPress Hooks

You can customize the behavior and appearance of the controls using the following WordPress filters and actions.

### Filters

- `arts/query_control/group_control/static_fields_controls/default_set`

  - **Description:** Modifies the default fields available in the static content repeater within the Query Group control.
  - **Parameters:** `$fields_set` (array) - The default set of static field controls (e.g., `['title' => 'Title', 'link' => 'Link', 'image' => 'Image']`).
  - **Example:** Add a 'subtitle' field.
    ```php
    add_filter( 'arts/query_control/group_control/static_fields_controls/default_set', function( $fields_set ) {
        $fields_set['subtitle'] = esc_html__( 'Subtitle', 'my-text-domain' );
        return $fields_set;
    } );
    ```

- `arts/query_control/group_control/dynamic_fields_controls/default_set`

  - **Description:** Modifies the default fields available for dynamic content selection within the Query Group control.
  - **Parameters:** `$fields_set` (array) - The default set of dynamic field controls (e.g., `post_type`, `posts_query`, `include_terms`, etc.).
  - **Example:** Remove the 'Order By' field.
    ```php
    add_filter( 'arts/query_control/group_control/dynamic_fields_controls/default_set', function( $fields_set ) {
        unset( $fields_set['order_by'] );
        unset( $fields_set['order_by_notice'] );
        return $fields_set;
    } );
    ```

- `arts/query_control/group_control/dynamic_fields_controls/group_name`

  - **Description:** Filters the group name (prefix) used for dynamic field controls within the Query Group.
  - **Parameters:** `$group_name` (string), `$query_group_instance` (QueryGroup).

- `arts/query_control/group_control/dynamic_fields_controls/condition_is_archive`

  - **Description:** Filters the boolean value indicating if the current context is an archive page, affecting conditional display of some dynamic fields.
  - **Parameters:** `$is_archive` (bool), `$query_group_instance` (QueryGroup).

- `arts/query_control/post_types/query_args`

  - **Description:** Modifies the query arguments used to fetch post types for the `QueryPostTypesSelect` control.
  - **Parameters:** `$args` (array) - Arguments passed to `get_post_types()`. Default: `['public' => true, '_builtin' => false]`.

- `arts/query_control/post_types/exclude`

  - **Description:** Modifies the list of post types explicitly excluded from the `QueryPostTypesSelect` control.
  - **Parameters:** `$exclude_types` (array) - Array of post type slugs to exclude.

- `arts/query_control/post_types/include`
  - **Description:** Modifies the list of post types explicitly included in the `QueryPostTypesSelect` control (these are added in addition to those fetched by `query_args`).
  - **Parameters:** `$include_types` (array) - Array of post type slugs to include.

- `arts/query_control/query_args`
  - **Description:** Modifies the WP_Query arguments used when fetching posts via `Plugin::get_posts_query_args()` and `Plugin::get_queried_posts()`.
  - **Parameters:** `$query_args` (array) - The query arguments array.
  - **Example:** Add custom meta query.
    ```php
    add_filter( 'arts/query_control/query_args', function( $query_args ) {
        $query_args['meta_query'] = array(
            array(
                'key'     => 'featured',
                'value'   => '1',
                'compare' => '='
            )
        );
        return $query_args;
    } );
    ```

### Actions

- `arts/query_control/group_control/static_fields_controls/repeater`
  - **Description:** Allows adding custom controls to the repeater used for static content items within the Query Group control.
  - **Parameters:** `$repeater` (\Elementor\Repeater) - The Elementor repeater instance., `$query_group_instance` (QueryGroup) - The current QueryGroup instance.
  - **Example:** Add a 'subtitle' text control to the static items repeater.
    ```php
    add_action( 'arts/query_control/group_control/static_fields_controls/repeater', function( $repeater, $query_group_instance ) {
        $repeater->add_control(
            'subtitle',
            [
                'label' => esc_html__( 'Subtitle', 'my-text-domain' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'label_block' => true,
            ]
        );
    }, 10, 2 );
    ```

## 📜 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 💖 Support

If you find this plugin useful, consider buying me a coffee:

<a href="https://buymeacoffee.com/artemsemkin" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" alt="Buy Me A Coffee" style="height: 60px !important;width: 217px !important;" ></a>

---

Made with ❤️ by [Artem Semkin](https://artemsemkin.com)
