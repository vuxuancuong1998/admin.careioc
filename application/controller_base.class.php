<?php
Abstract Class baseController {

/*
 * @registry object
 */
protected $registry;
protected $model;
protected $view;

function __construct($registry) {
	$this->registry = $registry;
	$this->model = baseModel::getInstance();
	$this->view  = baseView::getInstance();
	$this->func  = general::getInstance();
	$this->helper  = general::getInstance();
	$this->home  = home::getInstance();
	$this->shop  = shop::getInstance();
	$this->book = book::getInstance();
	$this->member = member::getInstance();
	$this->pdf = pdf::getInstance();
}

protected function hasAdminSession()
{
	return $this->hasSiteAccess('admin');
}

protected function requireAdminSession()
{
	if (!$this->hasAdminSession()) {
		header('Location: ' . XC_URL . '/login');
		exit;
	}
}

protected function isSuperAdmin()
{
	return isset($_SESSION['user']['id'], $_SESSION['user']['is_admin'])
		&& (int) $_SESSION['user']['id'] > 0
		&& (int) $_SESSION['user']['is_admin'] === 1;
}

protected function hasSiteAccess($siteCode)
{
	if (empty($_SESSION['user']['id'])) return false;
	if ($this->isSuperAdmin()) return true;
	if (!in_array($siteCode, array('admin', 'backend', 'dashboard'), true)) return false;

	global $db;
	$userId = (int) $_SESSION['user']['id'];
	$siteCode = $db->escapestring($siteCode);
	$db->query("SELECT id FROM ioc_user_site_access
		WHERE user_id = $userId AND site_code = '$siteCode'
			AND is_active = 1 AND deleted_at IS NULL LIMIT 1");
	return (bool) $db->fetch_object(true);
}

protected function hasMenuPermission($menuCode, $operation = 'view')
{
	return $this->helper->userHasMenu($menuCode, $operation);
}

protected function requireMenuPermission($menuCode, $operation = 'view', $redirect = null)
{
	if ($this->hasMenuPermission($menuCode, $operation)) return;
	if ($redirect !== null) {
		header('Location: ' . $redirect);
		exit;
	}
	http_response_code(403);
	exit('Bạn không có quyền truy cập chức năng này.');
}


/**
 * @all controllers must contain an index method
 */
abstract function index();
}

?>
