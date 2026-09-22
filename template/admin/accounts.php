<?php require_once('header.php'); ?>

    <!-- Main Content Container -->
    <main class="flex-1 overflow-y-auto p-4 sm:p-6 no-scrollbar space-y-5">

      <!-- Breadcrumb & Tiêu đề trang -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-brand-600 mb-1">
            <span>CARE IOC</span>
            <span class="text-slate-300">/</span>
            <span>Hệ Thống</span>
            <span class="text-slate-300">/</span>
            <span class="text-slate-700 font-bold">Phân Quyền <?php echo htmlspecialchars($siteNames[$site], ENT_QUOTES, 'UTF-8'); ?></span>
          </div>
          <h2 class="text-xl sm:text-2xl font-bold text-slate-800 tracking-tight">Quản Lý Tài Khoản & Phân Quyền</h2>
          <p class="text-xs text-slate-500 mt-0.5">Danh sách người dùng và thiết lập quyền truy cập cho khu vực <strong class="text-brand-700"><?php echo htmlspecialchars($siteNames[$site], ENT_QUOTES, 'UTF-8'); ?></strong>.</p>
        </div>

        <?php if ($canCreate): ?>
          <button type="button" id="btn-add-account" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-brand-500/20 transition-all shrink-0">
            <i class="ph-bold ph-user-plus text-base"></i> Thêm Tài Khoản
          </button>
        <?php endif; ?>
      </div>

      <!-- Thẻ điều hướng 3 khu vực tương ứng với menu -->
      <section class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <?php foreach ($siteNames as $siteCode => $siteName): ?>
          <?php if (!$this->helper->userHasMenu('admin.accounts.' . $siteCode, 'view')) continue; ?>
          <?php $isActiveSite = ($site === $siteCode); ?>
          <a href="<?php echo XC_URL; ?>/accounts/<?php echo $siteCode; ?>"
             class="p-4 sm:p-5 rounded-2xl border transition-all relative overflow-hidden group <?php echo $isActiveSite ? 'bg-gradient-to-tr from-brand-700 to-blue-600 text-white border-transparent shadow-lg shadow-brand-500/25 ring-2 ring-brand-500/20' : 'bg-white border-blue-100 hover:border-blue-300 text-slate-700 hover:shadow-md'; ?>">
            <div class="flex items-center justify-between">
              <div>
                <span class="text-[11px] font-bold uppercase tracking-wider <?php echo $isActiveSite ? 'text-blue-100' : 'text-slate-400'; ?>">Khu vực quản trị</span>
                <h3 class="mt-1 text-lg font-bold <?php echo $isActiveSite ? 'text-white' : 'text-slate-800'; ?>">Trang <?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?></h3>
              </div>
              <div class="w-11 h-11 rounded-xl flex items-center justify-center text-xl shrink-0 transition-transform group-hover:scale-105 <?php echo $isActiveSite ? 'bg-white/20 text-white shadow-inner' : 'bg-blue-50 text-brand-600'; ?>">
                <i class="ph-bold <?php echo $siteCode === 'admin' ? 'ph-shield-star' : ($siteCode === 'backend' ? 'ph-database' : 'ph-chart-line-up'); ?>"></i>
              </div>
            </div>
            <div class="mt-4 flex items-center justify-between text-xs <?php echo $isActiveSite ? 'text-blue-100' : 'text-slate-500'; ?>">
              <span class="font-medium"><?php echo (int) $counts[$siteCode]; ?> tài khoản đang hoạt động</span>
              <span class="inline-flex items-center gap-1 font-semibold <?php echo $isActiveSite ? 'text-white underline underline-offset-2' : 'text-brand-600 group-hover:translate-x-0.5 transition-transform'; ?>">
                <?php echo $isActiveSite ? 'Đang quản trị' : 'Chuyển trang'; ?>
                <i class="ph ph-arrow-right text-xs"></i>
              </span>
            </div>
          </a>
        <?php endforeach; ?>
      </section>

      <!-- Danh sách tài khoản của khu vực đang chọn -->
      <section class="bg-white rounded-2xl border border-blue-100 shadow-sm overflow-hidden">
        
        <!-- Thanh công cụ tìm kiếm và lọc -->
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 bg-blue-50/20">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-blue-100 text-brand-700 flex items-center justify-center text-base font-bold shrink-0">
              <i class="ph-bold ph-users-three"></i>
            </div>
            <div>
              <h3 class="font-bold text-slate-800 text-sm">Danh Sách Tài Khoản · Trang <?php echo htmlspecialchars($siteNames[$site], ENT_QUOTES, 'UTF-8'); ?></h3>
              <p class="text-[11px] text-slate-400">Super Admin tự động sở hữu toàn quyền quản trị trên cả 3 trang.</p>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <div class="relative w-full sm:w-72">
              <input id="account-search" type="search" placeholder="Tìm theo tên, username, email..." class="w-full pl-9 pr-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-brand-500 text-slate-700 shadow-xs transition-all">
              <i class="ph ph-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-sm"></i>
            </div>
          </div>
        </div>

        <!-- Bảng dữ liệu -->
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs border-collapse">
            <thead>
              <tr class="bg-blue-50/50 text-slate-500 border-b border-blue-100 font-semibold uppercase text-[11px] tracking-wider">
                <th class="py-3.5 px-4">Tài Khoản</th>
                <th class="py-3.5 px-4">Thông Tin Liên Hệ</th>
                <th class="py-3.5 px-4 text-center">Vai Trò</th>
                <th class="py-3.5 px-4 text-center">Trạng Thái</th>
                <th class="py-3.5 px-4 text-center">Đăng Nhập Gần Nhất</th>
                <th class="py-3.5 px-4 text-center w-36">Thao Tác</th>
              </tr>
            </thead>
            <tbody id="account-table" class="divide-y divide-slate-100 font-medium text-slate-700">
              <?php if (!$accounts): ?>
                <tr>
                  <td colspan="6" class="py-12 px-4 text-center text-slate-400">
                    <i class="ph ph-user-circle-gear text-3xl mb-1 text-slate-300"></i>
                    <p class="text-xs">Chưa có tài khoản nào được phân quyền cho trang này.</p>
                  </td>
                </tr>
              <?php endif; ?>

              <?php foreach ($accounts as $account): ?>
                <tr class="account-row hover:bg-blue-50/30 transition"
                    data-search="<?php echo htmlspecialchars(strtolower($account->username . ' ' . $account->full_name . ' ' . $account->email . ' ' . $account->phone), ENT_QUOTES, 'UTF-8'); ?>">
                  <td class="py-3 px-4">
                    <div class="flex items-center gap-3">
                      <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand-700 to-blue-500 flex items-center justify-center text-white font-bold text-xs shadow-sm ring-2 ring-blue-100 shrink-0">
                        <?php echo htmlspecialchars(strtoupper(mb_substr($account->full_name ?: $account->username, 0, 1, 'UTF-8')), ENT_QUOTES, 'UTF-8'); ?>
                      </div>
                      <div class="min-w-0">
                        <p class="font-bold text-slate-800 truncate"><?php echo htmlspecialchars($account->full_name, ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="text-[11px] text-slate-400 font-mono"><?php echo htmlspecialchars($account->username, ENT_QUOTES, 'UTF-8'); ?></p>
                      </div>
                    </div>
                  </td>
                  <td class="py-3 px-4">
                    <p class="text-slate-700 font-medium"><?php echo htmlspecialchars($account->email ?: '—', ENT_QUOTES, 'UTF-8'); ?></p>
                    <p class="text-[11px] text-slate-400"><?php echo htmlspecialchars($account->phone ?: 'Chưa có SĐT', ENT_QUOTES, 'UTF-8'); ?></p>
                  </td>
                  <td class="py-3 px-4 text-center">
                    <?php if ((int) $account->is_admin === 1): ?>
                      <span class="px-2.5 py-1 rounded-full text-[11px] bg-purple-50 text-purple-700 font-bold border border-purple-100 inline-flex items-center gap-1">
                        <i class="ph-bold ph-shield-star"></i> Super Admin
                      </span>
                    <?php else: ?>
                      <span class="px-2.5 py-1 rounded-full text-[11px] bg-blue-50 text-brand-700 font-semibold border border-blue-100">
                        <?php echo htmlspecialchars($siteNames[$site], ENT_QUOTES, 'UTF-8'); ?>
                      </span>
                    <?php endif; ?>
                  </td>
                  <td class="py-3 px-4 text-center">
                    <?php if ((int) $account->site_active === 1 && (int) $account->is_active === 1): ?>
                      <span class="px-2.5 py-1 rounded-full text-[11px] bg-emerald-50 text-emerald-600 font-semibold border border-emerald-100 inline-flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Hoạt động
                      </span>
                    <?php else: ?>
                      <span class="px-2.5 py-1 rounded-full text-[11px] bg-rose-50 text-rose-600 font-semibold border border-rose-100 inline-flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Đã khóa
                      </span>
                    <?php endif; ?>
                  </td>
                  <td class="py-3 px-4 text-center text-slate-500 font-mono text-[11px]">
                    <?php echo $account->last_login_at ? date('d/m/Y H:i', strtotime($account->last_login_at)) : '<span class="text-slate-400">Chưa đăng nhập</span>'; ?>
                  </td>
                  <td class="py-3 px-4">
                    <div class="flex items-center justify-center gap-1 text-base">
                      <?php if ((int) $account->is_admin !== 1 && $canUpdate): ?>
                        <button type="button" class="btn-permissions p-1.5 text-violet-600 hover:bg-violet-50 rounded-lg transition" title="Phân quyền menu"
                                data-user-id="<?php echo (int) $account->id; ?>"
                                data-name="<?php echo htmlspecialchars($account->full_name, ENT_QUOTES, 'UTF-8'); ?>">
                          <i class="ph ph-key text-base"></i>
                        </button>
                      <?php endif; ?>

                      <?php if ($canUpdate): ?>
                        <button type="button" class="btn-edit p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Chỉnh sửa tài khoản"
                                data-account='<?php echo htmlspecialchars(json_encode(array('id'=>(int)$account->id,'username'=>$account->username,'full_name'=>$account->full_name,'email'=>$account->email,'phone'=>$account->phone,'is_active'=>(int)$account->site_active,'is_admin'=>(int)$account->is_admin), JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>'>
                          <i class="ph ph-pencil-simple text-base"></i>
                        </button>
                      <?php endif; ?>

                      <?php if ($canUpdate && ((int) $account->is_admin !== 1 || !empty($_SESSION['user']['is_admin']))): ?>
                        <button type="button" class="btn-reset-password p-1.5 text-amber-500 hover:bg-amber-50 rounded-lg transition" title="Đặt lại mật khẩu"
                                data-user-id="<?php echo (int) $account->id; ?>"
                                data-name="<?php echo htmlspecialchars($account->full_name, ENT_QUOTES, 'UTF-8'); ?>">
                          <i class="ph ph-lock-key text-base"></i>
                        </button>
                      <?php endif; ?>

                      <?php if ($canDelete && (int) $account->is_admin !== 1 && (int) $account->id !== $currentUserId): ?>
                        <button type="button" class="btn-delete p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition" title="Xóa quyền khỏi site"
                                data-id="<?php echo (int) $account->id; ?>"
                                data-name="<?php echo htmlspecialchars($account->full_name, ENT_QUOTES, 'UTF-8'); ?>">
                          <i class="ph ph-trash text-base"></i>
                        </button>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Chân bảng thống kê -->
        <div class="px-4 py-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 bg-slate-50/30">
          <span>Tổng số: <strong class="text-brand-700 font-bold"><?php echo count($accounts); ?></strong> tài khoản cho trang <?php echo htmlspecialchars($siteNames[$site], ENT_QUOTES, 'UTF-8'); ?></span>
          <span class="text-[11px] text-slate-400">Hệ thống CARE IOC</span>
        </div>
      </section>

    </main>
  </div>

  <!-- ==================== MODAL THÊM / SỬA TÀI KHOẢN ==================== -->
  <div id="account-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="w-full max-w-xl rounded-3xl bg-white shadow-2xl border border-blue-100 overflow-hidden">
      <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-blue-50/50">
        <div class="flex items-center gap-2.5 text-brand-700">
          <i class="ph-bold ph-user-gear text-xl"></i>
          <h3 id="account-modal-title" class="font-bold text-base text-slate-800">Thêm Tài Khoản</h3>
        </div>
        <button type="button" class="btn-close-modal text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition">
          <i class="ph ph-x text-lg"></i>
        </button>
      </div>

      <form id="account-form" class="space-y-4 p-6" novalidate>
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="site_code" value="<?php echo htmlspecialchars($site, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="id" id="account-id" value="0">
        <?php if ($site === 'admin'): ?>
          <input type="hidden" name="is_admin_present" value="1">
        <?php endif; ?>

        <div class="grid gap-3.5 sm:grid-cols-2">
          <label class="space-y-1 block">
            <span class="text-xs font-semibold text-slate-700">Tên đăng nhập <b class="text-rose-500">*</b></span>
            <input name="username" id="account-username" required minlength="3" maxlength="120" pattern="[A-Za-z0-9._@-]+" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs sm:text-sm outline-none focus:border-brand-500 focus:bg-white text-slate-700 transition" placeholder="VD: bs_tuan">
          </label>
          <label class="space-y-1 block">
            <span class="text-xs font-semibold text-slate-700">Họ và tên <b class="text-rose-500">*</b></span>
            <input name="full_name" id="account-full-name" required maxlength="255" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs sm:text-sm outline-none focus:border-brand-500 focus:bg-white text-slate-700 transition" placeholder="VD: TS.BS Trần Quốc Tuấn">
          </label>
          <label class="space-y-1 block">
            <span class="text-xs font-semibold text-slate-700">Email</span>
            <input type="email" name="email" id="account-email" maxlength="255" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs sm:text-sm outline-none focus:border-brand-500 focus:bg-white text-slate-700 transition" placeholder="tuantq@hospital.gov.vn">
          </label>
          <label class="space-y-1 block">
            <span class="text-xs font-semibold text-slate-700">Số điện thoại</span>
            <input name="phone" id="account-phone" maxlength="30" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs sm:text-sm outline-none focus:border-brand-500 focus:bg-white text-slate-700 transition" placeholder="0901234567">
          </label>
          <label class="space-y-1 sm:col-span-2 block">
            <span class="text-xs font-semibold text-slate-700">Mật khẩu <b id="password-required" class="text-rose-500">*</b></span>
            <input type="password" name="password" id="account-password" minlength="8" maxlength="255" autocomplete="new-password" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs sm:text-sm outline-none focus:border-brand-500 focus:bg-white text-slate-700 transition" placeholder="Tối thiểu 8 ký tự">
            <p class="text-[11px] text-slate-400 mt-1">Khi chỉnh sửa, hãy để trống nếu không muốn thay đổi mật khẩu.</p>
          </label>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 rounded-2xl bg-blue-50/40 border border-blue-100 p-3.5">
          <label class="flex items-center gap-2 text-xs font-medium text-slate-700 cursor-pointer">
            <input type="checkbox" name="is_active" id="account-active" value="1" class="h-4 w-4 rounded text-brand-600 focus:ring-brand-500" checked>
            <span>Tài khoản hoạt động trên trang này</span>
          </label>
          <?php if ($site === 'admin'): ?>
            <label class="flex items-center gap-2 text-xs font-semibold text-purple-700 cursor-pointer">
              <input type="checkbox" name="is_admin" id="account-super-admin" value="1" class="h-4 w-4 rounded text-purple-600 focus:ring-purple-500">
              <span>Super Admin (Toàn quyền trên 3 trang)</span>
            </label>
          <?php endif; ?>
        </div>

        <div class="flex items-center justify-end gap-2.5 border-t border-slate-100 pt-4">
          <button type="button" class="btn-close-modal rounded-xl border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">Hủy</button>
          <button type="submit" class="rounded-xl bg-brand-600 hover:bg-brand-700 px-5 py-2 text-xs font-semibold text-white shadow-md shadow-brand-500/20 transition">Lưu Tài Khoản</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ==================== MODAL ĐẶT LẠI MẬT KHẨU ==================== -->
  <div id="password-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="w-full max-w-md rounded-3xl bg-white shadow-2xl border border-blue-100 overflow-hidden">
      <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-amber-50/50">
        <div class="flex items-center gap-2.5 text-amber-600">
          <i class="ph-bold ph-lock-key text-xl"></i>
          <div>
            <h3 class="font-bold text-base text-slate-800">Đặt Lại Mật Khẩu</h3>
            <p id="password-user-name" class="text-xs text-slate-400"></p>
          </div>
        </div>
        <button type="button" class="btn-close-password text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition">
          <i class="ph ph-x text-lg"></i>
        </button>
      </div>

      <form id="password-form" class="space-y-4 p-6" novalidate>
        <input type="hidden" name="user_id" id="password-user-id">
        <label class="block space-y-1">
          <span class="text-xs font-semibold text-slate-700">Mật khẩu mới <b class="text-rose-500">*</b></span>
          <input type="password" name="password" id="reset-password" required minlength="8" maxlength="255" autocomplete="new-password" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs sm:text-sm outline-none focus:border-amber-500 text-slate-700 transition" placeholder="Tối thiểu 8 ký tự">
        </label>
        <label class="block space-y-1">
          <span class="text-xs font-semibold text-slate-700">Nhập lại mật khẩu mới <b class="text-rose-500">*</b></span>
          <input type="password" name="password_confirmation" id="reset-password-confirmation" required minlength="8" maxlength="255" autocomplete="new-password" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs sm:text-sm outline-none focus:border-amber-500 text-slate-700 transition" placeholder="Xác nhận lại mật khẩu">
        </label>
        <div class="flex items-center justify-end gap-2.5 border-t border-slate-100 pt-4">
          <button type="button" class="btn-close-password rounded-xl border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">Hủy</button>
          <button type="submit" class="rounded-xl bg-amber-500 hover:bg-amber-600 px-5 py-2 text-xs font-semibold text-white shadow-md shadow-amber-500/20 transition">Đặt Lại Mật Khẩu</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ==================== MODAL PHÂN QUYỀN MENU ==================== -->
  <div id="permission-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="flex max-h-[90vh] w-full max-w-5xl flex-col overflow-hidden rounded-3xl bg-white shadow-2xl border border-blue-100">
      <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-violet-50/50">
        <div class="flex items-center gap-2.5 text-violet-700">
          <i class="ph-bold ph-key text-xl"></i>
          <div>
            <h3 class="font-bold text-base text-slate-800">Phân Quyền Menu</h3>
            <p id="permission-user-name" class="text-xs text-slate-500"></p>
          </div>
        </div>
        <button type="button" class="btn-close-permission text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition">
          <i class="ph ph-x text-lg"></i>
        </button>
      </div>

      <form id="permission-form" class="flex min-h-0 flex-1 flex-col">
        <input type="hidden" name="user_id" id="permission-user-id">

        <div class="p-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between text-xs px-6">
          <span class="text-slate-500">Tích chọn các quyền được phép thao tác trên từng danh mục menu</span>
          <button type="button" id="btn-toggle-all-permissions" class="text-brand-600 hover:underline font-semibold">Chọn tất cả</button>
        </div>

        <div class="overflow-auto p-4 sm:p-6 no-scrollbar">
          <table class="w-full min-w-[750px] text-xs border-collapse">
            <thead class="sticky top-0 bg-slate-100 text-slate-600 uppercase text-[11px] font-semibold tracking-wider">
              <tr>
                <th class="px-4 py-3 text-left">Chức Năng Menu</th>
                <?php foreach (array('view'=>'Xem','create'=>'Thêm','update'=>'Sửa','delete'=>'Xóa','import'=>'Import','export'=>'Export') as $key => $label): ?>
                  <th class="px-3 py-3 text-center w-20"><?php echo $label; ?></th>
                <?php endforeach; ?>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
              <?php foreach ($menus as $menu): ?>
                <tr class="hover:bg-blue-50/30 transition" data-menu-id="<?php echo (int) $menu->id; ?>">
                  <td class="px-4 py-3 <?php echo $menu->parent_id ? 'pl-8 text-slate-600' : 'font-bold text-slate-800'; ?>">
                    <div class="flex items-center gap-2">
                      <?php if ($menu->parent_id): ?>
                        <span class="text-slate-300">↳</span>
                      <?php endif; ?>
                      <i class="ph <?php echo htmlspecialchars($menu->icon ?: ($menu->parent_id ? 'ph-circle' : 'ph-folder-notch-open'), ENT_QUOTES, 'UTF-8'); ?> text-sm text-brand-600"></i>
                      <span><?php echo htmlspecialchars($menu->menu_name, ENT_QUOTES, 'UTF-8'); ?></span>
                      <span class="ml-1 text-[10px] font-mono text-slate-400">(<?php echo htmlspecialchars($menu->menu_code, ENT_QUOTES, 'UTF-8'); ?>)</span>
                    </div>
                  </td>
                  <?php foreach (array('view','create','update','delete','import','export') as $permissionKey): ?>
                    <td class="px-3 py-3 text-center">
                      <input type="checkbox" class="permission-check h-4 w-4 rounded text-brand-600 focus:ring-brand-500 cursor-pointer" data-permission="<?php echo $permissionKey; ?>">
                    </td>
                  <?php endforeach; ?>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="flex items-center justify-end gap-2.5 border-t border-slate-100 p-4 bg-slate-50/50">
          <button type="button" class="btn-close-permission rounded-xl border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">Hủy</button>
          <button type="submit" class="rounded-xl bg-violet-600 hover:bg-violet-700 px-5 py-2 text-xs font-semibold text-white shadow-md shadow-violet-500/20 transition">Lưu Phân Quyền</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ==================== LOGIC XỬ LÝ AJAX TÀI KHOẢN ==================== -->
  <script>
    const appUrl = <?php echo json_encode(XC_URL); ?>;
    const siteCode = <?php echo json_encode($site); ?>;
    const csrf = <?php echo json_encode($csrf); ?>;
    const permissionData = <?php echo json_encode($permissions, JSON_UNESCAPED_UNICODE); ?>;
    const toast = Swal.mixin({
      toast: true,
      position: 'bottom-end',
      showConfirmButton: false,
      timer: 3200,
      timerProgressBar: true
    });

    // Modal tài khoản (Thêm / Sửa)
    function openAccountModal(account = null) {
      $('#account-form')[0].reset();
      $('#account-id').val(account ? account.id : 0);
      $('#account-username').val(account ? account.username : '');
      $('#account-full-name').val(account ? account.full_name : '');
      $('#account-email').val(account ? (account.email || '') : '');
      $('#account-phone').val(account ? (account.phone || '') : '');
      $('#account-active').prop('checked', !account || Number(account.is_active) === 1);
      $('#account-super-admin').prop('checked', account && Number(account.is_admin) === 1);
      $('#account-password').prop('required', !account).val('');
      $('#password-required').toggle(!account);
      $('#account-modal-title').text(account ? 'Chỉnh Sửa Tài Khoản' : 'Thêm Tài Khoản Mới (Trang <?php echo htmlspecialchars($siteNames[$site], ENT_QUOTES, 'UTF-8'); ?>)');
      $('#account-modal').removeClass('hidden').addClass('flex');
    }

    function closeAccountModal() {
      $('#account-modal').addClass('hidden').removeClass('flex');
    }

    $('#btn-add-account').on('click', () => openAccountModal());
    $('.btn-edit').on('click', function () { openAccountModal($(this).data('account')); });
    $('.btn-close-modal').on('click', closeAccountModal);

    // Modal Đặt lại mật khẩu
    $('.btn-reset-password').on('click', function () {
      $('#password-form')[0].reset();
      $('#password-user-id').val($(this).data('user-id'));
      $('#password-user-name').text($(this).data('name'));
      $('#password-modal').removeClass('hidden').addClass('flex');
      window.setTimeout(() => $('#reset-password').trigger('focus'), 50);
    });

    $('.btn-close-password').on('click', () => $('#password-modal').addClass('hidden').removeClass('flex'));

    $('#password-form').on('submit', function (event) {
      event.preventDefault();
      if (!this.checkValidity()) { this.reportValidity(); return; }
      if ($('#reset-password').val() !== $('#reset-password-confirmation').val()) {
        toast.fire({ icon: 'error', title: 'Mật khẩu xác nhận không khớp.' });
        return;
      }
      const button = $(this).find('button[type="submit"]').prop('disabled', true).text('Đang xử lý...');
      $.ajax({
        url: appUrl + '/api/resetAccountPassword',
        method: 'POST',
        dataType: 'json',
        data: {
          csrf: csrf,
          site_code: siteCode,
          user_id: $('#password-user-id').val(),
          password: $('#reset-password').val(),
          password_confirmation: $('#reset-password-confirmation').val()
        }
      })
      .done(response => {
        toast.fire({ icon: 'success', title: response.message });
        $('#password-modal').addClass('hidden').removeClass('flex');
        $('#password-form')[0].reset();
      })
      .fail(xhr => {
        toast.fire({ icon: 'error', title: (xhr.responseJSON || {}).message || 'Không thể đặt lại mật khẩu.' });
      })
      .always(() => button.prop('disabled', false).text('Đặt Lại Mật Khẩu'));
    });

    // Lưu tài khoản
    $('#account-form').on('submit', function (event) {
      event.preventDefault();
      if (!this.checkValidity()) { this.reportValidity(); return; }
      const button = $(this).find('button[type="submit"]').prop('disabled', true).text('Đang lưu...');
      $.ajax({
        url: appUrl + '/api/saveAccount',
        method: 'POST',
        dataType: 'json',
        data: $(this).serialize()
      })
      .done(response => {
        toast.fire({ icon: 'success', title: response.message });
        setTimeout(() => window.location.reload(), 500);
      })
      .fail(xhr => {
        toast.fire({ icon: 'error', title: (xhr.responseJSON || {}).message || 'Không thể lưu tài khoản.' });
      })
      .always(() => button.prop('disabled', false).text('Lưu Tài Khoản'));
    });

    // Xóa tài khoản khỏi site
    $('.btn-delete').on('click', function () {
      const id = $(this).data('id');
      const name = $(this).data('name');
      Swal.fire({
        title: 'Xác nhận xóa quyền truy cập?',
        text: name + ' sẽ không còn quyền truy cập vào trang này.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Đồng ý xóa',
        cancelButtonText: 'Hủy bỏ',
        confirmButtonColor: '#e11d48'
      }).then(result => {
        if (!result.isConfirmed) return;
        $.ajax({
          url: appUrl + '/api/deleteAccount',
          method: 'POST',
          dataType: 'json',
          data: { csrf: csrf, site_code: siteCode, id: id }
        })
        .done(response => {
          toast.fire({ icon: 'success', title: response.message });
          setTimeout(() => window.location.reload(), 500);
        })
        .fail(xhr => {
          toast.fire({ icon: 'error', title: (xhr.responseJSON || {}).message || 'Không thể xóa quyền truy cập.' });
        });
      });
    });

    // Modal Phân quyền Menu
    $('.btn-permissions').on('click', function () {
      const userId = String($(this).data('user-id'));
      $('#permission-user-id').val(userId);
      $('#permission-user-name').text($(this).data('name') + ' · Trang ' + siteCode.toUpperCase());
      $('.permission-check').prop('checked', false);

      const userPermissions = permissionData[userId] || {};
      $('#permission-form tr[data-menu-id]').each(function () {
        const menuId = String($(this).data('menu-id'));
        const values = userPermissions[menuId] || {};
        $(this).find('.permission-check').each(function () {
          $(this).prop('checked', Number(values[$(this).data('permission')] || 0) === 1);
        });
      });

      $('#permission-modal').removeClass('hidden').addClass('flex');
    });

    $('.btn-close-permission').on('click', () => $('#permission-modal').addClass('hidden').removeClass('flex'));

    // Toggle tất cả quyền
    let allChecked = false;
    $('#btn-toggle-all-permissions').on('click', function () {
      allChecked = !allChecked;
      $('.permission-check').prop('checked', allChecked);
      $(this).text(allChecked ? 'Bỏ chọn tất cả' : 'Chọn tất cả');
    });

    $('#permission-form').on('submit', function (event) {
      event.preventDefault();
      const permissions = {};
      $(this).find('tr[data-menu-id]').each(function () {
        const menuId = String($(this).data('menu-id'));
        permissions[menuId] = {};
        $(this).find('.permission-check').each(function () {
          permissions[menuId][$(this).data('permission')] = $(this).is(':checked') ? 1 : 0;
        });
      });

      const button = $(this).find('button[type="submit"]').prop('disabled', true).text('Đang lưu...');
      $.ajax({
        url: appUrl + '/api/saveAccountPermissions',
        method: 'POST',
        dataType: 'json',
        data: {
          csrf: csrf,
          site_code: siteCode,
          user_id: $('#permission-user-id').val(),
          permissions: JSON.stringify(permissions)
        }
      })
      .done(response => {
        toast.fire({ icon: 'success', title: response.message });
        $('#permission-modal').addClass('hidden').removeClass('flex');
        setTimeout(() => window.location.reload(), 500);
      })
      .fail(xhr => {
        toast.fire({ icon: 'error', title: (xhr.responseJSON || {}).message || 'Không thể cập nhật phân quyền.' });
      })
      .always(() => button.prop('disabled', false).text('Lưu Phân Quyền'));
    });

    // Tìm kiếm nhanh tài khoản
    $('#account-search').on('input', function () {
      const value = $(this).val().toLowerCase().trim();
      $('.account-row').each(function () {
        $(this).toggle($(this).data('search').includes(value));
      });
    });
  </script>

<?php require_once('footer.php'); ?>
