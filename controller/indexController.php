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
		$this->view->admintmp('accounts');
	}

	public function index()
    {
		$this->requireAdminSession();
		
		global $db;
		// echo "SSSSSSSSSSSS";
		$this->view->data["pagetitle"] = "Tổng quan";
		$this->view->data['adminMenus'] = $this->helper->getUserMenus('admin');
		
		$this->view->admintmp("index");
		/*
		global $db;
		$db->query("SELECT *, p.id as placeid FROM bds_places as p
		LEFT JOIN hicrm_districts as d ON p.place_district = d.id
		LEFT JOIN hicrm_provinces as pr ON p.place_province = pr.id
		");
		$this->view->data["places"] = $db->fetch_object();
		$db->query("SELECT * FROM bds_projects ORDER BY project_create_time DESC LIMIT 8");
		$this->view->data["projects"] = $db->fetch_object();
		$db->query("SELECT * FROM bds_news ORDER BY news_date DESC LIMIT 8");
		$this->view->data["news"] = $db->fetch_object();
        //if(!(isset($_SESSION['user']['id']) && $_SESSION['user']['id'] != "")){ header("Location: ".XC_URL."/login"); }
		$this->view->data["pagedata"] = "home";
		$this->view->show('index');
		*/
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
