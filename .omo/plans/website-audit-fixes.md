# Website Audit Fixes Execution Plan

## TL;DR

> **Quick Summary**: Fix the SIMPEG app audit findings with security/runtime issues first, then correctness/performance, then cheap UI/accessibility cleanup. The plan uses existing Laravel/PHPUnit patterns, adds regression tests after implementation, and requires agent-executed QA evidence for every task.
>
> **Deliverables**:
> - Harden role-based access for reports, backup, profile edit, Diklat update, and pegawai/user mappings.
> - Centralize safer upload/delete behavior through existing `FileUploadHelper` patterns.
> - Fix malformed sidebar/logo markup, confirm modal behavior, asset paths, and quick accessibility/security UI issues.
> - Improve report correctness/performance for education ranking, gender buckets, Diklat counts, and N+1 hotspots.
> - Add targeted PHPUnit regression tests and final verification evidence.
>
> **Estimated Effort**: Large
> **Parallel Execution**: YES - 4 implementation waves + final verification
> **Critical Path**: Task 1 → Task 4/5/6 → Task 10/11 → Task 13 → F1-F4

---

## Context

### Original Request
User first requested a comprehensive read-only website audit in Indonesian: runtime bugs, silent/logic bugs, weak error handling, broken UI components, routing issues, API/data fetching, security, performance, accessibility, SEO, and anything beyond those categories. User then asked to convert the findings into an executable plan for the current program and re-audit if anything was missed.

### Interview Summary
**Key Decisions**:
- Scope: include all High + Medium findings and Low items only if cheap and related to touched files.
- Test strategy: tests-after + mandatory agent-executed QA.
- Report access: `pegawai` must not access dashboard reports; reports are for `admin` and `superadmin`.
- Profile self-edit: pegawai may edit personal non-authoritative fields and photo; unique/admin fields such as `nip`, `unit_kerja_id`, `status_kepegawaian`, `nilai_tpp`, `user_id`, role/account mapping are server-side protected.

**Research Findings**:
- Test infrastructure exists: PHPUnit Unit + Feature suites, including Security, Diklat, AdminSuperadminUiux tests.
- `phpunit.xml` does not force sqlite; execution must respect configured testing DB.
- `RoleMiddleware` exists and supports `role:*`; backup route currently uses `can:superadmin` while no `Gate::define('superadmin')` was found in `app/`.
- `FileUploadHelper` exists at `app/Support/FileUploadHelper.php` and is already tested at `tests/Unit/Support/FileUploadHelperTest.php`.
- Multiple controllers still use `storeAs(..., time().'_'.$file->getClientOriginalName(), ...)` or hidden previous-file inputs.

### Metis Review
**Identified Gaps** (addressed):
- Add release/default guardrails: zero downtime, no schema-breaking changes, no broad refactor.
- Clarify file retention: hard-delete only current DB-owned old file after safe replacement; no historical orphan cleanup in this plan.
- Clarify backup strategy: prefer `mysqldump --single-transaction` if available; fallback to chunked streaming PHP dump; no new dependencies.
- Require role matrix tests for every authorization fix.
- Require direct URL hardening, not just hiding links.
- Require query/performance acceptance criteria for report N+1 hotspots.

---

## Work Objectives

### Core Objective
Remediate the audit findings safely without broad redesign: close authorization/privacy holes, fix file handling risks, correct report logic, improve backup reliability, and clean broken UI/accessibility issues while preserving existing Laravel/Blade conventions.

### Concrete Deliverables
- Report routes and exports reject `pegawai` direct URL access.
- Profile self-edit has server-side allowlist for personal editable fields and photo only.
- Diklat update enforces admin unit scope on new `pegawai_id`.
- Pegawai update validates/protects `user_id` and deletes old images only from DB-owned paths.
- Backup routes use consistent superadmin authorization and safer streaming/export behavior.
- Upload controllers stop trusting original filenames/client-provided old file paths where touched.
- Sidebar/logo markup is valid; confirm modal and confirm JS are robust and accessible.
- Report logic correctness and N+1 hotspots have targeted fixes.
- Regression tests and QA evidence exist for every implementation task.

### Definition of Done
- [ ] Targeted PHPUnit security/correctness tests pass.
- [ ] `php artisan test --testsuite=Feature` passes or any existing unrelated failures are documented with evidence.
- [ ] `php artisan test --testsuite=Unit` passes.
- [ ] `npm run build` passes for touched frontend assets/views.
- [ ] Final verification wave F1-F4 approves, then user gives explicit okay.

### Must Have
- Role matrix coverage for `superadmin`, `admin`, `pegawai` on sensitive routes/actions.
- Direct URL restrictions, not menu-only restrictions.
- No file deletion based on client-provided hidden path.
- No original filename trust for newly touched upload flows.
- Preserve Indonesian flash copy and existing named routes where possible.
- Tests-after + agent QA evidence for each task.

### Must NOT Have (Guardrails)
- No schema rename or legacy table rename.
- No new composer/npm dependencies unless executor gets explicit user approval.
- No broad controller rewrite beyond touched concerns.
- No app-wide redesign of sidebar/layout/report pages.
- No destructive historical orphan file cleanup in this plan.
- No relying on `can:superadmin` unless Gate is explicitly defined and tested; prefer existing `role:superadmin`.
- No task may require manual human verification as its only acceptance criterion.

---

## Verification Strategy (MANDATORY)

> **ZERO HUMAN INTERVENTION** - ALL verification is agent-executed. No exceptions.
> Acceptance criteria requiring "user manually tests/confirms" are FORBIDDEN.

### Test Decision
- **Infrastructure exists**: YES
- **Automated tests**: Tests-after
- **Framework**: PHPUnit Feature/Unit via `php artisan test`
- **Agent-Executed QA**: ALWAYS mandatory.

### QA Policy
Every task MUST include agent-executed QA scenarios. Evidence saved to `.omo/evidence/task-{N}-{scenario-slug}.{ext}`.

- **Frontend/UI**: Use Playwright if browser interaction is needed; otherwise Bash assertions against rendered/route responses are acceptable.
- **Backend/API/Web routes**: Use Bash/PHPUnit/curl-like HTTP feature tests.
- **Storage/File behavior**: Use PHPUnit with `Storage::fake('public')` where practical.
- **Performance/query behavior**: Use query-count tests where practical; otherwise instrument DB query log in a Feature test.

---

## Execution Strategy

### Parallel Execution Waves

```
Wave 1 (Foundation + tests scaffolding; start immediately):
├── Task 1: Security regression test scaffolding + role matrix fixtures [quick]
├── Task 2: File upload/delete inventory and helper contract tests [quick]
├── Task 3: Frontend markup/a11y test baseline [visual-engineering]
├── Task 4: Backup authorization/streaming test baseline [quick]
└── Task 5: Report correctness/performance fixtures [quick]

Wave 2 (Independent high-risk fixes):
├── Task 6: Report route authorization hardening (depends: 1) [unspecified-high]
├── Task 7: Profile self-edit allowlist hardening (depends: 1) [unspecified-high]
├── Task 8: Diklat update admin-scope hardening (depends: 1) [quick]
├── Task 9: Pegawai user_id + DB-owned file delete hardening (depends: 1,2) [unspecified-high]
├── Task 10: Backup route + safer dump implementation (depends: 4) [deep]
└── Task 11: Sidebar/logo/asset path quick UI fixes (depends: 3) [visual-engineering]

Wave 3 (Shared pattern rollout + correctness/performance):
├── Task 12: Convert high-risk upload controllers to helper-safe naming/delete (depends: 2,9) [unspecified-high]
├── Task 13: Report logic correctness + N+1 reduction (depends: 5,6) [deep]
├── Task 14: Diklat/profile count + orphan file cleanup-on-delete fixes (depends: 8) [quick]
├── Task 15: Route duplication cleanup for user pegawai management (depends: 1) [quick]
└── Task 16: Confirm modal/JS accessibility and robustness (depends: 3) [visual-engineering]

Wave 4 (Cheap related Low cleanup + regression consolidation):
├── Task 17: target=_blank rel + alt text touched-view cleanup (depends: 11,16) [visual-engineering]
├── Task 18: Low-risk controller hygiene: hasFile, stale comments, destroy file cleanup (depends: 12,14) [quick]
├── Task 19: Regression suite consolidation and focused commands (depends: 6-18) [quick]
└── Task 20: Build/smoke QA evidence collection (depends: 11,16,17,19) [unspecified-high]

Wave FINAL (After ALL tasks — 4 parallel reviews, then user okay):
├── Task F1: Plan compliance audit (oracle)
├── Task F2: Code quality review (unspecified-high)
├── Task F3: Real manual QA execution (unspecified-high)
└── Task F4: Scope fidelity check (deep)
-> Present results -> Get explicit user okay
```

