<?php require_once('header.php'); ?>

    <!-- Main Content Container -->
    <main class="flex-1 overflow-y-auto p-3 sm:p-5 no-scrollbar bg-[#f1f5f9] flex flex-col space-y-3">

      <!-- Tiêu đề trang ở chính giữa theo hình mẫu -->
      <div class="text-center py-1">
        <h2 class="text-xl sm:text-2xl font-bold text-[#003893] tracking-wide uppercase">Thiết lập tham số</h2>
      </div>

      <!-- Layout 2 cột như hình mẫu -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-3.5 flex-1 min-h-[580px]">

        <!-- ==================== KHUNG TRÁI: DANH SÁCH CẤU HÌNH ==================== -->
        <section class="lg:col-span-7 bg-white rounded-lg border border-[#0b5394] shadow-xs flex flex-col overflow-hidden">
          
          <!-- Thanh tiêu đề bảng xanh đậm -->
          <div class="bg-[#0b5394] text-white px-3 py-2 flex items-center justify-between text-xs font-bold shrink-0">
            <div class="flex items-center gap-2">
              <i class="ph-bold ph-table text-sm"></i>
              <span>Danh sách cấu hình</span>
            </div>
            <button type="button" onclick="toggleGroupCollapse()" class="text-white hover:text-blue-200 transition" title="Thu gọn / Mở rộng nhóm">
              <i class="ph-bold ph-caret-up text-xs" id="group-collapse-icon"></i>
            </button>
          </div>

          <!-- Bộ 3 ô lọc tìm kiếm theo từng cột có nút x xóa nhanh -->
          <div class="bg-slate-100 border-b border-slate-200 px-2 py-1.5 grid grid-cols-12 gap-1.5 shrink-0 text-xs">
            <!-- Lọc Mã cấu hình -->
            <div class="col-span-4 relative">
              <input type="text"
                     id="filter-key"
                     placeholder="Mã cấu hình..."
                     class="w-full text-xs pl-2 pr-5 py-1 bg-white border border-slate-300 rounded focus:outline-none focus:border-blue-500 font-mono text-slate-700">
              <button type="button" class="btn-clear-filter absolute right-1.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 font-bold text-xs" data-target="#filter-key">✕</button>
            </div>

            <!-- Lọc Giá trị TL -->
            <div class="col-span-3 relative">
              <input type="text"
                     id="filter-value"
                     placeholder="Giá trị TL..."
                     class="w-full text-xs pl-2 pr-5 py-1 bg-white border border-slate-300 rounded focus:outline-none focus:border-blue-500 text-slate-700">
              <button type="button" class="btn-clear-filter absolute right-1.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 font-bold text-xs" data-target="#filter-value">✕</button>
            </div>

            <!-- Lọc Tên cấu hình -->
            <div class="col-span-5 relative">
              <input type="text"
                     id="filter-name"
                     placeholder="Tên cấu hình..."
                     class="w-full text-xs pl-2 pr-5 py-1 bg-white border border-slate-300 rounded focus:outline-none focus:border-blue-500 text-slate-700">
              <button type="button" class="btn-clear-filter absolute right-1.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 font-bold text-xs" data-target="#filter-name">✕</button>
            </div>
          </div>

          <!-- Bảng danh sách cấu hình -->
          <div class="flex-1 overflow-y-auto overflow-x-auto no-scrollbar max-h-[580px]">
            <table class="w-full text-left text-xs border-collapse">
              <thead class="sticky top-0 z-10 bg-slate-100 border-b border-slate-300 text-slate-700 font-semibold shadow-xs">
                <tr>
                  <th class="w-4/12 px-2.5 py-1.5 text-center border-r border-slate-200">Mã cấu hình</th>
                  <th class="w-3/12 px-2.5 py-1.5 text-center border-r border-slate-200">Giá trị TL</th>
                  <th class="w-5/12 px-2.5 py-1.5 text-center">Tên cấu hình</th>
                </tr>
              </thead>
              <tbody id="config-table-body" class="divide-y divide-slate-100 font-normal text-slate-800">
                <!-- Dòng nhóm phân loại như trong hình -->
                <tr id="group-header-row" class="bg-slate-200/80 text-slate-800 font-bold text-[11px] cursor-pointer hover:bg-slate-300/80 select-none">
                  <td colspan="3" class="px-2.5 py-1">
                    <span class="inline-flex items-center gap-1">
                      <span id="group-toggle-symbol" class="font-mono text-xs font-bold">[-]</span>
                      <span>Cấu hình hệ thống</span>
                    </span>
                  </td>
                </tr>

                <?php if (empty($configs)): ?>
                  <tr id="no-config-row">
                    <td colspan="3" class="py-12 text-center text-slate-400 text-xs">
                      Chưa có tham số cấu hình nào 
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($configs as $idx => $item): ?>
                    <?php $isSensitive = strtoupper((string) $item->system_key) === 'PASSWORD_IOC_HIS'; ?>
                    <tr class="config-row cursor-pointer transition border-b border-slate-100 hover:bg-blue-50/70 select-none <?php echo $idx === 0 ? 'bg-blue-100/70 border-l-4 border-l-blue-600 font-semibold' : ''; ?>"
                        data-id="<?php echo (int) $item->id; ?>"
                        data-key="<?php echo htmlspecialchars($item->system_key, ENT_QUOTES, 'UTF-8'); ?>"
                        data-name="<?php echo htmlspecialchars($item->system_name, ENT_QUOTES, 'UTF-8'); ?>"
                        data-value="<?php echo $isSensitive ? '' : htmlspecialchars($item->system_value, ENT_QUOTES, 'UTF-8'); ?>"
                        data-sensitive="<?php echo $isSensitive ? '1' : '0'; ?>"
                        data-status="<?php echo (int) $item->system_status; ?>">
                      <td class="px-2.5 py-1.5 font-mono text-[11px] font-bold text-slate-800 border-r border-slate-100 truncate max-w-[160px]" title="<?php echo htmlspecialchars($item->system_key, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($item->system_key, ENT_QUOTES, 'UTF-8'); ?>
                      </td>
                      <td class="px-2.5 py-1.5 text-[11px] text-slate-700 border-r border-slate-100 truncate max-w-[120px]" title="<?php echo $isSensitive ? 'Giá trị nhạy cảm đã được ẩn' : htmlspecialchars($item->system_value, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo $isSensitive ? '••••••••' : htmlspecialchars($item->system_value, ENT_QUOTES, 'UTF-8'); ?>
                      </td>
                      <td class="px-2.5 py-1.5 text-[11px] text-slate-800 truncate max-w-[220px]" title="<?php echo htmlspecialchars($item->system_name, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($item->system_name, ENT_QUOTES, 'UTF-8'); ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <!-- Thanh chân bảng thống kê số lượng -->
          <div class="px-3 py-1.5 bg-slate-50 border-t border-slate-200 text-[11px] text-slate-500 flex items-center justify-between shrink-0">
            <span>Tổng số: <strong id="total-configs-count" class="text-slate-800 font-bold"><?php echo count($configs); ?></strong> cấu hình</span>
            <span class="text-slate-400">Bấm dòng để xem / sửa</span>
          </div>
        </section>

        <!-- ==================== KHUNG PHẢI: THÔNG TIN CẤU HÌNH THIẾT LẬP ==================== -->
        <section class="lg:col-span-5 bg-white rounded-lg border border-[#0b5394] shadow-xs flex flex-col overflow-hidden">
          
          <!-- Thanh tiêu đề xanh đậm -->
          <div class="bg-[#0b5394] text-white px-3 py-2 flex items-center justify-between text-xs font-bold shrink-0">
            <div class="flex items-center gap-2">
              <i class="ph-bold ph-gear-six text-sm"></i>
              <span>Thông tin cấu hình thiết lập</span>
            </div>
            <span id="form-mode-badge" class="text-[10px] px-2 py-0.5 rounded bg-blue-700/80 text-blue-100 font-medium">Đang xem</span>
          </div>

          <!-- Form thông tin cấu hình chi tiết -->
          <form id="config-form" class="p-3.5 sm:p-4 space-y-3 flex-1 flex flex-col justify-between">
            <input type="hidden" id="config-id" name="id" value="0">

            <div class="space-y-3">
              <!-- Hàng 1: Tên cấu hình -->
              <div class="flex flex-col sm:flex-row sm:items-center gap-1.5 sm:gap-2">
                <label for="config-name" class="w-24 text-xs font-semibold text-slate-700 shrink-0">Tên cấu hình</label>
                <input type="text"
                       id="config-name"
                       name="system_name"
                       required
                       readonly
                       class="flex-1 text-xs px-2.5 py-1.5 bg-slate-50 border border-slate-300 rounded focus:outline-none focus:border-blue-500 text-slate-800 transition"
                       placeholder="Nhập tên mô tả tham số cấu hình...">
              </div>

              <!-- Hàng 2: Search Mã CH -->
              <div class="flex flex-col sm:flex-row sm:items-center gap-1.5 sm:gap-2">
                <label for="config-search-key" class="w-24 text-xs font-semibold text-slate-700 shrink-0">Search Mã CH</label>
                <div class="flex-1 relative">
                  <input type="text"
                         id="config-search-key"
                         placeholder="Nhập mã hoặc ID để tìm nhanh..."
                         class="w-full text-xs px-2.5 py-1.5 bg-white border border-slate-300 rounded focus:outline-none focus:border-blue-500 text-slate-800 transition">
                  <i class="ph ph-magnifying-glass absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                </div>
              </div>

              <!-- Hàng 3: Mã cấu hình & Giá trị (Nằm trên cùng 1 hàng ngang như hình mẫu) -->
              <div class="grid grid-cols-1 sm:grid-cols-12 gap-2">
                <!-- Mã cấu hình với màu nền vàng nhạt đặc trưng #fef08a -->
                <div class="sm:col-span-7 flex flex-col sm:flex-row sm:items-center gap-1.5 sm:gap-2">
                  <label for="config-key" class="w-24 text-xs font-semibold text-slate-700 shrink-0">Mã cấu hình</label>
                  <div class="flex-1 relative">
                    <input type="text"
                           id="config-key"
                           name="system_key"
                           required
                           readonly
                           class="w-full text-xs font-mono font-bold px-2.5 py-1.5 bg-[#fef08a] border border-amber-300 rounded focus:outline-none focus:ring-1 focus:ring-amber-500 uppercase text-slate-800 transition"
                           placeholder="MÃ_CẤU_HÌNH">
                  </div>
                </div>

                <!-- Giá trị -->
                <div class="sm:col-span-5 flex flex-col sm:flex-row sm:items-center gap-1.5 sm:gap-2">
                  <label for="config-value" class="text-xs font-semibold text-slate-700 shrink-0 sm:pr-1">Giá trị</label>
                  <input type="text"
                         id="config-value"
                         name="system_value"
                         readonly
                         class="flex-1 text-xs px-2.5 py-1.5 bg-slate-50 border border-slate-300 rounded focus:outline-none focus:border-blue-500 text-slate-800 transition"
                         placeholder="Giá trị...">
                </div>
              </div>

              <!-- Hàng 4: Mô tả -->
              <div class="flex flex-col sm:flex-row items-start gap-1.5 sm:gap-2">
                <label for="config-description" class="w-24 text-xs font-semibold text-slate-700 shrink-0 pt-1">Mô tả</label>
                <textarea id="config-description"
                          rows="4"
                          readonly
                          class="flex-1 text-xs px-2.5 py-1.5 bg-slate-50 border border-slate-300 rounded focus:outline-none focus:border-blue-500 text-slate-800 resize-y transition"
                          placeholder="Mô tả hướng dẫn hoặc phạm vi ảnh hưởng của cấu hình..."></textarea>
              </div>

              
            </div>

            <!-- Hàng 6: Thanh nút bấm chức năng (Xanh dương đồng bộ hình mẫu) -->
            <div class="pt-4 border-t border-slate-200 flex flex-wrap items-center justify-center sm:justify-end gap-2">
              <!-- Nút Thêm -->
              <button type="button"
                      id="btn-add"
                      class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#2563eb] hover:bg-[#1d4ed8] text-white text-xs font-semibold rounded shadow-xs transition active:scale-95">
                <i class="ph-bold ph-plus text-xs"></i>
                <span>Thêm</span>
              </button>

              <!-- Nút Sửa -->
              <button type="button"
                      id="btn-edit"
                      class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#2563eb] hover:bg-[#1d4ed8] text-white text-xs font-semibold rounded shadow-xs transition active:scale-95">
                <i class="ph-bold ph-pencil-simple text-xs"></i>
                <span>Sửa</span>
              </button>

              <!-- Nút Lưu -->
              <button type="submit"
                      id="btn-save"
                      disabled
                      class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#2563eb] hover:bg-[#1d4ed8] text-white text-xs font-semibold rounded shadow-xs transition active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-[#2563eb]">
                <i class="ph-bold ph-floppy-disk text-xs"></i>
                <span>Lưu</span>
              </button>

              <!-- Nút Hủy -->
              <button type="button"
                      id="btn-cancel"
                      disabled
                      class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#2563eb] hover:bg-[#1d4ed8] text-white text-xs font-semibold rounded shadow-xs transition active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-[#2563eb]">
                <i class="ph-bold ph-x-circle text-xs"></i>
                <span>Hủy</span>
              </button>

              <!-- Nút Reload Cache -->
              <button type="button"
                      id="btn-reload-cache"
                      class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#2563eb] hover:bg-[#1d4ed8] text-white text-xs font-semibold rounded shadow-xs transition active:scale-95">
                <i class="ph-bold ph-arrows-clockwise text-xs"></i>
                <span>Reload Cache</span>
              </button>
            </div>
          </form>
        </section>

      </div>
    </main>

    <!-- Script xử lý tương tác UI, AJAX và SweetAlert Toast -->
    <script>
      const csrfToken = <?php echo json_encode($csrf); ?>;
      const appUrl = <?php echo json_encode(XC_URL); ?>;

      // Cấu hình Toast SweetAlert góc dưới bên phải theo quy ước AGENTS.md
      const toast = Swal.mixin({
        toast: true,
        position: 'bottom-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
      });

      // Trạng thái form: 'view', 'add', 'edit'
      let currentMode = 'view';
      let selectedRow = null;

      // Nạp dữ liệu dòng được chọn vào form bên phải
      function loadRowToForm(row) {
        if (!row || row.length === 0) return;

        $('.config-row').removeClass('bg-blue-100/70 border-l-4 border-l-blue-600 font-semibold');
        row.addClass('bg-blue-100/70 border-l-4 border-l-blue-600 font-semibold');
        selectedRow = row;

        const id = row.data('id');
        const key = row.data('key');
        const name = row.data('name');
        const value = row.data('value');
        const isSensitive = parseInt(row.data('sensitive'), 10) === 1;
        const status = parseInt(row.data('status'), 10);

        $('#config-id').val(id);
        $('#config-key').val(key);
        $('#config-name').val(name);
        $('#config-value')
          .attr('type', isSensitive ? 'password' : 'text')
          .attr('placeholder', isSensitive ? 'Để trống nếu không thay đổi mật khẩu' : 'Giá trị...')
          .val(value);
        $('#config-description').val(name);
        $('#config-status-locked').prop('checked', status === 0);

        setFormMode('view');
      }

      // Quản lý trạng thái form (Xem, Thêm, Sửa)
      function setFormMode(mode) {
        currentMode = mode;
        const isEditable = (mode === 'add' || mode === 'edit');

        $('#config-name').prop('readonly', !isEditable).toggleClass('bg-white', isEditable).toggleClass('bg-slate-50', !isEditable);
        $('#config-key').prop('readonly', !isEditable);
        $('#config-value').prop('readonly', !isEditable).toggleClass('bg-white', isEditable).toggleClass('bg-slate-50', !isEditable);
        $('#config-description').prop('readonly', !isEditable).toggleClass('bg-white', isEditable).toggleClass('bg-slate-50', !isEditable);
        $('#config-status-locked').prop('disabled', !isEditable);

        $('#btn-add').prop('disabled', isEditable).toggleClass('opacity-40 cursor-not-allowed', isEditable);
        $('#btn-edit').prop('disabled', isEditable || !selectedRow).toggleClass('opacity-40 cursor-not-allowed', isEditable || !selectedRow);
        $('#btn-save').prop('disabled', !isEditable).toggleClass('opacity-40 cursor-not-allowed', !isEditable);
        $('#btn-cancel').prop('disabled', !isEditable).toggleClass('opacity-40 cursor-not-allowed', !isEditable);

        if (mode === 'add') {
          $('#form-mode-badge').text('Đang thêm mới').removeClass('bg-blue-700/80 bg-amber-700/80').addClass('bg-emerald-700/80');
        } else if (mode === 'edit') {
          $('#form-mode-badge').text('Đang chỉnh sửa').removeClass('bg-blue-700/80 bg-emerald-700/80').addClass('bg-amber-700/80');
        } else {
          $('#form-mode-badge').text('Đang xem').removeClass('bg-emerald-700/80 bg-amber-700/80').addClass('bg-blue-700/80');
        }
      }

      // Chọn dòng đầu tiên khi tải trang
      $(document).ready(function() {
        const firstRow = $('.config-row').first();
        if (firstRow.length > 0) {
          loadRowToForm(firstRow);
        } else {
          setFormMode('view');
        }
      });

      // Click chọn dòng trong bảng
      $(document).on('click', '.config-row', function() {
        if (currentMode !== 'view') {
          Swal.fire({
            title: 'Hủy thay đổi?',
            text: 'Bạn đang ở chế độ chỉnh sửa. Bạn có muốn hủy để xem cấu hình khác?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Ở lại',
            confirmButtonColor: '#2563eb'
          }).then((result) => {
            if (result.isConfirmed) {
              loadRowToForm($(this));
            }
          });
          return;
        }
        loadRowToForm($(this));
      });

      // Bấm nút Thêm
      $('#btn-add').on('click', function() {
        $('#config-id').val(0);
        $('#config-key').val('');
        $('#config-name').val('');
        $('#config-value').attr('type', 'text').attr('placeholder', 'Giá trị...').val('');
        $('#config-description').val('');
        $('#config-status-locked').prop('checked', false);
        setFormMode('add');
        $('#config-name').focus();
      });

      // Bấm nút Sửa
      $('#btn-edit').on('click', function() {
        if (!selectedRow) {
          toast.fire({ icon: 'warning', title: 'Vui lòng chọn cấu hình cần sửa.' });
          return;
        }
        setFormMode('edit');
        $('#config-value').focus();
      });

      // Bấm nút Hủy
      $('#btn-cancel').on('click', function() {
        if (selectedRow) {
          loadRowToForm(selectedRow);
        } else {
          setFormMode('view');
        }
      });

      // Submit Form (Lưu cấu hình)
      $('#config-form').on('submit', function(e) {
        e.preventDefault();

        const id = parseInt($('#config-id').val(), 10) || 0;
        const key = $('#config-key').val().trim().toUpperCase();
        const name = $('#config-name').val().trim();
        const value = $('#config-value').val().trim();
        const isLocked = $('#config-status-locked').is(':checked');
        const status = isLocked ? 0 : 1;

        if (!name) {
          toast.fire({ icon: 'warning', title: 'Vui lòng nhập tên cấu hình.' });
          $('#config-name').focus();
          return;
        }
        if (!key) {
          toast.fire({ icon: 'warning', title: 'Vui lòng nhập mã cấu hình.' });
          $('#config-key').focus();
          return;
        }

        const btnSave = $('#btn-save');
        btnSave.prop('disabled', true).addClass('opacity-50 cursor-not-allowed');

        $.ajax({
          url: appUrl + '/api/saveSystemConfig',
          method: 'POST',
          dataType: 'json',
          data: {
            csrf: csrfToken,
            id: id,
            system_key: key,
            system_name: name,
            system_value: value,
            system_status: status
          }
        })
        .done(function(response) {
          toast.fire({ icon: 'success', title: response.message || 'Lưu cấu hình thành công.' });
          setTimeout(() => window.location.reload(), 600);
        })
        .fail(function(xhr) {
          const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Có lỗi xảy ra khi lưu cấu hình.';
          toast.fire({ icon: 'error', title: msg });
        })
        .always(function() {
          btnSave.prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');
        });
      });

      // Bấm nút Reload Cache
      $('#btn-reload-cache').on('click', function() {
        const btn = $(this);
        btn.prop('disabled', true).addClass('opacity-50');

        $.ajax({
          url: appUrl + '/api/reloadSystemCache',
          method: 'POST',
          dataType: 'json',
          data: { csrf: csrfToken }
        })
        .done(function(response) {
          toast.fire({ icon: 'success', title: response.message || 'Đã làm mới bộ nhớ đệm cấu hình thành công.' });
        })
        .fail(function(xhr) {
          const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Không thể làm mới cache.';
          toast.fire({ icon: 'error', title: msg });
        })
        .always(function() {
          btn.prop('disabled', false).removeClass('opacity-50');
        });
      });

      // Tìm kiếm theo 3 cột độc lập
      function applyColumnFilters() {
        const filterKey = $('#filter-key').val().toLowerCase().trim();
        const filterValue = $('#filter-value').val().toLowerCase().trim();
        const filterName = $('#filter-name').val().toLowerCase().trim();

        let visibleCount = 0;

        $('.config-row').each(function() {
          const row = $(this);
          const key = String(row.data('key')).toLowerCase();
          const val = String(row.data('value')).toLowerCase();
          const name = String(row.data('name')).toLowerCase();

          const matchKey = !filterKey || key.includes(filterKey);
          const matchVal = !filterValue || val.includes(filterValue);
          const matchName = !filterName || name.includes(filterName);

          if (matchKey && matchVal && matchName) {
            row.show();
            visibleCount++;
          } else {
            row.hide();
          }
        });

        $('#total-configs-count').text(visibleCount);
      }

      $('#filter-key, #filter-value, #filter-name').on('input', applyColumnFilters);

      // Nút xóa nhanh bộ lọc (x)
      $('.btn-clear-filter').on('click', function() {
        const target = $(this).data('target');
        $(target).val('');
        applyColumnFilters();
      });

      // Tìm nhanh mã cấu hình từ Search Mã CH bên khung phải
      $('#config-search-key').on('input', function() {
        const val = $(this).val().toLowerCase().trim();
        if (!val) return;

        let found = false;
        $('.config-row').each(function() {
          const row = $(this);
          const key = String(row.data('key')).toLowerCase();
          const id = String(row.data('id')).toLowerCase();

          if (key.includes(val) || id === val) {
            loadRowToForm(row);
            row[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            found = true;
            return false; // Dừng vòng lặp khi tìm thấy
          }
        });
      });

      // Thu gọn / Mở rộng nhóm
      let isGroupCollapsed = false;
      function toggleGroupCollapse() {
        isGroupCollapsed = !isGroupCollapsed;
        if (isGroupCollapsed) {
          $('.config-row').hide();
          $('#group-toggle-symbol').text('[+]');
          $('#group-collapse-icon').removeClass('ph-caret-up').addClass('ph-caret-down');
        } else {
          applyColumnFilters();
          $('#group-toggle-symbol').text('[-]');
          $('#group-collapse-icon').removeClass('ph-caret-down').addClass('ph-caret-up');
        }
      }

      $('#group-header-row').on('click', toggleGroupCollapse);
    </script>

<?php require_once('footer.php'); ?>
