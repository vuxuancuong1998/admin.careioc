<?php
Class adminController extends baseController
{
	public function index()
	{
		// if(!(isset($_SESSION['user']['id']) && $_SESSION['user']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
		
		$this->view->admintmp("index");
	}

	/**
	 * Quản lý số liệu khám chữa bệnh ngoại trú theo ngày.
	 */
	public function outpatient()
	{
		header('Location: ' . XC_URL . '/backend/outpatient');
		return;
		global $db;
		if (empty($_SESSION['outpatient_csrf'])) {
			$_SESSION['outpatient_csrf'] = bin2hex(random_bytes(24));
		}

		$flash = array('type' => '', 'message' => '');
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			if (!hash_equals($_SESSION['outpatient_csrf'], isset($_POST['csrf']) ? $_POST['csrf'] : '')) {
				$flash = array('type' => 'error', 'message' => 'Phiên làm việc đã hết hạn. Vui lòng thử lại.');
			} else {
				$action = isset($_POST['action']) ? $_POST['action'] : '';
				if ($action === 'save') {
					$result = $this->saveOutpatientRecord($_POST);
					$flash = array('type' => $result['ok'] ? 'success' : 'error', 'message' => $result['message']);
				} elseif ($action === 'delete') {
					$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
					if ($id > 0) {
						$db->query("DELETE FROM ioc_outpatient_daily WHERE id = " . $id);
						$flash = array('type' => 'success', 'message' => 'Đã xóa bản ghi ngoại trú.');
					} else {
						$flash = array('type' => 'error', 'message' => 'Không xác định được bản ghi cần xóa.');
					}
				} elseif ($action === 'import') {
					$result = $this->importOutpatientFile(isset($_FILES['import_file']) ? $_FILES['import_file'] : null);
					$flash = array('type' => $result['ok'] ? 'success' : 'error', 'message' => $result['message']);
				}
			}
		}

		$where = array('1=1');
		if (!empty($_GET['from_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['from_date'])) {
			$where[] = "o.report_date >= '" . $db->escapestring($_GET['from_date']) . " 00:00:00'";
		}
		if (!empty($_GET['to_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['to_date'])) {
			$where[] = "o.report_date <= '" . $db->escapestring($_GET['to_date']) . " 23:59:59'";
		}
		if (!empty($_GET['department_id'])) {
			$where[] = 'o.department_id = ' . (int) $_GET['department_id'];
		}
		if (!empty($_GET['payer_type_id'])) {
			$where[] = 'o.payer_type_id = ' . (int) $_GET['payer_type_id'];
		}

		$db->query("SELECT o.*, d.department_code, d.department_name, p.payer_type_name
			FROM ioc_outpatient_daily o
			INNER JOIN ioc_departments d ON d.id = o.department_id
			INNER JOIN ioc_payer_types p ON p.id = o.payer_type_id
			WHERE " . implode(' AND ', $where) . " ORDER BY o.report_date DESC, d.department_name, p.payer_type_sort_order");
		$records = $db->fetch_object();
		$db->query("SELECT id, department_code, department_name FROM ioc_departments WHERE department_status = 1 ORDER BY department_name");
		$departments = $db->fetch_object();
		$db->query("SELECT id, payer_type_code, payer_type_name FROM ioc_payer_types WHERE payer_type_status = 1 ORDER BY payer_type_sort_order, payer_type_name");
		$payerTypes = $db->fetch_object();

		$this->view->data['records'] = is_array($records) ? $records : array();
		$this->view->data['departments'] = is_array($departments) ? $departments : array();
		$this->view->data['payerTypes'] = is_array($payerTypes) ? $payerTypes : array();
		$this->view->data['flash'] = $flash;
		$this->view->data['csrf'] = $_SESSION['outpatient_csrf'];
		$this->view->admintmp('outpatient');
	}

	private function saveOutpatientRecord($input)
	{
		global $db;
		$date = isset($input['report_date']) ? trim($input['report_date']) : '';
		$departmentId = isset($input['department_id']) ? (int) $input['department_id'] : 0;
		$payerTypeId = isset($input['payer_type_id']) ? (int) $input['payer_type_id'] : 0;
		if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !checkdate((int) substr($date, 5, 2), (int) substr($date, 8, 2), (int) substr($date, 0, 4)) || !$departmentId || !$payerTypeId) {
			return array('ok' => false, 'message' => 'Vui lòng chọn ngày, khoa/phòng và đối tượng thanh toán hợp lệ.');
		}
		$db->query("SELECT (SELECT COUNT(*) FROM ioc_departments WHERE id = $departmentId AND department_status = 1) AS department_ok, (SELECT COUNT(*) FROM ioc_payer_types WHERE id = $payerTypeId AND payer_type_status = 1) AS payer_ok");
		$references = $db->fetch_object(true);
		if (!$references || !$references->department_ok || !$references->payer_ok) {
			return array('ok' => false, 'message' => 'Khoa/phòng hoặc đối tượng thanh toán không còn hợp lệ.');
		}
		$columns = array('visit_count', 'revisit_count', 'waiting_count', 'examining_count', 'completed_count', 'referral_count');
		$values = array();
		foreach ($columns as $column) {
			$value = isset($input[$column]) ? filter_var($input[$column], FILTER_VALIDATE_INT) : 0;
			if ($value === false || $value < 0) return array('ok' => false, 'message' => 'Các chỉ tiêu số lượng phải là số nguyên không âm.');
			$values[$column] = (int) $value;
		}
		$wait = isset($input['avg_wait_minutes']) && $input['avg_wait_minutes'] !== '' ? filter_var($input['avg_wait_minutes'], FILTER_VALIDATE_FLOAT) : null;
		if ($wait !== null && ($wait === false || $wait < 0)) return array('ok' => false, 'message' => 'Thời gian chờ trung bình phải là số không âm.');
		$id = isset($input['id']) ? (int) $input['id'] : 0;
		$set = "report_date = '" . $db->escapestring($date . ' 00:00:00') . "', department_id = $departmentId, payer_type_id = $payerTypeId";
		foreach ($values as $column => $value) $set .= ", $column = $value";
		$set .= ', avg_wait_minutes = ' . ($wait === null ? 'NULL' : number_format((float) $wait, 2, '.', ''));
		if ($id > 0) {
			$db->query("UPDATE ioc_outpatient_daily SET $set WHERE id = $id");
			return array('ok' => true, 'message' => 'Đã cập nhật số liệu ngoại trú.');
		}
		$db->query("INSERT INTO ioc_outpatient_daily SET $set");
		return array('ok' => true, 'message' => 'Đã ghi nhận số liệu ngoại trú.');
	}

	private function importOutpatientFile($file)
	{
		if (!$file || $file['error'] !== UPLOAD_ERR_OK || $file['size'] <= 0) return array('ok' => false, 'message' => 'Vui lòng chọn tệp Excel (.xlsx) hoặc CSV hợp lệ.');
		if ($file['size'] > 5 * 1024 * 1024) return array('ok' => false, 'message' => 'Tệp import không được lớn hơn 5 MB.');
		$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		if (!in_array($extension, array('csv', 'xlsx'), true)) return array('ok' => false, 'message' => 'Chỉ hỗ trợ tệp .xlsx hoặc .csv.');
		$rows = $extension === 'csv' ? $this->readOutpatientCsv($file['tmp_name']) : $this->readOutpatientXlsx($file['tmp_name']);
		if (!$rows || count($rows) < 2) return array('ok' => false, 'message' => 'Tệp chưa có dữ liệu để import.');
		$headers = array_map(array($this, 'normalizeImportHeader'), array_shift($rows));
		$required = array('report_date', 'department_id', 'payer_type_id');
		foreach ($required as $field) if (!in_array($field, $headers, true)) return array('ok' => false, 'message' => 'Thiếu cột bắt buộc: ' . $field . '.');
		$success = 0; $errors = array();
		foreach ($rows as $line => $row) {
			if (!array_filter($row, function ($value) { return trim((string) $value) !== ''; })) continue;
			$input = array_combine($headers, array_pad($row, count($headers), ''));
			$input['report_date'] = $this->normalizeImportDate(isset($input['report_date']) ? $input['report_date'] : '');
			$input['department_id'] = $this->resolveImportReference('ioc_departments', 'department_name', 'department_code', $input['department_id']);
			$input['payer_type_id'] = $this->resolveImportReference('ioc_payer_types', 'payer_type_name', 'payer_type_code', $input['payer_type_id']);
			$result = $this->saveOutpatientRecord($input);
			if ($result['ok']) $success++; else $errors[] = 'dòng ' . ($line + 2) . ': ' . $result['message'];
		}
		$message = "Đã import $success bản ghi.";
		if ($errors) $message .= ' Bỏ qua ' . count($errors) . ' dòng (' . implode('; ', array_slice($errors, 0, 3)) . ').';
		return array('ok' => $success > 0, 'message' => $message);
	}

	private function readOutpatientCsv($path) {
		$handle = fopen($path, 'r'); if (!$handle) return array();
		$rows = array(); while (($row = fgetcsv($handle, 0, ',')) !== false) $rows[] = array_map(function ($v) { return preg_replace('/^\xEF\xBB\xBF/', '', trim($v)); }, $row); fclose($handle); return $rows;
	}

	private function readOutpatientXlsx($path) {
		if (!class_exists('ZipArchive')) return array(); $zip = new ZipArchive(); if ($zip->open($path) !== true) return array();
		$shared = array(); $xml = $zip->getFromName('xl/sharedStrings.xml');
		if ($xml) { $doc = simplexml_load_string($xml); foreach ($doc->si as $item) $shared[] = (string) $item->t ?: implode('', $item->r->t); }
		$sheet = $zip->getFromName('xl/worksheets/sheet1.xml'); $zip->close(); if (!$sheet) return array();
		$doc = simplexml_load_string($sheet); $doc->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main'); $rows = array();
		foreach ($doc->xpath('//x:sheetData/x:row') as $row) { $values = array(); $index = 0; foreach ($row->c as $cell) { $ref = preg_replace('/\d+/', '', (string) $cell['r']); $cellIndex = 0; foreach (str_split($ref) as $letter) $cellIndex = $cellIndex * 26 + (ord($letter) - 64); while ($index < $cellIndex - 1) { $values[] = ''; $index++; } $raw = (string) $cell->v; $values[] = (string) $cell['t'] === 's' && isset($shared[(int) $raw]) ? $shared[(int) $raw] : $raw; $index++; } $rows[] = $values; }
		return $rows;
	}

	private function normalizeImportHeader($header) {
		$header = strtolower(trim((string) $header));
		$map = array('ngay' => 'report_date', 'ngay_bao_cao' => 'report_date', 'report_date' => 'report_date', 'khoa_phong' => 'department_id', 'department' => 'department_id', 'department_id' => 'department_id', 'doi_tuong_thanh_toan' => 'payer_type_id', 'payer_type' => 'payer_type_id', 'payer_type_id' => 'payer_type_id', 'luot_kham' => 'visit_count', 'visit_count' => 'visit_count', 'tai_kham' => 'revisit_count', 'revisit_count' => 'revisit_count', 'dang_cho' => 'waiting_count', 'waiting_count' => 'waiting_count', 'dang_kham' => 'examining_count', 'examining_count' => 'examining_count', 'hoan_thanh' => 'completed_count', 'completed_count' => 'completed_count', 'thoi_gian_cho_tb' => 'avg_wait_minutes', 'avg_wait_minutes' => 'avg_wait_minutes', 'chuyen_tuyen' => 'referral_count', 'referral_count' => 'referral_count');
		$ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $header); $ascii = preg_replace('/[^a-z0-9]+/', '_', $ascii); return isset($map[trim($ascii, '_')]) ? $map[trim($ascii, '_')] : trim($ascii, '_');
	}

	private function normalizeImportDate($value) { if (is_numeric($value) && $value > 25569) return gmdate('Y-m-d', ((int) $value - 25569) * 86400); $time = strtotime(str_replace('/', '-', trim((string) $value))); return $time ? date('Y-m-d', $time) : ''; }
	private function resolveImportReference($table, $nameColumn, $codeColumn, $value) { global $db; if (is_numeric($value)) return (int) $value; $value = $db->escapestring(trim((string) $value)); $db->query("SELECT id FROM $table WHERE $nameColumn = '$value' OR $codeColumn = '$value' LIMIT 1"); $row = $db->fetch_object(true); return $row ? (int) $row->id : 0; }
	public function login()
	{
		
		$this->view->show("backend/login");
	}
	
	public function logout(){
		session_unset();
		header('Location:' .XC_URL. '/admin/login');
	}
	public function users($para)
	{
		
		global $db;
		if(!(isset($_SESSION['user']['id']) && $_SESSION['user']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
		if(isset($para[1]) && $para[1] == "detail" ){
			$id = $para[2];
			// print_r('aaa');
			$db->query("SELECT *, u.id as uid FROM hicrm_users as u 
					LEFT JOIN hicrm_status as s ON u.user_status = s.id
					LEFT JOIN hicrm_user_groups as g ON u.user_group = g.id
					LEFT JOIN hicrm_departments as d ON u.user_dept = d.id
					WHERE u.id = '".$id."'");
			$user = $db->fetch_object(true);		
			$this->view->data['user'] = $user;
			$this->view->show('backend/user-detail');
		}else{
			$db->query("SELECT *, u.id as uid FROM hicrm_users as u 
						LEFT JOIN hicrm_status as s ON u.user_status = s.id
						LEFT JOIN hicrm_user_groups as g ON u.user_group = g.id
						LEFT JOIN hicrm_departments as d ON u.user_dept = d.id
						WHERE u.user_status NOT IN(99)
						");
			$users = $db->fetch_object();
			$db->query("SELECT * FROM hicrm_departments");
			$departments = $db->fetch_object();
			$db->query("SELECT * FROM hicrm_user_groups");
			$user_group = $db->fetch_object();
			$this->view->data['positions'] = $positions;
			$this->view->data['departments'] = $departments;
			$this->view->data['user_group'] = $user_group;
			$this->view->data['users'] = $users;
			$this->view->show("backend/users");
		}
	}
	public function editusers($para){
		$id = $para[1];
		if(!(isset($_SESSION['user']['id']) && $_SESSION['user']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
		global $db;
		$db->query("SELECT *, u.id as uid FROM hicrm_users as u 
					LEFT JOIN hicrm_status as s ON u.user_status = s.id
					LEFT JOIN hicrm_user_groups as g ON u.user_group = g.id
					LEFT JOIN hicrm_departments as d ON u.user_dept = d.id
					 WHERE u.user_status NOT IN(99) and u.id = '$id'
					");
		$user = $db->fetch_object(true);
		$db->query('SELECT * FROM hicrm_positions');
		$positions = $db->fetch_object();
		$db->query("SELECT * FROM hicrm_departments");
		$departments = $db->fetch_object();
		$db->query("SELECT * FROM hicrm_user_groups");
		$user_group = $db->fetch_object();
		$this->view->data['departments'] = $departments;
		$this->view->data['user_group'] = $user_group;
		$this->view->data['user'] = $user;
		$this->view->show('backend/edit-user');
	}
	
	//add customers
	public function addusers()
	{	
		global $db;
		if(!(isset($_SESSION['user']['id']) && $_SESSION['user']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
		$db->query("SELECT * FROM hicrm_customers ORDER BY id DESC LIMIT 1");
		$lastno = $db->fetch_object(true)->customer_code;
		$prefix = $this->helper->get_config("customer_prefix");
		//PREFIX1234567
		$lastno = substr($lastno,-7);
		$lastno = $lastno+1;
		$lastno = $prefix."".str_pad($lastno, 7, '0', STR_PAD_LEFT);
		//Ma NV
		$db->query("SELECT * FROM hicrm_employees ORDER BY id DESC LIMIT 1");
		$lastno2 = $db->fetch_object(true)->employee_code;
		$prefix_employee = $this->helper->get_config("employee_prefix");
		//PREFIX1234567
		$lastno2 = substr($lastno2,-7);
		$lastno2 = $lastno2+1;
		$lastno2 = $prefix_employee."".str_pad($lastno2, 7, '0', STR_PAD_LEFT);
		
		$db->query("SELECT * FROM hicrm_employees");
		$customer_staff = $db->fetch_object();
		$db->query('SELECT * FROM hicrm_branchs ORDER BY id ASC');
		$branches = $db->fetch_object();
		$db->query('SELECT * FROM hicrm_positions');
		$positions = $db->fetch_object();
		$db->query("SELECT * FROM hicrm_departments");
		$departments = $db->fetch_object();
		$this->view->data['departments'] = $departments;
		$this->view->data['positions'] = $positions;
		$this->view->data['branches'] = $branches; 
		$this->view->data['customer_staff'] = $customer_staff;
		$this->view->data["customer_code"] = $lastno;
		$this->view->data["employee_code"] = $lastno2;
		$this->view->show("backend/add-users");
	}
	//end
	public function gioithieu($para){
		$id = $para[1];
		if(!(isset($_SESSION['user']['id']) && $_SESSION['user']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
		global $db;
		// $db->query("SELECT *, t.id as tid FROM hicrm_type as t 
		// 			LEFT JOIN hicrm_status as s ON u.user_status = s.id
		// 			LEFT JOIN hicrm_user_groups as g ON u.user_group = g.id
		// 			LEFT JOIN hicrm_departments as d ON u.user_dept = d.id
		// 			 WHERE u.user_status NOT IN(99) and u.id = '$id'
		// 			");
		$db->query("SELECT *, i.id as iid FROM hicrm_introduce  as i
					LEFT JOIN hicrm_categories as c ON i.introduce_id_type = c.id
					where i.introduce_id_type = '".$id."' " );
		$introduce = $db->fetch_object(true);
		$db->query("SELECT * FROM hicrm_categories WHERE category_status NOT IN (99) AND category_parent = 1");
		$category = $db->fetch_object();
		$this->view->data['id'] = $id;
		$this->view->data['introduce'] = $introduce;
		$this->view->data['category'] = $category;
		$this->view->show('backend/gioithieu');
	}
	public function dmType(){
		global $db;
		if(!(isset($_SESSION['user']['id']) && $_SESSION['user']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
		$db->query("SELECT *, t.id as tid FROM hicrm_type as t 
					LEFT JOIN hicrm_dmtype as dmt ON t.type_detail = dmt.id
					 WHERE t.type_status NOT IN(99)");
		$type = $db->fetch_object();
		$db->query("SELECT * FROM hicrm_dmtype");
		$dmtype = $db->fetch_object();
		$this->view->data["type"] = $type;
		$this->view->data["dmtype"] = $dmtype;
		$this->view->show("backend/dmtype");
	}
	public function dmimages(){
		global $db;
		if(!(isset($_SESSION['user']['id']) && $_SESSION['user']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
		$db->query("SELECT *, i.id as imageid FROM hicrm_images as i 
					LEFT JOIN hicrm_status as s ON i.image_status = s.id
					LEFT JOIN hicrm_users as u ON i.image_user_created = u.id
					 WHERE i.image_status NOT IN(99) ORDER BY i.image_created_date DESC");
		$images = $db->fetch_object();
		$this->view->data["images"] = $images;
		$this->view->show("backend/dmimage");
	}
	public function bookings(){
		global $db;
		if(!(isset($_SESSION['user']['id']) && $_SESSION['user']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
		$db->query("SELECT *, b.id as ibk FROM hicrm_bookings as b 
					LEFT JOIN hicrm_booking_status as bs ON b.booking_status = bs.id
					LEFT JOIN hicrm_employees as e ON b.booking_doctor = e.id
					ORDER BY b.booking_created_date DESC");
		$bookings = $db->fetch_object();
		$this->view->data["bookings"] = $bookings;
		$this->view->show("backend/bookings");
	}
	// public function news(){
	// 	global $db;
	// 	if(!(isset($_SESSION['user']['id']) && $_SESSION['user']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
	// 	$db->query("SELECT *, t.id as tid FROM hicrm_type as t 
	// 				LEFT JOIN hicrm_dmtype as dmt ON t.type_detail = dmt.id
	// 				 WHERE t.type_status NOT IN(99)");
	// 	$type = $db->fetch_object();
	// 	$db->query("SELECT * FROM hicrm_dmtype");
	// 	$dmtype = $db->fetch_object();
	// 	$this->view->data["type"] = $type;
	// 	$this->view->data["dmtype"] = $dmtype;
	// 	$this->view->show("backend/dmnews");
	// }
	public function news($para){
		global $db;
		$method = $para[1];
		$id = $para[2];
		// echo $id;
		if(!(isset($_SESSION['user']['id']) && $_SESSION['user']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
		$db->query("SELECT *, n.id as nid FROM hicrm_news as n
					LEFT JOIN hicrm_users as u ON n.new_user_created = u.id
					LEFT JOIN hicrm_type as t ON n.new_type = t.type_detail WHERE new_status NOT IN (99) ORDER BY n.new_created_date DESC
					");
		$news = $db->fetch_object();
		$db->query("SELECT * FROM hicrm_dmtype");
		if(isset($method) && $method == 'add'){
			$this->view->data['method'] = 'add';
			
			$this->view->show("backend/new-add");


		}elseif(isset($method) && $method == 'edit'){
			$db->query("SELECT * FROM hicrm_news WHERE id = '".$id."'");		
			$new_detai = $db->fetch_object(true);
			// echo $new_detai->new_name;
			$this->view->data['new_detail'] = $new_detai;
			$this->view->data['method'] = 'edit';
			$this->view->show("backend/new-add");
		}elseif(isset($method) && $method == 'detail'){
			$db->query("SELECT * FROM hicrm_news WHERE id = '".$id."'");		
			$new_detai = $db->fetch_object(true);
			// echo $new_detai->new_name;
			$this->view->data['new_detail'] = $new_detai;
			$this->view->data['method'] = 'edit';
			$this->view->show("backend/new-detail");
		}
		else{
		$dmtype = $db->fetch_object();
		$this->view->data["news"] = $news;
		$this->view->data["dmtype"] = $dmtype;
		$this->view->show("backend/news");
		}
	}
	public function events($para){
		global $db;
		$method = $para[1];
		$id = $para[2];
		// echo $id;
		if(!(isset($_SESSION['user']['id']) && $_SESSION['user']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
		$db->query("SELECT *, e.id as eid FROM hicrm_events as e
					LEFT JOIN hicrm_users as u ON e.event_user_created = u.id
					LEFT JOIN hicrm_categories as c ON e.event_type = c.id WHERE e.event_status NOT IN (99) ORDER BY e.event_created_date DESC
					");
		$events = $db->fetch_object();
		// $db->query("SELECT * FROM hicrm_dmtype");
		if(isset($method) && $method == 'add'){
			$this->view->data['method'] = 'add';
			$this->view->show("backend/event-add");


		}elseif(isset($method) && $method == 'edit'){
			$db->query("SELECT * FROM hicrm_events WHERE id = '".$id."'");		
			$event_detai = $db->fetch_object(true);
			// echo $event_detai->event_name;
			$this->view->data['event_detail'] = $event_detai;
			$this->view->data['method'] = 'edit';
			$this->view->show("backend/event-add");
		}elseif(isset($method) && $method == 'detail'){
			$db->query("SELECT * FROM hicrm_events WHERE id = '".$id."'");		
			$event_detai = $db->fetch_object(true);
			// echo $event_detai->event_name;
			$this->view->data['event_detail'] = $event_detai;
			$this->view->data['method'] = 'edit';
			$this->view->show("backend/event-detail");
		}
		else{
		$dmtype = $db->fetch_object();
		$this->view->data["events"] = $events;
		// $this->view->data["dmtype"] = $dmtype;
		$this->view->show("backend/events");
		}
	}
	public function products($para)
	{
		if(!(isset($_SESSION['user']['id']) && $_SESSION['user']['id'] != "")){ header("Location: ".XC_URL."/login"); }
		global $db;
		
		if(isset($para) && $para[1] == "new")
		{
			$db->query("SELECT * FROM hicrm_units WHERE unit_status NOT IN (99)");
			$this->view->data["units"] = $db->fetch_object();
			$db->query("SELECT * FROM hicrm_product_categories");
			$this->view->data["product_categories"] = $db->fetch_object();
			$this->view->data['method'] = $para[1];
			$this->view->show("backend/product_action");
		}elseif(isset($para) && $para[1] == "update"){
			$id = $para[2];
			$db->query("SELECT * FROM hicrm_units WHERE unit_status NOT IN (99)");
			$this->view->data["units"] = $db->fetch_object();
			$db->query("SELECT * FROM hicrm_product_categories");
			$this->view->data["product_categories"] = $db->fetch_object();
			$db->query("SELECT *, p.id as pid FROM hicrm_products as p
			LEFT JOIN hicrm_product_categories as c ON p.product_category = c.id
			LEFT JOIN hicrm_units as u ON p.product_unit = u.id WHERE p.id = '".$id."'");
			$this->view->data["product"] = $db->fetch_object(true);
			$this->view->data['method'] = $para[1];
			
			$this->view->show("backend/product_action");
		}
		elseif(isset($para) && $para[1] == "detail")
		{
			
		}
		else
		{
			$db->query("SELECT *, p.id as pid FROM hicrm_products as p
			LEFT JOIN hicrm_product_categories as c ON p.product_category = c.id
			LEFT JOIN hicrm_units as u ON p.product_unit = u.id
			LEFT JOIN hicrm_taxs as t ON p.product_tax_id = t.id
			ORDER BY p.id DESC
			");
			$this->view->data["products"] = $db->fetch_object();
			$this->view->data["active_menu"] = "products";
			$this->view->data["pagetitle"] = "Danh sách sản phẩm";
			$this->view->show("backend/products");
			//Danh sách sản phẩm
		}
	}
	public function service($para){
		if(!(isset($_SESSION['user']['id']) && $_SESSION['user']['id'] != "")){ header("Location: ".XC_URL."/login"); }
		global $db;
		if(isset($para) && $para[2] == "add"){
			$db->query("SELECT * FROM hicrm_categories WHERE id = '".$para[1]."'");
			$nameCategory = $db->fetch_object(true)->category_name;
			$this->view->data['category_name'] = $nameCategory;
			$this->view->data['service_category'] = $para[1];
			$this->view->data['method'] = 'add';
			$this->view->show('backend/service_action');
		}elseif(isset($para) && $para[2] == "edit"){
			$db->query("SELECT * FROM hicrm_service WHERE id = '".$para[3]."'");
			$service_detail = $db->fetch_object(true);
			$db->query("SELECT * FROM hicrm_categories WHERE id = '".$para[1]."'");
			$nameCategory = $db->fetch_object(true)->category_name;
			$this->view->data['category_name'] = $nameCategory;
			$this->view->data['service_detail'] = $service_detail;

			$this->view->data['service_category'] = $para[1];
			$this->view->data['service_id'] = $para[3];
			$this->view->data['method'] = 'edit';
			$this->view->show('backend/service_action');

		}elseif(isset($para) && $para[2] == "detail"){
			$db->query("SELECT * FROM hicrm_service WHERE id = '".$para[3]."'");
			$service_detail = $db->fetch_object(true);
			$this->view->data['service_detail'] = $service_detail;
			$this->view->data['id'] = $para[1];
			$this->view->show('backend/service_detail');
			
		}else{
			$db->query("SELECT * FROM hicrm_categories WHERE id = '".$para[1]."'");
			$nameCategory = $db->fetch_object(true)->category_name;
			$db->query("SELECT *, s.id as sid FROM hicrm_service as s
						LEFT JOIN hicrm_categories as c ON s.service_category = c.id WHERE c.id = '".$para[1]."'AND s.service_status NOT IN(99) ORDER BY s.service_created_date DESC
			");
			$services = $db->fetch_object();
			$this->view->data['category_name'] = $nameCategory;
			$this->view->data['id'] = $para[1];
			$this->view->data['services'] = $services;
			$this->view->show("backend/service");
		}

		
	}
	public function lichcongtac($para){
		if(!(isset($_SESSION['user']['id']) && $_SESSION['user']['id'] != "")){ header("Location: ".XC_URL."/login"); }
		global $db;
		if(isset($para) && $para[1] == "add"){
			$db->query("SELECT * FROM hicrm_calendar_works WHERE id = '".$para[1]."'");
			$calendar_work_name = $db->fetch_object(true)->calendar_work_name;
			$this->view->data['calendar_work_name'] = $calendar_work_name;
			$this->view->data['method'] = 'add';
			$this->view->show('backend/lichcongtac_action');
		}elseif(isset($para) && $para[1] == "edit"){
			$db->query("SELECT *, w.id as wid FROM hicrm_calendar_works as w
						LEFT JOIN hicrm_users as u ON w.calendar_work_user_created = u.id
			 WHERE w.id = '".$para[2]."'");
			$calendar_work_detail = $db->fetch_object(true);
			$this->view->data['calendar_work_detail'] = $calendar_work_detail;
			$this->view->data['id'] = $para[1];
			$this->view->data['method'] = 'edit';
			$this->view->show('backend/lichcongtac_action');
		}else{
			$db->query("SELECT *, w.id as wid FROM hicrm_calendar_works as w
						LEFT JOIN hicrm_users as u ON w.calendar_work_user_created = u.id ORDER BY w.calendar_work_created_date DESC
			 ");
			$calendar_work = $db->fetch_object();
			$this->view->data['calendar_work'] = $calendar_work;
			$this->view->data['id'] = $para[1];
			$this->view->show("backend/lichcongtac");
		}

		
	}
	public function categories($para)
	{
		if(!(isset($_SESSION['user']['id']) && $_SESSION['user']['id'] != "")){ header("Location: ".XC_URL."/login"); }
		// echo $para[1];
		if(isset($para[1]) && $para[1] != "")
		{
			global $db;
			switch($para[1])
			{
				case "users":
				{
					$db->query("SELECT *, u.id as uid FROM hicrm_users as u 
					LEFT JOIN hicrm_status as s ON u.user_status = s.id
					LEFT JOIN hicrm_user_groups as g ON u.user_group = g.id WHERE u.user_status NOT IN(99)
					");
					$this->view->data["users"] = $db->fetch_object();
					$page = "category_users";
					$title = "Quản lý người dùng";
					break;
				}
				case "accounts":
				{
					$db->query("SELECT *, a.id as aid FROM hicrm_accounts as a
					LEFT JOIN hicrm_status as s ON a.account_status = s.id
					WHERE a.account_status NOT IN (99) ORDER BY a.id ASC");
					$accounts = $db->fetch_object();
					$this->view->data['accounts'] = $accounts;
					$page = "category_accounts";
					$title = "Hệ thống tài khoản";
					break;
				}
				
				case "departments":
				{
					$db->query("SELECT * FROM hicrm_departments WHERE depart_status NOT IN(99) ORDER BY id DESC");
					$this->view->data["departments"] = $db->fetch_object();
					$page = "category_departments";
					$active_menu = "category_departments";
					$title = "Danh sách phòng ban";
					break;
				}
				
				case "units":
				{
					$db->query("SELECT *, un.id as unid FROM hicrm_units as un
					LEFT JOIN hicrm_status as st ON un.unit_status = st.id WHERE un.unit_status NOT IN(99) ORDER BY un.id ASC");
					$this->view->data["units"] = $db->fetch_object();
					$page = "category_units";
					$title = "Danh sách đơn vị tính ";
					break;
				}
				
				case "products":
				{
					// echo 'sss';
					$db->query("SELECT *, p.id as pid FROM hicrm_product_categories as p
					LEFT JOIN hicrm_status as st ON p.category_status = st.id
					WHERE p.category_status NOT IN(99) ORDER BY p.id ASC");
					$this->view->data["category_products"] = $db->fetch_object();
					$page = "category_products";
					$title = "Danh sách danh mục loại thuốc";
					$add = "Thêm loại thuốc";
					$this->view->data["pagetitle"] = $title;
					$this->view->data["add"] = $add;

					$this->view->show('backend/categories');
					break;
				}
				case "general":
				{
					global $db;
					if(!(isset($_SESSION['user']['id']) && $_SESSION['user']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
					$db->query("SELECT *, c.id as cid FROM hicrm_categories as c
								LEFT JOIN hicrm_category_parent as cp ON c.category_parent = cp.id
								WHERE c.category_status NOT IN(99)");
					$category = $db->fetch_object();
					$db->query("SELECT * FROM hicrm_category_parent");
					$category_parent = $db->fetch_object();
					$this->view->data['general'] = 'Danh mục cha';
					$this->view->data["category"] = $category;
					$this->view->data["category_parent"] = $category_parent;
					$this->view->show("backend/categories");
					break;
				}
				
				default:
				{
					break;
				}
			}
			$this->view->data["active_menu"] = "categories";
			$this->view->data["pagetitle"] = $title;
			// $this->view->data["backend/categories"] = $page;
			$this->view->show('categories');
		}
		else
		{
			header("Location: ".XC_URL."/admin");
		}
	}
	
	public function profile(){
		$model_user = $this->model->get('user');
		$get_user = $model_user -> get_user($_SESSION['user']['id']);
		$this->view->data['user'] = $get_user;
		$this->view->show('profile');
	}
	public function setting(){
		//echo $para[1];
		$type = $_GET['type'];
		$model_user = $this->model->get('user');
		$get_user = $model_user -> get_user($_SESSION['user']['id']);
		$this->view->data['user'] = $get_user;
		$this->view->data['type'] = $type;
		
		$this->view->show('backend/settings');
	}
	
	public function employees($para = ''){
		
		global $db;
		//Ma NV
		$db->query("SELECT * FROM hicrm_employees ORDER BY id DESC LIMIT 1");
		$lastno2 = $db->fetch_object(true)->employee_code;
		$prefix_employee = $this->helper->get_config("employee_prefix");
		//PREFIX1234567
		$lastno2 = substr($lastno2,-7);
		$lastno2 = $lastno2+1;
		$lastno2 = $prefix_employee."".str_pad($lastno2, 7, '0', STR_PAD_LEFT);
		if(isset($para[1]) && $para[1] == "detail" ){
		$id = $para[2];
		$db->query("SELECT *, e.id as employeeid FROM hicrm_employees as e 
		LEFT JOIN hicrm_branchs as b ON e.employee_branch = b.id 
		LEFT JOIN hicrm_departments as d ON e.employee_department = d.id
		LEFT JOIN hicrm_positions as p ON e.employee_position = p.id
		WHERE e.id = '".$id."'");
		$employee = $db->fetch_object(true);
		
		$this->view->data['employee'] = $employee;
		$this->view->show('employee-detail');
		}else{
			$str = '';
			if(isset($_GET['keyword']) && $_GET['keyword'] != ''){
				$keyword = $_GET['keyword'];
				$str .= " AND e.employee_name LIKE '%".$keyword."%' OR e.employee_code = '".$keyword."' ";
			}
			
			$db->query("SELECT *, e.id as employeeid FROM hicrm_employees as e 
			LEFT JOIN hicrm_branchs as b ON e.employee_branch = b.id 
			LEFT JOIN hicrm_departments as d ON e.employee_department = d.id
			LEFT JOIN hicrm_positions as p ON e.employee_position = p.id
			WHERE e.employee_status = '1' ".$str." ORDER BY e.id ASC");
			$employees = $db->fetch_object();
			//print_r($employees);
			$db->query('SELECT * FROM hicrm_branchs ORDER BY id ASC');
			$branches = $db->fetch_object();
			$db->query('SELECT * FROM hicrm_positions');
			$positions = $db->fetch_object();
			$db->query("SELECT * FROM hicrm_departments");
			$departments = $db->fetch_object();
			$this->view->data["employee_code"] = $lastno2;
			$this->view->data['employees'] = $employees;
			$this->view->data['branches'] = $branches;
			$this->view->data['positions'] = $positions;
			$this->view->data['departments'] = $departments;
			$this->view->show('employees');
		}
	}
	public function editEmployee_($para){
		$id = $para[1];
		global $db;
		$db->query("SELECT *, e.id as employeeid FROM hicrm_employees as e 
		LEFT JOIN hicrm_branchs as b ON e.employee_branch = b.id 
		LEFT JOIN hicrm_departments as d ON e.employee_department = d.id
		LEFT JOIN hicrm_positions as p ON e.employee_position = p.id
		WHERE e.id = '".$id."'");
		$employee = $db->fetch_object(true);
		$db->query('SELECT * FROM hicrm_branchs ORDER BY id ASC');
		$branches = $db->fetch_object();
		$db->query('SELECT * FROM hicrm_positions');
		$positions = $db->fetch_object();
		$db->query("SELECT * FROM hicrm_departments");
		$departments = $db->fetch_object();
			$db->query("SELECT * FROM hicrm_departments");
			$departments = $db->fetch_object();
		$this->view->data['employees'] = $employees;
		$this->view->data['branches'] = $branches;
		$this->view->data['positions'] = $positions;
		$this->view->data['employee'] = $employee;
		$this->view->data['departments'] = $departments;
		$this->view->show('edit-employee');
	}
	public function editEmployee($para){
		$id = $para[1];
		global $db;
		$db->query("SELECT *, e.id as employeeid FROM hicrm_employees as e 
		LEFT JOIN hicrm_branchs as b ON e.employee_branch = b.id 
		LEFT JOIN hicrm_departments as d ON e.employee_department = d.id
		LEFT JOIN hicrm_positions as p ON e.employee_position = p.id
		WHERE e.id = '".$id."'");
		$employee = $db->fetch_object(true);
		$db->query('SELECT * FROM hicrm_branchs ORDER BY id ASC');
		$branches = $db->fetch_object();
		$db->query('SELECT * FROM hicrm_positions');
		$positions = $db->fetch_object();
		$db->query("SELECT * FROM hicrm_departments");
		$departments = $db->fetch_object();
			$db->query("SELECT * FROM hicrm_departments");
			$departments = $db->fetch_object();
		$this->view->data['employees'] = $employees;
		$this->view->data['branches'] = $branches;
		$this->view->data['positions'] = $positions;
		$this->view->data['employee'] = $employee;
		$this->view->data['departments'] = $departments;
		$this->view->show('backend/edit-employee');
	}
	
	//end account
	private function countorderbydate($date)
	{
		global $db;
		$sqlorder = ($_SESSION['staff']['group'] != 1)? "AND order_staff = '".$_SESSION['staff']['id']."'" : "";
		$db->query("SELECT count(*) as countorder FROM ow_orders WHERE date(order_time) = '".date("Y-m-d",strtotime($date))."' ".$sqlorder);
		return $db->fetch_object(true)->countorder;
	}
	private function countorderbydatedeposited($date)
	{
		global $db;
		$sqlorder = ($_SESSION['staff']['group'] != 1)? "AND order_staff = '".$_SESSION['staff']['id']."'" : "";
		$db->query("SELECT count(*) as countorder FROM ow_orders WHERE order_status > 1 AND date(order_time) = '".date("Y-m-d",strtotime($date))."' ".$sqlorder);
		return $db->fetch_object(true)->countorder;
	}
	public function config()
    {
		if(!(isset($_SESSION['staff']['id']) && $_SESSION['staff']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
		if(isset($_POST['updatekey']) && $_POST['updatekey'] != "")
		{
			global $db;
			$listkey = explode(',',$_POST['updatekey']);
			for($i = 0;$i< count($listkey);$i++)
			{
				$db->query("SELECT id FROM ow_configs WHERE config_key = '".$listkey[$i]."'");
				if($db->num_row())
				{
					$db->query("UPDATE ow_configs SET config_value = '".$_POST[$listkey[$i]]."' WHERE config_key = '".$listkey[$i]."'");
				}
				else
				{
					$db->query("INSERT INTO ow_configs(config_key,config_value) VALUES('".$listkey[$i]."','".$_POST[$listkey[$i]]."')");
				}
			}
			header("Location: ".XC_URL."/admin/config");
		}
		else
		{
			$this->view->show("config");
		}
		
    }
	
	public function agency($para)
	{
		if(!(isset($_SESSION['staff']['id']) && $_SESSION['staff']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
		global $db;
		if(isset($para[1]) && $para[1] != "")
		{
			$db->query("SELECT *, (SELECT count(id) FROM ow_orders WHERE uid = u.id) as countpost, (SELECT sum(order_total) FROM ow_orders WHERE uid = u.id) as sumorder, (SELECT count(id) FROM ow_users WHERE user_referal = u.id) as countreferal,u.id as uid FROM ow_users as u
			LEFT JOIN ow_agency as a ON u.id = a.uid
			LEFT JOIN ow_agency_level as al ON a.agent_level = al.id
			LEFT JOIN ow_customer_address as ud ON u.id = ud.uid
			LEFT JOIN ow_wards as w ON ud.wardid = w.id
			LEFT JOIN ow_districts as d ON ud.districtid = d.id
			LEFT JOIN ow_provinces as p ON ud.provinceid = p.id
			WHERE u.id = '".$para[1]."'
			ORDER BY user_created_time DESC");
			if($db->num_row())
			{
				$this->view->data["user"] = $user = $db->fetch_object(true);
				$spp = 10;
				$page = 1;
				if(isset($_GET['page']) && $_GET['page'] != "")
				{
					$page = $_GET['page'];
				}
				$cp = $page - 1;
				$db->query("SELECT * FROM ow_orders WHERE uid = '".$user->uid."'");
				$totalsms = $db->num_row();
				$totalpage = $totalsms/$spp;
				$sql = "SELECT *, o.id as oid,staff.user_fullname as staff_fullname FROM ow_orders as o
				LEFT JOIN ow_users as u ON o.uid = u.id
				LEFT JOIN ow_order_status as ot ON o.order_status = ot.id
				LEFT JOIN hicrm_users as staff ON o.order_staff = staff.id
				WHERE o.uid = '".$user->uid."'
				ORDER BY o.order_time DESC LIMIT ".$cp*$spp.",".$spp;
				$db->query($sql);
				
				$this->view->data["orders"] = $db->fetch_object();
				$this->view->data['totalpost'] = $totalsms;
				$this->view->data["page"] = $page;
				$this->view->data["spp"] = $spp;
				$this->view->data['totalpage'] = $totalpage;
				$db->query("SELECT *, (SELECT count(id) FROM ow_orders WHERE uid = u.id) as countreforder FROM ow_users as u 
				WHERE user_referal = '".$user->uid."'");
				$this->view->data["refusers"] = $db->fetch_object();
				$this->view->show("agency_detail");
			}
			else
			{
				header("Location: ".XC_URL."/admin/agency");
			}
		}
		else
		{
			$db->query("SELECT *, (SELECT count(id) FROM ow_orders WHERE uid = u.id) as countpost FROM ow_users as u
			LEFT JOIN ow_agency as a ON u.id = a.uid
			LEFT JOIN ow_agency_level as al ON a.agent_level = al.id
			WHERE u.user_is_agency = 1 OR u.user_is_agency = 2
			ORDER BY user_created_time DESC");
			$this->view->data["users"] = $db->fetch_object();
			$this->view->show("agency");
		}
		
	}
	public function transactions()
	{
		if(!(isset($_SESSION['staff']['id']) && $_SESSION['staff']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
		global $db;
		$db->query("SELECT *, t.id as tid FROM ow_transactions as t
		LEFT JOIN ow_users as u ON t.uid = u.id
		ORDER BY trans_time DESC");
		$this->view->data["transactions"] = $db->fetch_object();
		$db->query("SELECT * FROM ow_users ORDER BY id DESC");
		$this->view->data["users"] = $db->fetch_object();
		$this->view->show("transactions");
	}
	public function deposites()
	{
		if(!(isset($_SESSION['staff']['id']) && $_SESSION['staff']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
		global $db;
		$db->query("SELECT *, t.id as tid FROM ow_transactions as t
		LEFT JOIN ow_users as u ON t.uid = u.id
		WHERE t.trans_type = 1
		ORDER BY trans_time DESC");
		$this->view->data["transactions"] = $db->fetch_object();
		$db->query("SELECT * FROM ow_users ORDER BY id DESC");
		$this->view->data["users"] = $db->fetch_object();
		$this->view->data["page"] = "deposite";
		$this->view->data["title"] = "Danh sách giao dịch nạp tiền";
		$this->view->show("transactions");
	}
	public function withdrawal()
	{
		if(!(isset($_SESSION['staff']['id']) && $_SESSION['staff']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
		global $db;
		$db->query("SELECT *, t.id as tid FROM ow_transactions as t
		LEFT JOIN ow_users as u ON t.uid = u.id
		WHERE t.trans_type = 3
		ORDER BY trans_time DESC");
		$this->view->data["transactions"] = $db->fetch_object();
		$this->view->data["page"] = "withdrawal";
		$this->view->data["title"] = "Danh sách giao dịch rút tiền";
		$this->view->show("transactions");
	}
	public function orders()
	{
		if(!(isset($_SESSION['staff']['id']) && $_SESSION['staff']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
		global $db;
		$query = ($_GET['keyword'] != "")? " AND (u.user_firstname LIKE '%".$_GET['keyword']."%' OR o.order_code = '".$_GET['keyword']."')" : "";
		$spp = 40;
		$page = 1;
		if(isset($_GET['page']) && $_GET['page'] != "")
		{
			$page = $_GET['page'];
		}
		$cp = $page - 1;
		$db->query("SELECT * FROM ow_orders");
		
		$totalsms = $db->num_row();
		$totalpage = $totalsms/$spp;
		if($_SESSION['staff']['group'] == 1)
		{
			$db->query("SELECT *, o.id as oid,staff.user_fullname as staff_fullname FROM ow_orders as o
			LEFT JOIN ow_users as u ON o.uid = u.id
			LEFT JOIN ow_order_status as ot ON o.order_status = ot.id
			LEFT JOIN hicrm_users as staff ON o.order_staff = staff.id
			WHERE o.order_status > 0 ".$query."
			ORDER BY o.order_time DESC LIMIT ".$cp*$spp.",".$spp);
		}
		else
		{
			$db->query("SELECT *, o.id as oid,staff.user_fullname as staff_fullname FROM ow_orders as o
			LEFT JOIN ow_users as u ON o.uid = u.id
			LEFT JOIN ow_order_status as ot ON o.order_status = ot.id
			LEFT JOIN hicrm_users as staff ON o.order_staff = staff.id
			WHERE order_status >0 AND order_staff = '".$_SESSION['staff']['id']."'".$query."
			ORDER BY o.order_time DESC LIMIT ".$cp*$spp.",".$spp);
		}
		$this->view->data["orders"] = $db->fetch_object();
		$this->view->data['totalpost'] = $totalsms;
		$this->view->data["page"] = $page;
		$this->view->data["spp"] = $spp;
		$this->view->data['totalpage'] = $totalpage;
		$this->view->data["page_name"] = "sms_list";
		$this->view->data["page_title"] = "Danh sách SMS";
		$this->view->show("orders");
	}
	public function places()
	{
		if(!(isset($_SESSION['staff']['id']) && $_SESSION['staff']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
		global $db;
		$db->query("SELECT *, p.id as placeid, (SELECT COUNT(*) FROM bds_posts WHERE post_district = p.place_district) as countpost FROM bds_places as p
		LEFT JOIN hicrm_districts as d ON p.place_district = d.id
		LEFT JOIN hicrm_provinces as pr ON p.place_province = pr.id
		");
		$this->view->data["places"] = $db->fetch_object();
		$this->view->show("places");
	}
	public function departments()
	{
		if(!(isset($_SESSION['staff']['id']) && $_SESSION['staff']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
		global $db;
		$db->query("SELECT * FROM hicrm_departments ORDER BY id DESC
		");
		$this->view->data["departments"] = $db->fetch_object();
		$this->view->show("departments");
	}
	public function fees()
	{
		if(!(isset($_SESSION['staff']['id']) && $_SESSION['staff']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
		global $db;
		$db->query("SELECT * FROM ow_fixed_fees ORDER BY fee_min ASC");
		$this->view->data["fees"] = $db->fetch_object();
		$this->view->data["page_name"] = "fees";
		$this->view->show("fees");
	}
	public function projects()
	{
		if(!(isset($_SESSION['staff']['id']) && $_SESSION['staff']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
		global $db;
		$db->query("SELECT * FROM bds_projects ORDER BY id DESC
		");
		$this->view->data["projects"] = $db->fetch_object();
		$this->view->show("project-manager");
	}
	public function provinces()
	{
		if(!(isset($_SESSION['staff']['id']) && $_SESSION['staff']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
		global $db;
		$db->query("SELECT *, (SELECT COUNT(*) FROM bds_posts WHERE post_province = p.id) as countpost FROM hicrm_provinces as p ORDER BY id ASC");
		$this->view->data["provinces"] = $db->fetch_object();
		$this->view->show("province_manager");
	}
	public function menu()
	{
		if(!(isset($_SESSION['staff']['id']) && $_SESSION['staff']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
		global $db;
		$db->query("SELECT * FROM bds_menus ORDER BY menu_order ASC");
		$this->view->data["menus"] = $db->fetch_object();
		$this->view->show("menu_manager");
	}
	// public function categories()
	// {
	// 	if(!(isset($_SESSION['staff']['id']) && $_SESSION['staff']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
	// 	global $db;
	// 	$db->query("SELECT *, (SELECT COUNT(*) FROM bds_posts WHERE post_category = c.id) as countpost FROM bds_categories as c ORDER BY id ASC");
	// 	$this->view->data["categories"] = $db->fetch_object();
	// 	$this->view->show("categories_manager");
	// }
	public function news_1()
	{
		if(!(isset($_SESSION['staff']['id']) && $_SESSION['staff']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
		global $db;
		
		$db->query("SELECT *, p.id as pid FROM bds_news as p 
		LEFT JOIN hicrm_users as u ON p.news_author = u.id
		LEFT JOIN bds_news_categories as c ON p.news_category = c.id
		ORDER BY p.news_date DESC");
		$this->view->data["posts"] = $db->fetch_object();
		$db->query("SELECT * FROM bds_news_categories");
		$this->view->data["news_cat"] = $db->fetch_object();
		$this->view->data["page_name"] = "news_all";
		$this->view->data["page_title"] = "Danh sách sự kiện";
		$this->view->data["news"] = $db->fetch_object();
		$this->view->show("news_manager");
	}
	public function pages()
	{
		if(!(isset($_SESSION['staff']['id']) && $_SESSION['staff']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
		global $db;
		
		$db->query("SELECT *, p.id as pid FROM bds_pages as p 
		LEFT JOIN hicrm_users as u ON p.page_author = u.id
		ORDER BY p.page_date DESC");
		$this->view->data["posts"] = $db->fetch_object();
		$this->view->data["page_name"] = "pages_all";
		$this->view->data["page_title"] = "Danh sách sự kiện";
		$this->view->data["news"] = $db->fetch_object();
		$this->view->show("page_manager");
	}
	public function category()
	{
		if(!(isset($_SESSION['staff']['id']) && $_SESSION['staff']['id'] != "")){ header("Location: ".XC_URL."/admin/login"); }
		global $db;
		$db->query("SELECT * FROM bds_categories ORDER BY id ASC");
		$this->view->data["categories"] = $db->fetch_object();
		$this->view->show("category_manager");
	}
	
	public function postsss($para)
	{
		switch($para[1])
		{
			case "new":
			{
				$this->view->show("post-add");
				break;
			}
			default:
				break;
		}
		
	}
	
	public function upload()
	{
		include('./class.uploader.php');
		$uploader = new Uploader();
		$data = $uploader->upload($_FILES['files'], array(
			'limit' => 10, //Maximum Limit of files. {null, Number}
			'maxSize' => 10, //Maximum Size of files {null, Number(in MB's)}
			'extensions' => null, //Whitelist for file extension. {null, Array(ex: array('jpg', 'png'))}
			'required' => false, //Minimum one file is required for upload {Boolean}
			'uploadDir' => './uploads/images/tour/', //Upload directory {String}
			'title' => array('name'), //New file name {null, String, Array} *please read documentation in README.md
			'removeFiles' => true, //Enable file exclusion {Boolean(extra for jQuery.filer), String($_POST field name containing json data with file names)}
			'perms' => null, //Uploaded file permisions {null, Number}
			'onCheck' => null, //A callback function name to be called by checking a file for errors (must return an array) | ($file) | Callback
			'onError' => null, //A callback function name to be called if an error occured (must return an array) | ($errors, $file) | Callback
			'onSuccess' => null, //A callback function name to be called if all files were successfully uploaded | ($files, $metas) | Callback
			'onUpload' => null, //A callback function name to be called if all files were successfully uploaded (must return an array) | ($file) | Callback
			'onComplete' => null, //A callback function name to be called when upload is complete | ($file) | Callback
			'onRemove' => 'onFilesRemoveCallback' //A callback function name to be called by removing files (must return an array) | ($removed_files) | Callback
		));
		
		if($data['isComplete']){
			$files = $data['data'];
			print_r($files);
			global $db;
			$db->query("INSERT INTO sgt_tour_images(tourid,image_path,thumb_path,images_type) VALUES('".$_SESSION['tourid']."','".$files['metas'][0]['name']."','".$files['metas'][0]['name']."','1')");
		}

		if($data['hasErrors']){
			$errors = $data['errors'];
			print_r($errors);
		}
		
		function onFilesRemoveCallback($removed_files){
			foreach($removed_files as $key=>$value){
				$file = '../uploads/' . $value;
				if(file_exists($file)){
					unlink($file);
				}
			}
			
			return $removed_files;
		}
	}
}
