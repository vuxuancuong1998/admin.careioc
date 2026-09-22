<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Đăng nhập Hệ thống Quản trị Điều hành bệnh viện thông minh</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Heroicons CDN cho icon -->
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4">

  <div class="w-full max-w-md bg-slate-800 rounded-2xl shadow-2xl border border-slate-700 p-8 space-y-6">
    <!-- Header -->
    <div class="text-center space-y-2">
      <div class="inline-flex p-3 rounded-full bg-indigo-600/10 text-indigo-400 mb-1">
        <ion-icon name="shield-checkmark" class="text-3xl"></ion-icon>
      </div>
      <h1 class="text-2xl font-bold text-white tracking-tight">ADMIN MASTER</h1>
      <p class="text-sm text-slate-400">Vui lòng nhập thông tin xác thực quản trị viên</p>
    </div>

    <!-- Alert Box (Mặc định ẩn) -->
    <div id="error-box" class="hidden p-3 rounded-lg bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm flex items-center gap-2">
      <ion-icon name="alert-circle-outline" class="text-lg flex-shrink-0"></ion-icon>
      <span id="error-message"></span>
    </div>

    <!-- Login Form -->
    <form id="admin-login-form" class="space-y-4" novalidate>
      <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
      <!-- Username / Email -->
      <div class="space-y-1">
        <label for="username" class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Tên đăng nhập hoặc Email</label>
        <div class="relative">
          <input 
            type="text" 
            id="username" 
            name="username" 
            required 
            placeholder="admin@example.com"
            class="w-full px-4 py-2.5 bg-slate-900/60 border border-slate-700 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm"
          />
        </div>
      </div>

      <!-- Password -->
      <div class="space-y-1">
        <div class="flex justify-between items-center">
          <label for="password" class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Mật khẩu</label>
          <a href="#" class="text-xs text-indigo-400 hover:text-indigo-300">Quên mật khẩu?</a>
        </div>
        <div class="relative">
          <input 
            type="password" 
            id="password" 
            name="password" 
            required 
            placeholder="••••••••"
            class="w-full px-4 py-2.5 bg-slate-900/60 border border-slate-700 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm pr-10"
          />
          <button 
            type="button" 
            id="toggle-password" 
            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-200 focus:outline-none"
          >
            <ion-icon name="eye-outline" id="eye-icon" class="text-lg"></ion-icon>
          </button>
        </div>
      </div>

      <!-- Submit Button -->
      <button 
        type="submit" 
        id="submit-btn"
        class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg shadow-lg shadow-indigo-600/30 transition duration-150 flex items-center justify-center gap-2 cursor-pointer text-sm"
      >
        <span id="btn-text">Đăng nhập</span>
        <div id="btn-spinner" class="hidden w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
      </button>
    </form>

    <!-- Footer Security Notice -->
    <div class="text-center pt-2 border-t border-slate-700/50">
      <p class="text-xs text-slate-500 flex items-center justify-center gap-1">
        <ion-icon name="lock-closed-outline"></ion-icon>
        Khu vực bảo mật cao. Mọi hành vi truy cập trái phép đều được ghi lại nhật ký (IP/Audit Log).
      </p>
    </div>
  </div>

  <script>
    const loginUrl = <?php echo json_encode(XC_URL . '/api/adminLogin'); ?>;
    const loggedOut = <?php echo !empty($loggedOut) ? 'true' : 'false'; ?>;
    const toast = Swal.mixin({
      toast: true,
      position: 'bottom-end',
      showConfirmButton: false,
      timer: 3200,
      timerProgressBar: true
    });

    if (loggedOut) {
      toast.fire({ icon: 'success', title: 'Đã đăng xuất an toàn.' });
    }

    // Ẩn/hiện mật khẩu
    const toggleBtn = document.getElementById('toggle-password');
    const pwdInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');

    toggleBtn.addEventListener('click', () => {
      const isPassword = pwdInput.type === 'password';
      pwdInput.type = isPassword ? 'text' : 'password';
      eyeIcon.setAttribute('name', isPassword ? 'eye-off-outline' : 'eye-outline');
    });

    // Xử lý gửi Form
    const form = document.getElementById('admin-login-form');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = document.getElementById('btn-text');
    const btnSpinner = document.getElementById('btn-spinner');
    const errorBox = document.getElementById('error-box');
    const errorMsg = document.getElementById('error-message');

    form.addEventListener('submit', (e) => {
      e.preventDefault();

      errorBox.classList.add('hidden');

      const username = form.username.value.trim();
      const password = form.password.value;
      if (!username || !password || username.length > 255 || password.length > 255) {
        showError('Vui lòng nhập tên đăng nhập và mật khẩu hợp lệ.');
        toast.fire({ icon: 'error', title: 'Thông tin đăng nhập chưa hợp lệ.' });
        return;
      }

      btnText.textContent = 'Đang xác thực...';
      btnSpinner.classList.remove('hidden');
      submitBtn.disabled = true;

      $.ajax({
        url: loginUrl,
        type: 'POST',
        dataType: 'json',
        data: $(form).serialize()
      }).done((response) => {
        if (!response.success) {
          showError(response.message || 'Không thể đăng nhập.');
          toast.fire({ icon: 'error', title: response.message || 'Không thể đăng nhập.' });
          return;
        }

        toast.fire({ icon: 'success', title: response.message });
        window.setTimeout(() => {
          window.location.href = response.data.redirect_url;
        }, 500);
      }).fail((xhr) => {
        const response = xhr.responseJSON || {};
        const message = response.message || 'Không thể kết nối đến máy chủ. Vui lòng thử lại.';
        showError(message);
        toast.fire({ icon: 'error', title: message });
      }).always(() => {
        btnText.textContent = 'Đăng nhập';
        btnSpinner.classList.add('hidden');
        submitBtn.disabled = false;
      });
    });

    function showError(msg) {
      errorMsg.textContent = msg;
      errorBox.classList.remove('hidden');
    }
  </script>
</body>
</html>
