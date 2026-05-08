# Code Style Rules

## Naming Conventions
- PHP Variables & Functions: snake_case (e.g., $form_id, get_plugin_data()).
- PHP Classes: PascalCase with underscores for namespaces (e.g., Cool_Form_Builder_Admin).
- PHP Methods: snake_case (standard WP style) or camelCase (if following modern PSR). Be consistent.
- JavaScript: camelCase for variables/functions, PascalCase for React components.
- Hooks (Actions/Filters): snake_case with a unique prefix (e.g., cool_form_after_save).
- Constants: UPPER_SNAKE_CASE (e.g., COOL_FORM_VERSION).

## Files
- PHP: class-filename.php for classes, kebab-case.php for others.
- JS: kebab-case.js.

## File Organization
- Abspath Guard: Every PHP file must start with if ( ! defined( 'ABSPATH' ) ) exit;.
- Structure: Group by functionality (e.g., includes/admin/, includes/api/, includes/frontend/).

## Autoloading
- Use a PSR-4 autoloader or a standard class-loader.php to avoid manual require_once chains.
- Template Parts: Keep HTML/UI logic in a templates/ or views/ folder; keep PHP logic in classes.

## WordPress Hooks & Priority
- Prefixing: Always prefix hooks and global variables with your brand/plugin slug (e.g., dragwyb_).
- Anonymous Functions: Avoid using closures/anonymous functions for hooks that need to be removed by other developers. Prefer class methods.
- Priorities: Explicitly define priority if it’s not the default 10.
- Parameter Count: Always specify the number of accepted arguments in add_filter() or add_action().

## Code Clarity & Logic
- No Magic Strings: Use constants for option names, post types, and meta keys.
- Type Casting: Explicitly cast IDs and numbers (e.g., $id = (int) $_GET['id']).
- Conditionals: Use "Yoda Conditions" (if ( true === $is_active )) for PHP to catch assignment errors, unless internal team preference differs.
- Early Returns: Use guard clauses to reduce nesting depth.

```php
if ( ! current_user_can( 'manage_options' ) ) {
    return;
}
```

## Documentation (PHPDoc)
- Functions: Every function must have a PHPDoc block with @param (type and description) and @return.
- Hooks: Document custom do_action and apply_filters so other developers know they exist.
- Versions: Use @since 1.0.0 to track when functions were added.

## Formatting
- PHP Spacing: Spaces inside parentheses: if ( $condition ) not if($condition).

- Indentation: WordPress standard uses Tabs for PHP/CSS and Spaces (2) for JS/React.
- Braces: Opening braces on the same line for functions and loops.
- Arrays: Prefer short array syntax [] over array() for PHP 7.4+.

## Frontend Integration
- Enqueuing: Never hardcode <script> tags. Use wp_enqueue_script() and wp_enqueue_style().
- Localization: Pass data from PHP to JS using wp_localize_script() or the wp_inline_script pattern.
- React/Gutenberg: Follow WordPress @wordpress/scripts build patterns for block development.