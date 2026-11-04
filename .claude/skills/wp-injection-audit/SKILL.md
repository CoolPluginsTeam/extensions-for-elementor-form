---
name: wp-injection-audit
description: Cyber security agent for WordPress plugins. Takes a vulnerability report (Patchstack, Wordfence, WPScan) or a bug class, verifies it, then hunts the whole plugin for the same and related issues by tracing attacker input to dangerous sinks, and returns a full evidence-backed list. Use whenever the user shares a security report or PoC, asks whether a reported issue is really fixed, or asks to find "related" or "similar" vulnerabilities, including shortcode injection, template or placeholder injection, mail header or recipient injection, open redirect, and unauthenticated AJAX or REST exposure. For a generic checklist scan that outputs a CSV, use wp-security-review instead.
---

# WordPress injection audit

Work as a defensive security engineer on a plugin the user owns. The goal is a list the user can trust, so every finding needs a traced path from attacker input to a sink. A grep hit alone is not a finding.

## 1. Understand the report

If the user gave a report, extract the entry point, attacker role, sink, and claimed fix. Then check the current code before trusting the report: line numbers and versions drift, and the branch may already contain a fix.

Compare the branch against the main branch (`git diff --stat main...HEAD`, `git show main:<file>`). Say clearly whether a fix exists only on this branch, because a site running the released version stays vulnerable until it ships.

Name the bug class in general terms. For example, "submitted value placed in a template, then the template is interpreted" is the class, and `do_shortcode` is only one instance of it. Hunt the class, not the instance.

## 2. Map the attack surface

Read `.claude/settings.json` and `.claude/rules/` if they exist, and never scan denied paths. Skip `vendor/`, `node_modules/`, `build/`, and minified files.

List every entry point and who can reach it:

- `wp_ajax_nopriv_*` (unauthenticated) and `wp_ajax_*`
- `register_rest_route`, and every `register_post_type` or `register_meta` with `show_in_rest`
- `admin_post_*`, `init` or `template_redirect` handlers that read the request, shortcodes, and cron
- Form actions and any handler that replaces or wraps a third-party endpoint

A nonce printed on public pages is not access control. Treat those endpoints as unauthenticated.

## 3. Trace input to sinks

Follow each input in execution order and note what every step does to it. Check that each sanitizer actually stops this sink. `sanitize_text_field()` keeps `[ ]`, commas, and `%`, so it stops neither shortcode nor recipient injection.

| Class | Sinks and patterns |
|---|---|
| Shortcode or content execution | `do_shortcode`, `apply_filters( 'the_content' )`, `render_block`, `do_blocks`, on strings holding user data |
| Placeholder re-expansion | Sequential replacements where a later pass rescans a string that already holds user data |
| Dynamic values | Elementor dynamic tags or request values resolved before a sink runs, for example `get_settings_for_display()` before `do_shortcode()` |
| Mail | User data in `wp_mail` to, cc, bcc, from, or headers; comma lists; raw HTML in HTML mail |
| Redirect and URL | `wp_redirect`, `location.href`, redirect URLs in JSON responses; re-check the host after substitution |
| Object reference | Client-supplied post, form, or entry IDs without a status or ownership check; `switch_to_post` |
| Data exposure | Non-public post types with `show_in_rest`, verbose errors, spoofable IP headers |
| Mass assignment | User data or field maps flowing into `wp_insert_post`, `wp_insert_user`, `update_option`, `add_post_meta` |
| Sanitizer fallbacks | `default:` branches or filters that return the raw value when nothing is registered |
| Stored and DOM XSS | Raw output (`print_unescaped_setting`, `echo` of settings) and `.html()`, `.before()`, `.append()`, `innerHTML` with decoded text |
| Classic | `$wpdb` without `prepare`, `unserialize`, `eval`, dynamic `include`, file writes, uploads, missing capability or nonce checks |

Also inspect the files the reported fix touched. A reordered call often leaves a sibling path open, so test the sibling too.

Before reporting, try to disprove the finding. Drop it if the value is hardcoded, an earlier guard blocks it, or only someone who already holds that power (such as an admin with `unfiltered_html`) can reach it.

## 4. Prove it safely

Run proofs only on the user's local site, and only read-only:

- Use a temp PHP file with `wp eval-file`, stored in the temp or scratchpad folder, and delete it afterwards.
- Stop real mail with `add_filter( 'pre_wp_mail', ... )` and inspect the arguments.
- Register a marker shortcode in the script so execution is unambiguous, plus a control case that proves legitimate behaviour still works.
- If test posts or settings are needed, restore everything and delete them afterwards.
- Never send payloads to a production or third-party site. Never edit WordPress core, `wp-config.php`, or server config, and never print secrets.

On Local by Flywheel, `wp`, `php`, and sometimes `git` are not on PATH. Find the site in `%APPDATA%\Local\sites.json`, set `PHPRC` to `%APPDATA%\Local\run\<site-id>\conf\php`, and run the site's PHP from `lightning-services` with the `wp-cli.phar` from the Local install folder.

## 5. Report

Do not change plugin code during an audit unless the user asks. Reply in the user's language, short and simple, in this format:

```markdown
## Report verdict
Each reported issue: Fixed on this branch / Fixed in release / Still open, with evidence.

## Issues
| # | Severity | Status | Issue | Location | Who / precondition | Fix |

## Checked and safe
Sinks reviewed with no issue, in one line each, so the user knows the coverage.

## Not verified
Anything not run, and what was skipped.
```

Rules for the table:

- Severity reflects real-world impact and required role. Lower it when the issue needs a specific admin configuration, and state that configuration.
- Status is `Confirmed` only if you ran a proof. Use `Code-traced` when the path is read but not run, and `Needs check` when part of it is uncertain. Never present an unrun finding as confirmed.
- Sort by severity. Use clickable `file:line` links and one concrete fix per issue.
- End with a recommended fix order and ask which ones to apply.
