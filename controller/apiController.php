<?php
/**
 * Project: thuvien.
 * File: tourController.php.
 * Author: cuongvx
 * Email: cuongvx@media.vn
 * Create Date: 09:54 - 07/07/2026
 */
Class apiController extends baseController
{ 
    public function index()
    {
		$this->jsonResponse(false, 'API không hợp lệ.', array(), 404);
    }

	public function adminLogin()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			$this->jsonResponse(false, 'Chỉ hỗ trợ yêu cầu POST.', array(), 405);
			return;
		}

		$csrf = isset($_POST['csrf']) ? (string) $_POST['csrf'] : '';
		if (empty($_SESSION['admin_login_csrf']) || $csrf === '' || !hash_equals($_SESSION['admin_login_csrf'], $csrf)) {
			$this->jsonResponse(false, 'Phiên đăng nhập đã hết hạn. Vui lòng tải lại trang.', array(), 419);
			return;
		}

		$identifier = isset($_POST['username']) ? trim((string) $_POST['username']) : '';
		$password = isset($_POST['password']) ? (string) $_POST['password'] : '';
		$identifierLength = function_exists('mb_strlen') ? mb_strlen($identifier, 'UTF-8') : strlen($identifier);
		if ($identifier === '' || $password === '' || $identifierLength > 255 || strlen($password) > 255) {
			$this->jsonResponse(false, 'Vui lòng nhập tên đăng nhập và mật khẩu hợp lệ.', array(), 422);
			return;
		}

		global $db;
		$safeIdentifier = $db->escapestring($identifier);
		$db->query("SELECT id, username, password, full_name, email, phone, is_admin
			FROM ioc_users
			WHERE (username = '$safeIdentifier' OR email = '$safeIdentifier')
				AND is_active = 1 AND user_status = 1
			LIMIT 1");
		$user = $db->fetch_object(true);

		$passwordValid = false;
		if ($user && !empty($user->password)) {
			$passwordValid = password_verify($password, $user->password);
			if (!$passwordValid && preg_match('/^[a-f0-9]{32}$/i', $user->password)) {
				$passwordValid = hash_equals(strtolower($user->password), md5($password));
			}
		}

		$siteAccess = array();
		if ($user) {
			$db->query('SELECT site_code FROM ioc_user_site_access WHERE user_id = ' . (int) $user->id . ' AND is_active = 1 AND deleted_at IS NULL');
			foreach ($db->fetch_object() as $access) $siteAccess[$access->site_code] = 1;
		}
		if (!$user || !$passwordValid || ((int) $user->is_admin !== 1 && empty($siteAccess['admin']))) {
			$this->jsonResponse(false, 'Thông tin đăng nhập không đúng hoặc tài khoản không có quyền quản trị.', array(), 401);
			return;
		}

		session_regenerate_id(true);
		$_SESSION['user'] = array(
			'id' => (int) $user->id,
			'username' => $user->username,
			'email' => $user->email,
			'fullname' => $user->full_name,
			'phone' => isset($user->phone) ? $user->phone : '',
			'is_admin' => (int) $user->is_admin,
			'site_access' => $siteAccess
		);
		$_SESSION['LoggedIn'] = 1;
		unset($_SESSION['admin_login_csrf']);
		$db->query('UPDATE ioc_users SET last_login_at = NOW(6) WHERE id = ' . (int) $user->id);

		$this->jsonResponse(true, 'Đăng nhập quản trị thành công.', array(
			'id' => (int) $user->id,
			'display_name' => $user->full_name,
			'redirect_url' => XC_URL . '/'
		));
	}

	public function saveAccount()
	{
		$site = $this->authorizeAccountRequest(isset($_POST['id']) && (int) $_POST['id'] > 0 ? 'update' : 'create');
		if ($site === false) return;

		$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
		$username = isset($_POST['username']) ? trim((string) $_POST['username']) : '';
		$fullName = isset($_POST['full_name']) ? trim((string) $_POST['full_name']) : '';
		$email = isset($_POST['email']) ? trim((string) $_POST['email']) : '';
		$phone = isset($_POST['phone']) ? trim((string) $_POST['phone']) : '';
		$password = isset($_POST['password']) ? (string) $_POST['password'] : '';
		$isActive = isset($_POST['is_active']) && (int) $_POST['is_active'] === 1 ? 1 : 0;

		if (!preg_match('/^[A-Za-z0-9._@-]{3,120}$/', $username)) {
			$this->jsonResponse(false, 'Tên đăng nhập dài 3–120 ký tự và chỉ gồm chữ, số, dấu chấm, gạch ngang hoặc @.', array(), 422);
			return;
		}
		$nameLength = function_exists('mb_strlen') ? mb_strlen($fullName, 'UTF-8') : strlen($fullName);
		if ($fullName === '' || $nameLength > 255) {
			$this->jsonResponse(false, 'Họ tên là bắt buộc và không được quá 255 ký tự.', array(), 422);
			return;
		}
		if ($email !== '' && (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255)) {
			$this->jsonResponse(false, 'Địa chỉ email không hợp lệ.', array(), 422);
			return;
		}
		if (strlen($phone) > 30 || ($phone !== '' && !preg_match('/^[0-9+().\s-]{7,30}$/', $phone))) {
			$this->jsonResponse(false, 'Số điện thoại không hợp lệ.', array(), 422);
			return;
		}
		if (($id === 0 && strlen($password) < 8) || strlen($password) > 255) {
			$this->jsonResponse(false, 'Mật khẩu mới phải có từ 8 đến 255 ký tự.', array(), 422);
			return;
		}

		global $db;
		$safeUsername = $db->escapestring($username);
		$safeEmail = $db->escapestring($email);
		$db->query("SELECT id FROM ioc_users WHERE (username = '$safeUsername'" . ($email !== '' ? " OR email = '$safeEmail'" : '') . ") AND id <> $id LIMIT 1");
		if ($db->fetch_object(true)) {
			$this->jsonResponse(false, 'Tên đăng nhập hoặc email đã được sử dụng.', array(), 409);
			return;
		}

		$existing = null;
		if ($id > 0) {
			$db->query("SELECT id, is_admin FROM ioc_users WHERE id = $id LIMIT 1");
			$existing = $db->fetch_object(true);
			if (!$existing) {
				$this->jsonResponse(false, 'Tài khoản không tồn tại hoặc đã bị xóa.', array(), 404);
				return;
			}
		}

		$isAdmin = $existing ? (int) $existing->is_admin : 0;
		if ($site === 'admin' && isset($_POST['is_admin_present']) && $this->isSuperAdmin()) {
			$requestedAdmin = isset($_POST['is_admin']) && (int) $_POST['is_admin'] === 1 ? 1 : 0;
			if ($id === (int) $_SESSION['user']['id'] && $isAdmin === 1 && $requestedAdmin === 0) {
				$this->jsonResponse(false, 'Bạn không thể tự hạ quyền super admin của chính mình.', array(), 422);
				return;
			}
			if ($isAdmin === 1 && $requestedAdmin === 0) {
				$db->query('SELECT COUNT(*) AS total FROM ioc_users WHERE is_admin = 1 AND is_active = 1 AND user_status = 1');
				if ((int) $db->fetch_object(true)->total <= 1) {
					$this->jsonResponse(false, 'Hệ thống phải luôn còn ít nhất một super admin hoạt động.', array(), 422);
					return;
				}
			}
			$isAdmin = $requestedAdmin;
		}

		$safeName = $db->escapestring($fullName);
		$safePhone = $db->escapestring($phone);
		$emailSql = $email === '' ? 'NULL' : "'$safeEmail'";
		$phoneSql = $phone === '' ? 'NULL' : "'$safePhone'";
		$currentUserId = (int) $_SESSION['user']['id'];
		$safeSite = $db->escapestring($site);
		$db->query('START TRANSACTION');
		if ($id > 0) {
			$setPassword = $password !== '' ? ", password = '" . $db->escapestring(password_hash($password, PASSWORD_DEFAULT)) . "'" : '';
			$db->query("UPDATE ioc_users SET username = '$safeUsername', full_name = '$safeName',
				email = $emailSql, phone = $phoneSql, is_active = $isActive,
				user_status = " . ($isActive ? 1 : 0) . ", is_admin = $isAdmin,
				updated_at = NOW(6)$setPassword WHERE id = $id");
		} else {
			$passwordHash = $db->escapestring(password_hash($password, PASSWORD_DEFAULT));
			$db->query("INSERT INTO ioc_users (username, password, full_name, email, phone, is_active, is_admin, user_status)
				VALUES ('$safeUsername', '$passwordHash', '$safeName', $emailSql, $phoneSql, $isActive, $isAdmin, " . ($isActive ? 1 : 0) . ")");
			$db->query('SELECT LAST_INSERT_ID() AS id');
			$id = (int) $db->fetch_object(true)->id;
		}
		$db->query("INSERT INTO ioc_user_site_access (user_id, site_code, is_active, created_by, updated_by, deleted_at)
			VALUES ($id, '$safeSite', $isActive, $currentUserId, $currentUserId, NULL)
			ON DUPLICATE KEY UPDATE is_active = VALUES(is_active), updated_by = VALUES(updated_by),
				deleted_at = NULL, updated_at = NOW(6)");
		if ($isAdmin === 1) {
			$db->query("INSERT INTO ioc_user_site_access (user_id, site_code, is_active, created_by, updated_by, deleted_at)
				SELECT $id, site_code, 1, $currentUserId, $currentUserId, NULL FROM (
					SELECT 'admin' AS site_code UNION ALL SELECT 'backend' UNION ALL SELECT 'dashboard'
				) sites
				ON DUPLICATE KEY UPDATE is_active = 1, updated_by = VALUES(updated_by), deleted_at = NULL, updated_at = NOW(6)");
		}
		$db->query('COMMIT');

		$this->jsonResponse(true, $existing ? 'Cập nhật tài khoản thành công.' : 'Thêm tài khoản thành công.', array('id' => $id));
	}

	public function deleteAccount()
	{
		$site = $this->authorizeAccountRequest('delete');
		if ($site === false) return;
		$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
		if ($id < 1) {
			$this->jsonResponse(false, 'Tài khoản cần xóa không hợp lệ.', array(), 422);
			return;
		}
		if ($id === (int) $_SESSION['user']['id']) {
			$this->jsonResponse(false, 'Bạn không thể tự xóa quyền truy cập của chính mình.', array(), 422);
			return;
		}

		global $db;
		$db->query("SELECT is_admin FROM ioc_users WHERE id = $id LIMIT 1");
		$user = $db->fetch_object(true);
		if (!$user) {
			$this->jsonResponse(false, 'Tài khoản không tồn tại.', array(), 404);
			return;
		}
		if ((int) $user->is_admin === 1) {
			$this->jsonResponse(false, 'Không thể xóa quyền site của super admin.', array(), 422);
			return;
		}

		$safeSite = $db->escapestring($site);
		$currentUserId = (int) $_SESSION['user']['id'];
		$db->query("UPDATE ioc_user_site_access SET is_active = 0, deleted_at = NOW(6),
			updated_by = $currentUserId WHERE user_id = $id AND site_code = '$safeSite'");
		$this->jsonResponse(true, 'Đã xóa quyền truy cập tài khoản khỏi trang ' . ucfirst($site) . '.', array('id' => $id));
	}

	public function resetAccountPassword()
	{
		$site = $this->authorizeAccountRequest('update');
		if ($site === false) return;

		$userId = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
		$password = isset($_POST['password']) ? (string) $_POST['password'] : '';
		$passwordConfirmation = isset($_POST['password_confirmation']) ? (string) $_POST['password_confirmation'] : '';
		if ($userId < 1) {
			$this->jsonResponse(false, 'Tài khoản cần đặt lại mật khẩu không hợp lệ.', array(), 422);
			return;
		}
		if (strlen($password) < 8 || strlen($password) > 255) {
			$this->jsonResponse(false, 'Mật khẩu mới phải có từ 8 đến 255 ký tự.', array(), 422);
			return;
		}
		if (!hash_equals($password, $passwordConfirmation)) {
			$this->jsonResponse(false, 'Mật khẩu xác nhận không khớp.', array(), 422);
			return;
		}

		global $db;
		$safeSite = $db->escapestring($site);
		$db->query("SELECT u.id, u.is_admin
			FROM ioc_users u
			LEFT JOIN ioc_user_site_access a ON a.user_id = u.id
				AND a.site_code = '$safeSite' AND a.deleted_at IS NULL
			WHERE u.id = $userId AND (u.is_admin = 1 OR a.id IS NOT NULL)
			LIMIT 1");
		$user = $db->fetch_object(true);
		if (!$user) {
			$this->jsonResponse(false, 'Tài khoản không tồn tại trong trang đang quản lý.', array(), 404);
			return;
		}
		if ((int) $user->is_admin === 1 && !$this->isSuperAdmin()) {
			$this->jsonResponse(false, 'Chỉ Super Admin được đặt lại mật khẩu của Super Admin khác.', array(), 403);
			return;
		}

		$passwordHash = $db->escapestring(password_hash($password, PASSWORD_DEFAULT));
		$db->query("UPDATE ioc_users SET password = '$passwordHash', updated_at = NOW(6) WHERE id = $userId");
		$this->jsonResponse(true, 'Đặt lại mật khẩu thành công.', array('user_id' => $userId));
	}

	public function saveAccountPermissions()
	{
		$site = $this->authorizeAccountRequest('update');
		if ($site === false) return;
		$userId = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
		$permissions = isset($_POST['permissions']) ? json_decode((string) $_POST['permissions'], true) : null;
		if ($userId < 1 || !is_array($permissions)) {
			$this->jsonResponse(false, 'Dữ liệu phân quyền không hợp lệ.', array(), 422);
			return;
		}

		global $db;
		$db->query("SELECT is_admin FROM ioc_users WHERE id = $userId LIMIT 1");
		$user = $db->fetch_object(true);
		if (!$user) {
			$this->jsonResponse(false, 'Tài khoản không tồn tại.', array(), 404);
			return;
		}
		if ((int) $user->is_admin === 1) {
			$this->jsonResponse(true, 'Super admin luôn có toàn quyền trên tất cả menu.', array('user_id' => $userId));
			return;
		}

		$safeSite = $db->escapestring($site);
		$db->query("SELECT id FROM ioc_user_site_access WHERE user_id = $userId
			AND site_code = '$safeSite' AND is_active = 1 AND deleted_at IS NULL LIMIT 1");
		if (!$db->fetch_object(true)) {
			$this->jsonResponse(false, 'Tài khoản chưa được cấp quyền truy cập site này.', array(), 422);
			return;
		}

		$db->query("SELECT id FROM ioc_site_menus WHERE site_code = '$safeSite' AND is_active = 1");
		$menuRows = $db->fetch_object();
		$currentUserId = (int) $_SESSION['user']['id'];
		$db->query('START TRANSACTION');
		foreach (is_array($menuRows) ? $menuRows : array() as $menu) {
			$menuId = (int) $menu->id;
			$values = isset($permissions[$menuId]) && is_array($permissions[$menuId]) ? $permissions[$menuId] : array();
			$view = !empty($values['view']) ? 1 : 0;
			$create = !empty($values['create']) ? 1 : 0;
			$update = !empty($values['update']) ? 1 : 0;
			$delete = !empty($values['delete']) ? 1 : 0;
			$import = !empty($values['import']) ? 1 : 0;
			$export = !empty($values['export']) ? 1 : 0;
			$db->query("INSERT INTO ioc_user_menu_permissions
				(user_id, menu_id, can_view, can_create, can_update, can_delete, can_import, can_export, is_active, created_by, updated_by)
				VALUES ($userId, $menuId, $view, $create, $update, $delete, $import, $export, 1, $currentUserId, $currentUserId)
				ON DUPLICATE KEY UPDATE can_view = VALUES(can_view), can_create = VALUES(can_create),
					can_update = VALUES(can_update), can_delete = VALUES(can_delete),
					can_import = VALUES(can_import), can_export = VALUES(can_export),
					is_active = 1, updated_by = VALUES(updated_by), updated_at = NOW(6)");
		}
		$db->query('COMMIT');
		$this->jsonResponse(true, 'Cập nhật phân quyền menu thành công.', array('user_id' => $userId));
	}

	public function saveDepartment()
	{
		$operation = isset($_POST['id']) && (int) $_POST['id'] > 0 ? 'update' : 'create';
		if (!$this->authorizeDepartmentRequest($operation)) return;

		$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
		$code = isset($_POST['department_code']) ? trim((string) $_POST['department_code']) : '';
		$name = isset($_POST['department_name']) ? trim((string) $_POST['department_name']) : '';
		$bed = isset($_POST['department_bed']) ? (int) $_POST['department_bed'] : 0;
		$status = isset($_POST['department_status']) ? (int) $_POST['department_status'] : 1;

		$codeLength = function_exists('mb_strlen') ? mb_strlen($code, 'UTF-8') : strlen($code);
		if ($code === '' || $codeLength > 20) {
			$this->jsonResponse(false, 'Mã khoa/phòng là bắt buộc và tối đa 20 ký tự.', array(), 422);
			return;
		}

		if (!preg_match('/^[A-Za-z0-9._\-\s]{1,20}$/u', $code)) {
			$this->jsonResponse(false, 'Mã khoa/phòng chỉ được chứa chữ cái, chữ số, dấu gạch ngang hoặc dấu chấm.', array(), 422);
			return;
		}

		$nameLength = function_exists('mb_strlen') ? mb_strlen($name, 'UTF-8') : strlen($name);
		if ($name === '' || $nameLength > 150) {
			$this->jsonResponse(false, 'Tên khoa/phòng là bắt buộc và tối đa 150 ký tự.', array(), 422);
			return;
		}

		if ($bed < 0) {
			$this->jsonResponse(false, 'Số lượng giường thực kê phải là số nguyên không âm.', array(), 422);
			return;
		}

		if (!in_array($status, array(1, 0, 99), true)) {
			$status = 1;
		}

		global $db;
		$safeCode = $db->escapestring($code);
		$safeName = $db->escapestring($name);

		$db->query("SELECT id FROM ioc_departments WHERE department_code = '$safeCode' AND department_status <> 99 AND id <> $id LIMIT 1");
		if ($db->fetch_object(true)) {
			$this->jsonResponse(false, 'Mã khoa/phòng "' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '" đã được sử dụng.', array(), 409);
			return;
		}

		if ($id > 0) {
			$db->query("SELECT id FROM ioc_departments WHERE id = $id LIMIT 1");
			if (!$db->fetch_object(true)) {
				$this->jsonResponse(false, 'Khoa/phòng không tồn tại hoặc đã bị xóa.', array(), 404);
				return;
			}

			$db->query("UPDATE ioc_departments SET
				department_code = '$safeCode',
				department_name = '$safeName',
				department_bed = $bed,
				department_status = $status,
				department_updated_at = NOW()
				WHERE id = $id");

			$this->jsonResponse(true, 'Cập nhật thông tin khoa/phòng thành công.', array('id' => $id));
		} else {
			$db->query("INSERT INTO ioc_departments
				(department_code, department_name, department_bed, department_status, department_created_at, department_updated_at)
				VALUES ('$safeCode', '$safeName', $bed, $status, NOW(), NOW())");
			$db->query('SELECT LAST_INSERT_ID() AS id');
			$newId = (int) $db->fetch_object(true)->id;

			$this->jsonResponse(true, 'Thêm khoa/phòng mới thành công.', array('id' => $newId));
		}
	}

	public function deleteDepartment()
	{
		if (!$this->authorizeDepartmentRequest('delete')) return;

		$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
		if ($id < 1) {
			$this->jsonResponse(false, 'Khoa/phòng cần xóa không hợp lệ.', array(), 422);
			return;
		}

		global $db;
		$db->query("SELECT id, department_name FROM ioc_departments WHERE id = $id LIMIT 1");
		$dept = $db->fetch_object(true);
		if (!$dept) {
			$this->jsonResponse(false, 'Khoa/phòng không tồn tại.', array(), 404);
			return;
		}

		$db->query("UPDATE ioc_departments SET department_status = 99, department_updated_at = NOW() WHERE id = $id");

		$this->jsonResponse(true, 'Đã xóa khoa/phòng "' . htmlspecialchars($dept->department_name, ENT_QUOTES, 'UTF-8') . '" thành công.', array('id' => $id));
	}

	private function authorizeDepartmentRequest($operation)
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			$this->jsonResponse(false, 'Chỉ hỗ trợ yêu cầu POST.', array(), 405);
			return false;
		}
		if (empty($_SESSION['user']['id'])) {
			$this->jsonResponse(false, 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.', array(), 401);
			return false;
		}
		$csrf = isset($_POST['csrf']) ? (string) $_POST['csrf'] : '';
		if (empty($_SESSION['department_management_csrf']) || $csrf === '' || !hash_equals($_SESSION['department_management_csrf'], $csrf)) {
			$this->jsonResponse(false, 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang.', array(), 419);
			return false;
		}
		if (!$this->hasMenuPermission('admin.departments', $operation)) {
			$this->jsonResponse(false, 'Bạn không có quyền thực hiện thao tác này.', array(), 403);
			return false;
		}
		return true;
	}

	public function saveSystemConfig()
	{
		if (!$this->authorizeSystemConfigRequest()) return;

		$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
		$key = isset($_POST['system_key']) ? trim((string) $_POST['system_key']) : '';
		$name = isset($_POST['system_name']) ? trim((string) $_POST['system_name']) : '';
		$value = isset($_POST['system_value']) ? trim((string) $_POST['system_value']) : '';
		$status = isset($_POST['system_status']) ? (int) $_POST['system_status'] : 1;

		if ($key === '') {
			$this->jsonResponse(false, 'Mã cấu hình không được để trống.', array(), 422);
			return;
		}

		if ($name === '') {
			$this->jsonResponse(false, 'Tên cấu hình không được để trống.', array(), 422);
			return;
		}

		global $db;
		$safeKey = $db->escapestring($key);
		$safeName = $db->escapestring($name);
		$safeValue = $db->escapestring($value);
		$isSensitiveHisPassword = strtoupper($key) === 'PASSWORD_IOC_HIS';

		$db->query("SELECT id FROM ioc_system WHERE system_key = '$safeKey' AND id <> $id LIMIT 1");
		if ($db->fetch_object(true)) {
			$this->jsonResponse(false, 'Mã cấu hình "' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . '" đã tồn tại.', array(), 409);
			return;
		}

		if ($id > 0) {
			$db->query("SELECT id, system_value FROM ioc_system WHERE id = $id LIMIT 1");
			$existingConfig = $db->fetch_object(true);
			if (!$existingConfig) {
				$this->jsonResponse(false, 'Cấu hình không tồn tại hoặc đã bị xóa.', array(), 404);
				return;
			}
			// Giao diện không đưa mật khẩu hiện tại xuống trình duyệt; để trống nghĩa là giữ nguyên.
			if ($isSensitiveHisPassword && $value === '') {
				$safeValue = $db->escapestring((string) $existingConfig->system_value);
			}

			$db->query("UPDATE ioc_system SET
				system_key = '$safeKey',
				system_name = '$safeName',
				system_value = '$safeValue',
				system_status = $status
				WHERE id = $id");
			if (in_array(strtoupper($key), array('URL_LOGIN_HIS', 'USERNAME_IOC_HIS', 'PASSWORD_IOC_HIS'), true)) {
				unset($_SESSION['ioc_his_auth']);
			}

			$this->jsonResponse(true, 'Cập nhật cấu hình thành công.', array('id' => $id));
		} else {
			if ($isSensitiveHisPassword && $value === '') {
				$this->jsonResponse(false, 'Mật khẩu HIS không được để trống khi tạo mới.', array(), 422);
				return;
			}
			$db->query("INSERT INTO ioc_system (system_key, system_name, system_value, system_status)
				VALUES ('$safeKey', '$safeName', '$safeValue', $status)");
			if (in_array(strtoupper($key), array('URL_LOGIN_HIS', 'USERNAME_IOC_HIS', 'PASSWORD_IOC_HIS'), true)) {
				unset($_SESSION['ioc_his_auth']);
			}
			$db->query('SELECT LAST_INSERT_ID() AS id');
			$newId = (int) $db->fetch_object(true)->id;

			$this->jsonResponse(true, 'Thêm cấu hình mới thành công.', array('id' => $newId));
		}
	}

	public function reloadSystemCache()
	{
		if (!$this->authorizeSystemConfigRequest()) return;

		if (function_exists('opcache_reset')) {
			@opcache_reset();
		}

		$this->jsonResponse(true, 'Đã làm mới bộ nhớ đệm cấu hình hệ thống thành công.');
	}

	public function changeSelfPassword()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			$this->jsonResponse(false, 'Chỉ hỗ trợ phương thức POST.', array(), 405);
			return;
		}
		if (empty($_SESSION['user']['id'])) {
			$this->jsonResponse(false, 'Phiên làm việc đã hết hạn. Vui lòng đăng nhập lại.', array(), 401);
			return;
		}

		$csrf = isset($_POST['csrf']) ? (string) $_POST['csrf'] : '';
		if (empty($_SESSION['user_action_csrf']) || !hash_equals($_SESSION['user_action_csrf'], $csrf)) {
			$this->jsonResponse(false, 'Mã bảo mật phiên đã hết hạn. Vui lòng tải lại trang.', array(), 419);
			return;
		}

		$currentPassword = isset($_POST['current_password']) ? (string) $_POST['current_password'] : '';
		$newPassword = isset($_POST['new_password']) ? (string) $_POST['new_password'] : '';
		$confirmPassword = isset($_POST['confirm_password']) ? (string) $_POST['confirm_password'] : '';

		if ($currentPassword === '') {
			$this->jsonResponse(false, 'Vui lòng nhập mật khẩu hiện tại.', array(), 422);
			return;
		}
		if (strlen($newPassword) < 8) {
			$this->jsonResponse(false, 'Mật khẩu mới phải có tối thiểu 8 ký tự.', array(), 422);
			return;
		}
		if (!hash_equals($newPassword, $confirmPassword)) {
			$this->jsonResponse(false, 'Xác nhận mật khẩu mới không khớp.', array(), 422);
			return;
		}

		global $db;
		$userId = (int) $_SESSION['user']['id'];
		$db->query("SELECT id, password FROM ioc_users WHERE id = $userId AND is_active = 1 LIMIT 1");
		$user = $db->fetch_object(true);

		if (!$user) {
			$this->jsonResponse(false, 'Tài khoản không tồn tại hoặc đã bị khóa.', array(), 404);
			return;
		}

		$validOld = password_verify($currentPassword, $user->password);
		if (!$validOld && preg_match('/^[a-f0-9]{32}$/i', $user->password)) {
			$validOld = hash_equals(strtolower($user->password), md5($currentPassword));
		}

		if (!$validOld) {
			$this->jsonResponse(false, 'Mật khẩu hiện tại không chính xác.', array(), 422);
			return;
		}

		$hash = $db->escapestring(password_hash($newPassword, PASSWORD_DEFAULT));
		$db->query("UPDATE ioc_users SET password = '$hash', updated_at = NOW(6) WHERE id = $userId");

		$this->jsonResponse(true, 'Đổi mật khẩu thành công.');
	}

	private function authorizeSystemConfigRequest()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			$this->jsonResponse(false, 'Chỉ hỗ trợ yêu cầu POST.', array(), 405);
			return false;
		}
		if (empty($_SESSION['user']['id'])) {
			$this->jsonResponse(false, 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.', array(), 401);
			return false;
		}
		$csrf = isset($_POST['csrf']) ? (string) $_POST['csrf'] : '';
		if (empty($_SESSION['system_config_csrf']) || $csrf === '' || !hash_equals($_SESSION['system_config_csrf'], $csrf)) {
			$this->jsonResponse(false, 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang.', array(), 419);
			return false;
		}
		return true;
	}

	private function authorizeAccountRequest($operation)
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			$this->jsonResponse(false, 'Chỉ hỗ trợ yêu cầu POST.', array(), 405);
			return false;
		}
		if (empty($_SESSION['user']['id'])) {
			$this->jsonResponse(false, 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.', array(), 401);
			return false;
		}
		$csrf = isset($_POST['csrf']) ? (string) $_POST['csrf'] : '';
		if (empty($_SESSION['account_management_csrf']) || $csrf === '' || !hash_equals($_SESSION['account_management_csrf'], $csrf)) {
			$this->jsonResponse(false, 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang.', array(), 419);
			return false;
		}
		$site = isset($_POST['site_code']) ? strtolower(trim((string) $_POST['site_code'])) : '';
		if (!in_array($site, array('admin', 'backend', 'dashboard'), true)) {
			$this->jsonResponse(false, 'Trang phân quyền không hợp lệ.', array(), 422);
			return false;
		}
		if (!$this->hasMenuPermission('admin.accounts.' . $site, $operation)) {
			$this->jsonResponse(false, 'Bạn không có quyền thực hiện thao tác này.', array(), 403);
			return false;
		}
		return $site;
	}

	private function jsonResponse($success, $message, $data = array(), $status = 200)
	{
		http_response_code($status);
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode(array(
			'success' => (bool) $success,
			'message' => (string) $message,
			'data' => $data
		), JSON_UNESCAPED_UNICODE);
	}
}
