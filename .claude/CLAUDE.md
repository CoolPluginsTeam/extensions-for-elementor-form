# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

This is a WordPress plugin called **Auto Deploy Testing** (slug: `autodeploy-testing`, version: `0.1`). It exists primarily as a sandbox for testing a GitHub Actions auto-deploy pipeline. The plugin itself exposes a single shortcode `[autodeploy_testing]`.

There are no build tools, package managers (npm/Composer), or test suites in this repository. Development is pure PHP targeting WordPress 5.0+ and PHP 7.0+.

## CI/CD pipeline

Every push to any branch triggers the **Plugin Quality Check** workflow (`.github/workflows/pcp.yml`):

- Uses `wordpress/plugin-check-action@v1` in **strict mode**, checking `plugin_repo` and `security` categories.
- On failure it generates an HTML report and emails it to the team via Gmail SMTP (credentials in GitHub secrets: `MAIL_USERNAME`).
- The `deploy-after-approval.yml` workflow handles deployment gating after the quality check passes.

**Claude Code integration** (`.github/workflows/claude-manual-review.yml`): mentioning `@claude` in any issue body, issue title, PR comment, or PR review triggers `anthropics/claude-code-action@v1` automatically. The action requires `ANTHROPIC_API_KEY` in GitHub secrets.

## Code architecture

Entry point `autodeploy-testing.php` does two things only: guards against direct access (`ABSPATH` check) and instantiates `AutoDeploy_Testing_Plugin` → calls `init()`.

All logic lives in `includes/class-autodeploy-testing-plugin.php`:

- `AutoDeploy_Testing_Plugin` is a `final` class — do not extend it.
- `init()` hooks `register()` onto WordPress's `init` action.
- `register()` adds the `[autodeploy_testing]` shortcode.
- `render_shortcode()` accepts an optional `version` attribute (defaults to `$this->version`) and returns an escaped `<span class="autodeploy-testing">` string.

When adding new functionality, follow this pattern: add a new method, hook it inside `register()`, keep `init()` and the constructor clean.

## Line endings

All files must use **LF** line endings (enforced by `.gitattributes`). Ensure your editor or git config does not convert to CRLF.

## WordPress coding standards

Use `esc_html()` / `esc_attr()` for all output. Use `esc_html__()` with the `autodeploy-testing` text domain for translatable strings. Match the existing `@since`, `@package`, and `@return` docblock style.
