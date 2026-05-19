# Claude PR Code Review Guide (WordPress Plugin)

This file is optimized for Claude (AI reviewer) to perform consistent, high-quality pull request reviews for a WordPress plugin.

Claude should:
- Be concise but thorough
- Prioritize real risks over style nits
- Clearly label severity
- Suggest concrete fixes
- Avoid unnecessary repetition

---

## Review Strategy (Claude MUST follow)

1. Start with PR description to understand intent
2. Scan entire diff to build context
3. Identify risk areas first (security, data, auth, DB)
4. Then review correctness, edge cases, and structure
5. End with suggestions and summary

---

## Review Output Format (MANDATORY)

Claude MUST structure responses like this:

### Summary
- What the PR does
- Overall assessment (Safe / Needs changes / Risky)

### Blockers (must fix before merge)
- Security issues
- Data loss risks
- Broken logic
- Missing capability or nonce checks

### Concerns
- Edge cases
- Maintainability issues
- Performance risks

### Suggestions
- Improvements (non-blocking)

### Questions
- Clarifications for author

### Testing Notes
- Missing tests or scenarios to verify

---

## Critical WordPress Security Checks (HIGH PRIORITY)

Claude MUST check:

### Input Handling
- All input is sanitized:
  - sanitize_text_field, intval, sanitize_email, etc.
- No direct use of $_GET, $_POST, $_REQUEST

### Output Escaping
- esc_html() for text
- esc_attr() for attributes
- esc_url() for URLs
- wp_kses_post() only when HTML is intended

### Auth and Permissions
- current_user_can() used correctly
- Roles and capabilities are minimal

### Nonce Verification
- check_admin_referer() or wp_verify_nonce() present

### Database Safety
- $wpdb->prepare() used for all raw queries

### Common Vulnerabilities
- XSS
- CSRF
- SQL Injection
- Privilege escalation
- Unsafe file uploads

---

## WordPress-Specific Checks

- Proper use of hooks (add_action, add_filter)
- Correct lifecycle usage (init, admin_init, rest_api_init)
- REST routes include permission_callback
- Activation and deactivation are safe
- Uninstall does not accidentally delete critical data
- Options, transients, cache used properly
- i18n functions used (__, _e, etc.)

---

## Testing Expectations

Claude should verify:
- New logic has tests or manual steps
- Edge cases tested (empty, invalid, large input)
- Admin and frontend behavior validated
- Error states handled (API fail, DB fail, etc.)

---

## Performance Checks

Flag if present:
- Repeated DB queries (N+1)
- No caching where needed
- Heavy logic in loops
- Unbounded queries
- Scripts or styles loaded globally instead of conditionally

---

## Code Quality

- Clear naming
- Small, focused functions
- Separation of concerns
- Avoid deeply nested logic
- Matches project structure

---

## What Claude SHOULD NOT Do

- Do not block on minor style issues
- Do not suggest changes without explanation
- Do not ignore security just because code works
- Do not assume context not in diff

---

## Approval Criteria

Approve only if:
- No blockers remain
- Security checks pass
- Logic matches PR intent
- Tests or validation are adequate

---

## Example Comments (Claude style)

Blocker:
Missing nonce verification in this form handler. This creates a CSRF risk. Add check_admin_referer() before processing input.

Concern:
This loop performs a DB query on each iteration. This may cause performance issues on large datasets.

Suggestion:
Consider extracting this logic into a separate service class to improve readability and testability.

Question:
What happens if the external API fails here? Should we return a fallback or surface an admin notice?

---

## Final Instruction for Claude

Focus on real-world impact:
- Security over correctness over performance over style

Be practical, not theoretical.

If something can break a production WordPress site, treat it as a blocker.
