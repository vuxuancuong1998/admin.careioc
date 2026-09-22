<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo isset($pagetitle) && $pagetitle ? htmlspecialchars($pagetitle, ENT_QUOTES, 'UTF-8') . ' | CARE IOC' : 'Hệ Thống Quản Trị IOC Điều Hành Y Tế Thông Minh'; ?></title>
  
  <!-- Tailwind CSS & Google Fonts -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Phosphor Icons, jQuery & SweetAlert2 -->
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
          },
          colors: {
            brand: {
              50: '#eff6ff',
              100: '#dbeafe',
              200: '#bfdbfe',
              500: '#3b82f6',
              600: '#2563eb', // Xanh dương chủ đạo
              700: '#1d4ed8',
              800: '#1e40af',
              900: '#1e3a8a',
            }
          }
        }
      }
    }
  </script>

  <style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .sidebar-transition {
      transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
  </style>
</head>

<body class="bg-[#f8fafc] text-slate-700 antialiased font-sans flex h-screen overflow-hidden">
<?php
  // Khởi tạo thông tin người dùng đang đăng nhập
  $currentUser = isset($_SESSION['user']) && is_array($_SESSION['user']) ? $_SESSION['user'] : array();
  $currentUserId = isset($currentUser['id']) ? (int) $currentUser['id'] : 0;

  $userDbInfo = null;
  if ($currentUserId > 0) {
    global $db;
    if (isset($db)) {
      $db->query("SELECT id, username, full_name, email, phone, is_admin, is_active, last_login_at, created_at FROM ioc_users WHERE id = $currentUserId LIMIT 1");
      $userDbInfo = $db->fetch_object(true);
      if ($userDbInfo) {
        $currentUser['username'] = $userDbInfo->username;
        $currentUser['fullname'] = $userDbInfo->full_name;
        if (!empty($userDbInfo->email)) $currentUser['email'] = $userDbInfo->email;
        if (!empty($userDbInfo->phone)) $currentUser['phone'] = $userDbInfo->phone;
        $currentUser['is_admin'] = (int) $userDbInfo->is_admin;
        $currentUser['last_login_at'] = $userDbInfo->last_login_at;
        $_SESSION['user'] = array_merge($_SESSION['user'], $currentUser);
      }
    }
  }

  $currentFullName = !empty($currentUser['fullname']) ? $currentUser['fullname'] : (!empty($currentUser['username']) ? $currentUser['username'] : 'Quản trị viên');
  $currentUsername = !empty($currentUser['username']) ? $currentUser['username'] : '';
  $currentUserEmail = !empty($currentUser['email']) ? $currentUser['email'] : 'Chưa cập nhật email';
  $currentUserPhone = !empty($currentUser['phone']) ? $currentUser['phone'] : 'Chưa cập nhật SĐT';
  $isSuperAdmin = !empty($currentUser['is_admin']) && (int) $currentUser['is_admin'] === 1;
  $roleTitle = $isSuperAdmin ? 'Super Admin' : 'Quản trị viên';
  $roleFullTitle = $isSuperAdmin ? 'Quản trị viên cấp cao (Super Admin)' : 'Quản trị viên chuyên mục';

  // Lấy các phân hệ được cấp quyền truy cập
  $siteLabels = array(
    'admin' => 'Trang Quản trị',
    'dashboard' => 'Điều hành IOC',
    'backend' => 'Nghiệp vụ Y tế'
  );
  $userAllowedSites = array();
  if ($isSuperAdmin) {
    $userAllowedSites = $siteLabels;
  } elseif (!empty($currentUser['site_access']) && is_array($currentUser['site_access'])) {
    foreach ($currentUser['site_access'] as $sCode => $hasAcc) {
      if ($hasAcc && isset($siteLabels[$sCode])) {
        $userAllowedSites[$sCode] = $siteLabels[$sCode];
      }
    }
  }
  if (empty($userAllowedSites)) {
    $userAllowedSites['admin'] = 'Trang Quản trị';
  }

  // Khởi tạo chữ cái đại diện (Initials) cho Avatar
  $avatarInitials = 'AD';
  if (!empty($currentFullName)) {
    $parts = preg_split('/\s+/u', trim($currentFullName));
    if (count($parts) >= 2) {
      $avatarInitials = mb_strtoupper(mb_substr($parts[0], 0, 1, 'UTF-8') . mb_substr(end($parts), 0, 1, 'UTF-8'), 'UTF-8');
    } else {
      $avatarInitials = mb_strtoupper(mb_substr($currentFullName, 0, 2, 'UTF-8'), 'UTF-8');
    }
  }

  if (empty($_SESSION['user_action_csrf'])) {
    $_SESSION['user_action_csrf'] = bin2hex(random_bytes(32));
  }
  $userActionCsrf = $_SESSION['user_action_csrf'];
?>

  <!-- OVERLAY CHO MOBILE -->
  <div id="mobile-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-30 hidden lg:hidden"></div>

  <!-- ==================== SIDEBAR TRÁI ==================== -->
  <aside id="sidebar" class="sidebar-transition bg-white border-r border-blue-100 flex flex-col z-40 fixed lg:static h-full w-64 shadow-[2px_0_15px_-3px_rgba(0,0,0,0.04)]">
    
    <!-- Header Logo -->
    <div class="h-16 flex items-center justify-between px-4 border-b border-blue-50">
      <div class="flex items-center gap-3 overflow-hidden">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-700 to-blue-500 flex items-center justify-center text-white text-xl font-bold shadow-md shadow-brand-500/20 shrink-0">
          <i class="ph-bold ph-heartbeat"></i>
        </div>
        <div class="sidebar-text flex flex-col">
          <span class="font-bold text-slate-800 leading-tight tracking-tight text-base">QUẢN TRỊ HỆ THỐNG</span>
          <span class="text-[11px] font-medium text-brand-600 tracking-wider">HỆ THỐNG ĐIỀU HÀNH</span>
        </div>
      </div>
      <button onclick="toggleSidebar()" class="lg:hidden text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100">
        <i class="ph ph-x text-lg"></i>
      </button>
    </div>

    <!-- Danh sách Menu -->
    <div class="flex-1 overflow-y-auto no-scrollbar py-4 px-3 space-y-1">
      <?php
        $menuRoots = array();
        $menuChildren = array();
        foreach (isset($adminMenus) && is_array($adminMenus) ? $adminMenus : array() as $menu) {
          if ($menu->parent_id) $menuChildren[(int) $menu->parent_id][] = $menu;
          else $menuRoots[] = $menu;
        }

        $activeRoute = isset($currentRoute) ? $currentRoute : '';
        if (!$activeRoute) {
          $reqUri = isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '';
          $basePath = parse_url(XC_URL, PHP_URL_PATH);
          if ($basePath && $basePath !== '/' && strpos($reqUri, $basePath) === 0) {
            $activeRoute = substr($reqUri, strlen($basePath));
          } else {
            $activeRoute = $reqUri;
          }
          $activeRoute = '/' . ltrim($activeRoute, '/');
        }
      ?>
      <div class="sidebar-text px-3 py-1.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Menu chức năng</div>
      <?php foreach ($menuRoots as $menu): ?>
        <?php
          $children = isset($menuChildren[(int) $menu->id]) ? $menuChildren[(int) $menu->id] : array();
          $isParentActive = false;
          if ($children) {
            foreach ($children as $c) {
              if (!empty($c->route) && ($c->route === $activeRoute || ($activeRoute !== '/' && strpos($activeRoute, $c->route) === 0))) {
                $isParentActive = true;
                break;
              }
              if (isset($activeMenuCode) && $c->menu_code === $activeMenuCode) {
                $isParentActive = true;
                break;
              }
            }
          } else {
            $isParentActive = (!empty($menu->route) && ($menu->route === $activeRoute || ($menu->route === '/' && ($activeRoute === '' || $activeRoute === '/')))) || (isset($activeMenuCode) && $menu->menu_code === $activeMenuCode);
          }
        ?>
        <?php if ($children): ?>
          <div class="pt-1">
            <button onclick="toggleSubmenu('db-menu-<?php echo (int) $menu->id; ?>', this)" class="w-full flex items-center justify-between px-3 py-2 rounded-xl font-medium text-sm transition-all duration-200 <?php echo $isParentActive ? 'text-brand-700 bg-blue-50/80 font-semibold' : 'text-slate-600 hover:bg-blue-50/60 hover:text-brand-700'; ?> focus:outline-none">
              <div class="flex min-w-0 items-center gap-3">
                <i class="ph <?php echo htmlspecialchars($menu->icon ?: 'ph-folder-notch-open', ENT_QUOTES, 'UTF-8'); ?> text-xl <?php echo $isParentActive ? 'text-brand-600' : 'text-slate-400'; ?> shrink-0"></i>
                <span class="sidebar-text truncate"><?php echo htmlspecialchars($menu->menu_name, ENT_QUOTES, 'UTF-8'); ?></span>
              </div>
              <i class="ph ph-caret-down text-xs <?php echo $isParentActive ? 'text-brand-600 rotate-180' : 'text-slate-400'; ?> transition-transform duration-200 submenu-arrow sidebar-text"></i>
            </button>
            <div id="db-menu-<?php echo (int) $menu->id; ?>" class="<?php echo $isParentActive ? '' : 'hidden'; ?> pl-8 pr-1 py-1 space-y-1">
              <?php foreach ($children as $child): ?>
                <?php
                  $isChildActive = (!empty($child->route) && ($child->route === $activeRoute || ($activeRoute !== '/' && strpos($activeRoute, $child->route) === 0))) || (isset($activeMenuCode) && $child->menu_code === $activeMenuCode);
                ?>
                <a href="<?php echo $child->route ? XC_URL . htmlspecialchars($child->route, ENT_QUOTES, 'UTF-8') : 'javascript:void(0)'; ?>" class="subnav-item flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs transition <?php echo $isChildActive ? 'text-brand-700 font-bold bg-blue-100/70 shadow-xs' : 'font-medium text-slate-600 hover:text-brand-700 hover:bg-blue-50/80'; ?>">
                  <i class="ph <?php echo htmlspecialchars($child->icon ?: 'ph-circle', ENT_QUOTES, 'UTF-8'); ?> text-sm <?php echo $isChildActive ? 'text-brand-600 font-bold' : 'text-blue-500'; ?>"></i>
                  <span class="sidebar-text"><?php echo htmlspecialchars($child->menu_name, ENT_QUOTES, 'UTF-8'); ?></span>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        <?php else: ?>
          <a href="<?php echo $menu->route ? XC_URL . htmlspecialchars($menu->route, ENT_QUOTES, 'UTF-8') : 'javascript:void(0)'; ?>" class="nav-item flex items-center gap-3 px-3 py-2 rounded-xl text-sm transition-all duration-200 <?php echo $isParentActive ? 'font-bold bg-brand-50 text-brand-700 shadow-xs' : 'font-medium text-slate-600 hover:bg-blue-50/60 hover:text-brand-700'; ?>">
            <i class="ph <?php echo htmlspecialchars($menu->icon ?: 'ph-circle', ENT_QUOTES, 'UTF-8'); ?> text-xl <?php echo $isParentActive ? 'text-brand-600' : 'text-slate-400'; ?> shrink-0"></i>
            <span class="sidebar-text truncate"><?php echo htmlspecialchars($menu->menu_name, ENT_QUOTES, 'UTF-8'); ?></span>
          </a>
        <?php endif; ?>
      <?php endforeach; ?>
      <?php if (!$menuRoots): ?>
        <div class="mx-2 rounded-xl bg-amber-50 p-3 text-xs text-amber-700">Tài khoản chưa được cấp menu chức năng.</div>
      <?php endif; ?>

    </div>

    <!-- Sync status -->
    <div class="p-3 border-t border-blue-50">
      <div class="sidebar-text flex items-center gap-2 p-2 rounded-xl bg-blue-50/60 text-xs text-brand-700 border border-blue-100">
        <span class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span>
        <span class="font-medium">Hệ thống điều hành thông minh</span>
      </div>
    </div>
  </aside>

  <!-- ==================== VÙNG NỘI DUNG CHÍNH (BÊN PHẢI) ==================== -->
  <div class="flex-1 flex flex-col h-full min-w-0 overflow-hidden">
    
    <!-- Top Header Navigation Bar -->
    <header class="h-16 bg-white border-b border-blue-100 flex items-center justify-between px-4 sm:px-6 z-20 shrink-0">
      
      <div class="flex items-center gap-3">
        <button onclick="toggleSidebar()" class="p-2 rounded-xl text-slate-500 hover:bg-blue-50 hover:text-brand-600 transition-colors" title="Ẩn/Hiện menu">
          <i class="ph ph-list text-xl font-bold"></i>
        </button>
        <div class="h-5 w-px bg-slate-200 hidden sm:block"></div>
        <h1 id="page-title" class="text-base sm:text-lg font-bold text-slate-800 tracking-tight truncate"><?php echo isset($pagetitle) && $pagetitle ? htmlspecialchars($pagetitle, ENT_QUOTES, 'UTF-8') : 'QUẢN TRỊ HỆ THỐNG'; ?></h1>
      </div>

      <div class="flex items-center gap-3 sm:gap-4">
        <!-- Search bar -->
        <div class="relative hidden md:block w-64">
          <input type="text" placeholder="Tìm kiếm nhanh khoa, bệnh nhân..." class="w-full pl-9 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-brand-500 focus:bg-white transition-all text-slate-700">
          <i class="ph ph-magnifying-glass absolute left-3 top-2 text-slate-400 text-sm"></i>
        </div>

        <!-- Chuông thông báo -->
        <button class="relative p-2 rounded-xl text-slate-500 hover:bg-blue-50 hover:text-brand-600 transition">
          <i class="ph ph-bell text-xl"></i>
          <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white"></span>
        </button>

        <!-- Dropdown User -->
        <div class="relative">
          <button id="user-dropdown-btn" onclick="toggleUserDropdown(event)" class="group flex items-center gap-2.5 p-1.5 rounded-xl hover:bg-blue-50/80 transition focus:outline-none" title="Tài khoản: <?php echo htmlspecialchars($currentFullName, ENT_QUOTES, 'UTF-8'); ?> (<?php echo htmlspecialchars($roleTitle, ENT_QUOTES, 'UTF-8'); ?>)">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand-700 to-blue-500 flex items-center justify-center text-white font-bold text-xs shadow-sm ring-2 ring-blue-100 group-hover:ring-brand-200 transition select-none tracking-wider uppercase shrink-0">
              <?php echo htmlspecialchars($avatarInitials, ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <div class="hidden sm:flex flex-col text-left max-w-[140px] md:max-w-[170px]">
              <span class="text-xs font-bold text-slate-800 leading-tight truncate group-hover:text-brand-700 transition">
                <?php echo htmlspecialchars($currentFullName, ENT_QUOTES, 'UTF-8'); ?>
              </span>
              <span class="text-[11px] text-brand-600 font-medium truncate">
                <?php echo htmlspecialchars($currentUserEmail, ENT_QUOTES, 'UTF-8'); ?>
              </span>
            </div>
            <i class="ph ph-caret-down text-slate-400 group-hover:text-brand-600 text-xs hidden sm:block transition-transform duration-200" id="user-dropdown-caret"></i>
          </button>

          <!-- Dropdown menu -->
          <div id="user-dropdown" class="hidden absolute right-0 mt-2.5 w-72 sm:w-80 bg-white rounded-2xl shadow-xl shadow-slate-200/80 border border-blue-100 py-2 z-50 transition-all divide-y divide-slate-100">
            <!-- Thông tin tóm tắt user -->
            <div class="px-4 py-3 bg-gradient-to-br from-blue-50/80 via-sky-50/40 to-white rounded-t-2xl">
              <div class="flex items-start gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-brand-700 to-blue-500 flex items-center justify-center text-white font-bold text-sm shadow-md shadow-brand-500/20 ring-2 ring-white select-none shrink-0 tracking-wider uppercase">
                  <?php echo htmlspecialchars($avatarInitials, ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <div class="min-w-0 flex-1">
                  <div class="flex items-center gap-1.5 flex-wrap">
                    <h4 class="text-xs font-bold text-slate-900 truncate" title="<?php echo htmlspecialchars($currentFullName, ENT_QUOTES, 'UTF-8'); ?>">
                      <?php echo htmlspecialchars($currentFullName, ENT_QUOTES, 'UTF-8'); ?>
                    </h4>
                    <?php if ($isSuperAdmin): ?>
                      <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">Super Admin</span>
                    <?php else: ?>
                      <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-brand-700 border border-blue-200">Quản trị viên</span>
                    <?php endif; ?>
                  </div>
                  <?php if (!empty($currentUsername)): ?>
                    <p class="text-[11px] text-slate-500 font-medium truncate mt-0.5">
                      <span class="text-slate-400">@</span><?php echo htmlspecialchars($currentUsername, ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Chi tiết liên hệ nhanh -->
              <div class="mt-3 pt-2.5 border-t border-blue-100/70 space-y-1.5 text-[11px]">
                <div class="flex items-center gap-2 text-slate-600">
                  <i class="ph ph-envelope-simple text-sm text-brand-600 shrink-0"></i>
                  <span class="truncate font-medium" title="<?php echo htmlspecialchars($currentUserEmail, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php echo htmlspecialchars($currentUserEmail, ENT_QUOTES, 'UTF-8'); ?>
                  </span>
                </div>
                <?php if (!empty($currentUser['phone'])): ?>
                <div class="flex items-center gap-2 text-slate-600">
                  <i class="ph ph-phone text-sm text-brand-600 shrink-0"></i>
                  <span class="truncate font-medium"><?php echo htmlspecialchars($currentUser['phone'], ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <?php endif; ?>
                <div class="flex items-center gap-2 text-slate-600">
                  <i class="ph ph-shield-check text-sm text-emerald-600 shrink-0"></i>
                  <span class="text-slate-500">Phân hệ: <span class="font-semibold text-slate-700"><?php echo htmlspecialchars(implode(', ', array_keys($userAllowedSites)), ENT_QUOTES, 'UTF-8'); ?></span></span>
                </div>
              </div>
            </div>

            <!-- Các hành động -->
            <div class="p-1.5 space-y-0.5">
              <button type="button" onclick="openUserProfileModal()" class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-blue-50 hover:text-brand-700 rounded-xl transition text-left group">
                <div class="w-7 h-7 rounded-lg bg-blue-50 group-hover:bg-blue-100 flex items-center justify-center text-brand-600 shrink-0 transition">
                  <i class="ph ph-user text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="font-semibold text-slate-800 group-hover:text-brand-700">Thông tin cá nhân</div>
                  <div class="text-[10px] text-slate-400">Xem đầy đủ hồ sơ & phân quyền</div>
                </div>
                <i class="ph ph-caret-right text-slate-300 group-hover:text-brand-600 text-xs transition"></i>
              </button>

              <button type="button" onclick="openChangePasswordModal()" class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-amber-50 hover:text-amber-800 rounded-xl transition text-left group">
                <div class="w-7 h-7 rounded-lg bg-amber-50 group-hover:bg-amber-100 flex items-center justify-center text-amber-600 shrink-0 transition">
                  <i class="ph ph-key text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="font-semibold text-slate-800 group-hover:text-amber-800">Đổi mật khẩu</div>
                  <div class="text-[10px] text-slate-400">Cập nhật mật khẩu bảo mật</div>
                </div>
                <i class="ph ph-caret-right text-slate-300 group-hover:text-amber-600 text-xs transition"></i>
              </button>
            </div>

            <!-- Nút đăng xuất -->
            <div class="p-1.5">
              <button type="button" onclick="logoutAction()" class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-xl transition text-left group">
                <div class="w-7 h-7 rounded-lg bg-rose-50 group-hover:bg-rose-100 flex items-center justify-center text-rose-600 shrink-0 transition">
                  <i class="ph ph-sign-out text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="font-semibold text-rose-600">Đăng xuất hệ thống</div>
                  <div class="text-[10px] text-rose-400">Kết thúc phiên làm việc</div>
                </div>
              </button>
            </div>
          </div>
        </div>

      </div>
    </header>