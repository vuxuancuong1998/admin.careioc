<?php

class ioc_apiController extends baseController
{
    const HIS_SESSION_KEY = 'ioc_his_auth';
    const CONNECT_TIMEOUT = 5;
    const REQUEST_TIMEOUT = 20;

    public function index()
    {
        $this->jsonResponse(false, 'API tích hợp không hợp lệ.', array(), 404);
    }

    /**
     * Đăng nhập VNPT-HIS bằng ba tham số trong ioc_system và giữ cookie HIS
     * trong PHP session hiện tại để các API HIS tiếp theo có thể tái sử dụng.
     */
    public function login_HIS()
    {
        if (!$this->authorizeRequest()) {
            return;
        }

        $config = $this->getHisConfig();
        if (!$config['success']) {
            $this->jsonResponse(false, $config['message'], array(), 422);
            return;
        }

        $endpoints = $this->buildHisEndpoints($config['data']['url']);
        if ($endpoints === false) {
            $this->jsonResponse(false, 'URL đăng nhập HIS không hợp lệ. Chỉ chấp nhận địa chỉ HTTP hoặc HTTPS.', array(), 422);
            return;
        }

        // Lấy JSESSIONID/SESSIONID trước khi gửi form đăng nhập.
        $loginPage = $this->httpRequest($endpoints['login_page'], 'GET');
        if (!$loginPage['transport_success']) {
            $this->jsonResponse(false, 'Không thể kết nối đến HIS. Vui lòng kiểm tra URL hoặc đường truyền.', array(), 502);
            return;
        }

        $cookies = $loginPage['cookies'];
        $captcha = $this->createRealPersonChallenge();
        $payload = array(
            'txtName' => $config['data']['username'],
            'txtPass' => $config['data']['password'],
            'defaultReal' => $captcha['text'],
            'defaultRealHash' => $captcha['hash'],
            'srcwidth' => '1366'
        );

        $login = $this->httpRequest($endpoints['login_action'], 'POST', $payload, $cookies);
        if (!$login['transport_success']) {
            $this->jsonResponse(false, 'HIS không phản hồi yêu cầu đăng nhập. Vui lòng thử lại.', array(), 502);
            return;
        }

        $cookies = array_merge($cookies, $login['cookies']);
        $location = isset($login['location']) ? (string) $login['location'] : '';
        $loggedIn = $login['status'] >= 300 && $login['status'] < 400
            && $location !== ''
            && stripos($location, '/main/') !== false;

        if (!$loggedIn) {
            unset($_SESSION[self::HIS_SESSION_KEY]);
            $this->jsonResponse(false, 'Đăng nhập HIS thất bại. Vui lòng kiểm tra tài khoản, mật khẩu và trạng thái dịch vụ HIS.', array(), 401);
            return;
        }

        $_SESSION[self::HIS_SESSION_KEY] = array(
            'cookies' => $cookies,
            'base_url' => $endpoints['app_base'],
            'username' => $config['data']['username'],
            'logged_in_at' => time(),
            'last_verified_at' => time()
        );

        $this->jsonResponse(true, 'Đăng nhập HIS thành công. Phiên đã sẵn sàng cho các API tiếp theo.', $this->statusData(true));
    }