### Dependency Matrix

| Task | Blocked By | Blocks | Wave |
|---|---|---|---|
| 1 | None | 6,7,8,9,15 | 1 |
| 2 | None | 9,12 | 1 |
| 3 | None | 11,16 | 1 |
| 4 | None | 10 | 1 |
| 5 | None | 13 | 1 |
| 6 | 1 | 13,19 | 2 |
| 7 | 1 | 19 | 2 |
| 8 | 1 | 14,19 | 2 |
| 9 | 1,2 | 12,19 | 2 |
| 10 | 4 | 19 | 2 |
| 11 | 3 | 17,20 | 2 |
| 12 | 2,9 | 18,19 | 3 |
| 13 | 5,6 | 19 | 3 |
| 14 | 8 | 18,19 | 3 |
| 15 | 1 | 19 | 3 |
| 16 | 3 | 17,20 | 3 |
| 17 | 11,16 | 20 | 4 |
| 18 | 12,14 | 19 | 4 |
| 19 | 6-18 | 20,F1-F4 | 4 |
| 20 | 11,16,17,19 | F1-F4 | 4 |

### Agent Dispatch Summary

- **Wave 1**: 5 tasks — T1 quick, T2 quick, T3 visual-engineering + frontend-ui-ux, T4 quick, T5 quick.
- **Wave 2**: 6 tasks — T6/T7/T9 unspecified-high, T8 quick, T10 deep, T11 visual-engineering + frontend-ui-ux.
- **Wave 3**: 5 tasks — T12 unspecified-high, T13 deep, T14 quick, T15 quick, T16 visual-engineering + frontend-ui-ux.
- **Wave 4**: 4 tasks — T17 visual-engineering, T18 quick, T19 quick, T20 unspecified-high.
- **FINAL**: 4 review agents — oracle, unspecified-high, unspecified-high, deep.

---

## TODOs

- [x] 1. Security regression test scaffolding + role matrix fixtures

  **What to do**:
  - Add/extend PHPUnit Feature tests for role matrix fixtures covering `superadmin`, `admin`, and `pegawai`.
  - Create reusable helpers/factories in existing test style; do not force sqlite changes.
  - Cover direct URL access baseline for report/profile/Diklat/Pegawai update actions before or alongside fixes.

  **Must NOT do**:
  - Do not change application behavior in this task except tests/fixtures.
  - Do not rewrite existing test architecture.

  **Recommended Agent Profile**:
  - **Category**: `quick`
    - Reason: focused test scaffolding in existing PHPUnit patterns.
  - **Skills**: [`ocs-test-regression-guard`]
    - `ocs-test-regression-guard`: regression coverage is the task domain.
  - **Skills Evaluated but Omitted**: `frontend-ui-ux` not needed; no UI implementation.

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 1
  - **Blocks**: 6, 7, 8, 9, 15
  - **Blocked By**: None

  **References**:
  - `tests/Feature/AdminSuperadminUiux/RouteAccessMatrixTest.php` - role matrix pattern to extend.
  - `tests/Feature/Security/BackupAuthorizationTest.php` - security authorization test style.
  - `tests/Feature/Diklat/DiklatAuthorizationTest.php` - Diklat role/scope test conventions.
  - `app/Http/Middleware/RoleMiddleware.php` - expected role middleware semantics.

  **Acceptance Criteria**:
  - [ ] New/updated tests establish reusable role fixtures for all affected security tasks.
  - [ ] `php artisan test tests/Feature/Security tests/Feature/AdminSuperadminUiux` passes or documents pre-existing failures.
  - [ ] Tests include at least one direct URL forbidden assertion for `pegawai` report access.

  **QA Scenarios**:
  ```
  Scenario: Role fixtures run successfully
    Tool: Bash
    Preconditions: Dependencies installed and test DB configured.
    Steps:
      1. Run `php artisan test tests/Feature/Security tests/Feature/AdminSuperadminUiux`.
      2. Capture full terminal output.
      3. Assert exit code is 0 or failures are unrelated and documented with file/test names.
    Expected Result: Targeted security/admin UI test groups execute and role fixtures are usable.
    Failure Indicators: Fatal fixture error, missing factory, auth user cannot be created.
    Evidence: .omo/evidence/task-1-role-fixtures.txt

  Scenario: Pegawai direct report URL denied test exists
    Tool: Bash
    Preconditions: Task tests are written.
    Steps:
      1. Search tests for a `pegawai` actor requesting a `/report` route.
      2. Assert expected status is 403 or redirect-forbidden behavior explicitly documented.
    Expected Result: Regression test prevents pegawai direct report access.
    Evidence: .omo/evidence/task-1-pegawai-report-denied.txt
  ```

  **Commit**: YES
  - Message: `test(security): add audit role fixtures`
  - Files: `tests/Feature/**`
  - Pre-commit: `php artisan test tests/Feature/Security tests/Feature/AdminSuperadminUiux`

- [x] 2. File upload/delete inventory and helper contract tests

  **What to do**:
  - Inventory controllers using `storeAs`, `getClientOriginalName`, hidden old-file fields (`gambarLama`, `fileLama`, equivalent), and direct `Storage::delete` from request data.
  - Extend `FileUploadHelper` unit tests if needed for UUID naming, safe public URL normalization, idempotent delete, unicode/double-extension handling.
  - Produce executor notes inside test comments or evidence; no broad storage migration.

  **Must NOT do**:
  - Do not migrate existing files or delete historical orphan files.
  - Do not change helper contract unless tests prove current contract insufficient.

  **Recommended Agent Profile**:
  - **Category**: `quick`
    - Reason: inventory + focused unit tests.
  - **Skills**: [`ocs-test-regression-guard`]
    - Regression guard around upload security.
  - **Skills Evaluated but Omitted**: `frontend-ui-ux` not applicable.

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 1
  - **Blocks**: 9, 12
  - **Blocked By**: None

  **References**:
  - `app/Support/FileUploadHelper.php` - canonical upload/delete helper.
  - `tests/Unit/Support/FileUploadHelperTest.php` - existing unit test pattern.
  - `app/Http/Controllers/PegawaiController.php:208` - unsafe client path delete pattern.
  - `app/Http/Controllers/*Controller.php` matches from audit - repeated `storeAs`/original filename patterns.

  **Acceptance Criteria**:
  - [ ] Inventory lists every affected upload/delete controller path.
  - [ ] Unit tests cover safe filename generation and delete normalization/idempotence.
  - [ ] `php artisan test tests/Unit/Support/FileUploadHelperTest.php` passes.

  **QA Scenarios**:
  ```
  Scenario: Helper rejects unsafe filename assumptions
    Tool: Bash
    Preconditions: Unit tests updated.
    Steps:
      1. Run `php artisan test tests/Unit/Support/FileUploadHelperTest.php`.
      2. Confirm tests include unicode, double extension, and public URL delete cases.
    Expected Result: FileUploadHelper tests pass and cover unsafe naming/delete edge cases.
    Evidence: .omo/evidence/task-2-helper-tests.txt

  Scenario: Upload risk inventory is complete
    Tool: Bash
    Preconditions: Codebase available.
    Steps:
      1. Run grep for `storeAs|getClientOriginalName|gambarLama|fileLama|Storage::disk\('public'\)->delete`.
      2. Compare output to task inventory.
    Expected Result: No unclassified high-risk upload/delete occurrence remains.
    Evidence: .omo/evidence/task-2-upload-inventory.txt
  ```

  **Commit**: YES
  - Message: `test(storage): cover upload helper safety`
  - Files: `tests/Unit/Support/FileUploadHelperTest.php`, optional test inventory notes
  - Pre-commit: `php artisan test tests/Unit/Support/FileUploadHelperTest.php`

