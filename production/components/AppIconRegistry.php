<?php

namespace app\components;

/* Live source of truth for "which top-bar icons exist" — reads them
   straight out of views/layouts/main.php's actual <li><a class="icon-X
   your-key-btn" title="Label"> markup instead of trusting a hand-maintained
   table that could silently drift out of sync (an icon added to the nav
   bar but forgotten in a separate registry would just never show up in
   the assignment screen). Same icon key can appear in several of
   main.php's role/route branches (Activity Library, Resource Library
   etc. are repeated per branch) — deduped by key here.

   Excludes the "Manage Permissions" icon itself (userpermissionslib-btn)
   since it isn't something an admin should be able to grant/revoke from
   themselves or others through this screen — it's controlled by role_id=0
   only, not by the icon-access table this class backs. */
class AppIconRegistry
{
    private static $_cache = null;

    const EXCLUDED_KEYS = ['userpermissionslib-btn'];

    /* Scoped to the main dashboard's icon bar: from the unconditional icons
       (Projects, Project Documents, Gantt Chart, Activity Relationships,
       Project Estimate — rendered for every logged-in user regardless of
       role) through the role-gated block right after it (Activity Library,
       Resource Library, Procurement, Site Office, KPI, Cost Dashboard,
       WBS, etc). Both live inside the same <ul class="round-icons"> on the
       dashboard, just at different points in main.php's markup.

       This <ul> is actually shared/reused across many other controller
       contexts too (Finance, User Masters, Operation Report...) via
       further elseif branches later in the same big conditional chain —
       those only render when you're on THOSE specific pages, not the main
       dashboard, so scanning the whole <ul> would over-include them. The
       end marker below stops the scan right after the dashboard's own
       role-gated block, before those other branches begin. */
    const SCAN_START_MARKER = 'id="add-project-nav-btn"';
    const SCAN_END_MARKER = "elseif(Yii::\$app->controller->id == 'projectsmain' && Yii::\$app->controller->action->id == 'reports')";

    public static function all()
    {
        if (self::$_cache !== null) {
            return self::$_cache;
        }

        $path = \Yii::getAlias('@app/views/layouts/main.php');
        $content = file_get_contents($path);

        $start = strpos($content, self::SCAN_START_MARKER);
        $end = strpos($content, self::SCAN_END_MARKER, $start !== false ? $start : 0);
        $slice = ($start !== false && $end !== false && $end > $start)
            ? substr($content, $start, $end - $start)
            : $content; // fall back to whole file if the markers ever move/rename, rather than finding nothing

        // Strip HTML comments (<!-- ... -->) and PHP line comments (// ...)
        // before matching, so icons commented out of the page (dead markup
        // that never actually renders, e.g. an old "Project Report" icon
        // left as `// echo '<li>...'`) don't get offered as assignable.
        $slice = preg_replace('/<!--.*?-->/s', '', $slice);
        $slice = preg_replace('/^\s*\/\/.*$/m', '', $slice);

        $icons = [];
        // Matches: class="icon-something some-key-btn" ... title="Label",
        // the older overNow*/reslib-style keys that don't end in -btn, and
        // icons with only a single class and no separate *-btn companion
        // (e.g. class="icon-chart3" alone for Gantt Chart) — for those the
        // icon class itself doubles as the key.
        if (preg_match_all(
            '/class="(icon-[\w-]+)(?:\s+([\w-]+))?"[^>]*title="([^"]+)"/',
            $slice,
            $matches,
            PREG_SET_ORDER
        )) {
            foreach ($matches as $m) {
                $iconClass = $m[1];
                $key = $m[2] !== '' ? $m[2] : $iconClass;
                $label = html_entity_decode($m[3]);
                if (in_array($key, self::EXCLUDED_KEYS, true)) continue;
                if (isset($icons[$key])) continue; // first title= wins for duplicates
                $icons[$key] = ['icon_key' => $key, 'icon_class' => $iconClass, 'label' => $label];
            }
        }

        return self::$_cache = array_values($icons);
    }
}