    /** Kiểm tra trực tiếp phiên HIS hiện tại, không trả cookie hay thông tin nhạy cảm. */
    public function status_HIS()
    {
        if (!$this->authorizeRequest()) {
            return;
        }

        if (empty($_SESSION[self::HIS_SESSION_KEY]['cookies']) || empty($_SESSION[self::HIS_SESSION_KEY]['base_url'])) {
            $this->jsonResponse(true, 'Chưa đăng nhập HIS.', $this->statusData(false));
            return;
        }

        $session = $_SESSION[self::HIS_SESSION_KEY];
        $checkUrl = rtrim($session['base_url'], '/') . '/main/manager.jsp?func=../admin/SelDept';
        $check = $this->httpRequest($checkUrl, 'GET', null, $session['cookies']);

        if (!$check['transport_success']) {
            $data = $this->statusData(true);
            $data['reachable'] = false;
            $this->jsonResponse(true, 'Đang lưu phiên HIS nhưng chưa thể xác minh do mất kết nối.', $data);
            return;
        }

        $location = isset($check['location']) ? (string) $check['location'] : '';
        $expired = $check['status'] === 401 || $check['status'] === 403
            || ($check['status'] >= 300 && $check['status'] < 400 && stripos($location, '/main/') === false)
            || stripos($check['body'], 'name="txtName"') !== false;

        if ($expired) {
            unset($_SESSION[self::HIS_SESSION_KEY]);
            $this->jsonResponse(true, 'Phiên HIS đã hết hạn. Vui lòng đăng nhập lại.', $this->statusData(false));
            return;
        }

        $_SESSION[self::HIS_SESSION_KEY]['cookies'] = array_merge($session['cookies'], $check['cookies']);
        $_SESSION[self::HIS_SESSION_KEY]['last_verified_at'] = time();
        $this->jsonResponse(true, 'Phiên HIS đang hoạt động.', $this->statusData(true));
    }

    /** Đăng xuất phía HIS (best effort) rồi luôn xóa phiên HIS cục bộ. */
    public function logout_HIS()
    {
        if (!$this->authorizeRequest()) {
            return;
        }

        if (!empty($_SESSION[self::HIS_SESSION_KEY]['base_url']) && !empty($_SESSION[self::HIS_SESSION_KEY]['cookies'])) {
            $session = $_SESSION[self::HIS_SESSION_KEY];
            $this->httpRequest(rtrim($session['base_url'], '/') . '/servlet/login.Logout', 'GET', null, $session['cookies']);
        }
        unset($_SESSION[self::HIS_SESSION_KEY]);
        $this->jsonResponse(true, 'Đã đăng xuất và xóa phiên HIS.', $this->statusData(false));
    }