- [x] 3. Frontend markup/a11y test baseline

  **What to do**:
  - Add lightweight tests/checks for sidebar logo markup, layout asset paths, confirm modal ARIA/focus hooks, and new-tab rel attributes.
  - Prefer Feature response assertions or view-render assertions consistent with existing test style.

  **Must NOT do**:
  - Do not redesign sidebar or modal.
  - Do not introduce JS test framework dependency.

  **Recommended Agent Profile**:
  - **Category**: `visual-engineering`
    - Reason: Blade markup/accessibility baseline.
  - **Skills**: [`frontend-ui-ux`, `ocs-test-regression-guard`]
    - `frontend-ui-ux`: UI/a11y domain.
    - `ocs-test-regression-guard`: protect markup fixes.
  - **Skills Evaluated but Omitted**: `impeccable-style` omitted; not a visual polish task.

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 1
  - **Blocks**: 11, 16
  - **Blocked By**: None

  **References**:
  - `resources/views/components/app/sidebar.blade.php:23-28` - malformed logo anchor.
  - `resources/views/layouts/app.blade.php` - asset path and confirm JS.
  - `resources/views/components/app/confirm-modal.blade.php` - modal markup.
  - `tests/Feature/AdminSuperadminUiux/SidebarRoleMatrixTest.php` - sidebar-related test pattern.

  **Acceptance Criteria**:
  - [ ] Tests/checks fail or assert against malformed sidebar/logo structure.
  - [ ] Tests/checks cover modal ARIA attributes after fix.
  - [ ] No new frontend dependency is added.

  **QA Scenarios**:
  ```
  Scenario: Sidebar markup baseline test executes
    Tool: Bash
    Preconditions: Test added.
    Steps:
      1. Run `php artisan test tests/Feature/AdminSuperadminUiux`.
      2. Confirm output includes the sidebar/UI contract test.
    Expected Result: UI contract tests execute without fatal render errors.
    Evidence: .omo/evidence/task-3-sidebar-baseline.txt

  Scenario: No JS dependency added
    Tool: Bash
    Preconditions: Task complete.
    Steps:
      1. Check `package.json` diff.
      2. Assert no new test/a11y JS packages were added.
    Expected Result: Markup baseline uses existing Laravel/PHPUnit approach.
    Evidence: .omo/evidence/task-3-no-js-deps.txt
  ```

  **Commit**: YES
  - Message: `test(ui): add markup accessibility guards`
  - Files: `tests/Feature/AdminSuperadminUiux/**`
  - Pre-commit: `php artisan test tests/Feature/AdminSuperadminUiux`

- [x] 4. Backup authorization/streaming test baseline

  **What to do**:
  - Extend backup tests to cover superadmin allowed, admin/pegawai denied, and route middleware consistency.
  - Add test seam for dump generator so streaming/fallback behavior can be verified without huge DB.

  **Must NOT do**:
  - Do not implement backup behavior in this task.
  - Do not shell out in tests without a controllable seam/mock.

  **Recommended Agent Profile**:
  - **Category**: `quick`
    - Reason: focused Feature/Unit backup tests.
  - **Skills**: [`ocs-test-regression-guard`]
    - Needed for security regression tests.
  - **Skills Evaluated but Omitted**: `frontend-ui-ux` not applicable.

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 1
  - **Blocks**: 10
  - **Blocked By**: None

  **References**:
  - `tests/Feature/Security/BackupAuthorizationTest.php` - existing backup auth tests.
  - `app/Http/Controllers/BackupDatabaseController.php` - current full-memory SQL dump.
  - `routes/web.php:430` - current `can:superadmin` route group.
  - `app/Http/Middleware/RoleMiddleware.php` - preferred middleware behavior.

  **Acceptance Criteria**:
  - [ ] Backup auth tests cover `superadmin`, `admin`, and `pegawai`.
  - [ ] Tests assert route path remains accessible only to superadmin.
  - [ ] Test design does not require a real huge database.

  **QA Scenarios**:
  ```
  Scenario: Backup auth matrix test runs
    Tool: Bash
    Preconditions: Tests added/updated.
    Steps:
      1. Run `php artisan test tests/Feature/Security/BackupAuthorizationTest.php`.
      2. Confirm superadmin success and admin/pegawai forbidden assertions are present.
    Expected Result: Backup route auth behavior is regression-tested.
    Evidence: .omo/evidence/task-4-backup-auth-tests.txt

  Scenario: Backup tests avoid huge DB dependency
    Tool: Bash
    Preconditions: Tests added/updated.
    Steps:
      1. Inspect test file for artificial huge table setup.
      2. Confirm streaming/generator checks use small fixtures or mocked seams.
    Expected Result: Backup tests remain fast and deterministic.
    Evidence: .omo/evidence/task-4-backup-fast-tests.txt
  ```

  **Commit**: YES
  - Message: `test(security): cover backup authorization`
  - Files: `tests/Feature/Security/BackupAuthorizationTest.php`
  - Pre-commit: `php artisan test tests/Feature/Security/BackupAuthorizationTest.php`

- [ ] 5. Report correctness/performance fixtures

  **What to do**:
  - Add test fixtures for education ranking index 0, unknown gender, report access roles, and report query behavior.
  - Add deterministic data for nominatif/DUK/bezetting/keadaan report correctness.

  **Must NOT do**:
  - Do not change report implementation in this task.
  - Do not add fragile assertions tied to pagination text only.

  **Recommended Agent Profile**:
  - **Category**: `quick`
    - Reason: focused report fixtures/tests.
  - **Skills**: [`ocs-test-regression-guard`]
    - Regression guard around report silent logic bugs.
  - **Skills Evaluated but Omitted**: `frontend-ui-ux` omitted; backend report logic focus.

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 1
  - **Blocks**: 13
  - **Blocked By**: None

  **References**:
  - `app/Http/Controllers/ReportController.php` - report logic under test.
  - `tests/Feature/AdminSuperadminUiux/ReportScopeExportTest.php` - report access/export test pattern.
  - `tests/Feature/Diklat/DiklatGapReportFixtures.php` - fixture style for deterministic report data.

  **Acceptance Criteria**:
  - [ ] Tests cover education level at rank index 0 not being treated as missing.
  - [ ] Tests cover unknown/null gender not being counted as perempuan.
  - [ ] Tests are deterministic and isolated.

  **QA Scenarios**:
  ```
  Scenario: Report fixtures produce deterministic assertions
    Tool: Bash
    Preconditions: Tests added.
    Steps:
      1. Run `php artisan test tests/Feature/AdminSuperadminUiux/ReportScopeExportTest.php` or new report test file.
      2. Confirm assertions reference seeded fixture identities and expected counts.
    Expected Result: Report logic tests are deterministic and meaningful.
    Evidence: .omo/evidence/task-5-report-fixtures.txt

  Scenario: Query-count guard is practical
    Tool: Bash
    Preconditions: Test added if feasible.
    Steps:
      1. Run the report performance test.
      2. Confirm it uses DB query logging or documented threshold without excessive flakiness.
    Expected Result: N+1 risk has a repeatable guard or documented fallback assertion.
    Evidence: .omo/evidence/task-5-query-guard.txt
  ```

  **Commit**: YES
  - Message: `test(report): cover audit correctness cases`
  - Files: `tests/Feature/**Report**.php`
  - Pre-commit: `php artisan test tests/Feature/AdminSuperadminUiux`

- [ ] 6. Report route authorization hardening

  **What to do**:
  - Restrict dashboard report routes and export/print routes to `admin` and `superadmin` using existing `role:admin,superadmin` middleware or equivalent controller guard.
  - Ensure `pegawai` direct URL access returns forbidden/redirect-forbidden consistently.
  - Preserve existing admin unit scoping and superadmin all-unit behavior.

  **Must NOT do**:
  - Do not rely on hiding sidebar links only.
  - Do not remove existing named routes used by sidebar/views.

  **Recommended Agent Profile**:
  - **Category**: `unspecified-high`
    - Reason: high-impact authorization/privacy fix across routes and tests.
  - **Skills**: [`ocs-test-regression-guard`]
    - Needed for role matrix regression.
  - **Skills Evaluated but Omitted**: `frontend-ui-ux` omitted; UI menu hiding is secondary.

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 2
  - **Blocks**: 13, 19
  - **Blocked By**: 1

  **References**:
  - `routes/web.php` report route definitions - apply middleware without route renames.
  - `app/Http/Controllers/ReportController.php:isAdminScoped()` - current admin-only scoping gap.
  - `resources/views/components/app/sidebar.blade.php` - report links should remain role-gated.
  - `tests/Feature/AdminSuperadminUiux/ReportScopeExportTest.php` - expected report scope behavior.

  **Acceptance Criteria**:
  - [ ] `pegawai` receives 403 or equivalent forbidden response for every report/print/export direct URL.
  - [ ] `admin` can access only own scoped report data.
  - [ ] `superadmin` can access all intended report data.
  - [ ] Existing report named routes remain valid.

  **QA Scenarios**:
  ```
  Scenario: Pegawai cannot access report direct URL
    Tool: Bash
    Preconditions: Test users for all roles exist.
    Steps:
      1. Run targeted report authorization tests.
      2. Assert `pegawai` GET `/report/nominatif?unit_kerja_id=1` returns 403 or configured forbidden response.
      3. Assert `pegawai` direct print/export URLs also forbidden.
    Expected Result: Pegawai has no dashboard report access by direct URL.
    Evidence: .omo/evidence/task-6-pegawai-report-forbidden.txt

  Scenario: Admin and superadmin access preserved
    Tool: Bash
    Preconditions: Admin and superadmin fixtures exist.
    Steps:
      1. Run tests for admin own-unit report access.
      2. Run tests for superadmin report access.
      3. Assert no route-not-found errors occur for named report routes.
    Expected Result: Authorized roles still access intended reports.
    Evidence: .omo/evidence/task-6-authorized-report-access.txt
  ```

  **Commit**: YES
  - Message: `fix(auth): restrict report access by role`
  - Files: `routes/web.php`, `app/Http/Controllers/ReportController.php`, report tests
  - Pre-commit: `php artisan test tests/Feature/AdminSuperadminUiux/ReportScopeExportTest.php`

