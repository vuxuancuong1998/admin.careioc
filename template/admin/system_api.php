<?php require_once('header.php'); ?>

<main class="flex-1 overflow-y-auto p-3 sm:p-5 no-scrollbar bg-[#f1f5f9] space-y-4">
  <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
    <div>
      <p class="text-xs font-bold uppercase tracking-[0.18em] text-blue-600">Tích hợp dữ liệu</p>
      <h2 class="mt-1 text-xl sm:text-2xl font-bold text-[#003893]">Quản trị hệ thống API</h2>
      <p class="mt-1 text-xs sm:text-sm text-slate-500">Theo dõi cấu hình, đăng nhập và phiên kết nối tới các hệ thống nguồn.</p>
    </div>
    <a href="<?php echo XC_URL; ?>/system" class="inline-flex items-center justify-center gap-2 px-3.5 py-2 bg-white border border-slate-300 text-slate-700 hover:border-blue-500 hover:text-blue-700 text-xs font-semibold rounded-lg transition shadow-xs">
      <i class="ph-bold ph-sliders-horizontal"></i>
      <span>Thiết lập tham số</span>
    </a>
  </div>

  <section class="grid grid-cols-1 xl:grid-cols-12 gap-4">
    <article class="xl:col-span-8 bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
      <div class="bg-[#0b5394] text-white px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-3">
          <span class="w-10 h-10 rounded-lg bg-white/15 flex items-center justify-center">
            <i class="ph-bold ph-hospital text-xl"></i>
          </span>
          <div>
            <div class="flex items-center gap-2">
              <h3 class="text-sm font-bold">VNPT-HIS</h3>
              <span id="his-status-badge" class="px-2 py-0.5 rounded-full bg-white/15 text-[10px] font-bold">Đang kiểm tra</span>
            </div>
            <p id="his-status-message" class="mt-0.5 text-[11px] text-blue-100">Đang xác minh phiên kết nối...</p>
          </div>
        </div>
        <div class="flex flex-wrap gap-2">
          <button type="button" id="btn-his-status" class="his-action inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/10 hover:bg-white/20 text-white text-xs font-semibold rounded-lg transition">
            <i class="ph-bold ph-arrows-clockwise"></i><span>Kiểm tra</span>
          </button>
          <button type="button" id="btn-his-login" class="his-action inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold rounded-lg transition">
            <i class="ph-bold ph-sign-in"></i><span>Đăng nhập</span>
          </button>
          <button type="button" id="btn-his-logout" disabled class="his-action inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-500 hover:bg-rose-600 text-white text-xs font-semibold rounded-lg transition disabled:opacity-40 disabled:cursor-not-allowed">
            <i class="ph-bold ph-sign-out"></i><span>Đăng xuất</span>
          </button>
        </div>
      </div>

      <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
        <div class="p-3 rounded-lg border border-slate-200 bg-slate-50">
          <p class="text-slate-500 mb-1">URL hệ thống</p>
          <p class="font-semibold text-slate-800 break-all"><?php echo $hisConfig['url'] !== '' ? htmlspecialchars($hisConfig['url'], ENT_QUOTES, 'UTF-8') : 'Chưa cấu hình'; ?></p>
        </div>
        <div class="p-3 rounded-lg border border-slate-200 bg-slate-50">
          <p class="text-slate-500 mb-1">Tài khoản tích hợp</p>
          <p id="his-session-user" class="font-semibold text-slate-800 break-all"><?php echo $hisConfig['username'] !== '' ? htmlspecialchars($hisConfig['username'], ENT_QUOTES, 'UTF-8') : 'Chưa cấu hình'; ?></p>
        </div>
        <div class="p-3 rounded-lg border border-slate-200 bg-slate-50">
          <p class="text-slate-500 mb-1">Cấu hình đăng nhập</p>
          <p class="font-semibold <?php echo $hisConfig['ready'] ? 'text-emerald-700' : 'text-rose-700'; ?>">
            <i class="ph-bold <?php echo $hisConfig['ready'] ? 'ph-check-circle' : 'ph-warning-circle'; ?> mr-1"></i>
            <?php echo $hisConfig['ready'] ? 'Đã đủ 3 tham số bắt buộc' : 'Thiếu hoặc chưa kích hoạt tham số'; ?>
          </p>
        </div>
        <div class="p-3 rounded-lg border border-slate-200 bg-slate-50">
          <p class="text-slate-500 mb-1">Phiên xác thực gần nhất</p>
          <p id="his-verified-at" class="font-semibold text-slate-800">—</p>
        </div>
      </div>
    </article>

    <aside class="xl:col-span-4 bg-white rounded-xl border border-slate-200 shadow-xs p-4">
      <div class="flex items-center gap-2 mb-3">
        <i class="ph-bold ph-info text-blue-600"></i>
        <h3 class="text-sm font-bold text-slate-800">Thông tin vận hành</h3>
      </div>
      <ul class="space-y-3 text-xs text-slate-600">
        <li class="flex gap-2"><i class="ph-bold ph-shield-check text-emerald-600 mt-0.5"></i><span>Hệ thống tự động đăng nhập và xác thực thông tin tài khoản.</span></li>
        <li class="flex gap-2"><i class="ph-bold ph-eye-slash text-blue-600 mt-0.5"></i><span>Các dữ liệu nhạy cảm được mã hóa và bảo vệ an toàn.</span></li>
        <li class="flex gap-2"><i class="ph-bold ph-clock-countdown text-amber-600 mt-0.5"></i><span>Dùng nút Kiểm tra để xác minh phiên trực tiếp với HIS trước khi đồng bộ dữ liệu.</span></li>
      </ul>
    </aside>
  </section>

  <section class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
      <div>
        <h3 class="text-sm font-bold text-slate-800">Các hệ thống tích hợp</h3>
        <p class="text-[11px] text-slate-500 mt-0.5">Tổng quan các nguồn dữ liệu của CARE IOC</p>
      </div>
      <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-blue-50 text-blue-700">1 đang cấu hình</span>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-px bg-slate-200">
      <div class="bg-white p-4"><div class="flex justify-between"><i class="ph-bold ph-hospital text-xl text-blue-700"></i><span class="text-[10px] font-bold text-emerald-700">ĐANG DÙNG</span></div><h4 class="mt-3 text-sm font-bold">HIS</h4><p class="mt-1 text-xs text-slate-500">Quản lý bệnh viện</p></div>
      <div class="bg-white p-4 opacity-70"><div class="flex justify-between"><i class="ph-bold ph-flask text-xl text-violet-600"></i><span class="text-[10px] font-bold text-slate-400">CHƯA CẤU HÌNH</span></div><h4 class="mt-3 text-sm font-bold">LIS</h4><p class="mt-1 text-xs text-slate-500">Xét nghiệm</p></div>
      <div class="bg-white p-4 opacity-70"><div class="flex justify-between"><i class="ph-bold ph-x-ray text-xl text-cyan-600"></i><span class="text-[10px] font-bold text-slate-400">CHƯA CẤU HÌNH</span></div><h4 class="mt-3 text-sm font-bold">RIS</h4><p class="mt-1 text-xs text-slate-500">Chẩn đoán hình ảnh</p></div>
      <div class="bg-white p-4 opacity-70"><div class="flex justify-between"><i class="ph-bold ph-file-medical text-xl text-amber-600"></i><span class="text-[10px] font-bold text-slate-400">CHƯA CẤU HÌNH</span></div><h4 class="mt-3 text-sm font-bold">EMR</h4><p class="mt-1 text-xs text-slate-500">Bệnh án điện tử</p></div>
    </div>
  </section>
