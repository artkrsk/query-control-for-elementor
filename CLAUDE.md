# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Arts Query Control for Elementor** is a WordPress plugin library that provides advanced query controls for Elementor page builder. It's a dual-language project (PHP backend, TypeScript frontend) that extends Elementor with custom controls for querying WordPress content (posts, terms, post types, menus).

## Key Commands

### PHP Development
```bash
# Code Quality
composer check                    # Run both phpcs and phpstan
composer phpcs                    # Check PHP code standards
composer phpcbf                   # Auto-fix PHP code standards
composer phpstan                  # Run static analysis (PHPStan level max)

# Dependencies
composer install                  # Install PHP dependencies
composer update                   # Update PHP dependencies
```

### TypeScript/Frontend Development
```bash
# Development
pnpm run dev                      # Start development build with watch mode (runs continuously in background)
pnpm run build                    # Build for production

# Code Quality
pnpm run lint                     # Run ESLint
pnpm run lint:fix                 # Auto-fix ESLint issues
pnpm run format                   # Format code with Prettier
pnpm run format:check             # Check code formatting

# Testing
pnpm test                         # Run tests with Vitest
pnpm run test:coverage            # Generate coverage report

# Dependencies
pnpm install                      # Install Node dependencies
pnpm run deps:check               # Check for dependency updates
pnpm run deps:update              # Update all dependencies
```

### Important Notes
- The `pnpm run dev` command runs continuously in the background with live reload enabled
- DO NOT run `pnpm run build` or `pnpm run dev` manually unless explicitly needed - dev is already running
- PHP code follows WordPress Coding Standards with some exclusions (see phpcs.xml)
- PHPStan is configured at maximum level with strict type checking

## Architecture

### PHP Structure (Backend)