- [ ] 7. Profile self-edit allowlist hardening

  **What to do**:
  - Split pegawai self-edit validation/update into an explicit allowlist for personal non-authoritative fields and photo.
  - Server-side protect unique/admin fields: `nip`, `unit_kerja_id`, `status_kepegawaian`, `nilai_tpp`, `user_id`, role/account mapping, and equivalent HR authority fields.
  - Preserve photo edit ability using safe upload behavior.

  **Must NOT do**:
  - Do not trust hidden/disabled form fields for protection.
  - Do not remove admin/superadmin ability to manage official data elsewhere.

  **Recommended Agent Profile**:
  - **Category**: `unspecified-high`
    - Reason: high-impact business authorization fix.
  - **Skills**: [`ocs-test-regression-guard`]
    - Needs regression tests for forbidden field mutation.
  - **Skills Evaluated but Omitted**: `frontend-ui-ux` omitted unless form display needs small messaging.

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 2
  - **Blocks**: 19
  - **Blocked By**: 1

  **References**:
  - `app/Http/Controllers/ProfilePegawaiController.php:250-277` - current broad update fields.
  - `tests/Feature/ProfilePegawaiUiContractTest.php` - profile UI/contract tests.
  - `resources/views/pages/dashboard/profile_pegawai/` - profile edit forms.

  **Acceptance Criteria**:
  - [ ] Crafted request from `pegawai` cannot change protected fields.
  - [ ] Pegawai can still update allowed personal/contact fields and photo.
  - [ ] Flash/error behavior remains Indonesian and user-friendly.

  **QA Scenarios**:
  ```
  Scenario: Pegawai protected fields ignored or rejected
    Tool: Bash
    Preconditions: Pegawai user and profile exist.
    Steps:
      1. Run profile self-edit security test with payload changing `nip`, `unit_kerja_id`, `nilai_tpp`, and `user_id`.
      2. Reload database row.
      3. Assert protected values remain unchanged.
    Expected Result: Protected HR/account fields cannot be changed by pegawai.
    Evidence: .omo/evidence/task-7-protected-fields.txt

  Scenario: Pegawai allowed profile fields still update
    Tool: Bash
    Preconditions: Pegawai user and profile exist.
    Steps:
      1. Run test updating allowed personal/contact fields and photo.
      2. Assert response success and database/storage update.
    Expected Result: Allowed profile self-service behavior remains functional.
    Evidence: .omo/evidence/task-7-allowed-fields.txt
  ```

  **Commit**: YES
  - Message: `fix(profile): restrict pegawai self edits`
  - Files: `app/Http/Controllers/ProfilePegawaiController.php`, profile tests, profile views if needed
  - Pre-commit: `php artisan test tests/Feature/ProfilePegawaiUiContractTest.php`

- [ ] 8. Diklat update admin-scope hardening

  **What to do**:
  - In `DiklatController::update()`, validate that the new `pegawai_id` belongs to the admin's `unit_kerja_id` when actor role is `admin`.
  - Preserve superadmin unrestricted behavior and existing model invariants.
  - Fix incorrect flash `with('Error,...')` to keyed `with('error', ...)` in touched catch block.

  **Must NOT do**:
  - Do not duplicate Diklat formula/scope logic outside existing service/model patterns more than necessary.
  - Do not weaken existing same-employee/same-year Rencana-Diklat model guards.

  **Recommended Agent Profile**:
  - **Category**: `quick`
    - Reason: focused controller authorization fix with tests.
  - **Skills**: [`ocs-test-regression-guard`]
    - Need targeted security regression.
  - **Skills Evaluated but Omitted**: `frontend-ui-ux` not needed.

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 2
  - **Blocks**: 14, 19
  - **Blocked By**: 1

  **References**:
  - `app/Http/Controllers/DiklatController.php:192-196` - existing old-row scope check.
  - `app/Services/DiklatScopeService.php` - canonical Diklat scoping approach.
  - `tests/Feature/Diklat/DiklatAuthorizationTest.php` - role scope tests.
  - `app/Models/Diklat.php` - link integrity model hooks.

  **Acceptance Criteria**:
  - [ ] Admin cannot update Diklat to a pegawai outside admin unit.
  - [ ] Admin can update Diklat within own unit.
  - [ ] Superadmin behavior remains unchanged.
  - [ ] Error flash uses `error` key.

  **QA Scenarios**:
  ```
  Scenario: Admin cross-unit Diklat update denied
    Tool: Bash
    Preconditions: Admin in unit A, pegawai in unit B, existing Diklat in unit A.
    Steps:
      1. Run targeted Diklat authorization test sending update payload with unit B `pegawai_id`.
      2. Assert forbidden/validation failure.
      3. Assert Diklat database row still belongs to original pegawai.
    Expected Result: Cross-unit move is blocked.
    Evidence: .omo/evidence/task-8-cross-unit-diklat-denied.txt

  Scenario: Admin same-unit Diklat update allowed
    Tool: Bash
    Preconditions: Admin and two pegawai in same unit.
    Steps:
      1. Run targeted update test with same-unit `pegawai_id`.
      2. Assert success redirect and database update.
    Expected Result: Legitimate same-unit update still works.
    Evidence: .omo/evidence/task-8-same-unit-diklat-allowed.txt
  ```

  **Commit**: YES
  - Message: `fix(diklat): enforce admin update scope`
  - Files: `app/Http/Controllers/DiklatController.php`, `tests/Feature/Diklat/**`
  - Pre-commit: `php artisan test tests/Feature/Diklat/DiklatAuthorizationTest.php`

- [ ] 9. Pegawai user_id + DB-owned file delete hardening

  **What to do**:
  - Remove unsafe reliance on request-provided old image path (`gambarLama`) for deletion.
  - Delete only the current DB-owned `Pegawai` photo path, after new file is safely stored and DB update is ready.
  - Validate or protect `user_id`: either prohibit changes in normal update or validate role/link uniqueness explicitly.
  - Add tests for arbitrary public disk path deletion attempts and crafted `user_id` changes.

  **Must NOT do**:
  - Do not delete files outside the expected images/document directories.
  - Do not allow null/arbitrary `user_id` assignment from omitted/crafted requests.

  **Recommended Agent Profile**:
  - **Category**: `unspecified-high`
    - Reason: high-risk file/security/data integrity fix.
  - **Skills**: [`ocs-test-regression-guard`]
    - Needed for file deletion and user mapping regression tests.
  - **Skills Evaluated but Omitted**: `frontend-ui-ux` not applicable.

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 2
  - **Blocks**: 12, 19
  - **Blocked By**: 1, 2

  **References**:
  - `app/Http/Controllers/PegawaiController.php:208` - unsafe delete from `$request->gambarLama`.
  - `app/Http/Controllers/PegawaiController.php:217` - unvalidated `user_id` assignment.
  - `app/Support/FileUploadHelper.php` - safe delete helper behavior.
  - `tests/Feature/Security/FileUploadSecurityTest.php` - existing file upload security patterns.

  **Acceptance Criteria**:
  - [ ] Crafted `gambarLama` cannot delete arbitrary public disk files.
  - [ ] Omitted/crafted `user_id` cannot null or hijack pegawai-user mapping.
  - [ ] Legitimate photo update still replaces the old DB-owned photo.

  **QA Scenarios**:
  ```
  Scenario: Arbitrary public file deletion blocked
    Tool: Bash
    Preconditions: `Storage::fake('public')` contains victim file and pegawai old photo.
    Steps:
      1. Run security test posting update with `gambarLama` pointing to victim file.
      2. Assert victim file still exists.
      3. Assert only DB-owned old photo is deleted when replacement succeeds.
    Expected Result: Client path cannot control deletion target.
    Evidence: .omo/evidence/task-9-arbitrary-delete-blocked.txt

  Scenario: Crafted user_id cannot hijack mapping
    Tool: Bash
    Preconditions: Two users and pegawai records exist.
    Steps:
      1. Run update test with payload `user_id` of another user or omitted `user_id`.
      2. Reload pegawai row.
      3. Assert original valid mapping remains unchanged unless authorized path explicitly validates it.
    Expected Result: `user_id` integrity is preserved.
    Evidence: .omo/evidence/task-9-user-id-protected.txt
  ```

  **Commit**: YES
  - Message: `fix(pegawai): protect file delete and user link`
  - Files: `app/Http/Controllers/PegawaiController.php`, `tests/Feature/Security/**`
  - Pre-commit: `php artisan test tests/Feature/Security/FileUploadSecurityTest.php`

