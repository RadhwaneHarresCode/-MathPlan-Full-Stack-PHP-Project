<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../config/helpers.php';
require_once __DIR__.'/../middleware/auth.php';

// All functions defined FIRST, dispatch at the bottom

function getAllDays(): never {
    $user = getUser();
    $rows = db()->query("SELECT * FROM plan_days ORDER BY day_number")->fetchAll();
    foreach($rows as &$r) $r['topics'] = json_decode($r['topics'], true);

    if($user) {
        $s = db()->prepare("SELECT day_id,completed,completed_at,notes FROM progress WHERE user_id=?");
        $s->execute([$user['id']]);
        $map = [];
        foreach($s->fetchAll() as $p) $map[$p['day_id']] = $p;
        foreach($rows as &$r) {
            $p = $map[$r['id']] ?? null;
            $r['progress'] = [
                'completed'    => (bool)($p['completed'] ?? false),
                'completed_at' => $p['completed_at'] ?? null,
                'notes'        => $p['notes'] ?? null,
            ];
        }
    }
    ok(['days' => $rows]);
}

function getOneDay(int $n): never {
    $s = db()->prepare("SELECT * FROM plan_days WHERE day_number=? LIMIT 1");
    $s->execute([$n]);
    $r = $s->fetch();
    if(!$r) fail(404, "Day $n not found");
    $r['topics'] = json_decode($r['topics'], true);
    ok(['day' => $r]);
}

function handleProgress(int $n): never {
    $user = mustAuth();
    $s = db()->prepare("SELECT id FROM plan_days WHERE day_number=? LIMIT 1");
    $s->execute([$n]);
    $day = $s->fetch();
    if(!$day) fail(404, "Day $n not found");

    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        $s = db()->prepare("SELECT completed,completed_at,notes FROM progress WHERE user_id=? AND day_id=? LIMIT 1");
        $s->execute([$user['id'], $day['id']]);
        $p = $s->fetch();
        ok([
            'day'          => $n,
            'completed'    => (bool)($p['completed'] ?? false),
            'completed_at' => $p['completed_at'] ?? null,
            'notes'        => $p['notes'] ?? null,
        ]);
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $d         = body();
        $completed = (bool)($d['completed'] ?? false);
        $notes     = isset($d['notes']) ? clean($d['notes']) : null;
        $at        = $completed ? date('Y-m-d H:i:s') : null;
        db()->prepare("
            INSERT INTO progress(user_id,day_id,completed,completed_at,notes)
            VALUES(?,?,?,?,?)
            ON DUPLICATE KEY UPDATE
                completed=VALUES(completed),
                completed_at=VALUES(completed_at),
                notes=COALESCE(VALUES(notes),notes),
                updated_at=NOW()
        ")->execute([$user['id'], $day['id'], (int)$completed, $at, $notes]);
        ok(['day' => $n, 'completed' => $completed, 'completed_at' => $at], 'Progress saved');
    }

    fail(405, 'Method not allowed');
}

// ============================================================
// DISPATCH — runs after all functions are defined
// ============================================================
$action = $_GET['action'] ?? 'all';
$day    = isset($_GET['day']) && is_numeric($_GET['day']) ? (int)$_GET['day'] : null;

if      ($action === 'all')                         getAllDays();
elseif  ($action === 'one'      && $day !== null)   getOneDay($day);
elseif  ($action === 'progress' && $day !== null)   handleProgress($day);
else    fail(404, 'Not found');