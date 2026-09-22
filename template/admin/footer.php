
  <!-- ==================== POPUP MODAL THÔNG TIN TÀI KHOẢN (HỒ SƠ CÁ NHÂN) ==================== -->
  <div id="user-profile-modal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl border border-blue-100 overflow-hidden transform transition-all">
      <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-blue-50/80 via-white to-blue-50/50">
        <div class="flex items-center gap-2.5 text-brand-700">
          <div class="w-8 h-8 rounded-xl bg-blue-100/80 flex items-center justify-center text-brand-600">
            <i class="ph-bold ph-user-circle text-lg"></i>
          </div>
          <div>
            <h3 class="font-bold text-base text-slate-800 leading-tight">Hồ Sơ Tài Khoản Quản Trị</h3>
            <p class="text-[11px] text-slate-500 font-medium">Thông tin chi tiết người dùng đang đăng nhập</p>
          </div>
        </div>
        <button onclick="closeUserProfileModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition">
          <i class="ph ph-x text-lg"></i>
        </button>
      </div>

      <div class="p-6 space-y-5">
        <!-- Avatar & Tên & Vai trò -->
        <div class="flex items-center gap-4 p-4 rounded-2xl bg-gradient-to-br from-blue-50/70 via-sky-50/30 to-indigo-50/20 border border-blue-100/70">
          <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-brand-700 to-blue-500 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-brand-500/25 ring-4 ring-white shrink-0 tracking-wider uppercase select-none">
            <?php echo htmlspecialchars(isset($avatarInitials) ? $avatarInitials : 'AD', ENT_QUOTES, 'UTF-8'); ?>
          </div>
          <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2 flex-wrap">
              <h4 class="text-base font-bold text-slate-900 truncate"><?php echo htmlspecialchars(isset($currentFullName) ? $currentFullName : 'Admin', ENT_QUOTES, 'UTF-8'); ?></h4>
              <?php if (!empty($isSuperAdmin)): ?>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                  <i class="ph-bold ph-crown text-xs"></i> Super Admin
                </span>
              <?php else: ?>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-brand-700 border border-blue-200">
                  <i class="ph-bold ph-shield-check text-xs"></i> Quản trị viên
                </span>
              <?php endif; ?>
            </div>
            <p class="text-xs text-slate-500 font-medium mt-1 flex items-center gap-2">
              <span><i class="ph ph-at text-slate-400"></i> <?php echo htmlspecialchars(isset($currentUsername) ? $currentUsername : '', ENT_QUOTES, 'UTF-8'); ?></span>
              <span class="text-slate-300">•</span>
              <span class="text-emerald-600 font-semibold flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Đang hoạt động
              </span>
            </p>
          </div>
        </div>

        <!-- Bảng chi tiết thông tin -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
          <div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100">
            <span class="text-slate-400 block text-[11px] font-medium mb-0.5">Email liên hệ</span>
            <span class="text-slate-800 font-semibold break-all flex items-center gap-1.5">
              <i class="ph ph-envelope text-brand-600 text-sm"></i>
              <?php echo htmlspecialchars(isset($currentUserEmail) ? $currentUserEmail : 'Chưa cập nhật', ENT_QUOTES, 'UTF-8'); ?>
            </span>
          </div>

          <div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100">
            <span class="text-slate-400 block text-[11px] font-medium mb-0.5">Số điện thoại</span>
            <span class="text-slate-800 font-semibold flex items-center gap-1.5">
              <i class="ph ph-phone text-brand-600 text-sm"></i>
              <?php echo htmlspecialchars(isset($currentUserPhone) ? $currentUserPhone : 'Chưa cập nhật', ENT_QUOTES, 'UTF-8'); ?>
            </span>
          </div>

          <div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100">
            <span class="text-slate-400 block text-[11px] font-medium mb-0.5">Mã tài khoản (User ID)</span>
            <span class="text-slate-800 font-semibold flex items-center gap-1.5">
              <i class="ph ph-identification-badge text-brand-600 text-sm"></i>
              #<?php echo (int) (isset($currentUserId) ? $currentUserId : 0); ?>
            </span>
          </div>

          <div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100">
            <span class="text-slate-400 block text-[11px] font-medium mb-0.5">Đăng nhập gần nhất</span>
            <span class="text-slate-800 font-semibold flex items-center gap-1.5">
              <i class="ph ph-clock text-brand-600 text-sm"></i>
              <?php
                if (!empty($userDbInfo->last_login_at)) {
                  echo date('H:i:s - d/m/Y', strtotime($userDbInfo->last_login_at));
                } else {
                  echo 'Phiên hiện tại';
                }
              ?>
            </span>
          </div>
        </div>

        <!-- Phân hệ được phân quyền -->
        <div class="p-3.5 bg-blue-50/40 rounded-xl border border-blue-100">
          <div class="flex items-center justify-between mb-2">
            <span class="text-slate-700 font-semibold text-xs flex items-center gap-1.5">
              <i class="ph-bold ph-shield text-brand-600"></i> Phân hệ được phân quyền
            </span>
            <span class="text-[11px] text-brand-700 font-medium">
              <?php echo count(isset($userAllowedSites) ? $userAllowedSites : array()); ?> phân hệ
            </span>
          </div>
          <div class="flex flex-wrap gap-2">
            <?php foreach (isset($userAllowedSites) ? $userAllowedSites : array() as $sCode => $sName): ?>
              <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-white text-slate-700 border border-blue-200/70 shadow-xs">
                <i class="ph ph-check-circle text-emerald-600"></i>
                <span><?php echo htmlspecialchars($sName, ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="text-[10px] text-slate-400 font-normal">(<?php echo htmlspecialchars($sCode, ENT_QUOTES, 'UTF-8'); ?>)</span>
              </span>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
        <button type="button" onclick="openChangePasswordModalFromProfile()" class="px-3.5 py-2 text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-xl transition flex items-center gap-1.5">
          <i class="ph-bold ph-key"></i> Đổi mật khẩu
        </button>
        <button type="button" onclick="closeUserProfileModal()" class="px-5 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl shadow-xs transition">
          Đóng
        </button>
      </div>
    </div>
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
      <form id="form-change-self-password" onsubmit="handlePasswordSubmit(event)" class="p-6 space-y-4">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(isset($userActionCsrf) ? $userActionCsrf : '', ENT_QUOTES, 'UTF-8'); ?>">
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Mật khẩu hiện tại <span class="text-rose-500">*</span></label>
          <input type="password" name="current_password" required class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500" placeholder="••••••••">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Mật khẩu mới <span class="text-rose-500">*</span></label>
          <input type="password" name="new_password" required minlength="8" class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500" placeholder="Tối thiểu 8 ký tự">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Xác nhận mật khẩu mới <span class="text-rose-500">*</span></label>
          <input type="password" name="confirm_password" required minlength="8" class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500" placeholder="••••••••">
        </div>
        <div class="pt-2 flex items-center justify-end gap-2.5">
          <button type="button" onclick="closeChangePasswordModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">Hủy</button>
          <button type="submit" id="btn-submit-password" class="px-5 py-2 text-xs font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-xl shadow-md shadow-brand-500/20 transition">Lưu Mật Khẩu</button>
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
      const dropdown = document.getElementById('user-dropdown');
      const caret = document.getElementById('user-dropdown-caret');
      if (dropdown) {
        const isHidden = dropdown.classList.contains('hidden');
        dropdown.classList.toggle('hidden');
        if (caret) {
          if (isHidden) {
            caret.classList.add('rotate-180');
          } else {
            caret.classList.remove('rotate-180');
          }
        }
      }
    }
    window.addEventListener('click', (e) => {
      const dropdown = document.getElementById('user-dropdown');
      const caret = document.getElementById('user-dropdown-caret');
      if (dropdown && !dropdown.classList.contains('hidden')) {
        if (!dropdown.contains(e.target)) {
          dropdown.classList.add('hidden');
          if (caret) caret.classList.remove('rotate-180');
        }
      }
    });

    // Modal Thông Tin Cá Nhân (Hồ sơ người dùng)
    function openUserProfileModal() {
      const dropdown = document.getElementById('user-dropdown');
      if (dropdown) dropdown.classList.add('hidden');
      const caret = document.getElementById('user-dropdown-caret');
      if (caret) caret.classList.remove('rotate-180');
      document.getElementById('user-profile-modal').classList.remove('hidden');
    }
    function closeUserProfileModal() {
      document.getElementById('user-profile-modal').classList.add('hidden');
    }
    function openChangePasswordModalFromProfile() {
      closeUserProfileModal();
      openChangePasswordModal();
    }

    // Modal Đổi Mật Khẩu
    function openChangePasswordModal() {
      const dropdown = document.getElementById('user-dropdown');
      if (dropdown) dropdown.classList.add('hidden');
      const caret = document.getElementById('user-dropdown-caret');
      if (caret) caret.classList.remove('rotate-180');
      document.getElementById('change-password-modal').classList.remove('hidden');
    }
    function closeChangePasswordModal() {
      document.getElementById('change-password-modal').classList.add('hidden');
      const form = document.getElementById('form-change-self-password');
      if (form) form.reset();
    }
    function handlePasswordSubmit(event) {
      event.preventDefault();
      const form = document.getElementById('form-change-self-password');
      const formData = new FormData(form);
      const submitBtn = document.getElementById('btn-submit-password');
      
      const newPass = formData.get('new_password');
      const confirmPass = formData.get('confirm_password');
      if (newPass !== confirmPass) {
        Swal.fire({ toast: true, position: 'bottom-end', icon: 'error', title: 'Xác nhận mật khẩu mới không khớp.', showConfirmButton: false, timer: 3000 });
        return;
      }
      
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerText = 'Đang lưu...';
      }

      $.ajax({
        url: <?php echo json_encode(XC_URL . '/api/changeSelfPassword'); ?>,
        type: 'POST',
        data: $(form).serialize(),
        dataType: 'json',
        success: function(res) {
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerText = 'Lưu Mật Khẩu';
          }
          if (res.success) {
            Swal.fire({ toast: true, position: 'bottom-end', icon: 'success', title: res.message || 'Đổi mật khẩu thành công.', showConfirmButton: false, timer: 3000 });
            closeChangePasswordModal();
          } else {
            Swal.fire({ toast: true, position: 'bottom-end', icon: 'error', title: res.message || 'Không thể đổi mật khẩu.', showConfirmButton: false, timer: 3000 });
          }
        },
        error: function(xhr) {
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerText = 'Lưu Mật Khẩu';
          }
          let msg = 'Đã có lỗi xảy ra khi đổi mật khẩu.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            msg = xhr.responseJSON.message;
          }
          Swal.fire({ toast: true, position: 'bottom-end', icon: 'error', title: msg, showConfirmButton: false, timer: 3000 });
        }
      });
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