- [ ] 10. Backup route + safer dump implementation

  **What to do**:
  - Replace or align route middleware from `can:superadmin` to existing `role:superadmin`, unless a Gate is deliberately defined and tested.
  - Refactor backup generation away from one giant in-memory string.
  - Prefer `mysqldump --single-transaction` when available and safe; fallback to chunked streaming PHP dump.
  - Improve SQL escaping/quoting strategy and response streaming.
  - Keep controller-level superadmin guard as defense-in-depth.

  **Must NOT do**:
  - Do not add new backup package dependency.
  - Do not expose DB password in logs/errors.
  - Do not write backup files permanently to public disk.

  **Recommended Agent Profile**:
  - **Category**: `deep`
    - Reason: runtime-sensitive backup/security/performance behavior.
  - **Skills**: [`ocs-runtime-validation`, `ocs-test-regression-guard`]
    - `ocs-runtime-validation`: environment-sensitive mysqldump/fallback behavior.
    - `ocs-test-regression-guard`: authorization and streaming tests.
  - **Skills Evaluated but Omitted**: `frontend-ui-ux` not applicable.

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 2
  - **Blocks**: 19
  - **Blocked By**: 4

  **References**:
  - `app/Http/Controllers/BackupDatabaseController.php:39-89` - current memory-heavy dump.
  - `routes/web.php:430` - current route middleware.
  - `tests/Feature/Security/BackupAuthorizationTest.php` - auth regression tests.
  - `config/database.php` - DB connection details source; avoid direct env in app code.

  **Acceptance Criteria**:
  - [ ] Backup route is superadmin-only via tested middleware/controller guard.
  - [ ] Admin and pegawai cannot trigger backup by direct URL.
  - [ ] Backup response streams or uses process output without building entire DB in memory.
  - [ ] Fallback handles small fixture DB and quotes null/numeric/string values correctly enough for restore tests.

  **QA Scenarios**:
  ```
  Scenario: Backup route authorization matrix
    Tool: Bash
    Preconditions: Role users exist.
    Steps:
      1. Run `php artisan test tests/Feature/Security/BackupAuthorizationTest.php`.
      2. Assert superadmin success, admin forbidden, pegawai forbidden.
    Expected Result: Backup route is superadmin-only.
    Evidence: .omo/evidence/task-10-backup-auth.txt

  Scenario: Backup generation does not use giant SQL string path
    Tool: Bash
    Preconditions: Backup implementation complete.
    Steps:
      1. Run targeted backup generation test on small fixture tables.
      2. Inspect code path for streaming response/generator/process usage.
      3. Assert output contains expected CREATE/INSERT or dump content without memory-heavy `DB::table($table)->get()` for all rows.
    Expected Result: Backup generation is streaming/chunked/process-based.
    Evidence: .omo/evidence/task-10-backup-streaming.txt
  ```

  **Commit**: YES
  - Message: `fix(backup): harden auth and streaming dump`
  - Files: `routes/web.php`, `app/Http/Controllers/BackupDatabaseController.php`, backup tests
  - Pre-commit: `php artisan test tests/Feature/Security/BackupAuthorizationTest.php`

- [ ] 11. Sidebar/logo/asset path quick UI fixes

  **What to do**:
  - Fix malformed sidebar logo anchor/SVG markup.
  - Replace relative logo/favicon paths in touched layouts with `asset()`.
  - Preserve existing sidebar route links and role-gated menu behavior.
  - Add/update tests from Task 3.

  **Must NOT do**:
  - Do not redesign sidebar layout or navigation hierarchy.
  - Do not rename routes.

  **Recommended Agent Profile**:
  - **Category**: `visual-engineering`
    - Reason: Blade markup/UI correctness.
  - **Skills**: [`frontend-ui-ux`]
    - UI/markup domain.
  - **Skills Evaluated but Omitted**: `impeccable-style` omitted; no visual polish beyond correctness.

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 2
  - **Blocks**: 17, 20
  - **Blocked By**: 3

  **References**:
  - `resources/views/components/app/sidebar.blade.php:23-28` - malformed anchor/SVG.
  - `resources/views/layouts/app.blade.php:11` - relative favicon path.
  - `resources/views/layouts/authentication.blade.php:51` - relative logo path/alt.
  - `tests/Feature/AdminSuperadminUiux/SidebarRoleMatrixTest.php` - sidebar regression tests.

  **Acceptance Criteria**:
  - [ ] Sidebar rendered HTML has valid logo link and SVG structure.
  - [ ] Logo/favicon use `asset()` helpers.
  - [ ] Existing sidebar role matrix tests pass.

  **QA Scenarios**:
  ```
  Scenario: Sidebar renders without malformed logo anchor
    Tool: Bash
    Preconditions: App test render available.
    Steps:
      1. Run sidebar UI contract test.
      2. Assert response contains `<a class="block" href="/"` or equivalent valid href.
      3. Assert SVG is not embedded inside href attribute text.
    Expected Result: Sidebar logo markup is valid and link points to root/dashboard as intended.
    Evidence: .omo/evidence/task-11-sidebar-logo.txt

  Scenario: Asset paths are absolute via asset helper
    Tool: Bash
    Preconditions: Layout views updated.
    Steps:
      1. Render/auth layout or inspect compiled response.
      2. Assert logo/favicon paths start with app asset URL path, not route-relative `images/...`.
    Expected Result: Images resolve on nested routes.
    Evidence: .omo/evidence/task-11-asset-paths.txt
  ```

  **Commit**: YES
  - Message: `fix(ui): repair sidebar logo markup`
  - Files: `resources/views/components/app/sidebar.blade.php`, `resources/views/layouts/*.blade.php`, UI tests
  - Pre-commit: `php artisan test tests/Feature/AdminSuperadminUiux/SidebarRoleMatrixTest.php`

- [ ] 12. Convert high-risk upload controllers to helper-safe naming/delete

  **What to do**:
  - Apply `FileUploadHelper` or equivalent safe pattern to high-risk controllers identified by Task 2 inventory.
  - Prioritize controllers with hidden old-file delete inputs and original filename storage: Hukuman, PenugasanLuarNegeri, Mutasi, Cuti, Penghargaan, Seminar, LatihanJabatan, InstansiLembaga, Sekretariat, Pegawai/Profile if not already covered.
  - Delete old files only from DB-stored current path after successful new upload/update.

  **Must NOT do**:
  - Do not migrate historical filenames.
  - Do not change public URL storage format unless helper already defines it.
  - Do not delete historical orphan files.

  **Recommended Agent Profile**:
  - **Category**: `unspecified-high`
    - Reason: multi-controller security consistency task.
  - **Skills**: [`ocs-test-regression-guard`]
    - Needs regression tests across upload paths.
  - **Skills Evaluated but Omitted**: `frontend-ui-ux` omitted unless forms need minor hidden input removal.

  **Parallelization**:
  - **Can Run In Parallel**: NO
  - **Parallel Group**: Wave 3 after helper/Pegawai pattern established
  - **Blocks**: 18, 19
  - **Blocked By**: 2, 9

  **References**:
  - `app/Support/FileUploadHelper.php` - canonical helper.
  - `tests/Feature/Security/FileUploadSecurityTest.php` - security test patterns.
  - Task 2 inventory evidence - complete list of affected controllers.
  - Controllers using `storeAs`/`getClientOriginalName` from audit search.

  **Acceptance Criteria**:
  - [ ] No touched upload path trusts original filename for stored filename.
  - [ ] No touched delete path trusts client-provided previous file path.
  - [ ] Targeted upload security tests pass.

  **QA Scenarios**:
  ```
  Scenario: Hidden file path cannot delete unrelated file in converted controllers
    Tool: Bash
    Preconditions: Storage fake tests cover at least representative document and image controllers.
    Steps:
      1. Run file upload security tests.
      2. Assert crafted `fileLama`/`gambarLama` values do not delete unrelated files.
    Expected Result: Converted controllers delete only DB-owned old files.
    Evidence: .omo/evidence/task-12-hidden-path-delete-blocked.txt

  Scenario: New uploads use helper-safe names
    Tool: Bash
    Preconditions: Upload tests complete.
    Steps:
      1. Upload file with original name `evil.php.jpg` or unicode/space-heavy name.
      2. Assert stored basename is UUID/helper-generated, not original filename.
    Expected Result: Stored file names are sanitized/generated.
    Evidence: .omo/evidence/task-12-safe-upload-names.txt
  ```

  **Commit**: YES
  - Message: `fix(storage): use safe upload delete paths`
  - Files: affected controllers, upload security tests
  - Pre-commit: `php artisan test tests/Feature/Security/FileUploadSecurityTest.php`

