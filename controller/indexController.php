<?php

Class indexController Extends baseController
{
	public function login()
	{
		if ($this->hasAdminSession()) {
			header('Location: ' . XC_URL . '/');
			exit;
		}

		if (empty($_SESSION['admin_login_csrf'])) {
			$_SESSION['admin_login_csrf'] = bin2hex(random_bytes(32));
		}

		$this->view->data['csrf'] = $_SESSION['admin_login_csrf'];
		$this->view->data['loggedOut'] = isset($_GET['logged_out']) && $_GET['logged_out'] === '1';
		$this->view->admintmp('login');
	}

	public function logout()
	{
		$_SESSION = array();
		if (ini_get('session.use_cookies')) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
		}
		session_destroy();
		header('Location: ' . XC_URL . '/login?logged_out=1');
		exit;
	}

	public function accounts($para = array())
	{
		$this->requireAdminSession();
		$site = isset($para[1]) ? strtolower(trim((string) $para[1])) : 'admin';
		if (!in_array($site, array('admin', 'backend', 'dashboard'), true)) {
			$site = 'admin';
		}

		$menuCode = 'admin.accounts.' . $site;
		if (!$this->hasMenuPermission($menuCode, 'view')) {
			header('Location: ' . XC_URL . '/');
			exit;
		}

		global $db;
		$safeSite = $db->escapestring($site);
		if (empty($_SESSION['account_management_csrf'])) {
			$_SESSION['account_management_csrf'] = bin2hex(random_bytes(32));
		}

		$db->query("SELECT u.id, u.username, u.full_name, u.email, u.phone, u.last_login_at,
			u.is_active, u.is_admin, u.user_status, a.id AS access_id,
			COALESCE(a.is_active, u.is_admin) AS site_active
			FROM ioc_users u
			LEFT JOIN ioc_user_site_access a ON a.user_id = u.id
				AND a.site_code = '$safeSite' AND a.deleted_at IS NULL
			WHERE u.is_admin = 1 OR a.id IS NOT NULL
			ORDER BY u.is_admin DESC, u.full_name, u.username");
		$accounts = $db->fetch_object();

		$db->query("SELECT id, site_code, menu_code, menu_name, parent_id, route, icon, sort_order
			FROM ioc_site_menus WHERE site_code = '$safeSite' AND is_active = 1
			ORDER BY sort_order, id");
		$menus = $db->fetch_object();

		$db->query("SELECT p.user_id, p.menu_id, p.can_view, p.can_create, p.can_update,
			p.can_delete, p.can_import, p.can_export
			FROM ioc_user_menu_permissions p
			INNER JOIN ioc_site_menus m ON m.id = p.menu_id
			WHERE m.site_code = '$safeSite' AND p.is_active = 1");
		$permissionRows = $db->fetch_object();
		$permissions = array();
		foreach (is_array($permissionRows) ? $permissionRows : array() as $permission) {
			$permissions[(int) $permission->user_id][(int) $permission->menu_id] = array(
				'view' => (int) $permission->can_view,
				'create' => (int) $permission->can_create,
				'update' => (int) $permission->can_update,
				'delete' => (int) $permission->can_delete,
				'import' => (int) $permission->can_import,
				'export' => (int) $permission->can_export
			);
		}

		$db->query("SELECT sites.site_code, COUNT(DISTINCT users.id) AS total
			FROM (
				SELECT 'admin' AS site_code UNION ALL SELECT 'backend' UNION ALL SELECT 'dashboard'
			) sites
			LEFT JOIN ioc_user_site_access access_rows ON access_rows.site_code = sites.site_code
				AND access_rows.is_active = 1 AND access_rows.deleted_at IS NULL
			LEFT JOIN ioc_users users ON users.id = access_rows.user_id AND users.is_active = 1
			GROUP BY sites.site_code");
		$countRows = $db->fetch_object();
		$counts = array('admin' => 0, 'backend' => 0, 'dashboard' => 0);
		foreach (is_array($countRows) ? $countRows : array() as $countRow) {
			$counts[$countRow->site_code] = (int) $countRow->total;
		}

		$this->view->data['site'] = $site;
		$this->view->data['siteNames'] = array('admin' => 'Admin', 'backend' => 'Backend', 'dashboard' => 'Dashboard');
		$this->view->data['accounts'] = is_array($accounts) ? $accounts : array();
		$this->view->data['menus'] = is_array($menus) ? $menus : array();
		$this->view->data['permissions'] = $permissions;
		$this->view->data['counts'] = $counts;
		$this->view->data['csrf'] = $_SESSION['account_management_csrf'];
		$this->view->data['currentUserId'] = (int) $_SESSION['user']['id'];
		$this->view->data['canCreate'] = $this->hasMenuPermission($menuCode, 'create');
		$this->view->data['canUpdate'] = $this->hasMenuPermission($menuCode, 'update');
		$this->view->data['canDelete'] = $this->hasMenuPermission($menuCode, 'delete');
		$this->view->data['adminMenus'] = $this->helper->getUserMenus('admin');
		$this->view->data['pagetitle'] = 'Tài khoản trang ' . $this->view->data['siteNames'][$site];
		$this->view->data['currentRoute'] = '/accounts/' . $site;
		$this->view->data['activeMenuCode'] = $menuCode;
		$this->view->admintmp('accounts');
	}

	public function index()
    {
		$this->requireAdminSession();
		
		global $db;
		$this->view->data["pagetitle"] = "Tổng Quan Quản Trị";
		$this->view->data['adminMenus'] = $this->helper->getUserMenus('admin');
		$this->view->data['currentRoute'] = '/';
		$this->view->data['activeMenuCode'] = 'admin.overview';

		// 1. Thống kê User hệ thống
		$db->query("SELECT 
			COUNT(*) AS total_users,
			SUM(CASE WHEN is_active = 1 AND user_status = 1 THEN 1 ELSE 0 END) AS active_users,
			SUM(CASE WHEN is_admin = 1 THEN 1 ELSE 0 END) AS admin_users,
			SUM(CASE WHEN is_active = 0 OR user_status = 0 THEN 1 ELSE 0 END) AS locked_users
			FROM ioc_users WHERE user_status != 99");
		$userStats = $db->fetch_object(true);
		$this->view->data['userStats'] = $userStats ? $userStats : (object) array('total_users' => 0, 'active_users' => 0, 'admin_users' => 0, 'locked_users' => 0);

		// Phân bổ người dùng theo các phân hệ (admin, backend, dashboard)
		$db->query("SELECT sites.site_code, COUNT(DISTINCT access_rows.user_id) AS total
			FROM (
				SELECT 'admin' AS site_code UNION ALL SELECT 'backend' UNION ALL SELECT 'dashboard'
			) sites
			LEFT JOIN ioc_user_site_access access_rows ON access_rows.site_code = sites.site_code
				AND access_rows.is_active = 1 AND access_rows.deleted_at IS NULL
			GROUP BY sites.site_code");
		$siteUserCounts = array('admin' => 0, 'backend' => 0, 'dashboard' => 0);
		foreach ((array) $db->fetch_object() as $row) {
			if (isset($row->site_code)) {
				$siteUserCounts[$row->site_code] = (int) $row->total;
			}
		}
		$this->view->data['siteUserCounts'] = $siteUserCounts;

		// Danh sách tài khoản người dùng gần đây
		$db->query("SELECT u.id, u.username, u.full_name, u.email, u.phone, u.is_admin, u.is_active, u.last_login_at,
			GROUP_CONCAT(DISTINCT a.site_code ORDER BY a.site_code SEPARATOR ', ') AS site_codes
			FROM ioc_users u
			LEFT JOIN ioc_user_site_access a ON a.user_id = u.id AND a.is_active = 1 AND a.deleted_at IS NULL
			WHERE u.user_status != 99
			GROUP BY u.id
			ORDER BY u.is_admin DESC, CASE WHEN u.last_login_at IS NULL THEN 1 ELSE 0 END, u.last_login_at DESC
			LIMIT 6");
		$recentUsers = $db->fetch_object();
		$this->view->data['recentUsers'] = is_array($recentUsers) ? $recentUsers : array();

		// 2. Thống kê Khoa / Phòng
		$db->query("SELECT 
			COUNT(*) AS total_depts,
			SUM(CASE WHEN department_status = 1 THEN 1 ELSE 0 END) AS active_depts,
			SUM(CASE WHEN department_status = 0 THEN 1 ELSE 0 END) AS paused_depts,
			SUM(CASE WHEN department_status = 1 THEN department_bed ELSE 0 END) AS total_beds
			FROM ioc_departments WHERE department_status != 99");
		$deptStats = $db->fetch_object(true);
		$this->view->data['deptStats'] = $deptStats ? $deptStats : (object) array('total_depts' => 0, 'active_depts' => 0, 'paused_depts' => 0, 'total_beds' => 0);

		// Danh sách khoa phòng trọng điểm (chỉ tiêu giường cao nhất)
		$db->query("SELECT id, department_code, department_name, department_bed, department_status
			FROM ioc_departments
			WHERE department_status = 1
			ORDER BY department_bed DESC, department_name ASC
			LIMIT 6");
		$topDepts = $db->fetch_object();
		$this->view->data['topDepts'] = is_array($topDepts) ? $topDepts : array();

		// 3. Thống kê Cấu hình & Tham số hệ thống
		$db->query("SELECT 
			COUNT(*) AS total_configs,
			SUM(CASE WHEN system_status = 1 THEN 1 ELSE 0 END) AS active_configs
			FROM ioc_system");
		$systemStats = $db->fetch_object(true);
		$this->view->data['systemStats'] = $systemStats ? $systemStats : (object) array('total_configs' => 0, 'active_configs' => 0);

		// 4. Thống kê Menu chức năng
		$db->query("SELECT COUNT(*) AS total_menus FROM ioc_site_menus WHERE site_code = 'admin' AND is_active = 1");
		$menuStats = $db->fetch_object(true);
		$this->view->data['menuStats'] = $menuStats ? $menuStats : (object) array('total_menus' => 0);
		
		$this->view->admintmp("index");
	}
	public function departments($para = array())
	{
		$this->requireAdminSession();

		$menuCode = 'admin.departments';
		if (!$this->hasMenuPermission($menuCode, 'view')) {
			header('Location: ' . XC_URL . '/');
			exit;
		}

		if (empty($_SESSION['department_management_csrf'])) {
			$_SESSION['department_management_csrf'] = bin2hex(random_bytes(32));
		}

		global $db;
		$db->query("SELECT id, department_code, department_name, department_bed, department_status,
			department_created_at, department_updated_at
			FROM ioc_departments
			ORDER BY CASE WHEN department_status = 99 THEN 1 ELSE 0 END, department_name ASC");
		$departmentRows = $db->fetch_object();
		$departments = is_array($departmentRows) ? $departmentRows : array();

		$total = 0;
		$active = 0;
		$deleted = 0;
		$totalBeds = 0;

		foreach ($departments as $dept) {
			$status = (int) $dept->department_status;
			if ($status === 99) {
				$deleted++;
			} else {
				$total++;
				if ($status === 1) {
					$active++;
					$totalBeds += (int) $dept->department_bed;
				}
			}
		}

		$this->view->data['departments'] = $departments;
		$this->view->data['counts'] = array(
			'total' => $total,
			'active' => $active,
			'deleted' => $deleted,
			'total_beds' => $totalBeds
		);
		$this->view->data['csrf'] = $_SESSION['department_management_csrf'];
		$this->view->data['canCreate'] = $this->hasMenuPermission($menuCode, 'create');
		$this->view->data['canUpdate'] = $this->hasMenuPermission($menuCode, 'update');
		$this->view->data['canDelete'] = $this->hasMenuPermission($menuCode, 'delete');
		$this->view->data['adminMenus'] = $this->helper->getUserMenus('admin');
		$this->view->data['pagetitle'] = 'Quản Lý Khoa Phòng';
		$this->view->data['currentRoute'] = '/departments';
		$this->view->data['activeMenuCode'] = $menuCode;
		$this->view->admintmp('departments');
	}

	public function system($para = array())
	{
		$this->requireAdminSession();

		if (empty($_SESSION['system_config_csrf'])) {
			$_SESSION['system_config_csrf'] = bin2hex(random_bytes(32));
		}

		global $db;
		$db->query("SELECT id, system_key, system_name, system_value, system_status
			FROM ioc_system
			ORDER BY system_key ASC");
		$configRows = $db->fetch_object();
		$configs = is_array($configRows) ? $configRows : array();

		$this->view->data['configs'] = $configs;
		$this->view->data['csrf'] = $_SESSION['system_config_csrf'];
		$this->view->data['adminMenus'] = $this->helper->getUserMenus('admin');
		$this->view->data['pagetitle'] = 'Thiết Lập Tham Số';
		$this->view->data['currentRoute'] = '/system';
		$this->view->data['activeMenuCode'] = 'admin.system';
		$this->view->admintmp('system');
	}

	public function system_api($para = array())
	{
		$this->requireAdminSession();

		$menuCode = 'admin.system_api';
		if (!$this->hasMenuPermission($menuCode, 'view')) {
			header('Location: ' . XC_URL . '/');
			exit;
		}

		if (empty($_SESSION['system_config_csrf'])) {
			$_SESSION['system_config_csrf'] = bin2hex(random_bytes(32));
		}

		global $db;
		$db->query("SELECT system_key, system_value, system_status FROM ioc_system
			WHERE system_key IN ('URL_LOGIN_HIS', 'USERNAME_IOC_HIS', 'PASSWORD_IOC_HIS')");
		$hisValues = array();
		foreach ((array) $db->fetch_object() as $row) {
			$hisValues[$row->system_key] = array(
				'value' => trim((string) $row->system_value),
				'active' => (int) $row->system_status === 1
			);
		}
		$requiredKeys = array('URL_LOGIN_HIS', 'USERNAME_IOC_HIS', 'PASSWORD_IOC_HIS');
		$hisReady = true;
		foreach ($requiredKeys as $key) {
			if (empty($hisValues[$key]['active']) || $hisValues[$key]['value'] === '') {
				$hisReady = false;
			}
		}

		$this->view->data['hisConfig'] = array(
			'ready' => $hisReady,
			'url' => isset($hisValues['URL_LOGIN_HIS']) ? $hisValues['URL_LOGIN_HIS']['value'] : '',
			'username' => isset($hisValues['USERNAME_IOC_HIS']) ? $hisValues['USERNAME_IOC_HIS']['value'] : '',
			'password_configured' => !empty($hisValues['PASSWORD_IOC_HIS']['value'])
		);
		$this->view->data['csrf'] = $_SESSION['system_config_csrf'];
		$this->view->data['adminMenus'] = $this->helper->getUserMenus('admin');
		$this->view->data['pagetitle'] = 'Quản trị hệ thống API';
		$this->view->data['currentRoute'] = '/system_api';
		$this->view->data['activeMenuCode'] = $menuCode;
		$this->view->admintmp('system_api');
	}

	private function countorderbydate($date)
	{
		global $db;
		$db->query("SELECT count(*) as countorder FROM ow_orders WHERE date(order_time) = '".date("Y-m-d",strtotime($date))."'");
		return $db->fetch_object(true)->countorder;
	}
	private function countorderbydatedeposited($date)
	{
		global $db;
		$db->query("SELECT count(*) as countorder FROM ow_orders WHERE order_status > 1 AND date(order_time) = '".date("Y-m-d",strtotime($date))."'");
		return $db->fetch_object(true)->countorder;
	}
	
}

?>
