<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản lý tài khoản <?php echo htmlspecialchars($siteNames[$site], ENT_QUOTES, 'UTF-8'); ?> | CARE IOC</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-700">
  <header class="sticky top-0 z-30 border-b border-blue-100 bg-white/95 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
      <div class="flex min-w-0 items-center gap-3">
        <a href="<?php echo XC_URL; ?>/" class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-blue-600 text-white shadow-lg shadow-blue-200">
          <i class="ph-bold ph-arrow-left"></i>
        </a>
        <div class="min-w-0">
          <p class="truncate text-xs font-semibold uppercase tracking-wider text-blue-600">CARE IOC · Quản trị hệ thống</p>
          <h1 class="truncate text-lg font-bold text-slate-900">Quản lý tài khoản và phân quyền</h1>
        </div>
      </div>
      <div class="hidden text-right sm:block">
        <p class="text-sm font-semibold text-slate-800"><?php echo htmlspecialchars($_SESSION['user']['fullname'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p class="text-xs text-emerald-600"><?php echo !empty($_SESSION['user']['is_admin']) ? 'Super Admin · Toàn quyền' : 'Quản trị phân quyền'; ?></p>
      </div>
    </div>
  </header>

  <main class="mx-auto max-w-7xl space-y-5 px-4 py-6 sm:px-6">
    <section class="grid grid-cols-1 gap-3 sm:grid-cols-3">
      <?php foreach ($siteNames as $siteCode => $siteName): ?>
        <?php if (!$this->helper->userHasMenu('admin.accounts.' . $siteCode, 'view')) continue; ?>
        <a href="<?php echo XC_URL; ?>/accounts/<?php echo $siteCode; ?>"
           class="rounded-2xl border p-4 transition <?php echo $site === $siteCode ? 'border-blue-500 bg-blue-600 text-white shadow-lg shadow-blue-200' : 'border-slate-200 bg-white hover:border-blue-300'; ?>">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-semibold uppercase tracking-wide <?php echo $site === $siteCode ? 'text-blue-100' : 'text-slate-400'; ?>">Tài khoản trang</p>
              <h2 class="mt-1 text-lg font-bold"><?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?></h2>
            </div>
            <span class="grid h-11 w-11 place-items-center rounded-xl <?php echo $site === $siteCode ? 'bg-white/15' : 'bg-blue-50 text-blue-600'; ?>">
              <i class="ph-bold <?php echo $siteCode === 'admin' ? 'ph-shield-star' : ($siteCode === 'backend' ? 'ph-database' : 'ph-chart-line-up'); ?> text-xl"></i>
            </span>
          </div>
          <p class="mt-3 text-sm <?php echo $site === $siteCode ? 'text-blue-100' : 'text-slate-500'; ?>"><?php echo (int) $counts[$siteCode]; ?> tài khoản đang hoạt động</p>
        </a>
      <?php endforeach; ?>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
      <div class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 class="font-bold text-slate-900">Tài khoản trang <?php echo htmlspecialchars($siteNames[$site], ENT_QUOTES, 'UTF-8'); ?></h2>
          <p class="mt-1 text-xs text-slate-500">Super admin luôn có toàn quyền trên cả ba site.</p>
        </div>
        <div class="flex flex-col gap-2 sm:flex-row">
          <label class="relative">
            <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input id="account-search" type="search" placeholder="Tìm tài khoản..." class="w-full rounded-xl border border-slate-200 py-2 pl-9 pr-3 text-sm outline-none focus:border-blue-500 sm:w-64">
          </label>
          <?php if ($canCreate): ?>
            <button type="button" id="btn-add-account" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
              <i class="ph-bold ph-user-plus"></i> Thêm tài khoản
            </button>
          <?php endif; ?>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] text-left text-sm">
          <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
            <tr>
              <th class="px-4 py-3">Tài khoản</th>
              <th class="px-4 py-3">Liên hệ</th>
              <th class="px-4 py-3">Vai trò</th>
              <th class="px-4 py-3">Trạng thái</th>
              <th class="px-4 py-3">Đăng nhập gần nhất</th>
              <th class="px-4 py-3 text-right">Thao tác</th>
            </tr>
          </thead>
          <tbody id="account-table" class="divide-y divide-slate-100">
            <?php if (!$accounts): ?>
              <tr><td colspan="6" class="px-4 py-12 text-center text-slate-400">Chưa có tài khoản cho trang này.</td></tr>
            <?php endif; ?>
            <?php foreach ($accounts as $account): ?>
              <tr class="account-row hover:bg-slate-50"
                  data-search="<?php echo htmlspecialchars(strtolower($account->username . ' ' . $account->full_name . ' ' . $account->email), ENT_QUOTES, 'UTF-8'); ?>">
                <td class="px-4 py-3">
                  <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-blue-50 font-bold text-blue-700">
                      <?php echo htmlspecialchars(strtoupper(substr($account->full_name, 0, 1)), ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                    <div><p class="font-semibold text-slate-900"><?php echo htmlspecialchars($account->full_name, ENT_QUOTES, 'UTF-8'); ?></p><p class="text-xs text-slate-500"><?php echo htmlspecialchars($account->username, ENT_QUOTES, 'UTF-8'); ?></p></div>
                  </div>
                </td>
                <td class="px-4 py-3"><p><?php echo htmlspecialchars($account->email ?: '—', ENT_QUOTES, 'UTF-8'); ?></p><p class="text-xs text-slate-400"><?php echo htmlspecialchars($account->phone ?: 'Chưa có số điện thoại', ENT_QUOTES, 'UTF-8'); ?></p></td>
                <td class="px-4 py-3">
                  <?php if ((int) $account->is_admin === 1): ?>
                    <span class="rounded-full bg-violet-100 px-2.5 py-1 text-xs font-bold text-violet-700">Super Admin</span>
                  <?php else: ?>
                    <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700"><?php echo htmlspecialchars($siteNames[$site], ENT_QUOTES, 'UTF-8'); ?></span>
                  <?php endif; ?>
                </td>
                <td class="px-4 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo (int) $account->site_active === 1 && (int) $account->is_active === 1 ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'; ?>"><?php echo (int) $account->site_active === 1 && (int) $account->is_active === 1 ? 'Hoạt động' : 'Đã khóa'; ?></span></td>
                <td class="px-4 py-3 text-slate-500"><?php echo $account->last_login_at ? date('d/m/Y H:i', strtotime($account->last_login_at)) : 'Chưa đăng nhập'; ?></td>
                <td class="px-4 py-3">
                  <div class="flex justify-end gap-1">
                    <?php if ((int) $account->is_admin !== 1 && $canUpdate): ?>
                      <button type="button" class="btn-permissions rounded-lg p-2 text-violet-600 hover:bg-violet-50" title="Phân quyền menu" data-user-id="<?php echo (int) $account->id; ?>" data-name="<?php echo htmlspecialchars($account->full_name, ENT_QUOTES, 'UTF-8'); ?>"><i class="ph-bold ph-key"></i></button>
                    <?php endif; ?>
                    <?php if ($canUpdate): ?>
                      <button type="button" class="btn-edit rounded-lg p-2 text-blue-600 hover:bg-blue-50" title="Sửa tài khoản"
                        data-account='<?php echo htmlspecialchars(json_encode(array('id'=>(int)$account->id,'username'=>$account->username,'full_name'=>$account->full_name,'email'=>$account->email,'phone'=>$account->phone,'is_active'=>(int)$account->site_active,'is_admin'=>(int)$account->is_admin), JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>'><i class="ph-bold ph-pencil-simple"></i></button>
                    <?php endif; ?>
                    <?php if ($canUpdate && ((int) $account->is_admin !== 1 || !empty($_SESSION['user']['is_admin']))): ?>
                      <button type="button" class="btn-reset-password rounded-lg p-2 text-amber-600 hover:bg-amber-50" title="Đặt lại mật khẩu" data-user-id="<?php echo (int) $account->id; ?>" data-name="<?php echo htmlspecialchars($account->full_name, ENT_QUOTES, 'UTF-8'); ?>"><i class="ph-bold ph-lock-key-open"></i></button>
                    <?php endif; ?>
                    <?php if ($canDelete && (int) $account->is_admin !== 1 && (int) $account->id !== $currentUserId): ?>
                      <button type="button" class="btn-delete rounded-lg p-2 text-rose-600 hover:bg-rose-50" title="Xóa khỏi site" data-id="<?php echo (int) $account->id; ?>" data-name="<?php echo htmlspecialchars($account->full_name, ENT_QUOTES, 'UTF-8'); ?>"><i class="ph-bold ph-trash"></i></button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <div id="account-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
    <div class="w-full max-w-2xl rounded-2xl bg-white shadow-2xl">
      <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4"><h3 id="account-modal-title" class="font-bold text-slate-900">Thêm tài khoản</h3><button type="button" class="btn-close-modal rounded-lg p-2 hover:bg-slate-100"><i class="ph-bold ph-x"></i></button></div>
      <form id="account-form" class="space-y-4 p-5" novalidate>
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="site_code" value="<?php echo htmlspecialchars($site, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="id" id="account-id" value="0">
        <?php if ($site === 'admin'): ?><input type="hidden" name="is_admin_present" value="1"><?php endif; ?>
        <div class="grid gap-4 sm:grid-cols-2">
          <label class="space-y-1"><span class="text-xs font-semibold text-slate-600">Tên đăng nhập <b class="text-rose-500">*</b></span><input name="username" id="account-username" required minlength="3" maxlength="120" pattern="[A-Za-z0-9._@-]+" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 outline-none focus:border-blue-500"></label>
          <label class="space-y-1"><span class="text-xs font-semibold text-slate-600">Họ và tên <b class="text-rose-500">*</b></span><input name="full_name" id="account-full-name" required maxlength="255" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 outline-none focus:border-blue-500"></label>
          <label class="space-y-1"><span class="text-xs font-semibold text-slate-600">Email</span><input type="email" name="email" id="account-email" maxlength="255" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 outline-none focus:border-blue-500"></label>
          <label class="space-y-1"><span class="text-xs font-semibold text-slate-600">Số điện thoại</span><input name="phone" id="account-phone" maxlength="30" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 outline-none focus:border-blue-500"></label>
          <label class="space-y-1 sm:col-span-2"><span class="text-xs font-semibold text-slate-600">Mật khẩu <b id="password-required" class="text-rose-500">*</b></span><input type="password" name="password" id="account-password" minlength="8" maxlength="255" autocomplete="new-password" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 outline-none focus:border-blue-500"><small class="text-xs text-slate-400">Khi sửa, để trống nếu không muốn đổi mật khẩu.</small></label>
        </div>
        <div class="flex flex-wrap gap-4 rounded-xl bg-slate-50 p-3">
          <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" id="account-active" value="1" class="h-4 w-4 rounded" checked> Tài khoản hoạt động</label>
          <?php if ($site === 'admin'): ?><label class="flex items-center gap-2 text-sm font-semibold text-violet-700"><input type="checkbox" name="is_admin" id="account-super-admin" value="1" class="h-4 w-4 rounded"> Super Admin — toàn quyền 3 site</label><?php endif; ?>
        </div>
        <div class="flex justify-end gap-2 border-t border-slate-100 pt-4"><button type="button" class="btn-close-modal rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold">Hủy</button><button type="submit" class="rounded-xl bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700">Lưu tài khoản</button></div>
      </form>
    </div>
  </div>

  <div id="password-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl">
      <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
        <div><h3 class="font-bold text-slate-900">Đặt lại mật khẩu</h3><p id="password-user-name" class="text-xs text-slate-500"></p></div>
        <button type="button" class="btn-close-password rounded-lg p-2 hover:bg-slate-100"><i class="ph-bold ph-x"></i></button>
      </div>
      <form id="password-form" class="space-y-4 p-5" novalidate>
        <input type="hidden" name="user_id" id="password-user-id">
        <label class="block space-y-1"><span class="text-xs font-semibold text-slate-600">Mật khẩu mới <b class="text-rose-500">*</b></span><input type="password" name="password" id="reset-password" required minlength="8" maxlength="255" autocomplete="new-password" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 outline-none focus:border-amber-500"><small class="text-xs text-slate-400">Từ 8 đến 255 ký tự.</small></label>
        <label class="block space-y-1"><span class="text-xs font-semibold text-slate-600">Nhập lại mật khẩu <b class="text-rose-500">*</b></span><input type="password" name="password_confirmation" id="reset-password-confirmation" required minlength="8" maxlength="255" autocomplete="new-password" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 outline-none focus:border-amber-500"></label>
        <div class="flex justify-end gap-2 border-t border-slate-100 pt-4"><button type="button" class="btn-close-password rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold">Hủy</button><button type="submit" class="rounded-xl bg-amber-500 px-5 py-2 text-sm font-semibold text-white hover:bg-amber-600">Đặt lại mật khẩu</button></div>
      </form>
    </div>
  </div>

  <div id="permission-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
    <div class="flex max-h-[90vh] w-full max-w-6xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
      <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4"><div><h3 class="font-bold text-slate-900">Phân quyền menu</h3><p id="permission-user-name" class="text-xs text-slate-500"></p></div><button type="button" class="btn-close-permission rounded-lg p-2 hover:bg-slate-100"><i class="ph-bold ph-x"></i></button></div>
      <form id="permission-form" class="flex min-h-0 flex-1 flex-col">
        <input type="hidden" name="user_id" id="permission-user-id">
        <div class="overflow-auto p-4">
          <table class="w-full min-w-[850px] text-sm">
            <thead class="sticky top-0 bg-slate-100 text-xs uppercase text-slate-500"><tr><th class="px-3 py-3 text-left">Menu</th><?php foreach (array('view'=>'Xem','create'=>'Thêm','update'=>'Sửa','delete'=>'Xóa','import'=>'Import','export'=>'Export') as $key=>$label): ?><th class="px-3 py-3 text-center"><?php echo $label; ?></th><?php endforeach; ?></tr></thead>
            <tbody class="divide-y divide-slate-100">
              <?php foreach ($menus as $menu): ?>
                <tr class="hover:bg-slate-50" data-menu-id="<?php echo (int) $menu->id; ?>">
                  <td class="px-3 py-3 <?php echo $menu->parent_id ? 'pl-8' : 'font-semibold text-slate-900'; ?>"><?php echo $menu->parent_id ? '↳ ' : ''; ?><?php echo htmlspecialchars($menu->menu_name, ENT_QUOTES, 'UTF-8'); ?><small class="ml-2 text-slate-400"><?php echo htmlspecialchars($menu->menu_code, ENT_QUOTES, 'UTF-8'); ?></small></td>
                  <?php foreach (array('view','create','update','delete','import','export') as $permissionKey): ?><td class="px-3 py-3 text-center"><input type="checkbox" class="permission-check h-4 w-4 rounded text-blue-600" data-permission="<?php echo $permissionKey; ?>"></td><?php endforeach; ?>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="flex justify-end gap-2 border-t border-slate-100 p-4"><button type="button" class="btn-close-permission rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold">Hủy</button><button type="submit" class="rounded-xl bg-violet-600 px-5 py-2 text-sm font-semibold text-white hover:bg-violet-700">Lưu phân quyền</button></div>
      </form>
    </div>
  </div>

  <script>
    const appUrl = <?php echo json_encode(XC_URL); ?>;
    const siteCode = <?php echo json_encode($site); ?>;
    const csrf = <?php echo json_encode($csrf); ?>;
    const permissionData = <?php echo json_encode($permissions, JSON_UNESCAPED_UNICODE); ?>;
    const toast = Swal.mixin({ toast: true, position: 'bottom-end', showConfirmButton: false, timer: 3200, timerProgressBar: true });

    function openAccountModal(account = null) {
      $('#account-form')[0].reset();
      $('#account-id').val(account ? account.id : 0);
      $('#account-username').val(account ? account.username : '');
      $('#account-full-name').val(account ? account.full_name : '');
      $('#account-email').val(account ? account.email || '' : '');
      $('#account-phone').val(account ? account.phone || '' : '');
      $('#account-active').prop('checked', !account || Number(account.is_active) === 1);
      $('#account-super-admin').prop('checked', account && Number(account.is_admin) === 1);
      $('#account-password').prop('required', !account).val('');
      $('#password-required').toggle(!account);
      $('#account-modal-title').text(account ? 'Sửa tài khoản' : 'Thêm tài khoản trang <?php echo htmlspecialchars($siteNames[$site], ENT_QUOTES, 'UTF-8'); ?>');
      $('#account-modal').removeClass('hidden').addClass('flex');
    }
    function closeAccountModal() { $('#account-modal').addClass('hidden').removeClass('flex'); }
    $('#btn-add-account').on('click', () => openAccountModal());
    $('.btn-edit').on('click', function () { openAccountModal($(this).data('account')); });
    $('.btn-close-modal').on('click', closeAccountModal);

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
      const button = $(this).find('button[type="submit"]').prop('disabled', true).text('Đang đặt lại...');
      $.ajax({ url: appUrl + '/api/resetAccountPassword', method: 'POST', dataType: 'json', data: { csrf, site_code: siteCode, user_id: $('#password-user-id').val(), password: $('#reset-password').val(), password_confirmation: $('#reset-password-confirmation').val() } })
        .done(response => { toast.fire({ icon: 'success', title: response.message }); $('#password-modal').addClass('hidden').removeClass('flex'); $('#password-form')[0].reset(); })
        .fail(xhr => toast.fire({ icon: 'error', title: (xhr.responseJSON || {}).message || 'Không thể đặt lại mật khẩu.' }))
        .always(() => button.prop('disabled', false).text('Đặt lại mật khẩu'));
    });

    $('#account-form').on('submit', function (event) {
      event.preventDefault();
      if (!this.checkValidity()) { this.reportValidity(); return; }
      const button = $(this).find('button[type="submit"]').prop('disabled', true).text('Đang lưu...');
      $.ajax({ url: appUrl + '/api/saveAccount', method: 'POST', dataType: 'json', data: $(this).serialize() })
        .done(response => { toast.fire({ icon: 'success', title: response.message }); setTimeout(() => window.location.reload(), 500); })
        .fail(xhr => toast.fire({ icon: 'error', title: (xhr.responseJSON || {}).message || 'Không thể lưu tài khoản.' }))
        .always(() => button.prop('disabled', false).text('Lưu tài khoản'));
    });

    $('.btn-delete').on('click', function () {
      const id = $(this).data('id'); const name = $(this).data('name');
      Swal.fire({ title: 'Xóa quyền truy cập?', text: name + ' sẽ không còn truy cập trang này.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Xóa quyền', cancelButtonText: 'Hủy', confirmButtonColor: '#e11d48' })
        .then(result => { if (!result.isConfirmed) return; $.ajax({ url: appUrl + '/api/deleteAccount', method: 'POST', dataType: 'json', data: { csrf, site_code: siteCode, id } }).done(response => { toast.fire({ icon: 'success', title: response.message }); setTimeout(() => window.location.reload(), 500); }).fail(xhr => toast.fire({ icon: 'error', title: (xhr.responseJSON || {}).message || 'Không thể xóa quyền truy cập.' })); });
    });

    $('.btn-permissions').on('click', function () {
      const userId = String($(this).data('user-id'));
      $('#permission-user-id').val(userId); $('#permission-user-name').text($(this).data('name') + ' · ' + siteCode);
      $('.permission-check').prop('checked', false);
      const userPermissions = permissionData[userId] || {};
      $('#permission-form tr[data-menu-id]').each(function () {
        const menuId = String($(this).data('menu-id')); const values = userPermissions[menuId] || {};
        $(this).find('.permission-check').each(function () { $(this).prop('checked', Number(values[$(this).data('permission')] || 0) === 1); });
      });
      $('#permission-modal').removeClass('hidden').addClass('flex');
    });
    $('.btn-close-permission').on('click', () => $('#permission-modal').addClass('hidden').removeClass('flex'));
    $('#permission-form').on('submit', function (event) {
      event.preventDefault(); const permissions = {};
      $(this).find('tr[data-menu-id]').each(function () { const menuId = String($(this).data('menu-id')); permissions[menuId] = {}; $(this).find('.permission-check').each(function () { permissions[menuId][$(this).data('permission')] = $(this).is(':checked') ? 1 : 0; }); });
      const button = $(this).find('button[type="submit"]').prop('disabled', true).text('Đang lưu...');
      $.ajax({ url: appUrl + '/api/saveAccountPermissions', method: 'POST', dataType: 'json', data: { csrf, site_code: siteCode, user_id: $('#permission-user-id').val(), permissions: JSON.stringify(permissions) } })
        .done(response => { toast.fire({ icon: 'success', title: response.message }); $('#permission-modal').addClass('hidden').removeClass('flex'); setTimeout(() => window.location.reload(), 500); })
        .fail(xhr => toast.fire({ icon: 'error', title: (xhr.responseJSON || {}).message || 'Không thể cập nhật phân quyền.' }))
        .always(() => button.prop('disabled', false).text('Lưu phân quyền'));
    });

    $('#account-search').on('input', function () { const value = $(this).val().toLowerCase().trim(); $('.account-row').each(function () { $(this).toggle($(this).data('search').includes(value)); }); });
  </script>
</body>
</html>