- [ ] 13. Report logic correctness + N+1 reduction

  **What to do**:
  - Fix education ranking checks so index `0` is not treated as missing.
  - Fix gender bucket logic so null/unknown values are not counted as perempuan.
  - Reduce obvious N+1 queries in nominatif, DUK, bezetting, and related print pages using eager loading, subqueries, or preloaded maps.
  - Preserve admin/superadmin scoping behavior from Task 6.

  **Must NOT do**:
  - Do not change report business definitions beyond audited bugs.
  - Do not rename report routes/views.
  - Do not introduce raw SQL with user input.

  **Recommended Agent Profile**:
  - **Category**: `deep`
    - Reason: report correctness and performance across several methods.
  - **Skills**: [`ocs-test-regression-guard`]
    - Needed for deterministic regression tests.
  - **Skills Evaluated but Omitted**: `frontend-ui-ux` omitted; view design not in scope.

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 3
  - **Blocks**: 19
  - **Blocked By**: 5, 6

  **References**:
  - `app/Http/Controllers/ReportController.php` - education ranking, gender buckets, N+1 hotspots.
  - `tests/Feature/AdminSuperadminUiux/ReportScopeExportTest.php` - report test patterns.
  - Task 5 report fixtures - deterministic expected data.

  **Acceptance Criteria**:
  - [ ] Education rank index 0 is handled correctly.
  - [ ] Unknown/null gender is not counted as perempuan.
  - [ ] Query count for representative report page does not grow linearly per pegawai where practical.
  - [ ] Report outputs remain scoped by role.

  **QA Scenarios**:
  ```
  Scenario: Education ranking index 0 is valid
    Tool: Bash
    Preconditions: Fixture includes education value at first rank position.
    Steps:
      1. Run report correctness test.
      2. Assert first-rank education is counted/compared as valid, not missing.
    Expected Result: Silent ranking bug is fixed.
    Evidence: .omo/evidence/task-13-education-rank.txt

  Scenario: Report query count avoids obvious N+1
    Tool: Bash
    Preconditions: Fixture includes multiple pegawai in a unit.
    Steps:
      1. Run performance/query-count report test.
      2. Assert query count stays under documented threshold for N pegawai.
    Expected Result: Representative report does not issue per-row pangkat/education queries.
    Evidence: .omo/evidence/task-13-query-count.txt
  ```

  **Commit**: YES
  - Message: `fix(report): correct stats and reduce queries`
  - Files: `app/Http/Controllers/ReportController.php`, report tests
  - Pre-commit: `php artisan test tests/Feature/AdminSuperadminUiux/ReportScopeExportTest.php`

- [ ] 14. Diklat/profile count + orphan file cleanup-on-delete fixes

  **What to do**:
  - Fix profile Diklat `not_realized_count` to count only relevant active/planned statuses, aligned with `DiklatGapAnalyticsService` where possible.
  - Ensure `DiklatController::destroy()` deletes current DB-owned certificate file safely.
  - Ensure Pegawai/Profile destroy/update cleanup only deletes DB-owned files.

  **Must NOT do**:
  - Do not rewrite Diklat analytics formulas outside existing service source of truth.
  - Do not scan/delete historical orphan files.

  **Recommended Agent Profile**:
  - **Category**: `quick`
    - Reason: targeted count/delete fixes leveraging existing services.
  - **Skills**: [`ocs-test-regression-guard`]
    - Regression tests needed for counts and file deletion.
  - **Skills Evaluated but Omitted**: `frontend-ui-ux` not needed.

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 3
  - **Blocks**: 18, 19
  - **Blocked By**: 8

  **References**:
  - `app/Http/Controllers/ProfilePegawaiController.php:201` - current count risk.
  - `app/Services/DiklatGapAnalyticsService.php` - source of truth for gap formulas.
  - `app/Http/Controllers/DiklatController.php:281-290` - delete without file cleanup.
  - `tests/Feature/Diklat/ProfilePegawaiDiklatPrintTest.php` - profile print Diklat tests.

  **Acceptance Criteria**:
  - [ ] Profile Diklat `not_realized_count` follows `DiklatGapAnalyticsService` active-gap definition: only active planned rows without realization count; `draft` and `cancelled` rows are excluded.
  - [ ] Deleting Diklat removes only its DB-owned certificate file.
  - [ ] Tests cover both count and file cleanup.

  **QA Scenarios**:
  ```
  Scenario: Profile print Diklat not-realized count ignores inactive statuses
    Tool: Bash
    Preconditions: Fixture has planned, draft, cancelled, realized rencana.
    Steps:
      1. Run profile Diklat print/count test.
      2. Assert not-realized count includes only active planned rows without realization.
    Expected Result: Count matches Diklat analytics definition.
    Evidence: .omo/evidence/task-14-diklat-count.txt

  Scenario: Diklat destroy removes certificate safely
    Tool: Bash
    Preconditions: Storage fake has certificate and unrelated file.
    Steps:
      1. Run Diklat destroy file cleanup test.
      2. Assert certificate removed and unrelated file remains.
    Expected Result: DB-owned file cleanup works without arbitrary deletion.
    Evidence: .omo/evidence/task-14-diklat-file-cleanup.txt
  ```

  **Commit**: YES
  - Message: `fix(diklat): align counts and file cleanup`
  - Files: `app/Http/Controllers/ProfilePegawaiController.php`, `app/Http/Controllers/DiklatController.php`, Diklat tests
  - Pre-commit: `php artisan test tests/Feature/Diklat/ProfilePegawaiDiklatPrintTest.php`

- [ ] 15. Route duplication cleanup for user pegawai management

  **What to do**:
  - Resolve duplicate `/manajemen_setup/data_user_pegawai` route definitions.
  - Preserve intended access for user pegawai management according to existing controller authorization: `superadmin` and allowed scoped `admin` if currently intended by tests.
  - Ensure sidebar links/named routes still resolve.

  **Must NOT do**:
  - Do not rename URLs unless unavoidable.
  - Do not loosen access compared to controller guards.

  **Recommended Agent Profile**:
  - **Category**: `quick`
    - Reason: focused route cleanup with access tests.
  - **Skills**: [`ocs-test-regression-guard`]
    - Route access tests needed.
  - **Skills Evaluated but Omitted**: `frontend-ui-ux` not needed unless sidebar link assertion fails.

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 3
  - **Blocks**: 19
  - **Blocked By**: 1

  **References**:
  - `routes/web.php:123` and `routes/web.php:131` - duplicate route definitions.
  - `app/Http/Controllers/UserPegawaiController.php` - controller access scope.
  - `tests/Feature/AdminSuperadminUiux/UserManagementAccessSearchTest.php` - user management tests.

  **Acceptance Criteria**:
  - [ ] Only one effective route definition remains for the URI/method pair.
  - [ ] Intended admin/superadmin access tests pass.
  - [ ] No sidebar route/link breaks.

  **QA Scenarios**:
  ```
  Scenario: No duplicate route remains
    Tool: Bash
    Preconditions: Routes updated.
    Steps:
      1. Run `php artisan route:list`.
      2. Search for `/manajemen_setup/data_user_pegawai` GET entries.
      3. Assert exactly one intended route entry exists.
    Expected Result: Duplicate route ambiguity removed.
    Evidence: .omo/evidence/task-15-route-list.txt

  Scenario: User pegawai management access preserved
    Tool: Bash
    Preconditions: Access tests available.
    Steps:
      1. Run `php artisan test tests/Feature/AdminSuperadminUiux/UserManagementAccessSearchTest.php`.
      2. Assert intended roles pass and unauthorized roles fail.
    Expected Result: Cleanup does not break intended management access.
    Evidence: .omo/evidence/task-15-access-tests.txt
  ```

  **Commit**: YES
  - Message: `fix(routes): remove duplicate user pegawai route`
  - Files: `routes/web.php`, user management tests if needed
  - Pre-commit: `php artisan test tests/Feature/AdminSuperadminUiux/UserManagementAccessSearchTest.php`

