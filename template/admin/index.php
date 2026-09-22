<?php require_once('header.php'); ?>

    <!-- Main Content Container -->
    <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 no-scrollbar space-y-6">
      
      <?php
        // Tính toán lời chào theo thời gian trong ngày
        $hour = (int) date('H');
        if ($hour >= 5 && $hour < 11) {
          $greetingTime = 'Chào buổi sáng';
          $greetingIcon = 'ph-sun text-amber-300';
        } elseif ($hour >= 11 && $hour < 14) {
          $greetingTime = 'Chào buổi trưa';
          $greetingIcon = 'ph-sun-dim text-amber-300';
        } elseif ($hour >= 14 && $hour < 18) {
          $greetingTime = 'Chào buổi chiều';
          $greetingIcon = 'ph-sun-horizon text-orange-300';
        } else {
          $greetingTime = 'Chào buổi tối';
          $greetingIcon = 'ph-moon-stars text-indigo-200';
        }

        $vnDays = array('Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy');
        $currentDateStr = $vnDays[(int) date('w')] . ', ngày ' . date('d/m/Y');
        
        $uTotal = isset($userStats->total_users) ? (int) $userStats->total_users : 0;
        $uActive = isset($userStats->active_users) ? (int) $userStats->active_users : 0;
        $uAdmin = isset($userStats->admin_users) ? (int) $userStats->admin_users : 0;
        
        $dTotal = isset($deptStats->total_depts) ? (int) $deptStats->total_depts : 0;
        $dActive = isset($deptStats->active_depts) ? (int) $deptStats->active_depts : 0;
        $dBeds = isset($deptStats->total_beds) ? (int) $deptStats->total_beds : 0;

        $sTotal = isset($systemStats->total_configs) ? (int) $systemStats->total_configs : 0;
        $sActive = isset($systemStats->active_configs) ? (int) $systemStats->active_configs : 0;
        $mTotal = isset($menuStats->total_menus) ? (int) $menuStats->total_menus : 0;
      ?>

      <!-- ==================== KHỐI LỜI CHÀO & BANNER TRUNG TÂM ==================== -->
      <section class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-brand-900 via-brand-700 to-blue-600 text-white p-6 sm:p-8 shadow-xl shadow-brand-900/10 border border-blue-400/20">
        <!-- Hiệu ứng đồ họa nền nhẹ -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-blue-400/20 rounded-full blur-2xl pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
          <div class="max-w-2xl space-y-3">
            <!-- Badges trạng thái & ngày -->
            <div class="flex flex-wrap items-center gap-2">
              <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/15 backdrop-blur-md border border-white/20 text-blue-50">
                <i class="ph <?php echo $greetingIcon; ?> text-sm"></i>
                <span><?php echo $currentDateStr; ?></span>
              </span>
              <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 backdrop-blur-md border border-emerald-400/30 text-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Hệ thống Quản trị sẵn sàng</span>
              </span>
              <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-white/10 text-white/90 border border-white/15">
                <i class="ph-bold ph-shield-check"></i> <?php echo htmlspecialchars($roleTitle, ENT_QUOTES, 'UTF-8'); ?>
              </span>
            </div>

            <!-- Lời chào chính -->
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white leading-snug">
              <?php echo $greetingTime; ?>, <span class="text-blue-100"><?php echo htmlspecialchars($currentFullName, ENT_QUOTES, 'UTF-8'); ?></span>! 👋
            </h2>

           
          </div>

          <!-- Lối tắt nhanh trong banner -->
          <div class="flex flex-wrap sm:flex-nowrap lg:flex-col gap-2.5 shrink-0">
            <a href="<?php echo XC_URL; ?>/accounts/admin" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-white text-brand-700 hover:bg-blue-50 text-xs font-bold shadow-lg shadow-black/10 transition group">
              <i class="ph-bold ph-user-gear text-base group-hover:scale-110 transition-transform"></i>
              <span>Quản lý Tài khoản</span>
            </a>
            <a href="<?php echo XC_URL; ?>/departments" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-white/15 hover:bg-white/25 backdrop-blur-md text-white border border-white/20 text-xs font-bold transition group">
              <i class="ph-bold ph-buildings text-base group-hover:scale-110 transition-transform"></i>
              <span>Danh mục Khoa phòng</span>
            </a>
            <a href="<?php echo XC_URL; ?>/system" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur-md text-white/90 border border-white/15 text-xs font-medium transition">
              <i class="ph ph-sliders-horizontal text-base"></i>
              <span>Tham số hệ thống</span>
            </a>
          </div>
        </div>
      </section>

      <!-- ==================== CÁC THẺ THỐNG KÊ QUẢN TRỊ ==================== -->
      <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        
        <!-- THẺ 1: LƯỢNG USER & TÀI KHOẢN -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-blue-200 transition-all group flex flex-col justify-between">
          <div>
            <div class="flex items-center justify-between mb-3">
              <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Tài khoản người dùng</span>
              <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-xl group-hover:bg-brand-600 group-hover:text-white transition-colors shadow-xs">
                <i class="ph-bold ph-users-three"></i>
              </div>
            </div>
            
            <div class="flex items-baseline gap-2">
              <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight"><?php echo number_format($uTotal); ?></h3>
              <span class="text-xs font-semibold text-slate-400">tài khoản</span>
            </div>

            <p class="mt-2 text-xs text-slate-600 flex items-center gap-1.5 font-medium">
              <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
              <span><strong><?php echo $uActive; ?></strong> đang hoạt động</span>
              <span class="text-slate-300">•</span>
              <span class="text-amber-600 font-semibold"><?php echo $uAdmin; ?> Admin</span>
            </p>
          </div>

          <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
            <div class="flex items-center gap-1.5 text-slate-500">
              <span class="px-1.5 py-0.5 rounded bg-blue-50 text-brand-700 font-semibold">Adm: <?php echo isset($siteUserCounts['admin']) ? $siteUserCounts['admin'] : 0; ?></span>
              <span class="px-1.5 py-0.5 rounded bg-sky-50 text-sky-700 font-semibold">DB: <?php echo isset($siteUserCounts['dashboard']) ? $siteUserCounts['dashboard'] : 0; ?></span>
              <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 font-semibold">BE: <?php echo isset($siteUserCounts['backend']) ? $siteUserCounts['backend'] : 0; ?></span>
            </div>
            <a href="<?php echo XC_URL; ?>/accounts/admin" class="font-bold text-brand-600 hover:text-brand-700 flex items-center gap-0.5 group-hover:translate-x-0.5 transition-transform">
              Chi tiết <i class="ph ph-caret-right"></i>
            </a>
          </div>
        </div>

        <!-- THẺ 2: KHOA / PHÒNG BỆNH VIỆN -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all group flex flex-col justify-between">
          <div>
            <div class="flex items-center justify-between mb-3">
              <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Khoa / Phòng trực thuộc</span>
              <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl group-hover:bg-emerald-600 group-hover:text-white transition-colors shadow-xs">
                <i class="ph-bold ph-buildings"></i>
              </div>
            </div>
            
            <div class="flex items-baseline gap-2">
              <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight"><?php echo number_format($dTotal); ?></h3>
              <span class="text-xs font-semibold text-slate-400">khoa / phòng</span>
            </div>

            <p class="mt-2 text-xs text-slate-600 flex items-center gap-1.5 font-medium">
              <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
              <span><strong><?php echo $dActive; ?></strong> khoa đang hoạt động (100%)</span>
            </p>
          </div>

          <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
            <span class="text-slate-500 font-medium truncate">BVĐK Khu vực Đăk Tô</span>
            <a href="<?php echo XC_URL; ?>/departments" class="font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-0.5 group-hover:translate-x-0.5 transition-transform">
              Quản lý <i class="ph ph-caret-right"></i>
            </a>
          </div>
        </div>

        <!-- THẺ 3: CHỈ TIÊU GIƯỜNG BỆNH KHOA -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-indigo-200 transition-all group flex flex-col justify-between">
          <div>
            <div class="flex items-center justify-between mb-3">
              <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Chỉ tiêu Giường điều trị</span>
              <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl group-hover:bg-indigo-600 group-hover:text-white transition-colors shadow-xs">
                <i class="ph-bold ph-bed"></i>
              </div>
            </div>
            
            <div class="flex items-baseline gap-2">
              <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight"><?php echo number_format($dBeds); ?></h3>
              <span class="text-xs font-semibold text-slate-400">giường bệnh</span>
            </div>

            <p class="mt-2 text-xs text-slate-600 flex items-center gap-1.5 font-medium">
              <i class="ph ph-check-circle text-indigo-500"></i>
              <span>Tổng chỉ tiêu kế hoạch phân bổ</span>
            </p>
          </div>

          <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
            <span class="text-slate-500 font-medium">Điều phối công suất</span>
            <a href="<?php echo XC_URL; ?>/departments" class="font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-0.5 group-hover:translate-x-0.5 transition-transform">
              Xem chỉ tiêu <i class="ph ph-caret-right"></i>
            </a>
          </div>
        </div>

        <!-- THẺ 4: CẤU HÌNH & THAM SỐ HỆ THỐNG -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-purple-200 transition-all group flex flex-col justify-between">
          <div>
            <div class="flex items-center justify-between mb-3">
              <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Tham số & Menu hệ thống</span>
              <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl group-hover:bg-purple-600 group-hover:text-white transition-colors shadow-xs">
                <i class="ph-bold ph-sliders-horizontal"></i>
              </div>
            </div>
            
            <div class="flex items-baseline gap-2">
              <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight"><?php echo number_format($sTotal); ?></h3>
              <span class="text-xs font-semibold text-slate-400">tham số</span>
            </div>

            <p class="mt-2 text-xs text-slate-600 flex items-center gap-1.5 font-medium">
              <span class="w-2 h-2 rounded-full bg-purple-500"></span>
              <span><strong><?php echo $sActive; ?></strong> tham số bật • <strong><?php echo $mTotal; ?></strong> menu</span>
            </p>
          </div>

          <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
            <span class="text-slate-500 font-medium">Tích hợp LIS / RIS</span>
            <a href="<?php echo XC_URL; ?>/system" class="font-bold text-purple-600 hover:text-purple-700 flex items-center gap-0.5 group-hover:translate-x-0.5 transition-transform">
              Cấu hình <i class="ph ph-caret-right"></i>
            </a>
          </div>
        </div>

      </section>

      <!-- ==================== KHỐI NỘI DUNG 2 CỘT: NGƯỜI DÙNG & KHOA PHÒNG ==================== -->
      <section class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- CỘT TRÁI (8 CỘT): DANH SÁCH TÀI KHOẢN NGƯỜI DÙNG -->
        <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden flex flex-col">
          <div class="p-5 sm:px-6 sm:py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-xl bg-blue-50 text-brand-600 flex items-center justify-center font-bold text-sm">
                <i class="ph-bold ph-user-list"></i>
              </div>
              <div>
                <h3 class="font-bold text-sm sm:text-base text-slate-800 leading-tight">Danh Sách Tài Khoản Người Dùng</h3>
                <p class="text-[11px] text-slate-500">Các tài khoản được phân quyền truy cập và điều hành</p>
              </div>
            </div>
            
            <div class="flex items-center gap-2">
              <a href="<?php echo XC_URL; ?>/accounts/admin" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-brand-700 bg-blue-50 hover:bg-blue-100 transition">
                <i class="ph-bold ph-plus"></i>
                <span class="hidden sm:inline">Phân quyền</span>
              </a>
              <a href="<?php echo XC_URL; ?>/accounts/admin" class="text-xs font-bold text-slate-500 hover:text-brand-600 px-2 py-1.5 transition">
                Tất cả (<?php echo $uTotal; ?>) →
              </a>
            </div>
          </div>

          <div class="overflow-x-auto flex-1">
            <table class="w-full text-left border-collapse text-xs">
              <thead>
                <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                  <th class="py-3 px-4">Người dùng</th>
                  <th class="py-3 px-4">Liên hệ</th>
                  <th class="py-3 px-4">Vai trò</th>
                  <th class="py-3 px-4">Phân hệ truy cập</th>
                  <th class="py-3 px-4">Đăng nhập cuối</th>
                  <th class="py-3 px-4 text-right">Thao tác</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <?php if (!empty($recentUsers) && is_array($recentUsers)): ?>
                  <?php foreach ($recentUsers as $u): ?>
                    <?php
                      // Chữ cái đại diện
                      $name = !empty($u->full_name) ? $u->full_name : $u->username;
                      $words = preg_split('/\s+/u', trim($name));
                      $initial = count($words) >= 2 
                        ? mb_strtoupper(mb_substr($words[0], 0, 1, 'UTF-8') . mb_substr(end($words), 0, 1, 'UTF-8'), 'UTF-8')
                        : mb_strtoupper(mb_substr($name, 0, 2, 'UTF-8'), 'UTF-8');
                      $isAdmin = (int) $u->is_admin === 1;
                    ?>
                    <tr class="hover:bg-blue-50/40 transition-colors group">
                      <td class="py-3 px-4">
                        <div class="flex items-center gap-2.5">
                          <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-brand-700 to-blue-500 text-white font-bold text-[11px] flex items-center justify-center shrink-0 shadow-xs uppercase select-none">
                            <?php echo htmlspecialchars($initial, ENT_QUOTES, 'UTF-8'); ?>
                          </div>
                          <div class="min-w-0">
                            <div class="font-bold text-slate-800 truncate max-w-[130px] group-hover:text-brand-600 transition">
                              <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                            <div class="text-[10px] text-slate-400 font-medium">@<?php echo htmlspecialchars($u->username, ENT_QUOTES, 'UTF-8'); ?></div>
                          </div>
                        </div>
                      </td>

                      <td class="py-3 px-4">
                        <div class="space-y-0.5">
                          <div class="text-slate-700 font-medium truncate max-w-[150px]"><?php echo htmlspecialchars($u->email ?: '—', ENT_QUOTES, 'UTF-8'); ?></div>
                          <?php if (!empty($u->phone)): ?>
                            <div class="text-[10px] text-slate-400 font-medium"><?php echo htmlspecialchars($u->phone, ENT_QUOTES, 'UTF-8'); ?></div>
                          <?php endif; ?>
                        </div>
                      </td>

                      <td class="py-3 px-4">
                        <?php if ($isAdmin): ?>
                          <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                            <i class="ph-bold ph-crown text-[10px]"></i> Super Admin
                          </span>
                        <?php else: ?>
                          <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-brand-700 border border-blue-200">
                            <i class="ph-bold ph-shield-check text-[10px]"></i> Quản trị
                          </span>
                        <?php endif; ?>
                      </td>

                      <td class="py-3 px-4">
                        <div class="flex flex-wrap gap-1">
                          <?php
                            $sites = !empty($u->site_codes) ? explode(', ', $u->site_codes) : array();
                            if ($isAdmin) {
                              $sites = array('admin', 'dashboard', 'backend');
                            }
                          ?>
                          <?php foreach ($sites as $sc): ?>
                            <span class="px-1.5 py-0.2 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                              <?php echo htmlspecialchars(strtoupper($sc), ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                          <?php endforeach; ?>
                          <?php if (empty($sites)): ?>
                            <span class="text-slate-400 text-[10px]">Chưa gán</span>
                          <?php endif; ?>
                        </div>
                      </td>

                      <td class="py-3 px-4 text-slate-500 whitespace-nowrap text-[11px]">
                        <?php if (!empty($u->last_login_at)): ?>
                          <span><?php echo date('H:i - d/m/Y', strtotime($u->last_login_at)); ?></span>
                        <?php else: ?>
                          <span class="text-slate-400">Chưa đăng nhập</span>
                        <?php endif; ?>
                      </td>

                      <td class="py-3 px-4 text-right">
                        <a href="<?php echo XC_URL; ?>/accounts/admin" class="p-1.5 rounded-lg text-slate-400 hover:text-brand-600 hover:bg-blue-50 transition inline-block" title="Xem phân quyền">
                          <i class="ph-bold ph-arrow-square-out text-base"></i>
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6" class="py-8 text-center text-slate-400">Chưa có người dùng nào được tạo trong hệ thống.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <div class="p-3 bg-slate-50/60 border-t border-slate-100 text-center">
            <a href="<?php echo XC_URL; ?>/accounts/admin" class="text-xs font-bold text-brand-600 hover:text-brand-700">
              Quản lý toàn bộ tài khoản và phân quyền chi tiết →
            </a>
          </div>
        </div>

        <!-- CỘT PHẢI (4 CỘT): KHOA PHÒNG & LỐI TẮT -->
        <div class="lg:col-span-4 space-y-6">
          
          <!-- HỘP 1: KHOA PHÒNG & CHỈ TIÊU GIƯỜNG -->
          <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
              <div class="flex items-center gap-2">
                <i class="ph-bold ph-bed text-base text-indigo-600"></i>
                <h3 class="font-bold text-sm text-slate-800">Khoa Phòng Trọng Điểm</h3>
              </div>
              <a href="<?php echo XC_URL; ?>/departments" class="text-xs font-bold text-indigo-600 hover:underline">
                Xem tất cả →
              </a>
            </div>

            <div class="p-4 space-y-2.5">
              <?php if (!empty($topDepts) && is_array($topDepts)): ?>
                <?php foreach ($topDepts as $dept): ?>
                  <div class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 border border-slate-100/70 transition">
                    <div class="min-w-0 flex-1 pr-2">
                      <div class="font-bold text-xs text-slate-800 truncate">
                        <?php echo htmlspecialchars($dept->department_name, ENT_QUOTES, 'UTF-8'); ?>
                      </div>
                      <div class="text-[10px] text-slate-400 font-medium">Mã: <?php echo htmlspecialchars($dept->department_code, ENT_QUOTES, 'UTF-8'); ?></div>
                    </div>
                    <div class="shrink-0 flex items-center gap-1.5">
                      <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-100">
                        <?php echo (int) $dept->department_bed; ?> <span class="text-[10px] font-normal ml-0.5">giường</span>
                      </span>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="text-xs text-slate-400 py-4 text-center">Chưa có khoa phòng nào.</div>
              <?php endif; ?>
            </div>

            <div class="px-4 py-3 bg-indigo-50/50 border-t border-indigo-100/60 flex items-center justify-between text-xs">
              <span class="text-slate-600 font-medium">Tổng chỉ tiêu kế hoạch:</span>
              <span class="font-bold text-indigo-700 text-sm"><?php echo number_format($dBeds); ?> giường</span>
            </div>
          </div>

          <!-- HỘP 2: LỐI TẮT QUẢN TRỊ NHANH -->
          <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5 space-y-3.5">
            <div class="flex items-center gap-2 text-slate-800 font-bold text-sm">
              <i class="ph-bold ph-lightning text-amber-500"></i>
              <h4>Lối Tắt Chức Năng</h4>
            </div>

            <div class="grid grid-cols-1 gap-2 text-xs">
              <a href="<?php echo XC_URL; ?>/accounts/admin" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-blue-50 hover:text-brand-700 transition group border border-slate-100">
                <div class="flex items-center gap-2.5">
                  <div class="w-7 h-7 rounded-lg bg-blue-100/70 text-brand-600 flex items-center justify-center text-sm">
                    <i class="ph-bold ph-user-circle-gear"></i>
                  </div>
                  <span class="font-semibold text-slate-700 group-hover:text-brand-700">Tài khoản Quản trị (Admin)</span>
                </div>
                <i class="ph ph-caret-right text-slate-400 group-hover:text-brand-600 transition"></i>
              </a>

              <a href="<?php echo XC_URL; ?>/accounts/dashboard" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-sky-50 hover:text-sky-700 transition group border border-slate-100">
                <div class="flex items-center gap-2.5">
                  <div class="w-7 h-7 rounded-lg bg-sky-100/70 text-sky-600 flex items-center justify-center text-sm">
                    <i class="ph-bold ph-presentation-chart"></i>
                  </div>
                  <span class="font-semibold text-slate-700 group-hover:text-sky-700">Phân quyền Dashboard IOC</span>
                </div>
                <i class="ph ph-caret-right text-slate-400 group-hover:text-sky-600 transition"></i>
              </a>

              <a href="<?php echo XC_URL; ?>/accounts/backend" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-emerald-50 hover:text-emerald-700 transition group border border-slate-100">
                <div class="flex items-center gap-2.5">
                  <div class="w-7 h-7 rounded-lg bg-emerald-100/70 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="ph-bold ph-stethoscope"></i>
                  </div>
                  <span class="font-semibold text-slate-700 group-hover:text-emerald-700">Phân quyền Backend Y tế</span>
                </div>
                <i class="ph ph-caret-right text-slate-400 group-hover:text-emerald-600 transition"></i>
              </a>

              <a href="<?php echo XC_URL; ?>/departments" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-indigo-50 hover:text-indigo-700 transition group border border-slate-100">
                <div class="flex items-center gap-2.5">
                  <div class="w-7 h-7 rounded-lg bg-indigo-100/70 text-indigo-600 flex items-center justify-center text-sm">
                    <i class="ph-bold ph-hospital"></i>
                  </div>
                  <span class="font-semibold text-slate-700 group-hover:text-indigo-700">Quản lý Khoa / Phòng</span>
                </div>
                <i class="ph ph-caret-right text-slate-400 group-hover:text-indigo-600 transition"></i>
              </a>

              <a href="<?php echo XC_URL; ?>/system" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-purple-50 hover:text-purple-700 transition group border border-slate-100">
                <div class="flex items-center gap-2.5">
                  <div class="w-7 h-7 rounded-lg bg-purple-100/70 text-purple-600 flex items-center justify-center text-sm">
                    <i class="ph-bold ph-gear"></i>
                  </div>
                  <span class="font-semibold text-slate-700 group-hover:text-purple-700">Cấu hình & Kết nối API</span>
                </div>
                <i class="ph ph-caret-right text-slate-400 group-hover:text-purple-600 transition"></i>
              </a>
            </div>
          </div>

        </div>

      </section>
    </main>
  </div>
<?php require_once('footer.php'); ?>