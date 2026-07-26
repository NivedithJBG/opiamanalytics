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
   button link to `ProjectPricing/Export/<id>`. **`ProjectPricingController.php`
   itself has since been deleted (2026-07, confirmed 100% dead)**, so this link
   now points at a nonexistent controller entirely, not just a missing action —
   same broken outcome as before (404), just one level higher. Still gated behind
   the dormant `_boq` tab (`department_tab.tab_id=2`, unassigned in `user_tabs`),
   so still not user-facing today.
   **Fix options:** remove the Export button from the generated HTML (recommended,
   since the controller it pointed to is gone), or build a real export endpoint
   elsewhere if this feature is still wanted.

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

## ✅ Controllers confirmed FULLY DEAD — DELETED (2026-07)

All 7 re-verified for reachability immediately before deletion (fresh grep across
`views/**/*.php` and `web/jsnew/**/*.js`), then deleted along with their orphaned
view files. Full dangling-reference sweep done afterward — every remaining hit on
these names traced to either (a) an unrelated model class with the same/similar
name that was correctly left untouched, (b) a commented-out/inert reference, or
(c) another already-dead file referencing this one (no regression, both sides
were already unreachable). All remaining controllers lint clean.

| Controller | Lines | Evidence |
|---|---|---|
| `JobcardController.php` | 1,433 | 25/25 actions dead. Only callers were 3 orphaned JS files (`web/jsnew/operations/jobcard.js`, `web/jsnew/jobcard.js`, `web/jsnew/muster.js` — the latter left in place, it's part of the separate still-flagged muster cluster in `ProjectsController.php`), none loaded by any view. Its own dormant tab (`_jobcard`, tab_id=21) has no view file to even reach it. The `Jobcard`/`OrderedResource` **models** are unrelated and were correctly left in place (still used by dead-but-present code in `ProjectsController.php`/`ProjectsmainController.php`). |
| `EstimateprojectmainController.php` | 1,122 | 9/9 actions dead. `web/jsnew/projects/allocation.js` still calls `estimateprojectmain/*` extensively, but its only loader (`views/projectsmain/_estimateallocation.php`) is itself genuinely dead (commented-out render call, not registered in `department_tab`) — two dead things pointing at each other, not a live break. |
| `ProjectPricingController.php` | 410 | 5/5 actions dead. 8 JS files referenced `projectpricing/*` routes but all 8 were themselves confirmed orphaned. `ProjectsmainController::actionBoqsearch`'s broken Export-button link (Live Bug #1 above) now points at a nonexistent controller instead of a missing action — same broken/dormant outcome either way. |
| `TermscondtnsController.php` | 854 | 12/12 actions dead. Zero references to `termscondtns/*` anywhere. Its own `views/termscondtns/` directory (4 files) deleted alongside it. **Correction (models sweep, see below):** only the `TermsCondtns` model (no suffix) is actually still used, by `ProjectsController.php:11159` — `TermsCondtnsContent` has zero references anywhere and should be treated as orphaned, not protected. |
| `ChatbotPageController.php` | 22 | 1/1 action dead — superseded by the embedded chatbot widget in `main.php` (backed by the confirmed-live `ChatbotController.php`, kept). `views/chatbot/index.php` deleted alongside it (its only referencer). |
| `SiteOfficeController.php` | 44 | 1/1 action dead. Superseded by `StorekeeperController`'s `#storeoffice-win` popup (confirmed live, routes through `storekeeper/index`, not this controller). |
| `SiteofficemobileController.php` | 44 | 1/1 action dead, and its own `render()` target was missing too (`views/siteofficemobile/` never existed). |

Also deleted, orphaned non-controller files tied to the above:
- `views/siteoffice/mobile.php`, `views/site-office/mobile.php` (two variants, both orphaned)
- `views/termscondtns/` (create.php, index.php, update.php, view.php, _form.php)
- `views/chatbot/index.php`

**Not yet deleted — still flagged, out of scope for this pass:**
- `views/financerequests/overrelay-financemaster.php` (49KB, fully orphaned — superseded by `views/projects/_accounttypes.php` + `_accountgroups.php` + `_accountsubgroups.php` + `_accounts.php` via `costing.php`). **Flagged for human diff-check before deletion** — large file, worth a quick comparison against the live versions in case it has logic the replacement is missing.
- `web/jsnew/voucher.js` (1,182 lines) — orphaned, and its own target actions (`ResourcesearchVoucher`, `Projectaccount`) don't even exist in the current `VoucherController.php` — a relic of an earlier controller shape.
- `web/jsnew/accountschedule.js` — orphaned, only caller (`views/accountschedule/update.php`) is itself unreachable.
- `views/financerequests/_financeverifications - Copy.php` — stray untracked backup file (note the literal space in the filename).
- `web/jsnew/muster.js`, `web/jsnew/leaseorders.js`, `web/jsnew/operations/workorders.js` — all orphaned, all had inert/commented-out references to the now-deleted Jobcard controller; still tied to the separate still-flagged `ProjectsController.php` muster/purchase-order clusters, not deleted in this pass.
- `web/jsnew/projects/allocation.js` + `views/projectsmain/_estimateallocation.php` — both dead, pointing at the now-deleted `EstimateprojectmainController`; left as-is since neither was on the approved delete list for this pass.

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
- 7 fully-dead controllers deleted wholesale: `JobcardController`, `EstimateprojectmainController`, `ProjectPricingController`, `TermscondtnsController`, `ChatbotPageController`, `SiteOfficeController`, `SiteofficemobileController` — see the DELETED table above for full evidence per controller.

---

## Not yet swept at all (controllers)

None — all 32 controllers in `production/controllers/` have now been through at least one reachability pass as of this audit. Three controllers (`ReportController`, `DashboardController`, `ProjectsetupController`) were swept at a coarse (live/dead ratio) level rather than full per-action line-number detail — itemize those before acting on them if a deeper cut is needed.

---

## Reverse sweep: `views/` and `web/jsnew/` (file-first, not controller-first)

The controller-first sweeps above catch dead code by starting from actions and checking
callers. This misses orphaned view/JS files that happen to hang off an otherwise-live
controller. This pass went the other direction: every file in `views/` (133 total) and
`web/jsnew/` (205 total) was checked for whether anything still loads/renders it.
**100% of both directories were inventoried and classified** — no sampling.

### Orphaned views — new findings (not already covered above)

**Yii1 CRUD-scaffold view clusters (32 files)** — the controller actions were already
flagged dead above; this confirms the view files themselves are equally unreachable
(their `create`/`update` actions all now return raw JSON, no `render()` at all, so even
the "live" half of these controllers doesn't touch these views):
- `views/accountschedule/` — all 8 files (`view/index/admin/create/update/_form/_search/_view.php`)
- `views/accountsitem/` — all 9 files (same set + `updatelive.php`, which has no controller action targeting it at all)
- `views/accountsmaster/` — all 8 files
- `views/accountssub/` — all 8 files

**`views/activity1_index` / `views/activity/index.php`** — `Activity1Controller::actionIndex()`'s only render target; zero references to `activity1/index` anywhere; the file itself is 0 bytes.

**`views/workgroups/` — entire directory (8 files)**, extending the earlier finding beyond just `index.php`. Directory-name/controller-id mismatch: `Workgroups1Controller` (id `workgroups1`) would resolve to `views/workgroups1/`, which doesn't exist — `views/workgroups/` (no "1") is pure leftover, zero controller ever targets it.

**`views/projectSetup/` — 9 of 10 files**, extending the "~95% dead ProjectsetupController" finding to view-file level (`view/tasklist/update/admin/create/index/_form/_search/_view.php`). Plus a stray backup: `views/projectSetup/create - Copy.php` (same "space in filename" pattern as `_financeverifications - Copy.php`).

**`views/projects/` cluster:**
- `views/projects/_projects.php` — confirmed genuinely orphaned and distinct from the live `views/projectsmain/_projects.php` (the only `render('_projects')` in the whole codebase resolves to the `projectsmain/` copy). Its dependency `web/jsnew/masters/projectsfunctions.js` orphaned too.
- `views/projects/_worktypes.php`, `views/projects/_directworkorders.php` — zero references anywhere, not even commented.
- **`views/projects/reporting.php` — significant structural finding.** `ProjectsController::actionReporting()` renders it, but nothing anywhere links to `projects/reporting` — only a stray `elseif` in `main.php:380` checks the action ID, never generates the link. This makes the page itself unreachable today, which cascades:
  - `views/projects/_logbook.php` (only rendered from `reporting.php:48`) + its exclusive JS `web/jsnew/projects/over_menu_operationreport.js` — both orphaned.
  - `_rproject.php`, `_log.php`, `_orders.php`, `_workorders.php`, `_schedulereport.php` (`views/projects/`), `_progressreport.php` (`views/projectsmain/`) — all `department_tab`-registered (dep_id=4) but their *only possible dispatch path* is `reporting.php`'s own dynamic-tab loop, which is itself unreachable. See "doubly-dormant" note below — kept on disk, not flagged for deletion, since fixing the one missing link in `main.php` would reactivate the whole chain at once.

**`views/projectsmain/` cluster — 3 files reachable only via a fully-commented-out render block**, distinct from `_schedulelisting`/`_boq` (which ARE `department_tab`-registered and genuinely dormant):
- `_scheduleactivity.php` → orphans `web/jsnew/projectsmain/_wbsscheduleactivity.js` (an older, superseded WBS UI — distinct from the live `#qe-modal`).
- `_schedulerelations.php` → orphans `web/jsnew/projectsmain/_schedulerelation.js` (distinct from the live `#rel-win` popup).
- `_scheduleassignresources.php` — orphaned outright, no JS dependency.
- `views/projectsmain/_schedulereport.php` — a *different* file from `views/projects/_schedulereport.php` (same name, different directory, both independently orphaned for different reasons — classic trap, verified by full path).
- `views/projectsmain/performancedashboard.php` — standalone dead view file. **Not the same thing** as the live `actionPerformancedashboard()` JSON endpoint or its consuming JS (`_performancedashboard.js`, loaded unconditionally in `main.php:2965`, very much alive) — only this specific `.php` view file is dead.

**Layouts — 3 more confirmed dead**, beyond the already-established "only `main.php` is active":
- `views/layouts/columnonenew.php` — set only by `ProjectsController::actionIndex()`, which itself renders a missing view (`views/projects/projectview.php` doesn't exist) and has zero live hrefs anywhere. Would double-fatal if ever reached.
- `views/layouts/opiamcolumnone.php` — zero references anywhere.
- `views/layouts/main_login_mobile.php` — zero references anywhere.

**`views/report/mobile.php` (+ `views/layouts/mobile.php`) — ambiguous, not asserted dead.** `ReportController::actionMobile()` renders it, but nothing in-app links to `report/mobile` — reachable only by typing the URL directly, or possibly an external bookmark/QR flow not visible in this codebase. Flagged as unverified, consistent with the original audit's own caution about this controller's "2 ambiguous mobile actions."

**`views/projectpricing/index.php`** — now a dangling remnant of the already-deleted `ProjectPricingController`; zero renderers.

### Orphaned JS — new findings

**140 of 205 JS files under `web/jsnew/` are never loaded by any view, live or dead** (cross-referenced against all 87 unique `<script src="jsnew/...">` lines across all 133 views — exhaustive, not sampled). ~9 were already named in this file; **~130 are new**. Grouped roughly by theme (full file list in the sweep transcript if needed — this is a summary):
- Finance/accounting legacy (~27 files: `accountbills.js`, `balancesheet.js`, `banks.js`, `bills.js`, `cashflow.js`, `journal.js`, `trial.js`, etc.)
- Estimate/activity/schedule legacy (~19 files: `estimate.js`, `iowestimate.js`, `wbsscheduleactivity.js`, `projects/activity.js`, `projects/estimate.js`, etc.)
- Procurement/orders legacy (~10 files: `purchaseorder.js`, `workorder.js`, `operationorders.js`, etc.)
- Administration/documents/HR legacy (~65 files: `administration.js`, `document.js`, `logbook.js`, `worktype.js`, `template.js`, etc.)
- Project-pricing family (6 files: `boq.js`, `billgeneration.js`, `drawing.js`, `gcc.js`, `pq.js`, `projectpricing.js`) — all reference the now-deleted `ProjectPricingController`, matching the dangling-view finding above.
- Third-party libs (`bootstrap.min.js`, `chart.js`, `jquery-latest.js`, `jquery-sortable.js`, `jquery.autocomplete.js`, `jspatch.js`) — **flagged for a manual check before deleting**, since library files are occasionally loaded via a non-view registration path (asset manifest, build config) that a `<script src>` grep wouldn't catch. Not confirmed dead with the same confidence as the rest.

**Duplicate-name traps confirmed** (same pattern as the already-known `resourcetype.js` split):
- `web/jsnew/resourcegroup.js` (orphaned) vs `web/jsnew/projectsmain/resourcegroup.js` (live, Resource Library popup) — spot-verified.
- `web/jsnew/accounts.js` (orphaned) vs `web/jsnew/masters/accounts.js` (live, Finance Masters).
- `web/jsnew/estimate.js`, `web/jsnew/projects/estimate.js`, `web/jsnew/wbsscheduleactivity.js`, `web/jsnew/projects/activity.js` — all orphaned, all near-namesakes of the live `web/jsnew/masters/estactivity.js` (Activity Library) — none of these four are the live one.

### Doubly-dormant department_tab entries (kept on disk, not orphaned — do not delete)

Same protection as `_boq`/`_schedulelisting`, but one level more fragile: these are
`department_tab`-registered AND their only dispatch path (`views/projects/reporting.php`)
is itself currently unlinked from anywhere. Reactivating requires two fixes (re-link
`reporting.php` AND assign a role in `user_tabs`), not just one — but per this session's
established policy, still kept on disk rather than deleted:
`views/projects/_rproject.php`, `_log.php`, `_orders.php`, `_workorders.php`,
`_schedulereport.php`, `views/projectsmain/_progressreport.php`.

### Second-pass dangling-reference check on the 7 already-deleted controllers

Re-ran the full sweep for `jobcard`, `estimateprojectmain`, `projectpricing`,
`termscondtns`, `chatbotpage`, `siteoffice`, `siteofficemobile` as route prefixes across
all 133 views + 205 JS files. **No new dangling references found** beyond what's already
documented above (already-orphaned JS files, one commented-out link in `main.php:703`,
two inert links in the already-dead `mainnew.php`). Confirms the original deletion pass
didn't miss anything live.

---

## Reverse sweep: `models/` (165 files, 100% inventoried)

### Category (c) — fully orphaned, zero references anywhere (safe to flag for deletion now)

**73 models total**, split by what would happen if they were ever invoked:

**51 Yii1 `CActiveRecord` leftovers** (would fatal-error if ever called — no `CActiveRecord` class loaded in this Yii2 app — in addition to being unreferenced): `ActivityGroup`, `ActivityReport`, `ActivityReportLog`, `ActivityReportResources`, `ActivityReportResourcesLog`, `ActivityReportTasks`, `Billitems`, `Boqbillitems`, `Boqbills`, `Bills`, `CashflowProject`, `Contravoucher`, `DespatchInward`, `DespatchOutward`, `Documenttype`, `Equipments`, `EstimateCashflow`, `EstimateProductResources`, `EstimateProjectResources`, `ExpenseStatement`, `Gcc`, `InvestmentResources`, `Investments`, `IowResources`, `Iowactivities`, `Iowproducts`, `Iowschmethedology`, `JournalsVoucher`, `MajorConsumablesResources`, `Materials`, `PiResources`, `ProActivities`, `ProductionReport`, `ProgressReportLog`, `ProjLogistics`, `ProjPi`, `ProjProducts`, `ProjectCashflow`, `ProjectEstimate`, `ProjectResources`, `ReportActivityNew`, `Reportiowactivities`, `Reporttasks`, `SiteReport`, `Subschedule`, `TaskComments`, `TaskUsers`, `TasksNewReport`, `TasksNewReportLog`, `Template`, `Trade`, `WbsEstimateStructure`, `WbsStructure`, `WorkItems`, `Worktypegroups`.

Special case: **`models/ConstructionController.php`** is not a model at all — a full 411-line legacy Yii1 *controller* class misplaced in `models/`. No route named `construction` exists anywhere. Flag for deletion or relocation.

**22 functional Yii2 models** (orphaned but would work correctly if ever invoked — no urgency difference, just noting they wouldn't crash): `ActivityReportIow`, `Brand`, `CauseOfDelay`, `ConstructionResources`, `EquipmentMovement`, `Folders`, `FuelIssueslip`, `IssueSlips` (confirms the Issue Slips feature removal earlier this session — controller/view/JS/DB row were removed but this model was left behind), `LogisticsResources`, `OverheadsResources`, `ProductResources`, `ProjectSetupResources`, `ScheduleResourceReportLog`, `ScheduleTaskReportCauseOfDelays`, `ScheduleTaskReportResources`, `TermsCondtnsContent` (see correction above), `Worktype` (dead `use` import in `Workgroups1Controller.php:7`, class never actually called), `Voucher-new.php`.

**`Voucher-new.php` is worth calling out specifically**: it declares `class Voucher extends \yii\db\ActiveRecord` — the *same class name* as the real, heavily-used `models/Voucher.php` (133 references). Since the filename doesn't match the class name, Yii2's autoloader can never resolve this file correctly — it's 91 lines of pure dead weight with a name collision baked in, not just an unused file.

### Category (b) — dead-adjacent (referenced only by code already flagged dead above, not yet fully orphaned)

These become truly orphaned the moment the dead code referencing them is eventually deleted — a natural "next wave" after the controller-level cleanup above:

| Model | Only referenced by (already flagged dead) |
|---|---|
| `Muster`, `Musterroll` | The muster/attendance/labour cluster in `ProjectsController.php` (~15-20 actions, already flagged above) |
| `IowEstimate`, `IowWbs`, `IowWbsActivities` | `ProjectsController.php`'s `actionBillsqtyiow`/`actionIowestimate`/etc., driven by orphaned JS (`billofquantities.js`, `iowestimate.js`, `actualestimate.js`) |
| `NewReport` | `ProjectsController::actionReportstart()`/`actionReportiow()` — zero callers |
| `Workgroups` | `ProjectsController::actionListestimateitems()`/`actionListiowestimateitems()`, driven by orphaned `estimate.js` |
| `MajorConsumables` | `ProjectsetupController`'s dead `actionIndex()`/`actionAdmin()` scaffold |
| `Drawings`, `TaskNotifications` | The shared `Controller.php` base class's `getNotifications()`/`getNotificationCount()` — only ever consumed by the dead `mainnew.php` layout's notification bell, not the live `main.php` |

*(`Notifications` and `UserProfile` also appear in that same dead `Controller.php` code path but are independently confirmed live elsewhere — `FinancerequestsController`/`VoucherController` and `ProjectsController`'s live user-management area respectively — so they stay protected.)*

### Category (a) — live/essential

The remaining ~80 models are referenced by confirmed-live or mostly-live code. ~35 were individually spot-checked to a specific live controller line; the rest were confirmed via reference-count well above the noise floor (10-100+ hits each) concentrated in already-confirmed-live files — not individually traced to a specific action, flagged here for transparency rather than asserting full certainty.

### Bonus finding — not a model-reachability issue, but a related inconsistency

`ProjectsController.php` references `WorkorderBills::` at 30+ call sites, but **`models/WorkorderBills.php` does not exist anywhere in the codebase.** Verified this isn't a live bug: every single call site traced back to one of the already-flagged-dead `actionWorkorderbills*`/`actionViewworkorderbills*`/`actionApproveorderbills*` actions (spot-checked `actionWorkorderbillshistory`, line 16319 — confirmed zero callers, matches the existing audit entry). Missing model inside already-dead code — not urgent, but flagging for completeness since it would need creating (or the calling code rewritten) if any of that cluster is ever revived instead of deleted.