- [ ] 16. Confirm modal/JS accessibility and robustness

  **What to do**:
  - Add `role="dialog"`, `aria-modal`, labelled-by/described-by IDs to confirm modal.
  - Add practical focus management/focus restore/trap behavior within existing Alpine pattern.
  - Harden global confirm JS: null-check forms, use `requestSubmit()` where appropriate, avoid bypassing native validation.

  **Must NOT do**:
  - Do not replace Alpine/modal architecture.
  - Do not change Indonesian button labels unless needed for clarity.

  **Recommended Agent Profile**:
  - **Category**: `visual-engineering`
    - Reason: Blade/Alpine accessibility + JS behavior.
  - **Skills**: [`frontend-ui-ux`]
    - Accessibility/UX domain.
  - **Skills Evaluated but Omitted**: `impeccable-style` omitted; this is correctness/a11y, not polish.

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 3
  - **Blocks**: 17, 20
  - **Blocked By**: 3

  **References**:
  - `resources/views/components/app/confirm-modal.blade.php` - modal Alpine state.
  - `resources/views/layouts/app.blade.php:74-127` - global confirm JS.
  - Task 3 UI/a11y baseline tests.

  **Acceptance Criteria**:
  - [ ] Modal has semantic dialog attributes and labels.
  - [ ] Escape/backdrop behavior remains intact.
  - [ ] Missing form for confirm buttons does not throw JS error.
  - [ ] `requestSubmit()` or equivalent preserves validation/submit handlers.

  **QA Scenarios**:
  ```
  Scenario: Modal has accessible dialog semantics
    Tool: Bash
    Preconditions: Modal markup updated.
    Steps:
      1. Run UI contract test or inspect rendered modal HTML.
      2. Assert `role="dialog"`, `aria-modal="true"`, and label/description IDs are present.
    Expected Result: Confirm modal exposes accessible semantics.
    Evidence: .omo/evidence/task-16-modal-aria.txt

  Scenario: Confirm handler handles missing form gracefully
    Tool: Playwright
    Preconditions: App page with a temporary/test confirm button outside form or fixture route available.
    Steps:
      1. Open page containing `.confirm-delete` outside a form.
      2. Click the button.
      3. Assert no console error occurs and page remains interactive.
    Expected Result: JS null-check prevents runtime crash.
    Evidence: .omo/evidence/task-16-confirm-no-form.png
  ```

  **Commit**: YES
  - Message: `fix(ui): harden confirm modal accessibility`
  - Files: `resources/views/components/app/confirm-modal.blade.php`, `resources/views/layouts/app.blade.php`, UI tests
  - Pre-commit: `php artisan test tests/Feature/AdminSuperadminUiux && npm run build`

- [ ] 17. `target="_blank"` rel + alt text touched-view cleanup

  **What to do**:
  - Add `rel="noopener noreferrer"` to touched/new-tab links from the audit list.
  - Add meaningful alt text for logos/photos that are informative in touched views; keep decorative alt empty only when truly decorative.
  - Limit changes to audited/touched Blade files.

  **Must NOT do**:
  - Do not rewrite page content or SEO strategy.
  - Do not alter links' destinations or open behavior.

  **Recommended Agent Profile**:
  - **Category**: `visual-engineering`
    - Reason: Blade accessibility/security cleanup.
  - **Skills**: [`frontend-ui-ux`]
    - Accessibility/UX domain.
  - **Skills Evaluated but Omitted**: `ocs-seo-audit` omitted because SEO scope is cheap/internal cleanup only.

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 4
  - **Blocks**: 20
  - **Blocked By**: 11, 16

  **References**:
  - `resources/views/pages/dashboard/report/*.blade.php` - target blank report links.
  - `resources/views/pages/dashboard/profile_pegawai/indexProfilePegawai.blade.php` - new tab/profile link.
  - `resources/views/auth/register.blade.php` - new-tab links.
  - `resources/views/layouts/authentication.blade.php` - logo alt/path.

  **Acceptance Criteria**:
  - [ ] Audited `target="_blank"` links include `rel="noopener noreferrer"`.
  - [ ] Informative images in touched views have meaningful alt text.
  - [ ] No route/link target changes.

  **QA Scenarios**:
  ```
  Scenario: New-tab links have rel protection
    Tool: Bash
    Preconditions: Blade files updated.
    Steps:
      1. Grep touched Blade files for `target="_blank"`.
      2. Assert each match includes `rel="noopener noreferrer"`.
    Expected Result: No audited target blank link lacks rel protection.
    Evidence: .omo/evidence/task-17-target-blank-rel.txt

  Scenario: Informative images have alt text
    Tool: Bash
    Preconditions: Blade files updated.
    Steps:
      1. Grep touched files for `<img` with empty `alt=""`.
      2. Confirm remaining empty alt values are explicitly decorative or remove them from touched informative images.
    Expected Result: Logo/photo images in touched views have useful alt text.
    Evidence: .omo/evidence/task-17-alt-text.txt
  ```

  **Commit**: YES
  - Message: `fix(ui): add link rel and image alt text`
  - Files: audited Blade views
  - Pre-commit: `php artisan test tests/Feature/AdminSuperadminUiux && npm run build`

- [ ] 18. Low-risk controller hygiene: hasFile, stale comments, destroy file cleanup

  **What to do**:
  - Convert `PegawaiController::store()` from `has('foto')` to `hasFile('foto')`.
  - Remove/repair stale copy-paste comments in touched controllers (e.g. Diklat comment mentioning unrelated domain).
  - Ensure destroy cleanup for touched file-owning controllers uses DB-owned path only.
  - Keep only low-risk/local fixes related to touched controllers.

  **Must NOT do**:
  - Do not perform broad comment cleanup across the whole app.
  - Do not change business behavior.

  **Recommended Agent Profile**:
  - **Category**: `quick`
    - Reason: small local hygiene fixes.
  - **Skills**: [`ocs-test-regression-guard`]
    - Ensure no regression around upload/delete.
  - **Skills Evaluated but Omitted**: `frontend-ui-ux` not applicable.

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 4
  - **Blocks**: 19
  - **Blocked By**: 12, 14

  **References**:
  - `app/Http/Controllers/PegawaiController.php:99` - `has('foto')` issue.
  - `app/Http/Controllers/PegawaiController.php:destroy()` - photo cleanup.
  - `app/Http/Controllers/DiklatController.php` - stale comment and touched catch/delete logic.

  **Acceptance Criteria**:
  - [ ] File upload checks use `hasFile()` in touched path.
  - [ ] Destroy cleanup deletes only DB-owned files.
  - [ ] Stale comment in touched Diklat code is removed or corrected.
  - [ ] Tests from Tasks 9/12/14 still pass.

  **QA Scenarios**:
  ```
  Scenario: Pegawai store ignores non-file foto field
    Tool: Bash
    Preconditions: Test added/updated.
    Steps:
      1. Run upload/security test with non-file `foto` field.
      2. Assert no fake upload path is stored.
    Expected Result: `hasFile()` behavior prevents non-file handling.
    Evidence: .omo/evidence/task-18-has-file.txt

  Scenario: Destroy cleanup remains DB-owned only
    Tool: Bash
    Preconditions: Storage fake tests exist.
    Steps:
      1. Run representative destroy cleanup tests.
      2. Assert unrelated files remain after destroy.
    Expected Result: Cleanup does not delete arbitrary files.
    Evidence: .omo/evidence/task-18-destroy-cleanup.txt
  ```

  **Commit**: YES
  - Message: `fix(storage): clean low-risk file handling`
  - Files: touched controllers/tests
  - Pre-commit: `php artisan test tests/Feature/Security/FileUploadSecurityTest.php`

- [ ] 19. Regression suite consolidation and focused commands

  **What to do**:
  - Run all targeted tests from Tasks 1-18.
  - Fix test grouping/naming so future maintainers can run audit regression coverage easily.
  - Document targeted command set in commit notes/evidence, not a new docs file unless already required by tests.

  **Must NOT do**:
  - Do not create new README/docs proactively.
  - Do not hide failing tests; classify unrelated existing failures with evidence.

  **Recommended Agent Profile**:
  - **Category**: `quick`
    - Reason: consolidation of tests/commands.
  - **Skills**: [`ocs-test-regression-guard`]
    - Regression suite ownership.
  - **Skills Evaluated but Omitted**: `frontend-ui-ux` not applicable.

  **Parallelization**:
  - **Can Run In Parallel**: NO
  - **Parallel Group**: Wave 4 after implementation tasks
  - **Blocks**: 20, F1-F4
  - **Blocked By**: 6-18

  **References**:
  - `phpunit.xml` - Unit/Feature suite definitions.
  - `tests/Feature/Security/**` - security regression suite.
  - `tests/Feature/AdminSuperadminUiux/**` - role/report/sidebar suite.
  - `tests/Feature/Diklat/**` - Diklat regression suite.

  **Acceptance Criteria**:
  - [ ] Focused security/report/profile/Diklat/UI tests pass.
  - [ ] Full Unit suite passes.
  - [ ] Full Feature suite is run; any unrelated failures are documented with exact test names/output.

  **QA Scenarios**:
  ```
  Scenario: Focused audit regression suite passes
    Tool: Bash
    Preconditions: Tasks 6-18 complete.
    Steps:
      1. Run `php artisan test tests/Feature/Security tests/Feature/AdminSuperadminUiux tests/Feature/Diklat tests/Feature/ProfilePegawaiUiContractTest.php`.
      2. Capture output and exit code.
    Expected Result: Focused audit regression tests pass.
    Evidence: .omo/evidence/task-19-focused-tests.txt

  Scenario: Full test suites assessed
    Tool: Bash
    Preconditions: App test DB configured.
    Steps:
      1. Run `php artisan test --testsuite=Unit`.
      2. Run `php artisan test --testsuite=Feature`.
      3. Capture pass/fail and exact unrelated failures if any.
    Expected Result: Full suites pass or failures are explicitly classified as pre-existing/unrelated with evidence.
    Evidence: .omo/evidence/task-19-full-suites.txt
  ```

  **Commit**: YES
  - Message: `test(regression): consolidate audit coverage`
  - Files: test files only if consolidation edits needed
  - Pre-commit: focused regression command + Unit suite

