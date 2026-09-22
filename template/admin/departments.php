<?php require_once('header.php'); ?>

    <!-- Main Content Container -->
    <main class="flex-1 overflow-y-auto p-4 sm:p-6 no-scrollbar space-y-5">

      <!-- Breadcrumb & Tiêu đề trang -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-brand-600 mb-1">
            <span>CARE IOC</span>
            <span class="text-slate-300">/</span>
            <span>Quản Lý Danh Mục</span>
            <span class="text-slate-300">/</span>
            <span class="text-slate-700 font-bold">Khoa Phòng</span>
          </div>
          <h2 class="text-xl sm:text-2xl font-bold text-slate-800 tracking-tight">Quản Lý Danh Mục Khoa Phòng</h2>
        </div>

        <?php if ($canCreate): ?>
          <button type="button" id="btn-add-department" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-brand-500/20 transition-all shrink-0 active:scale-95">
            <i class="ph-bold ph-plus-circle text-base"></i> Thêm Khoa Phòng
          </button>
        <?php endif; ?>
      </div>

      <!-- Thẻ thống kê tổng quan -->
      <section class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Tổng khoa phòng -->
        <div class="bg-white p-4 rounded-2xl border border-blue-100 shadow-sm flex items-center justify-between">
          <div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Khoa / Phòng</span>
            <h4 class="mt-1 text-xl sm:text-2xl font-black text-slate-800"><?php echo number_format($counts['total']); ?></h4>
            <span class="text-[11px] text-slate-400 font-medium">Đang quản lý</span>
          </div>
          <div class="w-11 h-11 rounded-xl bg-blue-50 text-brand-600 flex items-center justify-center text-xl shrink-0">
            <i class="ph-bold ph-buildings"></i>
          </div>
        </div>

        <!-- Tổng giường bệnh -->
        <div class="bg-white p-4 rounded-2xl border border-blue-100 shadow-sm flex items-center justify-between">
          <div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Giường Thực Kê</span>
            <h4 class="mt-1 text-xl sm:text-2xl font-black text-blue-600"><?php echo number_format($counts['total_beds']); ?></h4>
            <span class="text-[11px] text-slate-400 font-medium">Toàn bệnh viện</span>
          </div>
          <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
            <i class="ph-bold ph-bed"></i>
          </div>
        </div>

        <!-- Đang hoạt động -->
        <div class="bg-white p-4 rounded-2xl border border-emerald-100 shadow-sm flex items-center justify-between">
          <div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Đang Sử Dụng</span>
            <h4 class="mt-1 text-xl sm:text-2xl font-black text-emerald-700"><?php echo number_format($counts['active']); ?></h4>
            <span class="text-[11px] text-emerald-500 font-medium">Hoạt động bình thường</span>
          </div>
          <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
            <i class="ph-bold ph-check-circle"></i>
          </div>
        </div>

        <!-- Ngừng sử dụng (Xóa mềm) -->
        <div class="bg-white p-4 rounded-2xl border border-rose-100 shadow-sm flex items-center justify-between">
          <div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-rose-500">Ngừng Sử Dụng</span>
            <h4 class="mt-1 text-xl sm:text-2xl font-black text-rose-600"><?php echo number_format($counts['deleted']); ?></h4>
            <span class="text-[11px] text-rose-400 font-medium">Đã xóa / Tạm khóa</span>
          </div>
          <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center text-xl shrink-0">
            <i class="ph-bold ph-archive"></i>
          </div>
        </div>
      </section>

      <!-- Danh sách khoa phòng -->
      <section class="bg-white rounded-2xl border border-blue-100 shadow-sm overflow-hidden">
        
        <!-- Thanh công cụ tìm kiếm và lọc -->
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 bg-blue-50/20">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-blue-100 text-brand-700 flex items-center justify-center text-base font-bold shrink-0">
              <i class="ph-bold ph-list-dashes"></i>
            </div>
            <div>
              <h3 class="font-bold text-slate-800 text-sm">Danh Sách Khoa Phòng</h3>
            </div>
          </div>

          <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
            <!-- Bộ lọc trạng thái -->
            <div class="inline-flex rounded-xl bg-slate-100 p-1 text-xs font-semibold text-slate-600">
              <button type="button" class="filter-status-btn px-3 py-1.5 rounded-lg transition-all bg-white text-brand-700 shadow-xs" data-status="all">Tất cả</button>
              <button type="button" class="filter-status-btn px-3 py-1.5 rounded-lg transition-all hover:text-slate-800" data-status="1">Đang dùng</button>
              <button type="button" class="filter-status-btn px-3 py-1.5 rounded-lg transition-all hover:text-slate-800" data-status="0">Tạm ngưng</button>
            </div>

            <!-- Ô tìm kiếm -->
            <div class="relative w-full sm:w-64">
              <input id="department-search" type="search" placeholder="Tìm theo mã hoặc tên khoa..." class="w-full pl-9 pr-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-brand-500 text-slate-700 shadow-xs transition-all">
              <i class="ph ph-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-sm"></i>
            </div>
          </div>
        </div>

        <!-- Bảng dữ liệu -->
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs border-collapse">
            <thead>
              <tr class="bg-blue-50/50 text-slate-500 border-b border-blue-100 font-semibold uppercase text-[11px] tracking-wider">
                <th class="py-3.5 px-4 w-14 text-center">STT</th>
                <th class="py-3.5 px-4">Mã Khoa / Phòng</th>
                <th class="py-3.5 px-4">Tên Khoa / Phòng</th>
                <th class="py-3.5 px-4 text-center">Giường Thực Kê</th>
                <th class="py-3.5 px-4 text-center">Trạng Thái</th>
                <th class="py-3.5 px-4 text-center">Cập Nhật Gần Nhất</th>
                <th class="py-3.5 px-4 text-center w-36">Thao Tác</th>
              </tr>
            </thead>
            <tbody id="department-table" class="divide-y divide-slate-100 font-medium text-slate-700">
              <?php if (empty($departments)): ?>
                <tr id="empty-row">
                  <td colspan="7" class="py-12 px-4 text-center text-slate-400">
                    <i class="ph ph-buildings text-3xl mb-2 text-slate-300 block"></i>
                    <p class="text-xs">Chưa có khoa phòng nào trong hệ thống.</p>
                  </td>
                </tr>
              <?php else: ?>
                <?php $stt = 0; ?>
                <?php foreach ($departments as $dept): ?>
                  <?php 
                    $stt++;
                    $status = (int) $dept->department_status;
                    $isDeleted = ($status === 99);
                  ?>
                  <tr class="department-row hover:bg-blue-50/30 transition <?php echo $isDeleted ? 'bg-rose-50/20' : ''; ?>"
                      data-id="<?php echo (int) $dept->id; ?>"
                      data-code="<?php echo htmlspecialchars($dept->department_code, ENT_QUOTES, 'UTF-8'); ?>"
                      data-name="<?php echo htmlspecialchars($dept->department_name, ENT_QUOTES, 'UTF-8'); ?>"
                      data-bed="<?php echo (int) $dept->department_bed; ?>"
                      data-status="<?php echo $status; ?>"
                      data-search="<?php echo htmlspecialchars(strtolower($dept->department_code . ' ' . $dept->department_name), ENT_QUOTES, 'UTF-8'); ?>">
                    <td class="py-3.5 px-4 text-center text-slate-400 font-mono"><?php echo $stt; ?></td>
                    <td class="py-3.5 px-4">
                      <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold font-mono bg-slate-100 text-slate-800 border border-slate-200">
                        <?php echo htmlspecialchars($dept->department_code, ENT_QUOTES, 'UTF-8'); ?>
                      </span>
                    </td>
                    <td class="py-3.5 px-4">
                      <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-brand-600 flex items-center justify-center text-sm shrink-0">
                          <i class="ph-bold ph-hospital"></i>
                        </div>
                        <div>
                          <p class="font-bold text-slate-800 <?php echo $isDeleted ? 'line-through text-slate-400' : ''; ?>">
                            <?php echo htmlspecialchars($dept->department_name, ENT_QUOTES, 'UTF-8'); ?>
                          </p>
                          <span class="text-[10px] text-slate-400">ID: #<?php echo (int) $dept->id; ?></span>
                        </div>
                      </div>
                    </td>
                    <td class="py-3.5 px-4 text-center">
                      <span class="inline-flex items-center gap-1 font-bold text-xs px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                        <i class="ph-bold ph-bed text-blue-500"></i>
                        <?php echo number_format((int) $dept->department_bed); ?> giường
                      </span>
                    </td>
                    <td class="py-3.5 px-4 text-center">
                      <?php if ($status === 1): ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                          Đang sử dụng
                        </span>
                      <?php elseif ($status === 0): ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                          <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                          Tạm ngưng
                        </span>
                      <?php elseif ($status === 99): ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                          <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                          Ngừng sử dụng (99)
                        </span>
                      <?php else: ?>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-600">
                          #<?php echo $status; ?>
                        </span>
                      <?php endif; ?>
                    </td>
                    <td class="py-3.5 px-4 text-center text-slate-500">
                      <?php echo $dept->department_updated_at ? htmlspecialchars(date('d/m/Y H:i', strtotime($dept->department_updated_at)), ENT_QUOTES, 'UTF-8') : '—'; ?>
                    </td>
                    <td class="py-3.5 px-4 text-center">
                      <div class="inline-flex items-center justify-center gap-1.5">
                        <?php if ($canUpdate): ?>
                          <button type="button"
                                  class="btn-edit p-1.5 rounded-lg text-slate-500 hover:text-brand-600 hover:bg-blue-50 transition"
                                  title="Chỉnh sửa khoa phòng">
                            <i class="ph-bold ph-pencil-simple text-base"></i>
                          </button>
                        <?php endif; ?>

                        <?php if ($canDelete && !$isDeleted): ?>
                          <button type="button"
                                  class="btn-delete p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition"
                                  title="Xóa khoa phòng (status = 99)">
                            <i class="ph-bold ph-trash text-base"></i>
                          </button>
                        <?php elseif ($isDeleted && $canUpdate): ?>
                          <button type="button"
                                  class="btn-restore p-1.5 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 transition"
                                  title="Khôi phục trạng thái đang sử dụng">
                            <i class="ph-bold ph-arrow-counter-clockwise text-base"></i>
                          </button>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="p-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400 bg-slate-50/50">
          <span>Hệ thống CARE IOC · Bệnh viện Đa khoa Khu vực Đăk Tô</span>
          <span>Tổng số: <strong class="text-slate-600 font-bold"><?php echo count($departments); ?></strong> bản ghi</span>
        </div>
      </section>
    </main>

    <!-- ==================== MODAL THÊM / SỬA KHOA PHÒNG ==================== -->
    <div id="department-modal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
      <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl border border-blue-100 overflow-hidden transform transition-all">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-blue-50/50">
          <div class="flex items-center gap-2.5 text-brand-700">
            <div class="w-8 h-8 rounded-xl bg-blue-100 text-brand-700 flex items-center justify-center text-lg font-bold">
              <i class="ph-bold ph-buildings" id="modal-icon"></i>
            </div>
            <div>
              <h3 id="modal-title" class="font-bold text-base text-slate-800">Thêm Khoa Phòng Mới</h3>
              <p class="text-[11px] text-slate-400">Nhập đầy đủ thông tin mã khoa, tên khoa và chỉ tiêu giường bệnh.</p>
            </div>
          </div>
          <button type="button" onclick="closeDepartmentModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-white transition">
            <i class="ph ph-x text-lg"></i>
          </button>
        </div>

        <!-- Modal Form -->
        <form id="department-form" class="p-6 space-y-4">
          <input type="hidden" id="department-id" name="id" value="0">

          <!-- Mã khoa phòng -->
          <div>
            <label for="department-code" class="block text-xs font-semibold text-slate-700 mb-1">
              Mã Khoa / Phòng <span class="text-rose-500">*</span>
            </label>
            <input type="text"
                   id="department-code"
                   name="department_code"
                   required
                   maxlength="20"
                   class="w-full px-3.5 py-2.5 text-xs font-mono uppercase bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-500 focus:bg-white transition text-slate-700"
                   placeholder="Ví dụ: HSCC, NGOAI, SAN, NOI...">
            <p class="text-[10px] text-slate-400 mt-1">Tối đa 20 ký tự, không chứa ký tự đặc biệt, duy nhất trên toàn hệ thống.</p>
          </div>

          <!-- Tên khoa phòng -->
          <div>
            <label for="department-name" class="block text-xs font-semibold text-slate-700 mb-1">
              Tên Khoa / Phòng <span class="text-rose-500">*</span>
            </label>
            <input type="text"
                   id="department-name"
                   name="department_name"
                   required
                   maxlength="150"
                   class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-500 focus:bg-white transition text-slate-700"
                   placeholder="Ví dụ: Khoa Hồi sức cấp cứu, Khoa Ngoại tổng hợp...">
          </div>

          <!-- Số giường thực kê & Trạng thái -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label for="department-bed" class="block text-xs font-semibold text-slate-700 mb-1">
                Số Giường Thực Kê <span class="text-rose-500">*</span>
              </label>
              <div class="relative">
                <input type="number"
                       id="department-bed"
                       name="department_bed"
                       min="0"
                       step="1"
                       value="0"
                       required
                       class="w-full pl-3.5 pr-8 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-500 focus:bg-white transition text-slate-700">
                <span class="absolute right-3 top-2.5 text-slate-400 text-xs font-medium">giường</span>
              </div>
            </div>

            <div>
              <label for="department-status" class="block text-xs font-semibold text-slate-700 mb-1">
                Trạng Thái
              </label>
              <select id="department-status"
                      name="department_status"
                      class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-500 focus:bg-white transition text-slate-700">
                <option value="1">1 - Đang sử dụng</option>
                <option value="0">0 - Tạm ngưng</option>
                <option value="99">99 - Ngừng sử dụng (Xóa)</option>
              </select>
            </div>
          </div>

          <!-- Buttons -->
          <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
            <button type="button" onclick="closeDepartmentModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
              Hủy
            </button>
            <button type="submit" id="btn-save-department" class="inline-flex items-center gap-2 px-5 py-2 text-xs font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-xl shadow-md shadow-brand-500/20 transition active:scale-95">
              <i class="ph-bold ph-floppy-disk"></i>
              <span>Lưu Thông Tin</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Script xử lý AJAX và Toast SweetAlert -->
    <script>
      const csrfToken = <?php echo json_encode($csrf); ?>;
      const appUrl = <?php echo json_encode(XC_URL); ?>;

      // Cấu hình Toast SweetAlert góc dưới bên phải theo đúng quy ước AGENTS.md
      const toast = Swal.mixin({
        toast: true,
        position: 'bottom-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
      });

      function openDepartmentModal(isEdit = false, data = {}) {
        const modal = document.getElementById('department-modal');
        const title = document.getElementById('modal-title');
        const icon = document.getElementById('modal-icon');
        const form = document.getElementById('department-form');

        form.reset();

        if (isEdit) {
          title.textContent = 'Cập Nhật Khoa Phòng';
          icon.className = 'ph-bold ph-pencil-simple';
          document.getElementById('department-id').value = data.id || 0;
          document.getElementById('department-code').value = data.code || '';
          document.getElementById('department-name').value = data.name || '';
          document.getElementById('department-bed').value = data.bed !== undefined ? data.bed : 0;
          document.getElementById('department-status').value = data.status !== undefined ? data.status : 1;
        } else {
          title.textContent = 'Thêm Khoa Phòng Mới';
          icon.className = 'ph-bold ph-plus-circle';
          document.getElementById('department-id').value = 0;
          document.getElementById('department-code').value = '';
          document.getElementById('department-name').value = '';
          document.getElementById('department-bed').value = 0;
          document.getElementById('department-status').value = 1;
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => document.getElementById(isEdit ? 'department-name' : 'department-code').focus(), 100);
      }

      function closeDepartmentModal() {
        const modal = document.getElementById('department-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }

      // Nút Thêm mới
      $('#btn-add-department').on('click', function() {
        openDepartmentModal(false);
      });

      // Nút Sửa
      $(document).on('click', '.btn-edit', function() {
        const row = $(this).closest('tr');
        const data = {
          id: row.data('id'),
          code: row.data('code'),
          name: row.data('name'),
          bed: row.data('bed'),
          status: row.data('status')
        };
        openDepartmentModal(true, data);
      });

      // Nút Khôi phục trạng thái
      $(document).on('click', '.btn-restore', function() {
        const row = $(this).closest('tr');
        const id = row.data('id');
        const name = row.data('name');
        const code = row.data('code');
        const bed = row.data('bed');

        Swal.fire({
          title: 'Khôi phục khoa phòng?',
          text: `Khôi phục trạng thái hoạt động cho "${name}" (${code})`,
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Đồng ý',
          cancelButtonText: 'Hủy',
          confirmButtonColor: '#2563eb'
        }).then((result) => {
          if (!result.isConfirmed) return;
          $.ajax({
            url: appUrl + '/api/saveDepartment',
            method: 'POST',
            dataType: 'json',
            data: {
              csrf: csrfToken,
              id: id,
              department_code: code,
              department_name: name,
              department_bed: bed,
              department_status: 1
            }
          })
          .done(function(response) {
            toast.fire({ icon: 'success', title: response.message || 'Khôi phục thành công.' });
            setTimeout(() => window.location.reload(), 600);
          })
          .fail(function(xhr) {
            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Có lỗi xảy ra khi khôi phục.';
            toast.fire({ icon: 'error', title: msg });
          });
        });
      });

      // Submit Form Thêm / Sửa
      $('#department-form').on('submit', function(e) {
        e.preventDefault();

        const code = $('#department-code').val().trim();
        const name = $('#department-name').val().trim();
        const bed = parseInt($('#department-bed').val(), 10);
        const status = parseInt($('#department-status').val(), 10);
        const id = parseInt($('#department-id').val(), 10);

        if (!code) {
          toast.fire({ icon: 'warning', title: 'Vui lòng nhập mã khoa/phòng.' });
          $('#department-code').focus();
          return;
        }
        if (!name) {
          toast.fire({ icon: 'warning', title: 'Vui lòng nhập tên khoa/phòng.' });
          $('#department-name').focus();
          return;
        }
        if (isNaN(bed) || bed < 0) {
          toast.fire({ icon: 'warning', title: 'Số giường thực kê phải là số không âm.' });
          $('#department-bed').focus();
          return;
        }

        const btnSave = $('#btn-save-department');
        btnSave.prop('disabled', true).addClass('opacity-70 cursor-not-allowed');

        $.ajax({
          url: appUrl + '/api/saveDepartment',
          method: 'POST',
          dataType: 'json',
          data: {
            csrf: csrfToken,
            id: id,
            department_code: code,
            department_name: name,
            department_bed: bed,
            department_status: status
          }
        })
        .done(function(response) {
          toast.fire({ icon: 'success', title: response.message || 'Lưu thông tin thành công.' });
          closeDepartmentModal();
          setTimeout(() => window.location.reload(), 600);
        })
        .fail(function(xhr) {
          const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Lỗi máy chủ khi lưu khoa phòng.';
          toast.fire({ icon: 'error', title: msg });
        })
        .always(function() {
          btnSave.prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
        });
      });

      // Nút Xóa (Xóa thì status = 99)
      $(document).on('click', '.btn-delete', function() {
        const row = $(this).closest('tr');
        const id = row.data('id');
        const name = row.data('name');
        const code = row.data('code');

        Swal.fire({
          title: 'Xác nhận xóa khoa phòng?',
          html: `<p class="text-xs text-slate-500">Khoa <strong>${name}</strong> (${code}) sẽ được chuyển sang trạng thái <strong>Ngừng sử dụng (status = 99)</strong>.</p>`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Đồng ý xóa',
          cancelButtonText: 'Hủy',
          confirmButtonColor: '#e11d48'
        }).then((result) => {
          if (!result.isConfirmed) return;

          $.ajax({
            url: appUrl + '/api/deleteDepartment',
            method: 'POST',
            dataType: 'json',
            data: {
              csrf: csrfToken,
              id: id
            }
          })
          .done(function(response) {
            toast.fire({ icon: 'success', title: response.message || 'Đã xóa khoa phòng thành công.' });
            setTimeout(() => window.location.reload(), 600);
          })
          .fail(function(xhr) {
            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Có lỗi xảy ra khi xóa khoa phòng.';
            toast.fire({ icon: 'error', title: msg });
          });
        });
      });

      // Tìm kiếm và lọc trạng thái
      let currentFilterStatus = 'all';

      function applyFilters() {
        const searchValue = $('#department-search').val().toLowerCase().trim();
        let visibleCount = 0;

        $('.department-row').each(function() {
          const row = $(this);
          const searchMatch = !searchValue || row.data('search').includes(searchValue);
          const rowStatus = String(row.data('status'));
          let statusMatch = true;

          if (currentFilterStatus !== 'all') {
            statusMatch = (rowStatus === currentFilterStatus);
          }

          if (searchMatch && statusMatch) {
            row.show();
            visibleCount++;
          } else {
            row.hide();
          }
        });

        if (visibleCount === 0) {
          if ($('#filter-empty-row').length === 0) {
            $('#department-table').append('<tr id="filter-empty-row"><td colspan="7" class="py-10 text-center text-slate-400 text-xs">Không tìm thấy khoa/phòng phù hợp với tiêu chí tìm kiếm.</td></tr>');
          }
        } else {
          $('#filter-empty-row').remove();
        }
      }

      $('#department-search').on('input', applyFilters);

      $('.filter-status-btn').on('click', function() {
        $('.filter-status-btn').removeClass('bg-white text-brand-700 shadow-xs').addClass('hover:text-slate-800');
        $(this).addClass('bg-white text-brand-700 shadow-xs').removeClass('hover:text-slate-800');
        currentFilterStatus = $(this).data('status');
        applyFilters();
      });
    </script>

<?php require_once('footer.php'); ?>
