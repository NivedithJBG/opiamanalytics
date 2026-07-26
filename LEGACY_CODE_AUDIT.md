# Legacy Code Audit — Flagged for Future Deletion

Generated during the popup-migration cleanup session (2026-07). This file is the
persisted record of every dead-code, duplication, and live-bug finding from a
controller-by-controller reachability sweep of the entire `production/controllers/`
directory. Nothing listed here has been deleted unless explicitly marked **DONE**.

**Do not delete anything from this list without re-verifying reachability first** —
the codebase changes; a re-check (grep the action/view name across `views/**/*.php`
and `web/jsnew/**/*.js`, and check `department_tab`/`user_tabs` for dynamic-tab
registration) takes minutes and prevents deleting something that quietly went live
since this audit.

## How "dead" was determined

An action/view is flagged dead only when **no caller was found anywhere** in
`views/**/*.php` or `web/jsnew/**/*.js`, checked recursively (a caller inside an
orphaned file doesn't count as live). Views/controllers registered in
`department_tab` but unassigned in `user_tabs` (currently **empty, 0 rows** —
confirmed repeatedly this session) are marked **dormant, not dead** — a future
role assignment could reactivate them, so their files are kept on disk rather than
deleted, following the pattern established throughout this session's tab removals.

---

## 🔴 LIVE BUGS — not yet fixed, lower urgency than Asset Register/Issue Slips (already fixed) because these are gated behind currently-dormant tabs, but will break the moment a role is ever assigned to them

1. **`ProjectsmainController::actionBoqsearch` (line ~3448)** hardcodes an "Export"
   button link to `ProjectPricing/Export/<id>` — **`actionExport` does not exist
   anywhere in `ProjectPricingController.php`**. Gated behind the dormant `_boq`
   tab (`department_tab.tab_id=2`, unassigned in `user_tabs`). Verified
   independently: `_boq.js:75` really does call `projectsmain/boqsearch`, which
   really does emit this broken link.
   **Fix options:** add `actionExport` to `ProjectPricingController`, or remove
   the Export button from the generated HTML.

2. **`_jobcard` tab (`department_tab.tab_id=21`) has no backing view file at all**
   — `views/projectsmain/_jobcard.php` (or wherever it would live) does not
   exist on disk. Reactivating this tab for any role would fail immediately on
   `ViewNotFoundException`, before even reaching `JobcardController`'s own
   issues (see below — that whole controller is dead too).

3. **`ReportController` is missing `actionScheduletaskclear`**, called by
   `web/jsnew/operations/progressreport.js:355` (bound to a `.cleartaskdet`
   click handler). The HTML that would trigger this click handler is not
   currently rendered anywhere, so it's dormant in practice — but a latent 404
   if that markup is ever restored.

---

## Controllers confirmed FULLY DEAD (safe to delete wholesale, pending one more reachability re-check at delete time)

| Controller | Lines | Evidence |
|---|---|---|
| `JobcardController.php` | 1,433 | 25/25 actions dead. Only callers are 3 orphaned JS files (`web/jsnew/operations/jobcard.js`, `web/jsnew/jobcard.js`, `web/jsnew/muster.js`), none loaded by any view. Its own dormant tab (`_jobcard`, tab_id=21) has no view file to even reach it. |
| `EstimateprojectmainController.php` | 1,122 | 9/9 actions dead. |
| `ProjectPricingController.php` | 410 | 5/5 actions dead (includes the missing `actionExport` gap above — i.e. even the actions that exist here aren't reachable). |
| `TermscondtnsController.php` | 854 | 12/12 actions dead. Zero references to `termscondtns/*` anywhere. |
| `ChatbotPageController.php` | 22 | 1/1 action dead — superseded by the embedded chatbot widget in `main.php` (backed by the confirmed-live `ChatbotController.php`, keep that one). Also orphans `views/chatbot/index.php`. |
| `SiteOfficeController.php` | 44 | 1/1 action dead. Superseded by `StorekeeperController`'s `#storeoffice-win` popup (confirmed live). |
| `SiteofficemobileController.php` | 44 | 1/1 action dead, and its own `render()` target is missing too (`views/siteofficemobile/` doesn't exist) — moot since unreachable, but a landmine either way. |

Also orphaned, non-controller files tied to the above:
- `views/siteoffice/mobile.php`, `views/site-office/mobile.php` (two variants, both orphaned — tied to the dead SiteOffice*/Siteofficemobile* controllers)
- `views/financerequests/overrelay-financemaster.php` (49KB, fully orphaned — superseded by `views/projects/_accounttypes.php` + `_accountgroups.php` + `_accountsubgroups.php` + `_accounts.php` via `costing.php`). **Flagged for human diff-check before deletion** — large file, worth a quick comparison against the live versions in case it has logic the replacement is missing.
- `web/jsnew/voucher.js` (1,182 lines) — orphaned, and its own target actions (`ResourcesearchVoucher`, `Projectaccount`) don't even exist in the current `VoucherController.php` — a relic of an earlier controller shape.
- `web/jsnew/accountschedule.js` — orphaned, only caller (`views/accountschedule/update.php`) is itself unreachable.
- `views/financerequests/_financeverifications - Copy.php` — stray untracked backup file (note the literal space in the filename).

---

## Controllers with MIXED live/dead actions (do not delete the file — only the specific dead actions listed)

### `ReportController.php` (882 lines) — mostly live
Core progress-reporting actions are live (confirmed via `web/jsnew/operations/progressreport.js`, used by the Storekeeper popup flow). ~7 actions are "stub but reachable" and 2 involve an ambiguous/unverified mobile path — needs closer reading before flagging specifics. **Not yet itemized to action-level in this pass; revisit before deletion.**

### `DashboardController.php` (3,105 lines) — ~70% live
12 of 17 actions confirmed live (dashboard charts, capacity/utilization — some of which this session's earlier work already depends on, e.g. `actionCapacityutiztn`). 5 actions confirmed dead. **Not yet itemized to specific line numbers in this pass; revisit before deletion.**

### `ProjectsetupController.php` (1,835 lines) — ~95% dead
Only 1 of 22 actions confirmed live (`actionResourcesearchbytyid`-equivalent, used elsewhere per cross-reference in the VendorlibraryController finding below). 21 actions dead. **High-value deletion candidate given the ratio — revisit for full action-level detail before deletion.**

### `VendorlibraryController.php` (383 lines) — mixed, ~64% live
**Live (7):** `actionVendors`, `actionGetresources`, `actionSearchvendors`, `actionUpdatevendor`, `actionAddallocation`, `actionRemoveallocation`, `actionDeletevendor` — all called from `vendorslib.js`, loaded unconditionally via `views/layouts/main.php:918`.
**Dead (4):** `actionSearch`, `actionCreate`, `actionUpdate`, `actionDeleteitem` — a "vendor types" sub-feature. Only caller is `web/jsnew/procurement/vendortypelib.js`, itself never `<script src>`-included anywhere, and no corresponding UI exists in the live vendor popup. Looks like a built-but-never-wired-up panel — either finish wiring it or delete both the 4 actions and `vendortypelib.js`.

### `ResourcetypeController.php` (406 lines) — mostly live (backs the confirmed-live Resource Library popup)
**Dead:** `actionIndex` (render target `views/resourcetype/` doesn't exist as a directory at all, AND uses `CActiveRecord::getDbConnection()` — a Yii1 class not present in this app, would fatal if ever reached — moot since unreachable), `actionCheckname` (only caller is the orphaned legacy `web/jsnew/resourcegroup.js`, not the live `resourcegroup.js` under `jsnew/projectsmain/`). Already-commented `actionView`/`actionDelete`/`actionAdmin` are dead by inspection, no action needed.
**Live:** `actionCreate`, `actionSearch`, `actionUpdatesort`, `actionUpdate`, `actionDeleteitem`, `actionGetoptions`.

### `SiteController.php` (151 lines) — mostly essential, leave alone
`actionContact`/`actionAbout` are dead (only linked from two orphaned legacy layouts, `main1.php`/`opiammain.php`, neither of which is ever set as the active layout). Low priority — small, standard Yii2 boilerplate, low risk either way.

### `ProjectsController.php` (25,265 lines) — extensively mixed, see prior detailed sweep this session
Already swept in depth earlier this session. Confirmed-dead clusters: holiday CRUD (5 actions, superseded by `ProjectsmainController::actionHolidays`), old Purchase/Work/Lease Order actions (~30+, superseded by `ProcurementController`), the entire muster/attendance/labour cluster (~20 actions), several `_old`/"new" duplicate pairs where both sides are dead, various broken page-render actions (missing view targets — `_assetregister_new.php`/`_assetlibrary_new.php` already fixed by removing the nav icons; `newreports.php`, others still dormant/unreachable so not yet urgent). See git log for the two commits this session that already addressed the Issue Slips cluster and Asset Register/Library nav icons from this controller's findings.

### `Workgroups1Controller.php` (476 lines) — mixed, DO NOT delete wholesale
**Live (3):** `actionIowgroupupdate`, `actionIowgroupdelete`, `actionIowgroupcreate` — used by the live Activity Library popup (`views/projects/_estactivity.php`) to manage the `iow_groups` master table.
**Dead (8):** `actionIndex`, `actionSearch`, `actionUpdate`, `actionUpdatesort`, `actionCheckdeleteworkgroup`, `actionDeleteworkgroup`, `actionCreate`, `actionIowgrouplist`.

Also dead, tied to this controller: `views/workgroups/index.php` (orphaned Yii1-era view, broken script-src path even in isolation), `web/jsnew/projects/workgroupfunctions.js` (orphaned; two-thirds of its own AJAX calls target `Projects1Controller`, which doesn't exist anywhere in the codebase).

---

## Controllers confirmed LIVE / essential — do not touch

- `ChatbotController.php` — backs the embedded chatbot widget in `main.php`, loaded on every page.
- `ResourcesController.php`, `ResourcegroupController.php` — fully live, back the Resource Library popup.
- `ApiController.php` — **cannot verify dead**, likely consumed by the separate React frontend (`public_html/static/`, compiled build not in this repo — grepping the bundle for string evidence was inconclusive). Leave alone pending explicit confirmation from whoever maintains the frontend source.
- `SamlController.php` — SSO/SAML login, cross-confirmed live via `SiteController::actionLogout()`.
- `FinancerequestsController.php`, `VoucherController.php`, `AccountsitemController.php`, `AccountssubController.php`, `AccountscheduleController.php`, `AccountsmasterController.php` — all have a live core (see below) plus a recurring dead Yii1-CRUD-scaffold pattern (`view`/`index`/`admin`, sometimes `update`/`delete`) that was superseded by hand-written AJAX-fragment actions but never removed. **No live bugs found in this domain** — every broken render()/missing-view case found here is also unreachable, so nothing crashes for a real user today (unlike the already-fixed Asset Register case).
  - `FinancerequestsController`: dead — `ledgerexcel`, `printledger`, `printtrialbalance`, `exporttrialbalance`, `printcashbook`, `printjournal`, `printbankbook`, `trialbalancecsv` (unused export/print variants), `fundverdata`, `getaddrowsapprov`, `getprojectsforapprove` (only caller is the dormant `_financeapprovals.php`/`.js` — an apparently-abandoned parallel "approvals" tab, worth asking whether it was meant to replace something before deleting), plus ~350 lines of commented-out "old" method duplicates already inert.
  - `VoucherController`: dead — `actionView` (render target directory doesn't exist, AND calls `$this->loadModel()` which isn't defined anywhere in the class — would double-fatal if ever reached, but unreachable today).
  - `AccountsitemController`/`AccountssubController`/`AccountscheduleController`/`AccountsmasterController`: each has the same dead `view`/`index`/`admin` scaffold trio (sometimes `update`/`delete`), consistently superseded by live `search`/`create`/custom-`update*`/`deleteitem` JSON actions. `AccountssubController` additionally has an entire 190-line dead comment block (lines ~1544-1737) containing 8 duplicate-named-but-inert methods — verified via `php -l` this isn't a fatal duplicate-declaration, just dead commented text.

---

## Already fixed this session (for reference, not further action needed)

- Issue Slips feature — fully removed (view, JS, 5 dead controller actions, `department_tab` row, both local and production DB).
- Asset Register / Asset Library nav icons — removed (crashed for real users on project_id=12; controller actions left in place in case the screens get rebuilt).
- WBS→Procurement resource allocation ID mismatch, resource re-sync-on-edit gap, `task_ids` population gap — all fixed in `ProjectsmainController.php`.
- Map-to-Task popup z-index bug — fixed in `main.php`.
- IOW Group activity filter, task-qty default from schedule qty — fixed.
- Dead duplicate duration-formula actions (`actionScheduleactivityupdate`, `actionSavescheduleqty`) — removed from `ProjectsmainController.php`.
- Earlier tab removals: Project, Reporting, Project Estimate, Project Schedule piano-tabs — view/JS files kept on disk (dormant-but-registered), `render()` calls removed, ~44 confirmed-dead controller actions removed from `ProjectsmainController.php` across two passes.

---

## Not yet swept at all

None — all 32 controllers in `production/controllers/` have now been through at least one reachability pass as of this audit. Three controllers (`ReportController`, `DashboardController`, `ProjectsetupController`) were swept at a coarse (live/dead ratio) level rather than full per-action line-number detail — itemize those before acting on them if a deeper cut is needed.
