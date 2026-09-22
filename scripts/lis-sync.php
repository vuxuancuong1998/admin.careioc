<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
// CLI authentication is memory-only; do not persist vendor tokens in session files.
ini_set('session.save_handler','files');
session_save_path(sys_get_temp_dir());
require dirname(__DIR__) . '/config.php';
session_write_close();
require dirname(__DIR__) . '/application/database.class.php';
require dirname(__DIR__) . '/controller/libs/LisDailySync.php';
$db = Database::getInstance();
$job = new LisDailySync();
$mode = isset($argv[1]) ? $argv[1] : 'refresh';
try {
    if ($mode==='status') {
        $pdo=new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME,DB_USER,DB_PASSWORD,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
        echo json_encode($pdo->query('SELECT status,COUNT(*) days,MIN(report_date) first_date,MAX(report_date) last_date FROM ioc_lis_dates GROUP BY status')->fetchAll(PDO::FETCH_ASSOC)),"\n";
        echo json_encode($pdo->query('SELECT report_date,last_error FROM ioc_lis_dates WHERE last_error IS NOT NULL ORDER BY report_date DESC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC),JSON_UNESCAPED_UNICODE),"\n";
        exit;
    }
    if ($mode==='install') {
        $pdo=new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME,DB_USER,DB_PASSWORD,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
        $pdo->exec(file_get_contents(dirname(__DIR__).'/database/migrations/20260915_lis_daily.sql'));
        $job->seedDates();
        echo "LIS tables and calendar ready.\n"; exit;
    }
    if ($mode==='final') {
        // Task Scheduler may start late after midnight: always select the most recent cutoff.
        $date=date('H:i:s')==='23:59:59'?date('Y-m-d'):date('Y-m-d',strtotime('yesterday'));
        $results=array($job->syncDay($date,true));
    } elseif ($mode==='refresh' || $mode==='backfill') {
        $results=$job->run(isset($argv[2])?(int)$argv[2]:10,$mode==='refresh');
    } else { throw new InvalidArgumentException('Use install, refresh, final or backfill [limit].'); }
    $ok=true;
    foreach($results as $r) { echo json_encode($r,JSON_UNESCAPED_UNICODE),"\n"; if(!$r['success'])$ok=false; }
    exit($ok?0:1);
} catch(Throwable $e) { fwrite(STDERR,"LIS job failed; check database and application logs.\n"); exit(1); }
