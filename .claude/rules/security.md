# Security Rules

## Secrets Management
- Zero Hardcoding: Never hardcode API keys (Claude, OpenAI), tokens, or database credentials.
- Environment Variables: Use $_ENV or getenv() for local development. For WordPress, prioritize defining sensitive constants in wp-config.php (outside the plugin folder).
- Exclusion: Add .env, .env.local, and .claude/ to .gitignore.

- Least Privilege: Use restricted API keys for production (e.g., limit Claude API keys to specific models).

## Input Validation (The "WordPress Way")
- Boundary Validation: Validate all inputs ($_POST, $_GET, REST params) at the entry point of the function.
- WP Unslash: Always use wp_unslash() before sanitization to remove WordPress's automatic magic slashes.
- Sanitization Map:
  - Text: sanitize_text_field()
  - Textarea/Code: sanitize_textarea_field() or wp_kses() with an allow-list.
  - Keys/Slugs: sanitize_key()
  - Integers: absint() or (int) cast.
- Reject Early: If an input fails a strict allow-list check, wp_die() or return a WP_Error immediately.

## Output Encoding & Escaping
- Late Escaping: Escape data at the point of echo, not before.
- Contextual Escaping:
  - HTML Body: esc_html()
  - Attributes: esc_attr()
  - URLs: esc_url()
  - JavaScript: wp_json_encode() (best for passing PHP arrays to JS).
- No Raw SQL: Never interpolate variables into strings. Use $wpdb->prepare().

## Authentication and Authorization
- Capability Checks: Every admin page and AJAX/REST handler must check current_user_can().
- Note: Use specific capabilities (e.g., edit_posts) rather than roles (e.g., admin).
- Nonces (CSRF):
  - Forms: Verify with check_admin_referer().
  - AJAX: Verify with check_ajax_referer().
  - REST API: Always implement a permission_callback in register_rest_route().

## PHP Security & Modern Practices
- Strict Typing: Include declare(strict_types=1); in all PHP files.
- Comparison: Use === instead of == to prevent type-juggling vulnerabilities.
- Serialization: Never use unserialize() on user input; use json_decode() instead to prevent PHP Object Injection.
- Direct Access Guard: Start every PHP file with:
  ```php
  if ( ! defined( 'ABSPATH' ) ) { exit; }
  ```

## WordPress Database Access ($wpdb)
- Parameterization: Use $wpdb->prepare( $query, ...$args ) for all dynamic queries.

  - Placeholders: Use %d (integer), %f (float), or %s (string).
  - Identifiers: If a table name or column name is dynamic, validate it against a hardcoded allow-list. Never pass it into prepare().

## File System & Uploads
- WP Filesystem: Use the WP_Filesystem API instead of direct file_put_contents or fopen.
  - Upload Security: Use wp_handle_upload(). Restrict allowed file types via the upload_mimes filter.
  - Path Traversal: Use validate_file() on any input used to build a file path.

## Dependencies & Packaging
- Audit: Run npm audit or composer audit regularly.

- Production Build: Your distribution ZIP must not include:
  - .git/, .github/, .vscode/, .claude/
  - node_modules/ or tests/
  - .env files or local config files.
  - Distignore: Use a .distignore file for compatibility with standard WordPress build tools.

## AI Specific Hardening (AI Form Builder)
- Proxying: All calls to Claude/OpenAI must be made server-side via PHP. Never expose keys in the browser console.
  - Rate Limiting: Use the WordPress Transients API to limit the number of AI requests per user/IP.
  - Prompt Sanitization: Treat AI prompts as executable code; sanitize and limit length to prevent prompt injection or excessive token usage.

## Error Handling
- Failing Securely: In AJAX/REST responses, do not return raw PHP errors. Use wp_send_json_error() with generic messages.
- Logging: Use error_log() for sensitive debugging data; never echo it to the screen in production.