</main>

<script>
  const csrfToken = <?php echo json_encode($csrf); ?>;
  const appUrl = <?php echo json_encode(XC_URL); ?>;
  let hisLoggedIn = false;

  const toast = Swal.mixin({
    toast: true,
    position: 'bottom-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
  });

  function setHisBusy(isBusy) {
    $('.his-action').prop('disabled', isBusy).toggleClass('opacity-50 cursor-not-allowed', isBusy);
    if (isBusy) $('#his-status-badge').text('Đang xử lý');
  }

  function renderHisStatus(data, message) {
    hisLoggedIn = !!(data && data.logged_in);
    const reachable = !data || data.reachable !== false;
    const badge = $('#his-status-badge');
    badge.removeClass('bg-white/15 bg-emerald-500 bg-amber-500 bg-rose-500');
    if (hisLoggedIn && reachable) {
      badge.text('Đã đăng nhập').addClass('bg-emerald-500');
    } else if (hisLoggedIn) {
      badge.text('Mất kết nối').addClass('bg-amber-500');
    } else {
      badge.text('Chưa đăng nhập').addClass('bg-rose-500');
    }
    $('#his-status-message').text(message || 'Chưa có thông tin trạng thái.');
    if (hisLoggedIn && data.username) $('#his-session-user').text(data.username);
    $('#his-verified-at').text(hisLoggedIn && data.last_verified_at ? data.last_verified_at : '—');
    $('#btn-his-login').prop('disabled', hisLoggedIn).toggleClass('opacity-40 cursor-not-allowed', hisLoggedIn);
    $('#btn-his-logout').prop('disabled', !hisLoggedIn).toggleClass('opacity-40 cursor-not-allowed', !hisLoggedIn);
  }

  function callHisAction(action, showToast) {
    setHisBusy(true);
    return $.ajax({
      url: appUrl + '/ioc_api/' + action,
      method: 'POST',
      dataType: 'json',
      data: { csrf: csrfToken }
    })
    .done(function(response) {
      renderHisStatus(response.data || {}, response.message || '');
      if (showToast) toast.fire({ icon: 'success', title: response.message || 'Thao tác HIS thành công.' });
    })
    .fail(function(xhr) {
      const response = xhr.responseJSON || {};
      renderHisStatus({ logged_in: false }, response.message || 'Không thể kết nối tới HIS.');
      toast.fire({ icon: 'error', title: response.message || 'Không thể thực hiện thao tác HIS.' });
    })
    .always(function() {
      setHisBusy(false);
      $('#btn-his-login').prop('disabled', hisLoggedIn).toggleClass('opacity-40 cursor-not-allowed', hisLoggedIn);
      $('#btn-his-logout').prop('disabled', !hisLoggedIn).toggleClass('opacity-40 cursor-not-allowed', !hisLoggedIn);
    });
  }

  $('#btn-his-status').on('click', function() { callHisAction('status_HIS', true); });
  $('#btn-his-login').on('click', function() { callHisAction('login_HIS', true); });
  $('#btn-his-logout').on('click', function() { callHisAction('logout_HIS', true); });
  $(document).ready(function() { callHisAction('status_HIS', false); });
</script>

<?php require_once('footer.php'); ?>