    private function authorizeRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, 'Chỉ hỗ trợ yêu cầu POST.', array(), 405);
            return false;
        }
        if (!$this->hasAdminSession()) {
            $this->jsonResponse(false, 'Phiên quản trị đã hết hạn. Vui lòng đăng nhập lại.', array(), 401);
            return false;
        }
        $csrf = isset($_POST['csrf']) ? (string) $_POST['csrf'] : '';
        if (empty($_SESSION['system_config_csrf']) || $csrf === '' || !hash_equals($_SESSION['system_config_csrf'], $csrf)) {
            // PHP 7.3 của dự án không có reason phrase cho 419 và sẽ biến nó thành HTTP 500.
            $this->jsonResponse(false, 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang.', array(), 403);
            return false;
        }
        return true;
    }

    private function getHisConfig()
    {
        global $db;
        $required = array('URL_LOGIN_HIS', 'USERNAME_IOC_HIS', 'PASSWORD_IOC_HIS');
        $db->query("SELECT system_key, system_value, system_status FROM ioc_system
            WHERE system_key IN ('URL_LOGIN_HIS', 'USERNAME_IOC_HIS', 'PASSWORD_IOC_HIS')");
        $values = array();
        foreach ((array) $db->fetch_object() as $row) {
            if ((int) $row->system_status === 1) {
                $values[$row->system_key] = trim((string) $row->system_value);
            }
        }
        foreach ($required as $key) {
            if (!isset($values[$key]) || $values[$key] === '') {
                return array('success' => false, 'message' => 'Thiếu hoặc chưa kích hoạt cấu hình ' . $key . '.');
            }
        }
        return array('success' => true, 'data' => array(
            'url' => $values['URL_LOGIN_HIS'],
            'username' => $values['USERNAME_IOC_HIS'],
            'password' => $values['PASSWORD_IOC_HIS']
        ));
    }

    private function buildHisEndpoints($configuredUrl)
    {
        $parts = parse_url(trim((string) $configuredUrl));
        if ($parts === false || empty($parts['scheme']) || empty($parts['host'])
            || !in_array(strtolower($parts['scheme']), array('http', 'https'), true)) {
            return false;
        }
        $origin = strtolower($parts['scheme']) . '://' . $parts['host'];
        if (isset($parts['port'])) {
            $origin .= ':' . (int) $parts['port'];
        }
        $path = isset($parts['path']) ? rtrim($parts['path'], '/') : '';
        $appPath = stripos($path, '/vnpthis') === 0 ? '/vnpthis' : ($path !== '' ? $path : '/vnpthis');
        $appBase = $origin . $appPath;
        return array(
            'app_base' => $appBase,
            'login_page' => $appBase . '/',
            'login_action' => $appBase . '/servlet/login.ValidateUser'
        );
    }

    /** Sinh captcha jquery.realperson và DJB2 hash tương ứng. */
    private function createRealPersonChallenge()
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $text = '';
        for ($i = 0; $i < 6; $i++) {
            $text .= $alphabet[random_int(0, 25)];
        }
        $hash = 5381;
        for ($i = 0; $i < strlen($text); $i++) {
            $hash = (($hash * 33) + ord($text[$i])) & 0xffffffff;
        }
        if ($hash >= 0x80000000) {
            $hash -= 0x100000000;
        }
        return array('text' => $text, 'hash' => (string) $hash);
    }

    private function httpRequest($url, $method, $payload = null, $cookies = array())
    {
        if (!function_exists('curl_init')) {
            return array('transport_success' => false, 'status' => 0, 'body' => '', 'cookies' => array(), 'location' => '');
        }

        $responseHeaders = array();
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, self::CONNECT_TIMEOUT);
        curl_setopt($curl, CURLOPT_TIMEOUT, self::REQUEST_TIMEOUT);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Accept: text/html,application/xhtml+xml,application/json;q=0.9,*/*;q=0.8',
            'User-Agent: CARE-IOC-HIS-Integration/1.0'
        ));
        curl_setopt($curl, CURLOPT_HEADERFUNCTION, function ($curlHandle, $headerLine) use (&$responseHeaders) {
            $length = strlen($headerLine);
            $headerLine = trim($headerLine);
            if ($headerLine !== '' && strpos($headerLine, ':') !== false) {
                list($name, $value) = explode(':', $headerLine, 2);
                $responseHeaders[strtolower(trim($name))][] = trim($value);
            }
            return $length;
        });

        if (!empty($cookies)) {
            $pairs = array();
            foreach ($cookies as $name => $value) {
                $pairs[] = $name . '=' . $value;
            }
            curl_setopt($curl, CURLOPT_COOKIE, implode('; ', $pairs));
        }
        if (strtoupper($method) === 'POST') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query((array) $payload, '', '&'));
        }

        $body = curl_exec($curl);
        $errno = curl_errno($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $newCookies = array();
        foreach (isset($responseHeaders['set-cookie']) ? $responseHeaders['set-cookie'] : array() as $setCookie) {
            $firstPart = explode(';', $setCookie, 2)[0];
            if (strpos($firstPart, '=') !== false) {
                list($name, $value) = explode('=', $firstPart, 2);
                $newCookies[trim($name)] = trim($value);
            }
        }
        $location = !empty($responseHeaders['location']) ? end($responseHeaders['location']) : '';
        return array(
            'transport_success' => $body !== false && $errno === 0 && $status > 0,
            'status' => $status,
            'body' => $body === false ? '' : (string) $body,
            'cookies' => $newCookies,
            'location' => $location
        );
    }

    private function statusData($loggedIn)
    {
        $session = isset($_SESSION[self::HIS_SESSION_KEY]) ? $_SESSION[self::HIS_SESSION_KEY] : array();
        return array(
            'logged_in' => (bool) $loggedIn,
            'reachable' => true,
            'username' => $loggedIn && isset($session['username']) ? (string) $session['username'] : '',
            'logged_in_at' => $loggedIn && isset($session['logged_in_at']) ? date('d/m/Y H:i:s', (int) $session['logged_in_at']) : null,
            'last_verified_at' => $loggedIn && isset($session['last_verified_at']) ? date('d/m/Y H:i:s', (int) $session['last_verified_at']) : null
        );
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
