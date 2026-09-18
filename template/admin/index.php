<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Hệ Thống Quản Trị IOC Điều Hành Y Tế Thông Minh</title>
  
  <!-- Tailwind CSS & Google Fonts -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Phosphor Icons -->
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
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
          <span class="font-bold text-slate-800 leading-tight tracking-tight text-base">IOC HOSPITAL</span>
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
      ?>
      <div class="sidebar-text px-3 py-1.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Menu chức năng</div>
      <?php foreach ($menuRoots as $menu): ?>
        <?php $children = isset($menuChildren[(int) $menu->id]) ? $menuChildren[(int) $menu->id] : array(); ?>
        <?php if ($children): ?>
          <div class="pt-1">
            <button onclick="toggleSubmenu('db-menu-<?php echo (int) $menu->id; ?>', this)" class="w-full flex items-center justify-between px-3 py-2 rounded-xl font-medium text-sm transition-all duration-200 text-slate-600 hover:bg-blue-50/60 hover:text-brand-700 focus:outline-none">
              <div class="flex min-w-0 items-center gap-3">
                <i class="ph <?php echo htmlspecialchars($menu->icon ?: 'ph-folder-notch-open', ENT_QUOTES, 'UTF-8'); ?> text-xl text-slate-400 shrink-0"></i>
                <span class="sidebar-text truncate"><?php echo htmlspecialchars($menu->menu_name, ENT_QUOTES, 'UTF-8'); ?></span>
              </div>
              <i class="ph ph-caret-down text-xs text-slate-400 transition-transform duration-200 submenu-arrow sidebar-text"></i>
            </button>
            <div id="db-menu-<?php echo (int) $menu->id; ?>" class="hidden pl-8 pr-1 py-1 space-y-1">
              <?php foreach ($children as $child): ?>
                <a href="<?php echo $child->route ? XC_URL . htmlspecialchars($child->route, ENT_QUOTES, 'UTF-8') : 'javascript:void(0)'; ?>" class="subnav-item flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:text-brand-700 hover:bg-blue-50/80 transition">
                  <i class="ph <?php echo htmlspecialchars($child->icon ?: 'ph-circle', ENT_QUOTES, 'UTF-8'); ?> text-sm text-blue-500"></i>
                  <span class="sidebar-text"><?php echo htmlspecialchars($child->menu_name, ENT_QUOTES, 'UTF-8'); ?></span>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        <?php else: ?>
          <a href="<?php echo $menu->route ? XC_URL . htmlspecialchars($menu->route, ENT_QUOTES, 'UTF-8') : 'javascript:void(0)'; ?>" class="nav-item flex items-center gap-3 px-3 py-2 rounded-xl font-medium text-sm transition-all duration-200 text-slate-600 hover:bg-blue-50/60 hover:text-brand-700">
            <i class="ph <?php echo htmlspecialchars($menu->icon ?: 'ph-circle', ENT_QUOTES, 'UTF-8'); ?> text-xl text-slate-400 shrink-0"></i>
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
        <span class="font-medium">Data Sync: Realtime API</span>
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
        <h1 id="page-title" class="text-base sm:text-lg font-bold text-slate-800 tracking-tight truncate">Tổng Quan IOC</h1>
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
          <button onclick="toggleUserDropdown(event)" class="flex items-center gap-2.5 p-1.5 rounded-xl hover:bg-blue-50 transition focus:outline-none">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand-700 to-blue-500 flex items-center justify-center text-white font-semibold text-sm shadow-sm ring-2 ring-blue-100">
              AD
            </div>
            <div class="hidden sm:flex flex-col text-left">
              <span class="text-xs font-bold text-slate-800 leading-tight">Admin Hệ Thống</span>
              <span class="text-[11px] text-brand-600 font-medium">ioc.admin@hospital.gov.vn</span>
            </div>
            <i class="ph ph-caret-down text-slate-400 text-xs hidden sm:block"></i>
          </button>

          <!-- Dropdown menu -->
          <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-blue-100 py-1.5 z-50">
            <div class="px-4 py-2 border-b border-slate-100">
              <p class="text-xs text-slate-400">Tài khoản quản trị</p>
              <p class="text-xs font-bold text-slate-800 truncate"><?php echo htmlspecialchars(isset($_SESSION['user']['fullname']) ? $_SESSION['user']['fullname'] : $_SESSION['user']['username'], ENT_QUOTES, 'UTF-8'); ?> (Admin)</p>
            </div>
            
            <div class="py-1">
              <a href="javascript:void(0)" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-600 hover:bg-blue-50 hover:text-brand-600 transition">
                <i class="ph ph-user text-base"></i> Thông tin cá nhân
              </a>
              <a href="javascript:void(0)" onclick="openChangePasswordModal()" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-600 hover:bg-blue-50 hover:text-brand-600 transition">
                <i class="ph ph-key text-base text-amber-500"></i> Đổi mật khẩu
              </a>
            </div>

            <div class="border-t border-slate-100 pt-1">
              <button onclick="logoutAction()" class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition text-left">
                <i class="ph ph-sign-out text-base"></i> Đăng xuất hệ thống
              </button>
            </div>
          </div>
        </div>

      </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 overflow-y-auto p-4 sm:p-6 no-scrollbar">
      
      <!-- TAB 1: TỔNG QUAN IOC DASHBOARD -->
      <section id="tab-dashboard" class="tab-content space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-white p-5 rounded-2xl border border-blue-100 shadow-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs font-medium text-slate-500">Lượt khám hôm nay</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">1,842</h3>
              </div>
              <div class="w-11 h-11 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-2xl">
                <i class="ph-bold ph-users-three"></i>
              </div>
            </div>
            <p class="mt-3 text-xs text-brand-600 font-semibold flex items-center gap-1">
              <i class="ph-bold ph-arrow-up-right"></i> +8.4% so với hôm qua
            </p>
          </div>

          <div class="bg-white p-5 rounded-2xl border border-blue-100 shadow-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs font-medium text-slate-500">Công suất giường viện</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">94.8%</h3>
              </div>
              <div class="w-11 h-11 rounded-xl bg-blue-50 text-brand-600 flex items-center justify-center text-2xl">
                <i class="ph-bold ph-bed"></i>
              </div>
            </div>
            <p class="mt-3 text-xs text-amber-500 font-semibold">568 / 600 giường đang sử dụng</p>
          </div>

          <div class="bg-white p-5 rounded-2xl border border-blue-100 shadow-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs font-medium text-slate-500">Tỷ lệ Bệnh Án Điện Tử</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">98.2%</h3>
              </div>
              <div class="w-11 h-11 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-2xl">
                <i class="ph-bold ph-file-arrow-up"></i>
              </div>
            </div>
            <p class="mt-3 text-xs text-brand-600 font-semibold">Ký số đúng hạn: 96.5%</p>
          </div>

          <div class="bg-white p-5 rounded-2xl border border-blue-100 shadow-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs font-medium text-slate-500">Trạng thái Server / HIS</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-1">Online</h3>
              </div>
              <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl">
                <i class="ph-bold ph-shield-check"></i>
              </div>
            </div>
            <p class="mt-3 text-xs text-slate-500 font-medium">Uptime: 99.98% (Độ trễ 14ms)</p>
          </div>
        </div>
      </section>

      <!-- ==================== TAB SUBMENU 1: KHOA PHÒNG ==================== -->
      <section id="tab-category-departments" class="tab-content hidden space-y-5">
        <!-- Control bar -->
        <div class="bg-white p-4 rounded-2xl border border-blue-100 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
          <div class="flex items-center gap-2 w-full sm:w-auto">
            <div class="relative w-full sm:w-72">
              <input type="text" placeholder="Tìm theo mã hoặc tên khoa..." class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-500">
              <i class="ph ph-magnifying-glass absolute left-3 top-2.5 text-slate-400"></i>
            </div>
            <select class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-600 focus:outline-none">
              <option value="">Tất cả loại khoa</option>
              <option value="inpatient">Nội trú</option>
              <option value="outpatient">Ngoại trú</option>
              <option value="paraclinical">Cận lâm sàng</option>
            </select>
          </div>

          <button onclick="handleAction('Thêm mới Khoa Phòng')" class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-brand-500/20 transition">
            <i class="ph-bold ph-plus"></i> Thêm Khoa Phòng
          </button>
        </div>

        <!-- Data Table Khoa Phòng -->
        <div class="bg-white rounded-2xl border border-blue-100 shadow-sm overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
              <thead>
                <tr class="bg-blue-50/50 text-slate-500 border-b border-blue-100 font-semibold uppercase text-[11px] tracking-wider">
                  <th class="py-3.5 px-4">Mã Khoa</th>
                  <th class="py-3.5 px-4">Tên Khoa / Phòng</th>
                  <th class="py-3.5 px-4">Phân Loại</th>
                  <th class="py-3.5 px-4 text-center">Giường Kế Hoạch</th>
                  <th class="py-3.5 px-4 text-center">Giường Thực Kê</th>
                  <th class="py-3.5 px-4 text-center">Trạng Thái</th>
                  <th class="py-3.5 px-4 text-center w-28">Thao Tác</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                <tr class="hover:bg-blue-50/30 transition">
                  <td class="py-3.5 px-4 font-bold text-brand-700">KKB-01</td>
                  <td class="py-3.5 px-4 font-semibold text-slate-800">Khoa Khám Bệnh Đa Khoa</td>
                  <td class="py-3.5 px-4"><span class="px-2.5 py-1 rounded-full text-[11px] bg-slate-100 text-slate-600">Ngoại trú</span></td>
                  <td class="py-3.5 px-4 text-center">0</td>
                  <td class="py-3.5 px-4 text-center">0</td>
                  <td class="py-3.5 px-4 text-center"><span class="px-2 py-0.5 rounded-full text-[11px] bg-emerald-50 text-emerald-600 font-semibold">Hoạt động</span></td>
                  <td class="py-3.5 px-4">
                    <div class="flex items-center justify-center gap-1.5 text-base">
                      <button onclick="handleAction('Xem chi tiết Khoa: KKB-01')" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Xem chi tiết"><i class="ph ph-eye"></i></button>
                      <button onclick="handleAction('Chỉnh sửa Khoa: KKB-01')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-lg transition" title="Sửa"><i class="ph ph-pencil-simple"></i></button>
                      <button onclick="handleAction('Xóa Khoa: KKB-01')" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition" title="Xóa"><i class="ph ph-trash"></i></button>
                    </div>
                  </td>
                </tr>
                <tr class="hover:bg-blue-50/30 transition">
                  <td class="py-3.5 px-4 font-bold text-brand-700">KNT-TM</td>
                  <td class="py-3.5 px-4 font-semibold text-slate-800">Khoa Nội Tim Mạch Can Thiệp</td>
                  <td class="py-3.5 px-4"><span class="px-2.5 py-1 rounded-full text-[11px] bg-blue-100 text-brand-700 font-semibold">Nội trú</span></td>
                  <td class="py-3.5 px-4 text-center font-bold">50</td>
                  <td class="py-3.5 px-4 text-center font-bold text-brand-700">62</td>
                  <td class="py-3.5 px-4 text-center"><span class="px-2 py-0.5 rounded-full text-[11px] bg-emerald-50 text-emerald-600 font-semibold">Hoạt động</span></td>
                  <td class="py-3.5 px-4">
                    <div class="flex items-center justify-center gap-1.5 text-base">
                      <button onclick="handleAction('Xem chi tiết Khoa: KNT-TM')" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Xem chi tiết"><i class="ph ph-eye"></i></button>
                      <button onclick="handleAction('Chỉnh sửa Khoa: KNT-TM')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-lg transition" title="Sửa"><i class="ph ph-pencil-simple"></i></button>
                      <button onclick="handleAction('Xóa Khoa: KNT-TM')" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition" title="Xóa"><i class="ph ph-trash"></i></button>
                    </div>
                  </td>
                </tr>
                <tr class="hover:bg-blue-50/30 transition">
                  <td class="py-3.5 px-4 font-bold text-brand-700">KHSCC</td>
                  <td class="py-3.5 px-4 font-semibold text-slate-800">Khoa Hồi Sức Cấp Cứu & Chống Độc</td>
                  <td class="py-3.5 px-4"><span class="px-2.5 py-1 rounded-full text-[11px] bg-blue-100 text-brand-700 font-semibold">Nội trú</span></td>
                  <td class="py-3.5 px-4 text-center font-bold">30</td>
                  <td class="py-3.5 px-4 text-center font-bold text-brand-700">35</td>
                  <td class="py-3.5 px-4 text-center"><span class="px-2 py-0.5 rounded-full text-[11px] bg-emerald-50 text-emerald-600 font-semibold">Hoạt động</span></td>
                  <td class="py-3.5 px-4">
                    <div class="flex items-center justify-center gap-1.5 text-base">
                      <button onclick="handleAction('Xem chi tiết Khoa: KHSCC')" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Xem chi tiết"><i class="ph ph-eye"></i></button>
                      <button onclick="handleAction('Chỉnh sửa Khoa: KHSCC')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-lg transition" title="Sửa"><i class="ph ph-pencil-simple"></i></button>
                      <button onclick="handleAction('Xóa Khoa: KHSCC')" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition" title="Xóa"><i class="ph ph-trash"></i></button>
                    </div>
                  </td>
                </tr>
                <tr class="hover:bg-blue-50/30 transition">
                  <td class="py-3.5 px-4 font-bold text-brand-700">CLS-HA</td>
                  <td class="py-3.5 px-4 font-semibold text-slate-800">Khoa Chẩn Đoán Hình Ảnh (PACS/RIS)</td>
                  <td class="py-3.5 px-4"><span class="px-2.5 py-1 rounded-full text-[11px] bg-purple-100 text-purple-700">Cận lâm sàng</span></td>
                  <td class="py-3.5 px-4 text-center">0</td>
                  <td class="py-3.5 px-4 text-center">0</td>
                  <td class="py-3.5 px-4 text-center"><span class="px-2 py-0.5 rounded-full text-[11px] bg-emerald-50 text-emerald-600 font-semibold">Hoạt động</span></td>
                  <td class="py-3.5 px-4">
                    <div class="flex items-center justify-center gap-1.5 text-base">
                      <button onclick="handleAction('Xem chi tiết Khoa: CLS-HA')" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Xem chi tiết"><i class="ph ph-eye"></i></button>
                      <button onclick="handleAction('Chỉnh sửa Khoa: CLS-HA')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-lg transition" title="Sửa"><i class="ph ph-pencil-simple"></i></button>
                      <button onclick="handleAction('Xóa Khoa: CLS-HA')" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition" title="Xóa"><i class="ph ph-trash"></i></button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- Pagination -->
          <div class="px-4 py-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Hiển thị 1 - 4 trên tổng số 18 khoa phòng</span>
            <div class="flex gap-1">
              <button class="px-2.5 py-1 border rounded-lg hover:bg-slate-50 disabled:opacity-50" disabled>Trước</button>
              <button class="px-2.5 py-1 border rounded-lg bg-brand-600 text-white font-bold">1</button>
              <button class="px-2.5 py-1 border rounded-lg hover:bg-slate-50">2</button>
              <button class="px-2.5 py-1 border rounded-lg hover:bg-slate-50">Sau</button>
            </div>
          </div>
        </div>
      </section>

      <!-- ==================== TAB SUBMENU 2: GIƯỜNG BỆNH ==================== -->
      <section id="tab-category-beds" class="tab-content hidden space-y-5">
        <div class="bg-white p-4 rounded-2xl border border-blue-100 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
          <div class="flex items-center gap-2 w-full sm:w-auto">
            <div class="relative w-full sm:w-72">
              <input type="text" placeholder="Tìm theo mã giường, số phòng..." class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-500">
              <i class="ph ph-magnifying-glass absolute left-3 top-2.5 text-slate-400"></i>
            </div>
            <select class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-600 focus:outline-none">
              <option value="">Tất cả trạng thái</option>
              <option value="occupied">Đang nằm</option>
              <option value="available">Giường trống</option>
              <option value="maintenance">Bảo trì</option>
            </select>
          </div>

          <button onclick="handleAction('Thêm Giường Mới')" class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-brand-500/20 transition">
            <i class="ph-bold ph-plus"></i> Thêm Giường Bệnh
          </button>
        </div>

        <!-- Data Table Giường Bệnh -->
        <div class="bg-white rounded-2xl border border-blue-100 shadow-sm overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
              <thead>
                <tr class="bg-blue-50/50 text-slate-500 border-b border-blue-100 font-semibold uppercase text-[11px] tracking-wider">
                  <th class="py-3.5 px-4">Mã Giường</th>
                  <th class="py-3.5 px-4">Khoa Trực Thuộc</th>
                  <th class="py-3.5 px-4">Số Phòng</th>
                  <th class="py-3.5 px-4">Loại Giường</th>
                  <th class="py-3.5 px-4 text-center">Trạng Thái</th>
                  <th class="py-3.5 px-4 text-center">Bệnh Nhân Hiện Tại</th>
                  <th class="py-3.5 px-4 text-center w-28">Thao Tác</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                <tr class="hover:bg-blue-50/30 transition">
                  <td class="py-3.5 px-4 font-bold text-brand-700">P201-G01</td>
                  <td class="py-3.5 px-4 font-semibold text-slate-800">Khoa Nội Tim Mạch</td>
                  <td class="py-3.5 px-4">Phòng 201 (Khu A)</td>
                  <td class="py-3.5 px-4"><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium">Tiêu chuẩn</span></td>
                  <td class="py-3.5 px-4 text-center"><span class="px-2.5 py-1 rounded-full text-[11px] bg-rose-50 text-rose-600 font-semibold">Đang nằm</span></td>
                  <td class="py-3.5 px-4 text-center font-bold text-slate-800">Nguyễn Văn Bình (PID: 108422)</td>
                  <td class="py-3.5 px-4">
                    <div class="flex items-center justify-center gap-1.5 text-base">
                      <button onclick="handleAction('Xem chi tiết Giường: P201-G01')" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Xem chi tiết"><i class="ph ph-eye"></i></button>
                      <button onclick="handleAction('Chỉnh sửa Giường: P201-G01')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-lg transition" title="Sửa"><i class="ph ph-pencil-simple"></i></button>
                      <button onclick="handleAction('Xóa Giường: P201-G01')" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition" title="Xóa"><i class="ph ph-trash"></i></button>
                    </div>
                  </td>
                </tr>
                <tr class="hover:bg-blue-50/30 transition">
                  <td class="py-3.5 px-4 font-bold text-brand-700">P201-G02</td>
                  <td class="py-3.5 px-4 font-semibold text-slate-800">Khoa Nội Tim Mạch</td>
                  <td class="py-3.5 px-4">Phòng 201 (Khu A)</td>
                  <td class="py-3.5 px-4"><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium">Tiêu chuẩn</span></td>
                  <td class="py-3.5 px-4 text-center"><span class="px-2.5 py-1 rounded-full text-[11px] bg-emerald-50 text-emerald-600 font-semibold">Giường trống</span></td>
                  <td class="py-3.5 px-4 text-center text-slate-400 font-normal">-- Không có --</td>
                  <td class="py-3.5 px-4">
                    <div class="flex items-center justify-center gap-1.5 text-base">
                      <button onclick="handleAction('Xem chi tiết Giường: P201-G02')" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Xem chi tiết"><i class="ph ph-eye"></i></button>
                      <button onclick="handleAction('Chỉnh sửa Giường: P201-G02')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-lg transition" title="Sửa"><i class="ph ph-pencil-simple"></i></button>
                      <button onclick="handleAction('Xóa Giường: P201-G02')" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition" title="Xóa"><i class="ph ph-trash"></i></button>
                    </div>
                  </td>
                </tr>
                <tr class="hover:bg-blue-50/30 transition">
                  <td class="py-3.5 px-4 font-bold text-brand-700">ICU-G05</td>
                  <td class="py-3.5 px-4 font-semibold text-slate-800">Hồi Sức Cấp Cứu</td>
                  <td class="py-3.5 px-4">Phòng Hồi sức đặc biệt</td>
                  <td class="py-3.5 px-4"><span class="px-2 py-0.5 rounded bg-purple-100 text-purple-700 font-semibold">ICU Đa năng</span></td>
                  <td class="py-3.5 px-4 text-center"><span class="px-2.5 py-1 rounded-full text-[11px] bg-rose-50 text-rose-600 font-semibold">Đang nằm</span></td>
                  <td class="py-3.5 px-4 text-center font-bold text-slate-800">Trần Thị Mai (PID: 108511)</td>
                  <td class="py-3.5 px-4">
                    <div class="flex items-center justify-center gap-1.5 text-base">
                      <button onclick="handleAction('Xem chi tiết Giường: ICU-G05')" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Xem chi tiết"><i class="ph ph-eye"></i></button>
                      <button onclick="handleAction('Chỉnh sửa Giường: ICU-G05')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-lg transition" title="Sửa"><i class="ph ph-pencil-simple"></i></button>
                      <button onclick="handleAction('Xóa Giường: ICU-G05')" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition" title="Xóa"><i class="ph ph-trash"></i></button>
                    </div>
                  </td>
                </tr>
                <tr class="hover:bg-blue-50/30 transition">
                  <td class="py-3.5 px-4 font-bold text-brand-700">VIP-G01</td>
                  <td class="py-3.5 px-4 font-semibold text-slate-800">Khoa Ngoại Tiêu Hóa</td>
                  <td class="py-3.5 px-4">Phòng Yêu cầu VIP 1</td>
                  <td class="py-3.5 px-4"><span class="px-2 py-0.5 rounded bg-amber-100 text-amber-700 font-semibold">Theo yêu cầu</span></td>
                  <td class="py-3.5 px-4 text-center"><span class="px-2.5 py-1 rounded-full text-[11px] bg-amber-50 text-amber-600 font-semibold">Bảo dưỡng</span></td>
                  <td class="py-3.5 px-4 text-center text-slate-400 font-normal">-- Tạm khóa --</td>
                  <td class="py-3.5 px-4">
                    <div class="flex items-center justify-center gap-1.5 text-base">
                      <button onclick="handleAction('Xem chi tiết Giường: VIP-G01')" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Xem chi tiết"><i class="ph ph-eye"></i></button>
                      <button onclick="handleAction('Chỉnh sửa Giường: VIP-G01')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-lg transition" title="Sửa"><i class="ph ph-pencil-simple"></i></button>
                      <button onclick="handleAction('Xóa Giường: VIP-G01')" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition" title="Xóa"><i class="ph ph-trash"></i></button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="px-4 py-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Hiển thị 1 - 4 trên tổng số 600 giường</span>
            <div class="flex gap-1">
              <button class="px-2.5 py-1 border rounded-lg bg-brand-600 text-white font-bold">1</button>
              <button class="px-2.5 py-1 border rounded-lg hover:bg-slate-50">2</button>
              <button class="px-2.5 py-1 border rounded-lg hover:bg-slate-50">...</button>
              <button class="px-2.5 py-1 border rounded-lg hover:bg-slate-50">150</button>
            </div>
          </div>
        </div>
      </section>

      <!-- ==================== TAB SUBMENU 3: NHÂN VIÊN ==================== -->
      <section id="tab-category-staff" class="tab-content hidden space-y-5">
        <div class="bg-white p-4 rounded-2xl border border-blue-100 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
          <div class="flex items-center gap-2 w-full sm:w-auto">
            <div class="relative w-full sm:w-72">
              <input type="text" placeholder="Tìm theo tên, mã NV, CCHN..." class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-500">
              <i class="ph ph-magnifying-glass absolute left-3 top-2.5 text-slate-400"></i>
            </div>
            <select class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-600 focus:outline-none">
              <option value="">Tất cả chức vụ</option>
              <option value="doctor">Bác sĩ</option>
              <option value="nurse">Điều dưỡng</option>
              <option value="tech">Kỹ thuật viên</option>
            </select>
          </div>

          <button onclick="handleAction('Thêm Nhân Viên Mới')" class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-brand-500/20 transition">
            <i class="ph-bold ph-user-plus"></i> Thêm Nhân Viên
          </button>
        </div>

        <!-- Data Table Nhân Viên -->
        <div class="bg-white rounded-2xl border border-blue-100 shadow-sm overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
              <thead>
                <tr class="bg-blue-50/50 text-slate-500 border-b border-blue-100 font-semibold uppercase text-[11px] tracking-wider">
                  <th class="py-3.5 px-4">Mã NV</th>
                  <th class="py-3.5 px-4">Họ và Tên</th>
                  <th class="py-3.5 px-4">Khoa / Bộ Phận</th>
                  <th class="py-3.5 px-4">Chức Danh</th>
                  <th class="py-3.5 px-4">Số CCHN / Ký Số</th>
                  <th class="py-3.5 px-4 text-center">Trạng Thái</th>
                  <th class="py-3.5 px-4 text-center w-28">Thao Tác</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                <tr class="hover:bg-blue-50/30 transition">
                  <td class="py-3.5 px-4 font-bold text-brand-700">BS-0089</td>
                  <td class="py-3.5 px-4">
                    <div class="flex items-center gap-2.5">
                      <div class="w-8 h-8 rounded-full bg-blue-100 text-brand-700 flex items-center justify-center font-bold text-xs">
                        TQ
                      </div>
                      <div>
                        <p class="font-bold text-slate-800">TS.BS Trần Quốc Tuấn</p>
                        <p class="text-[11px] text-slate-400">tuantq@hospital.gov.vn</p>
                      </div>
                    </div>
                  </td>
                  <td class="py-3.5 px-4 font-semibold text-slate-700">Khoa Nội Tim Mạch</td>
                  <td class="py-3.5 px-4"><span class="px-2.5 py-1 rounded-md text-[11px] bg-blue-50 text-brand-700 font-semibold">Trưởng Khoa</span></td>
                  <td class="py-3.5 px-4">
                    <span class="font-mono text-slate-600">001248/BYT-CCHN</span>
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 ml-1.5" title="Đã cấu hình chữ ký số HSM"></span>
                  </td>
                  <td class="py-3.5 px-4 text-center"><span class="px-2 py-0.5 rounded-full text-[11px] bg-emerald-50 text-emerald-600 font-semibold">Đang làm việc</span></td>
                  <td class="py-3.5 px-4">
                    <div class="flex items-center justify-center gap-1.5 text-base">
                      <button onclick="handleAction('Xem hồ sơ: TS.BS Trần Quốc Tuấn')" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Xem chi tiết"><i class="ph ph-eye"></i></button>
                      <button onclick="handleAction('Sửa thông tin: TS.BS Trần Quốc Tuấn')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-lg transition" title="Sửa"><i class="ph ph-pencil-simple"></i></button>
                      <button onclick="handleAction('Khóa/Xóa NV: TS.BS Trần Quốc Tuấn')" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition" title="Xóa"><i class="ph ph-trash"></i></button>
                    </div>
                  </td>
                </tr>
                <tr class="hover:bg-blue-50/30 transition">
                  <td class="py-3.5 px-4 font-bold text-brand-700">DD-0142</td>
                  <td class="py-3.5 px-4">
                    <div class="flex items-center gap-2.5">
                      <div class="w-8 h-8 rounded-full bg-pink-100 text-pink-700 flex items-center justify-center font-bold text-xs">
                        MH
                      </div>
                      <div>
                        <p class="font-bold text-slate-800">CNĐD. Lê Mỹ Hạnh</p>
                        <p class="text-[11px] text-slate-400">hanhlm@hospital.gov.vn</p>
                      </div>
                    </div>
                  </td>
                  <td class="py-3.5 px-4 font-semibold text-slate-700">Hồi Sức Cấp Cứu</td>
                  <td class="py-3.5 px-4"><span class="px-2.5 py-1 rounded-md text-[11px] bg-slate-100 text-slate-700 font-medium">Điều dưỡng chính</span></td>
                  <td class="py-3.5 px-4"><span class="font-mono text-slate-600">009842/BYT-CCHN</span></td>
                  <td class="py-3.5 px-4 text-center"><span class="px-2 py-0.5 rounded-full text-[11px] bg-emerald-50 text-emerald-600 font-semibold">Đang làm việc</span></td>
                  <td class="py-3.5 px-4">
                    <div class="flex items-center justify-center gap-1.5 text-base">
                      <button onclick="handleAction('Xem hồ sơ: Lê Mỹ Hạnh')" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Xem chi tiết"><i class="ph ph-eye"></i></button>
                      <button onclick="handleAction('Sửa thông tin: Lê Mỹ Hạnh')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-lg transition" title="Sửa"><i class="ph ph-pencil-simple"></i></button>
                      <button onclick="handleAction('Khóa/Xóa NV: Lê Mỹ Hạnh')" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition" title="Xóa"><i class="ph ph-trash"></i></button>
                    </div>
                  </td>
                </tr>
                <tr class="hover:bg-blue-50/30 transition">
                  <td class="py-3.5 px-4 font-bold text-brand-700">IT-0005</td>
                  <td class="py-3.5 px-4">
                    <div class="flex items-center gap-2.5">
                      <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">
                        VC
                      </div>
                      <div>
                        <p class="font-bold text-slate-800">KS. Vũ Xuân Cường</p>
                        <p class="text-[11px] text-slate-400">cuongvx@hospital.gov.vn</p>
                      </div>
                    </div>
                  </td>
                  <td class="py-3.5 px-4 font-semibold text-slate-700">Phòng CNTT & Chuyển Đổi Số</td>
                  <td class="py-3.5 px-4"><span class="px-2.5 py-1 rounded-md text-[11px] bg-purple-50 text-purple-700 font-semibold">Quản trị DB/IOC</span></td>
                  <td class="py-3.5 px-4"><span class="font-mono text-slate-400">-- N/A --</span></td>
                  <td class="py-3.5 px-4 text-center"><span class="px-2 py-0.5 rounded-full text-[11px] bg-emerald-50 text-emerald-600 font-semibold">Đang làm việc</span></td>
                  <td class="py-3.5 px-4">
                    <div class="flex items-center justify-center gap-1.5 text-base">
                      <button onclick="handleAction('Xem hồ sơ: Vũ Xuân Cường')" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Xem chi tiết"><i class="ph ph-eye"></i></button>
                      <button onclick="handleAction('Sửa thông tin: Vũ Xuân Cường')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-lg transition" title="Sửa"><i class="ph ph-pencil-simple"></i></button>
                      <button onclick="handleAction('Khóa/Xóa NV: Vũ Xuân Cường')" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition" title="Xóa"><i class="ph ph-trash"></i></button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="px-4 py-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Hiển thị 1 - 3 trên tổng số 325 cán bộ nhân viên</span>
            <div class="flex gap-1">
              <button class="px-2.5 py-1 border rounded-lg bg-brand-600 text-white font-bold">1</button>
              <button class="px-2.5 py-1 border rounded-lg hover:bg-slate-50">2</button>
              <button class="px-2.5 py-1 border rounded-lg hover:bg-slate-50">Sau</button>
            </div>
          </div>
        </div>
      </section>

      <!-- CÁC TAB KHÁC -->
      <section id="tab-outpatient" class="tab-content hidden space-y-4">
        <div class="bg-white p-6 rounded-2xl border border-blue-100"><h2 class="font-bold text-slate-800">Quản Lý & Thống Kê Tiếp Đón Khám Bệnh Ngoại Trú</h2></div>
      </section>
      <section id="tab-inpatient" class="tab-content hidden space-y-4">
        <div class="bg-white p-6 rounded-2xl border border-blue-100"><h2 class="font-bold text-slate-800">Giám Sát Bệnh Nhân Nội Trú & Biến Động Nhập/Ra Viện</h2></div>
      </section>
      <section id="tab-it-infra" class="tab-content hidden space-y-4">
        <div class="bg-white p-6 rounded-2xl border border-blue-100"><h2 class="font-bold text-slate-800">Hạ Tầng Máy Chủ, Cơ Sở Dữ Liệu & Thiết Bị Mạng</h2></div>
      </section>
      <section id="tab-security" class="tab-content hidden space-y-4">
        <div class="bg-white p-6 rounded-2xl border border-blue-100"><h2 class="font-bold text-slate-800">Bảo Đảm An Toàn Thông Tin & Báo Cáo Tuân Thủ</h2></div>
      </section>

    </main>
  </div>

  <!-- ==================== POPUP MODAL ĐỔI MẬT KHẨU ==================== -->
  <div id="change-password-modal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl border border-blue-100 overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-blue-50/50">
        <div class="flex items-center gap-2 text-brand-700">
          <i class="ph-bold ph-key text-xl"></i>
          <h3 class="font-bold text-base text-slate-800">Đổi Mật Khẩu Quản Trị</h3>
        </div>
        <button onclick="closeChangePasswordModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
          <i class="ph ph-x text-lg"></i>
        </button>
      </div>
      <form onsubmit="handlePasswordSubmit(event)" class="p-6 space-y-4">
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Mật khẩu hiện tại</label>
          <input type="password" required class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-brand-500" placeholder="••••••••">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Mật khẩu mới</label>
          <input type="password" required minlength="8" class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-brand-500" placeholder="Tối thiểu 8 ký tự">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Xác nhận mật khẩu mới</label>
          <input type="password" required minlength="8" class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-brand-500" placeholder="••••••••">
        </div>
        <div class="pt-2 flex items-center justify-end gap-2.5">
          <button type="button" onclick="closeChangePasswordModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Hủy</button>
          <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-xl shadow-md shadow-brand-500/20">Lưu Mật Khẩu</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ==================== JAVASCRIPT ==================== -->
  <script>
    let isSidebarCollapsed = false;

    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('mobile-overlay');
      const isMobile = window.innerWidth < 1024;

      if (isMobile) {
        if (sidebar.classList.contains('-translate-x-full')) {
          sidebar.classList.remove('-translate-x-full');
          overlay.classList.remove('hidden');
        } else {
          sidebar.classList.add('-translate-x-full');
          overlay.classList.add('hidden');
        }
      } else {
        isSidebarCollapsed = !isSidebarCollapsed;
        const textElements = document.querySelectorAll('.sidebar-text');
        if (isSidebarCollapsed) {
          sidebar.classList.remove('w-64');
          sidebar.classList.add('w-20');
          textElements.forEach(el => el.classList.add('hidden'));
        } else {
          sidebar.classList.remove('w-20');
          sidebar.classList.add('w-64');
          textElements.forEach(el => el.classList.remove('hidden'));
        }
      }
    }

    function initSidebarState() {
      const sidebar = document.getElementById('sidebar');
      if (window.innerWidth < 1024) {
        sidebar.classList.add('-translate-x-full');
      } else {
        sidebar.classList.remove('-translate-x-full');
      }
    }
    window.addEventListener('resize', initSidebarState);
    window.addEventListener('DOMContentLoaded', initSidebarState);

    // Xổ / Thu submenu danh mục
    function toggleSubmenu(menuId, button) {
      const menu = document.getElementById(menuId);
      const arrow = button.querySelector('.submenu-arrow');
      menu.classList.toggle('hidden');
      if (menu.classList.contains('hidden')) {
        arrow.classList.remove('rotate-180');
      } else {
        arrow.classList.add('rotate-180');
      }
    }

    // Chuyển tab nội dung
    function switchTab(tabId, element) {
      document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));
      const selectedTab = document.getElementById(`tab-${tabId}`);
      if (selectedTab) selectedTab.classList.remove('hidden');

      document.querySelectorAll('.nav-item').forEach(btn => {
        btn.classList.remove('bg-brand-50', 'text-brand-700', 'font-semibold');
        btn.classList.add('text-slate-600');
        const icon = btn.querySelector('i');
        if (icon) icon.classList.remove('text-brand-600');
      });

      document.querySelectorAll('.subnav-item').forEach(btn => {
        btn.classList.remove('text-brand-700', 'font-bold', 'bg-blue-50/80');
        btn.classList.add('text-slate-600');
      });

      if (element.classList.contains('subnav-item')) {
        element.classList.add('text-brand-700', 'font-bold', 'bg-blue-50/80');
        element.classList.remove('text-slate-600');
      } else {
        element.classList.add('bg-brand-50', 'text-brand-700', 'font-semibold');
        element.classList.remove('text-slate-600');
        const icon = element.querySelector('i');
        if (icon) icon.classList.add('text-brand-600');
      }

      const pageTitle = document.getElementById('page-title');
      const textSpan = element.querySelector('span.sidebar-text');
      pageTitle.innerText = textSpan ? textSpan.innerText : 'Quản Trị Hệ Thống';

      if (window.innerWidth < 1024) toggleSidebar();
    }

    // Dropdown User
    function toggleUserDropdown(event) {
      event.stopPropagation();
      document.getElementById('user-dropdown').classList.toggle('hidden');
    }
    window.addEventListener('click', () => {
      const dropdown = document.getElementById('user-dropdown');
      if (!dropdown.classList.contains('hidden')) dropdown.classList.add('hidden');
    });

    // Modal Đổi Mật Khẩu
    function openChangePasswordModal() {
      document.getElementById('user-dropdown').classList.add('hidden');
      document.getElementById('change-password-modal').classList.remove('hidden');
    }
    function closeChangePasswordModal() {
      document.getElementById('change-password-modal').classList.add('hidden');
    }
    function handlePasswordSubmit(event) {
      event.preventDefault();
      Swal.fire({ toast: true, position: 'bottom-end', icon: 'info', title: 'Chức năng đổi mật khẩu đang được hoàn thiện.', showConfirmButton: false, timer: 3000 });
      closeChangePasswordModal();
    }

    // Thao tác xem/sửa/xóa test
    function handleAction(msg) {
      Swal.fire({ toast: true, position: 'bottom-end', icon: 'info', title: 'Thao tác: ' + msg, showConfirmButton: false, timer: 3000 });
    }

    // Đăng xuất
    function logoutAction() {
      Swal.fire({
        title: 'Đăng xuất khỏi IOC?',
        text: 'Phiên quản trị hiện tại sẽ được kết thúc.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Đăng xuất',
        cancelButtonText: 'Ở lại',
        confirmButtonColor: '#e11d48'
      }).then((result) => {
        if (result.isConfirmed) window.location.href = <?php echo json_encode(XC_URL . '/logout'); ?>;
      });
    }
  </script>
</body>
</html>