**Namespace Root:** `Arts\QueryControl\`

**Core Architecture:**
- `Plugin.php` - Main plugin singleton extending `Arts\Base\Plugins\BasePlugin`
  - Manages two managers via `ManagersContainer`: Controls and Compatibility
  - Provides static helpers: `get_queried_posts()` and `get_posts_query_args()`

**Containers** (`src/php/Containers/`):
- `ManagersContainer.php` - Type-safe container extending `Arts\Base\Containers\ManagersContainer` for managers

**Managers** (`src/php/Managers/`):
- `Controls.php` - Registers all query controls and their AJAX handlers with Elementor
- `Compatibility.php` - Handles script/style enqueuing for Elementor editor

**Controls** (`src/php/Controls/`):
- Individual Select Controls:
  - `QueryPostsSelect.php` - Select specific posts with autocomplete
  - `QueryTermsSelect.php` - Select taxonomy terms with autocomplete
  - `QueryPostTypesSelect.php` - Select post types
  - `QueryMenusSelect.php` - Select WordPress menus
- `QueryGroup.php` - Complex group control that combines multiple query options
  - Supports both dynamic (WP_Query) and static (custom repeater) content modes
  - Includes filtering by post type, terms, specific posts, ordering, etc.

**Base Classes** (`src/php/Base/`):
- Base classes for custom controls extending Elementor's control system

**Control Pattern:**
- All select controls extend base query control classes
- Each has its own AJAX action for autocomplete functionality
- Support `multiple` and `sortable` options for multi-select with drag-reorder
- Use Select2 for the UI with customizable `select2options`

### TypeScript Structure (Frontend)

**Entry Point:** `src/ts/index.ts`

**Core** (`src/ts/core/`):
- `BaseQueryControlView.ts` - Base view implementation for query controls
  - Handles AJAX requests, autocomplete, sortable functionality
  - Manages control lifecycle (onRender, onDestroy)
- `BaseQueryControlViewStatic.ts` - Static properties for query control classes
- `logic/` - Pure logic functions:
  - `options.ts` - Option comparison and merging utilities
  - `queryData.ts` - Query data building for AJAX requests
  - `validation.ts` - Type guards and validation helpers

**Controls** (`src/ts/controls/`):
- Flat structure with one file per control:
  - `QueryPostsSelect.ts` - Posts selection control
  - `QueryTermsSelect.ts` - Terms selection control
  - `QueryPostTypesSelect.ts` - Post types selection control
  - `QueryMenusSelect.ts` - Menus selection control
  - `index.ts` - Exports and `registerAllControls()` function

**Constants** (`src/ts/constants/`):
- `CONTROL_NAMES.ts` - Control type identifiers for Elementor registration

**Utilities** (`src/ts/utils/`):
- `ajax.ts` - AJAX request handling (`loadObjectsElementor`, `fetchElementor`)
- `autocomplete.ts` - Select2 autocomplete configuration
- `sortable.ts` - Drag-and-drop reordering with jQuery UI Sortable
- `controls.ts` - Control creation and registration helpers
- `spinner.ts` - Loading indicator helpers
- `data.ts` - Data normalization helpers

**Control Registration Pattern:**
```typescript
// Each control uses registerControlView from utils/controls.ts
export const registerQueryPostsSelect = (): boolean => {
  return registerControlView(CONTROL_NAMES.POSTS_SELECT, QueryPostsSelectView)
}
```

### Build System

Uses custom build system from `__build__` submodule (part of Arts Framework):
- Build configuration: `project.config.js` (main), `project.development.js`, `project.production.js`
- Builds JavaScript to UMD format: `src/php/libraries/arts-query-control-for-elementor/index.umd.js`
- Compiles SASS to CSS: `src/php/libraries/arts-query-control-for-elementor/index.css`
- Includes BrowserSync live reload with HTTPS support
- Supports WordPress sync for hot reloading in development

### Dependencies

**PHP Dependencies:**
- Requires: `arts/base`, `arts/utilities` (local Framework packages)
- Dev: PHPStan, PHP_CodeSniffer (WPCS), CaptainHook, Elementor stubs

**TypeScript Dependencies:**
- Runtime: `@arts/elementor-types` (local Framework package)
- Dev: ESLint, Prettier, TypeScript, Vitest

**Important:** This package is part of the Arts Framework monorepo structure at `/Users/art/Projects/Framework/packages/`

## WordPress Hooks & Filters

The plugin provides extensive WordPress filters and actions for customization:

**Filters:**
- `arts/query_control/query_args` - Modify WP_Query arguments globally
- `arts/query_control/group_control/static_fields_controls/default_set` - Customize static content fields
- `arts/query_control/group_control/dynamic_fields_controls/default_set` - Customize dynamic query fields
- `arts/query_control/post_types/query_args` - Modify post type query args
- `arts/query_control/post_types/exclude` - Exclude specific post types
- `arts/query_control/post_types/include` - Include specific post types

**Actions:**
- `arts/query_control/group_control/static_fields_controls/repeater` - Add custom controls to static repeater

See README.md for detailed examples of hook usage.

## Common Development Patterns

### Adding a New Query Control

1. Create PHP control class in `src/php/Controls/` extending `QueryControl` base
2. Implement AJAX handlers (`ajax_action_get`, `ajax_action_autocomplete`)
3. Create TypeScript control in `src/ts/controls/[Name].ts` with register function
4. Register PHP control in `src/php/Managers/Controls.php`
5. Export TypeScript control from `src/ts/controls/index.ts`
6. Add control type to `src/ts/constants/CONTROL_NAMES.ts`

### Control Options

All query select controls support:
- `query` - Array of query parameters (post_type, taxonomy filters, etc.)
- `multiple` - Boolean for multi-select
- `sortable` - Boolean to enable drag-and-drop reordering
- `select2options` - Object passed to Select2 (allowClear, closeOnSelect, etc.)

### Query Modes

The QueryGroup control has two modes:
- **Dynamic** - Uses WP_Query to fetch posts with filters (post_type, terms, include/exclude)
- **Static** - Custom repeater with manual content items (title, link, image, etc.)

Query settings:
- `posts_query: 'include'` - Include specific posts/terms
- `posts_query: 'exclude'` - Exclude specific posts/terms
- `posts_amount` - Control number of results (respects archive context)
- `order_by`, `order` - Sorting options

## Code Style Notes

- PHP: Follows WordPress Coding Standards (see phpcs.xml for exclusions)
- PHP: Uses strict types, full type annotations (PHPStan level max)
- PHP: PSR-4 autoloading with namespace `Arts\QueryControl\`
- TypeScript: ESLint + Prettier configured
- TypeScript: Strict mode enabled, uses ES modules
- Comments: PHP uses WordPress docblock style, TypeScript uses JSDoc-style `/** */` comments
