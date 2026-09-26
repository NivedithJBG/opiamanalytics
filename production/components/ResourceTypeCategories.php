<?php

namespace app\components;

/* Single source of truth for "which resourcetype names belong to which
   Procurement/Indents document category" — PO (Purchase Order/Indents),
   LO (Lease Order), WO (Work Order). Before this class, each screen that
   needed this mapping hardcoded its own separate copy (some by name, some
   by numeric resourcetype_Id) — one of those copies (Work Order's
   resourcetype_Id = 4) didn't even match this database's real ids and
   silently broke that whole tab. Resolving by name here, not id, since
   resourcetype_Id values are assigned per-database and are not portable
   constants — the same failure mode must not recur. */
class ResourceTypeCategories
{
    private static $_map = [
        'po' => ['Materials', 'Consumables', 'Purchased Inputs', 'Tools and Tackles', 'Owned equipments'],
        'lo' => ['Leased equipments'],
        'wo' => ['Sub: Contractors', 'Labour'],
    ];

    private static $_idCache = [];

    /* Raw name list for a category — for call sites that build
       "rt.Name IN (...)" SQL directly. Exact-name matching is correct here:
       this database has multiple different resourcetype_Id rows sharing
       the same name (e.g. three separate ids all named "Materials"), and
       matching by name already correctly covers every one of them. */
    public static function names($category)
    {
        return self::$_map[$category] ?? [];
    }

    /* Resolves a category's names to the resourcetype_Id(s) actually
       present in the database right now, via LIKE per name fragment (same
       robustness already proven for Work Order's Sub Contractor lookup —
       tolerates punctuation/spelling variations like "Sub: Contractors",
       and automatically picks up a type the moment it's created with a
       matching name, e.g. "Labour" or "Tools and Tackles", with no code
       change needed then). Cached per category for the life of the
       request. */
    public static function ids($category)
    {
        if (isset(self::$_idCache[$category])) {
            return self::$_idCache[$category];
        }

        $ids = [];
        $db = \Yii::$app->db;
        foreach (self::names($category) as $name) {
            $rows = $db->createCommand(
                "SELECT ResourceType_Id FROM resourcetype WHERE Name LIKE :n",
                [':n' => '%' . str_replace(':', '%', $name) . '%']
            )->queryColumn();
            foreach ($rows as $id) {
                $id = (int)$id;
                if (!in_array($id, $ids, true)) $ids[] = $id;
            }
        }

        self::$_idCache[$category] = $ids;
        return $ids;
    }
}