- [ ] 20. Build/smoke QA evidence collection

  **What to do**:
  - Run `npm run build` and collect output.
  - Smoke-test key flows: login/dashboard render, sidebar visible, report route forbidden for pegawai, admin report access, profile self-edit allowed/protected fields, representative upload replacement, backup superadmin access.
  - Save evidence files under `.omo/evidence/task-20-*`.

  **Must NOT do**:
  - Do not mark final verification complete; this task prepares evidence for F1-F4.
  - Do not require human-only clicking as the verification method.

  **Recommended Agent Profile**:
  - **Category**: `unspecified-high`
    - Reason: cross-feature QA evidence collection.
  - **Skills**: [`ocs-runtime-validation`, `frontend-ui-ux`]
    - `ocs-runtime-validation`: runtime/build validation.
    - `frontend-ui-ux`: UI smoke checks.
  - **Skills Evaluated but Omitted**: `impeccable-style` omitted; no polish review.

  **Parallelization**:
  - **Can Run In Parallel**: NO
  - **Parallel Group**: Wave 4 final task
  - **Blocks**: F1-F4
  - **Blocked By**: 11, 16, 17, 19

  **References**:
  - `package.json` - build command.
  - `routes/web.php` - smoke route paths.
  - Evidence paths from Tasks 1-19 - consolidate QA proof.

  **Acceptance Criteria**:
  - [ ] `npm run build` passes.
  - [ ] Smoke QA evidence exists for authorization, profile, upload, backup, sidebar/modal.
  - [ ] Evidence file names match `.omo/evidence/task-20-*`.

  **QA Scenarios**:
  ```
  Scenario: Frontend build passes
    Tool: Bash
    Preconditions: Node dependencies installed.
    Steps:
      1. Run `npm run build`.
      2. Capture output and exit code.
    Expected Result: Build completes successfully.
    Evidence: .omo/evidence/task-20-npm-build.txt

  Scenario: Critical web smoke paths behave correctly
    Tool: Playwright/Bash
    Preconditions: App server available with seeded users or Feature-test equivalent.
    Steps:
      1. Authenticate as pegawai and request a report URL; assert forbidden.
      2. Authenticate as admin and request own report; assert success.
      3. Render sidebar/dashboard; assert no malformed logo and no console errors from confirm JS.
      4. Run profile self-edit smoke; assert protected fields unchanged and allowed field updated.
    Expected Result: Critical patched flows work together.
    Evidence: .omo/evidence/task-20-critical-smoke.png
  ```

  **Commit**: YES
  - Message: `test(qa): capture audit smoke evidence`
  - Files: no source files unless smoke tests are codified
  - Pre-commit: `npm run build` + focused test command

---

## Final Verification Wave (MANDATORY — after ALL implementation tasks)

> 4 review agents run in PARALLEL. ALL must APPROVE. Present consolidated results to user and get explicit "okay" before completing.
> Do NOT auto-proceed after verification. Wait for user's explicit approval before marking work complete.

- [ ] F1. **Plan Compliance Audit**

  **Recommended Agent Profile**:
  - **Category**: `oracle`
    - Reason: read-only verification against the plan's explicit Must Have / Must NOT Have requirements.
  - **Skills**: []

  **Acceptance Criteria**:
  - [ ] Every Must Have is verified with file reads, route tests, or command output.
  - [ ] Every Must NOT Have is searched and no forbidden pattern remains.
  - [ ] Evidence files for Tasks 1-20 exist under `.omo/evidence/`.
  - [ ] Final output is `Must Have [N/N] | Must NOT Have [N/N] | Tasks [N/N] | VERDICT: APPROVE/REJECT`.

  **What to do**: Read the plan end-to-end. For each Must Have: verify implementation exists using file reads, route tests, and command results. For each Must NOT Have: search codebase for forbidden patterns and reject with file:line if found. Check evidence files exist in `.omo/evidence/`. Compare deliverables against plan.

- [ ] F2. **Code Quality Review**

  **Recommended Agent Profile**:
  - **Category**: `unspecified-high`
    - Reason: broad code quality, test, and build review across all changed files.
  - **Skills**: [`ocs-test-regression-guard`]

  **Acceptance Criteria**:
  - [ ] Focused regression tests and full Unit/Feature suites are run or failures are classified with exact output.
  - [ ] `npm run build` is run and result recorded.
  - [ ] Changed files are reviewed for debug output, empty catches, commented-out code, unused imports, broad rewrites, and inconsistent Indonesian copy.
  - [ ] Final output is `Build [PASS/FAIL] | Tests [N pass/N fail] | Files [N clean/N issues] | VERDICT`.

  **What to do**: Run focused tests, full Unit/Feature tests where feasible, and `npm run build`. Review all changed files for quality and AI-slop patterns.

- [ ] F3. **Real Manual QA Execution**

  **Recommended Agent Profile**:
  - **Category**: `unspecified-high`
    - Reason: cross-feature runtime QA of browser/web/storage/report flows.
  - **Skills**: [`frontend-ui-ux`, `ocs-runtime-validation`]

  **Acceptance Criteria**:
  - [ ] Every QA scenario from Tasks 1-20 is executed or explicitly marked not applicable with reason.
  - [ ] Evidence is saved under `.omo/evidence/final-qa/`.
  - [ ] Direct URLs, forbidden roles, upload replacement, report pages, backup download path, sidebar render, and modal keyboard behavior are tested.
  - [ ] Final output is `Scenarios [N/N pass] | Integration [N/N] | Edge Cases [N tested] | VERDICT`.

  **What to do**: Execute EVERY QA scenario from EVERY task using commands/browser as specified. Capture evidence under `.omo/evidence/final-qa/`.

- [ ] F4. **Scope Fidelity Check**

  **Recommended Agent Profile**:
  - **Category**: `deep`
    - Reason: holistic diff-to-plan reasoning and scope-creep detection.
  - **Skills**: []

  **Acceptance Criteria**:
  - [ ] Every High and Medium audit finding is mapped to a completed task or documented rejection reason.
  - [ ] Low cleanup remains cheap/related to touched files.
  - [ ] No unrelated feature/refactor or schema-breaking change is present.
  - [ ] Final output is `Tasks [N/N compliant] | Contamination [CLEAN/N issues] | Unaccounted [CLEAN/N files] | VERDICT`.

  **What to do**: Compare actual diff against this plan. Verify all high/medium issues are addressed, low cleanup stays cheap/related, and no unrelated feature/refactor was introduced.

---

## Commit Strategy

- Prefer atomic commits by concern/wave:
  - `test(security): add audit regression coverage`
  - `fix(auth): harden report and profile access`
  - `fix(storage): prevent unsafe file deletion`
  - `fix(report): correct rankings and reduce queries`
  - `fix(ui): repair sidebar and modal accessibility`
- Tiny same-file cleanup may be batched safely if tests remain focused.
- Before commit: run relevant targeted tests and record evidence.

---

## Success Criteria

### Verification Commands
```bash
php artisan test tests/Feature/Security
php artisan test tests/Feature/AdminSuperadminUiux
php artisan test tests/Feature/Diklat
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
npm run build
```

### Final Checklist
- [ ] All High findings fixed or explicitly covered by tests.
- [ ] All Medium findings fixed or explicitly covered by tests.
- [ ] Low items fixed only where cheap/related.
- [ ] `pegawai` cannot access report routes directly.
- [ ] Pegawai self-edit cannot change unique/admin fields.
- [ ] File deletion uses DB-owned paths only.
- [ ] Backup is superadmin-only and does not load entire DB into one string when fallback/streaming is available.
- [ ] Sidebar/logo markup validates in rendered HTML.
- [ ] QA evidence exists for every task.
