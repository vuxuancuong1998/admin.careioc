<?php
require_once __DIR__ . '/ExternalSystemApiClient.php';

class LisDailySync
{
    private $pdo;
    private $client;
    public function __construct($pdo = null, $client = null)
    {
        $this->pdo = $pdo ?: new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASSWORD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        $this->client = $client ?: new ExternalSystemApiClient();
        $this->pdo->exec("SET time_zone = '+07:00'");
    }
    public static function validDate($date)
    {
        return is_string($date) && preg_match('/^\d{4}-\d{2}-\d{2}$/D', $date) && checkdate((int)substr($date,5,2),(int)substr($date,8,2),(int)substr($date,0,4)) && $date >= '2024-01-01' && $date <= date('Y-m-d');
    }
    private function query($sql, $values = array())
    {
        $s = $this->pdo->prepare($sql);
        $s->execute($values);
        return $s;
    }
    public function seedDates()
    {
        $last = $this->query('SELECT MAX(report_date) FROM ioc_lis_dates')->fetchColumn();
        $date = new DateTimeImmutable($last ? $last . ' +1 day' : '2024-01-01');
        $end = new DateTimeImmutable('today');
        $s = $this->pdo->prepare('INSERT IGNORE INTO ioc_lis_dates(report_date,day_number,month_number,year_number) VALUES(?,?,?,?)');
        while ($date <= $end) {
            $s->execute(array($date->format('Y-m-d'),$date->format('j'),$date->format('n'),$date->format('Y')));
            $date = $date->modify('+1 day');
        }
    }
    private static function countValue($row, $key)
    {
        if (!array_key_exists($key,$row) || !preg_match('/^\d{1,10}$/D',(string)$row[$key])) throw new RuntimeException('LIS trả số lượng không hợp lệ. Vui lòng kiểm tra mẫu báo cáo nguồn.');
        return (int)$row[$key];
    }
    public static function normalize($rows)
    {
        if (!is_array($rows) || ($rows && array_keys($rows) !== range(0,count($rows)-1))) throw new RuntimeException('Cấu trúc báo cáo LIS đã thay đổi. Vui lòng kiểm tra API thống kê.');
        $services = array();
        $total = array_fill_keys(array('total_count','outpatient_count','inpatient_count','insurance_count','self_pay_count'),0);
        foreach ($rows as $r) {
            if (!is_array($r) || empty($r['tenXetNghiem']) || !isset($r['tenLoai'])) throw new RuntimeException('Thiếu tên dịch vụ trong báo cáo LIS.');
            $name = trim($r['tenXetNghiem']); $group = trim($r['tenLoai']);
            if (mb_strlen($name)>512 || mb_strlen($group)>255) throw new RuntimeException('Tên dịch vụ LIS vượt độ dài cho phép.');
            $counts = array(
                'total_count'=>self::countValue($r,'tongCong'),
                'outpatient_count'=>self::countValue($r,'ngoaiTruBhyt')+self::countValue($r,'ngoaiTru'),
                'inpatient_count'=>self::countValue($r,'noiTruBhyt')+self::countValue($r,'noiTru'),
                'insurance_count'=>self::countValue($r,'tongBhyt'),
                'self_pay_count'=>self::countValue($r,'tong')
            );
            if ($counts['total_count'] !== $counts['insurance_count']+$counts['self_pay_count'] || $counts['inpatient_count']+$counts['outpatient_count']>$counts['total_count']) throw new RuntimeException('Số liệu tổng và phân loại LIS không khớp. Vui lòng kiểm tra báo cáo nguồn.');
            $code = isset($r['maXetNghiem']) ? trim((string)$r['maXetNghiem']) : '';
            if (strlen($code)>128) throw new RuntimeException('Mã dịch vụ LIS không hợp lệ.');
            $key = hash('sha256',json_encode(array($group,$code,$name),JSON_UNESCAPED_UNICODE));
            // The source can split the same named service into multiple report rows.
            if (isset($services[$key])) {
                foreach ($counts as $k=>$v) $services[$key][$k]+=$v;
            } else {
                $services[$key] = array_merge(array('service_key'=>$key,'service_code'=>$code ?: null,'service_group'=>$group,'service_name'=>$name),$counts);
            }
            foreach ($total as $k=>$v) $total[$k]+=$counts[$k];
        }
        return array('total'=>$total,'services'=>array_values($services));
    }
    public function syncDay($date, $final = false)
    {
        if (!self::validDate($date) || ($final && $date===date('Y-m-d') && date('H:i:s')<'23:59:59')) return array('success'=>false,'message'=>'Ngày đồng bộ không hợp lệ hoặc chưa đến giờ chốt.');
        $lock = 'careioc_lis_' . $date;
        if (!(int)$this->query('SELECT GET_LOCK(?,0)',array($lock))->fetchColumn()) return array('success'=>false,'message'=>'Ngày này đang được đồng bộ. Vui lòng thử lại sau.');
        $mode = $final ? 'final' : 'temporary';
        try {
            $this->seedDates();
            $this->query('UPDATE ioc_lis_dates SET last_attempt_at=NOW() WHERE report_date=?',array($date));
            $result = $this->client->sync('lis',array('from_date'=>$date,'to_date'=>$date));
            if (!$result['success']) throw new RuntimeException('Không lấy đủ dữ liệu LIS. Kiểm tra kết nối và cấu hình đăng nhập, sau đó đồng bộ lại.');
            $payload = null;
            foreach ($result['data']['endpoints'] as $ep) if ($ep['api_code']==='get_tk_hd_khoa_xn') {
                if ($payload !== null) throw new RuntimeException('Cấu hình trùng API thống kê LIS.');
                $payload = $ep['data'];
            }
            if ($payload === null) throw new RuntimeException('Chưa cấu hình API get_tk_hd_khoa_xn.');
            $data = self::normalize($payload);
            $this->pdo->beginTransaction();
            $this->saveSnapshot('staging',$date,$data,'temporary');
            if ($final) $this->saveSnapshot('daily',$date,$data,'final');
            $this->query('UPDATE ioc_lis_dates SET status=?,next_attempt_at=NULL,last_error=NULL WHERE report_date=?',array($final?'final':'temporary',$date));
            $this->query('INSERT INTO ioc_lis_sync_log(report_date,sync_mode,success,message,created_at) VALUES(?,?,1,?,NOW())',array($date,$mode,'Đồng bộ thành công'));
            $this->pdo->commit();
            return array('success'=>true,'message'=>$final?'Đã đồng bộ và chốt dữ liệu ngày.':'Đã làm mới dữ liệu tạm.','data'=>array('report_date'=>$date,'totals'=>$data['total']));
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            $message = $e instanceof PDOException ? 'Không lưu được dữ liệu. Vui lòng kiểm tra kết nối cơ sở dữ liệu và chạy lại.' : $e->getMessage();
            error_log('LIS sync failed: ' . $date . ' [' . get_class($e) . ']');
            $this->query('UPDATE ioc_lis_dates SET next_attempt_at=DATE_ADD(NOW(),INTERVAL 30 MINUTE),last_error=? WHERE report_date=?',array($message,$date));
            $this->query('INSERT INTO ioc_lis_sync_log(report_date,sync_mode,success,message,created_at) VALUES(?,?,0,?,NOW())',array($date,$mode,$message));
            return array('success'=>false,'message'=>$message,'data'=>array('report_date'=>$date));
        } finally {
            $this->query('SELECT RELEASE_LOCK(?)',array($lock));
        }
    }
    private function saveSnapshot($suffix,$date,$data,$status)
    {
        $table = 'ioc_lis_' . $suffix;
        $detail = 'ioc_lis_service_' . $suffix;
        $this->query("INSERT INTO $table(report_date,total_count,outpatient_count,inpatient_count,insurance_count,self_pay_count,synced_at,status) VALUES(?,?,?,?,?,?,NOW(),?) ON DUPLICATE KEY UPDATE total_count=VALUES(total_count),outpatient_count=VALUES(outpatient_count),inpatient_count=VALUES(inpatient_count),insurance_count=VALUES(insurance_count),self_pay_count=VALUES(self_pay_count),synced_at=VALUES(synced_at),status=VALUES(status)",array_merge(array($date),array_values($data['total']),array($status)));
        $this->query("DELETE FROM $detail WHERE report_date=?",array($date));
        foreach ($data['services'] as $row) $this->query("INSERT INTO $detail(report_date,service_key,service_code,service_group,service_name,total_count,outpatient_count,inpatient_count,insurance_count,self_pay_count) VALUES(?,?,?,?,?,?,?,?,?,?)",array_merge(array($date),array_values($row)));
    }
    public function run($limit = 10, $refresh = true)
    {
        $this->seedDates();
        $results = array();
        if ($refresh) $results[] = $this->syncDay(date('Y-m-d'));
        $dates = $this->query("SELECT report_date FROM ioc_lis_dates WHERE report_date<CURDATE() AND status<>'final' AND (next_attempt_at IS NULL OR next_attempt_at<=NOW()) ORDER BY (report_date=DATE_SUB(CURDATE(),INTERVAL 1 DAY)) DESC, report_date ASC LIMIT " . max(1,min(1000,(int)$limit)))->fetchAll(PDO::FETCH_COLUMN);
        foreach ($dates as $date) {
            $r = $this->syncDay($date,true); $results[]=$r;
            // Back off a failed source instead of hammering hundreds of historical days.
            if (!$r['success']) break;
        }
        return $results;
    }
    public function records($from,$to,$page=1)
    {
        if (!self::validDate($from) || !self::validDate($to) || $from>$to) throw new InvalidArgumentException('Chọn khoảng ngày hợp lệ từ 01/01/2024 đến hôm nay.');
        $page=max(1,(int)$page); $offset=($page-1)*31;
        $rows=$this->query("SELECT d.*,COALESCE(f.total_count,t.total_count) total_count,COALESCE(f.outpatient_count,t.outpatient_count) outpatient_count,COALESCE(f.inpatient_count,t.inpatient_count) inpatient_count,COALESCE(f.insurance_count,t.insurance_count) insurance_count,COALESCE(f.self_pay_count,t.self_pay_count) self_pay_count,COALESCE(f.synced_at,t.synced_at) synced_at FROM ioc_lis_dates d LEFT JOIN ioc_lis_daily f ON f.report_date=d.report_date LEFT JOIN ioc_lis_staging t ON t.report_date=d.report_date WHERE d.report_date BETWEEN ? AND ? ORDER BY d.report_date DESC LIMIT 31 OFFSET $offset",array($from,$to))->fetchAll(PDO::FETCH_ASSOC);
        return array('rows'=>$rows,'page'=>$page,'total'=>(int)$this->query('SELECT COUNT(*) FROM ioc_lis_dates WHERE report_date BETWEEN ? AND ?',array($from,$to))->fetchColumn());
    }
    public function details($date)
    {
        if (!self::validDate($date)) throw new InvalidArgumentException('Ngày không hợp lệ.');
        $final = $this->query('SELECT 1 FROM ioc_lis_daily WHERE report_date=?',array($date))->fetchColumn();
        return $this->query('SELECT service_code,service_group,service_name,total_count,outpatient_count,inpatient_count,insurance_count,self_pay_count FROM ioc_lis_service_' . ($final?'daily':'staging') . ' WHERE report_date=? ORDER BY service_group,service_name',array($date))->fetchAll(PDO::FETCH_ASSOC);
    }
}
