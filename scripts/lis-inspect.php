<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require dirname(__DIR__) . '/config.php';
require dirname(__DIR__) . '/application/database.class.php';
require dirname(__DIR__) . '/controller/libs/ExternalSystemApiClient.php';
$db = Database::getInstance();
$db->query('SELECT lis_api_code, lis_api_name FROM ioc_lis WHERE lis_status=1');
echo json_encode($db->fetch_object(), JSON_UNESCAPED_UNICODE), "\n";
$r = (new ExternalSystemApiClient())->sync('lis', array('from_date'=>date('Y-m-d',strtotime('yesterday')), 'to_date'=>date('Y-m-d',strtotime('yesterday'))));
echo json_encode(array('success'=>$r['success'], 'message'=>$r['message']), JSON_UNESCAPED_UNICODE), "\n";
function shape($v, $depth=0) {
    if (!is_array($v)) return gettype($v);
    if ($depth>5) return 'array';
    $out=array(); foreach (array_slice($v,0,2,true) as $k=>$x) $out[$k]=shape($x,$depth+1);
    if (array_keys($v)!==range(0,count($v)-1)) { $out=array(); foreach($v as $k=>$x) $out[$k]=shape($x,$depth+1); }
    return $out;
}
foreach(isset($r['data']['endpoints'])?$r['data']['endpoints']:array() as $ep) echo json_encode(array('code'=>$ep['api_code'],'shape'=>shape($ep['data'])), JSON_UNESCAPED_UNICODE), "\n";
foreach(isset($r['data']['endpoints'])?$r['data']['endpoints']:array() as $ep) {
  $seen=array(); foreach ($ep['data'] as $row) { $key=$row['tenLoai'].'|'.$row['tenXetNghiem']; if(isset($seen[$key])) echo 'DUP: ',json_encode(array($seen[$key],$row),JSON_UNESCAPED_UNICODE),"\n"; $seen[$key]=$row; }
  echo 'Rows: ', count($ep['data']), "\n";
  foreach(array_slice($ep['data'],0,8) as $row) echo json_encode(array_intersect_key($row,array_flip(array('tenLoai','tenXetNghiem','noiTruBhyt','ngoaiTruBhyt','tongBhyt','noiTru','ngoaiTru','kskTaiVien','kskNgoaiVien','treEm','tong','tongCong'))),JSON_UNESCAPED_UNICODE),"\n";
}

