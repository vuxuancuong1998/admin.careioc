<?php
if(PHP_SAPI!=='cli') { http_response_code(404); exit; }
session_save_path(sys_get_temp_dir());
require dirname(__DIR__).'/config.php';
session_write_close();
require dirname(__DIR__).'/controller/libs/LisDailySync.php';
function check($condition,$message) { if(!$condition)throw new RuntimeException($message); echo "PASS: $message\n"; }
$row=array('tenLoai'=>'Hóa sinh','tenXetNghiem'=>'Creatinin','noiTruBhyt'=>'2','ngoaiTruBhyt'=>'3','tongBhyt'=>'5','noiTru'=>'1','ngoaiTru'=>'4','tong'=>'5','tongCong'=>'10');
$n=LisDailySync::normalize(array($row,$row));
check(count($n['services'])===1 && $n['total']['total_count']===20 && $n['total']['outpatient_count']===14,'Merge source service rows without losing counts');
check(!LisDailySync::validDate('2024-02-30') && LisDailySync::validDate('2024-02-29') && !LisDailySync::validDate('2023-12-31'),'Date boundaries and leap year');
foreach(array(array('tong'=>'bad'),array('tongCong'=>'99')) as $bad) {
 $caught=false; try{LisDailySync::normalize(array(array_merge($row,$bad)));}catch(RuntimeException $e){$caught=true;}
 check($caught,'Reject malformed or inconsistent source counts');
}
class LisFixtureClient {
 public $rows; public $fail=false;
 public function sync($system,$filters) { return array('success'=>!$this->fail,'data'=>array('endpoints'=>array(array('api_code'=>'get_tk_hd_khoa_xn','data'=>$this->rows)))); }
}
$pdo=new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',DB_USER,DB_PASSWORD,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
// Connection-scoped tables shadow production tables; real records remain untouched.
foreach(array('dates','staging','daily','service_staging','service_daily','sync_log') as $suffix) {
 $ddl=$pdo->query('SHOW CREATE TABLE ioc_lis_'.$suffix)->fetch(PDO::FETCH_NUM)[1];
 $ddl=preg_replace('/CREATE TABLE /','CREATE TEMPORARY TABLE ',$ddl,1);
 $ddl=preg_replace('/,\s*CONSTRAINT [^\n]+/','',$ddl);
 $pdo->exec($ddl);
}
$client=new LisFixtureClient();$client->rows=array($row);
$job=new LisDailySync($pdo,$client);$job->seedDates();$job->seedDates();
check((int)$pdo->query('SELECT COUNT(*) FROM ioc_lis_dates')->fetchColumn()===(int)(new DateTimeImmutable('2024-01-01'))->diff(new DateTimeImmutable('today'))->days+1,'Calendar from 2024 seeded idempotently');
$date=date('Y-m-d',strtotime('yesterday'));
check($job->syncDay($date,true)['success'],'Finalize daily snapshot');
check($job->syncDay($date,true)['success'] && (int)$pdo->query('SELECT COUNT(*) FROM ioc_lis_service_daily')->fetchColumn()===1,'Retry replaces snapshot without duplication');
$client->fail=true;check(!$job->syncDay($date,true)['success'],'Source failure reported');
check((int)$pdo->query('SELECT total_count FROM ioc_lis_daily')->fetchColumn()===10,'Failed source preserves final snapshot');
$client->fail=false;$client->rows=array();check($job->syncDay(date('Y-m-d'))['success'],'Empty successful report accepted as zero');
check((int)$pdo->query('SELECT COUNT(*) FROM ioc_lis_daily')->fetchColumn()===1,'Today staging does not finalize early');
$pdo->exec('DROP TEMPORARY TABLE ioc_lis_service_staging');
$client->rows=array(array_merge($row,array('tongCong'=>'12','tong'=>'7')));
check(!$job->syncDay($date,true)['success'],'Storage failure rolls transaction back');
check((int)$pdo->query("SELECT total_count FROM ioc_lis_staging WHERE report_date='$date'")->fetchColumn()===10,'Rollback preserves previous staging total');
echo "All LIS tests passed.\n";
