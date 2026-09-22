<?php require_once 'header.php';
function ief($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
$isEdit = !empty($record);
$fields = array('inpatient_opening_patient_count' => 'Người bệnh đầu ngày', 'inpatient_admission_count' => 'Nhập viện trong ngày', 'inpatient_inpatient_daily_discharge_count' => 'Ra viện trong ngày', 'inpatient_transfer_count' => 'Chuyển khoa vào', 'inpatient_hospital_transfer_count' => 'Chuyển viện/chuyển tuyến', 'inpatient_death_count' => 'Tử vong trong ngày', 'inpatient_occupied_beds' => 'Giường đang sử dụng'); ?>
<main class="content-body">
    <section class="page-section">
        <div class="section-title-bar">
            <div class="section-title"><i class="fa-solid fa-bed-pulse"></i> <?php echo $isEdit ? 'Sửa số liệu điều trị nội trú' : 'Thêm số liệu điều trị nội trú'; ?></div><a href="<?php echo XC_URL; ?>/backend/inpatient" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>
        </div>
        <?php if (!$isEdit): ?><div class="card" style="margin-bottom:18px">
                <div class="card-header">
                    <div class="card-title"><i class="fa-solid fa-file-import"></i> Import dữ liệu</div>
                </div>
                <div class="card-body">
                    <form id="inpatientImportForm" enctype="multipart/form-data" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap"><input type="hidden" name="csrf" value="<?php echo ief($csrf); ?>"><input type="file" name="import_file" accept=".xlsx,.csv" required><a class="btn btn-outline" href="<?php echo XC_URL; ?>/backend/inpatient/template"><i class="fa-solid fa-download"></i> Tải file mẫu</a><span style="font-size:12px;color:var(--text-muted)">Tiêu đề file mẫu bằng tiếng Việt. Bắt buộc: Ngày ghi nhận, Khoa điều trị, Đối tượng KCB.</span><button class="btn btn-success"><i class="fa-solid fa-upload"></i> Import</button></form>
                </div>
            </div><?php endif; ?>
        <div class="card">
            <div class="card-body">
                <form id="inpatientForm" class="form-grid" novalidate><input type="hidden" name="csrf" value="<?php echo ief($csrf); ?>"><input type="hidden" name="id" value="<?php echo $isEdit ? (int)$record->id : ''; ?>">
                    <div class="form-group"><label class="form-label">Ngày ghi nhận <span class="req">*</span></label><select class="form-select" name="inpatient_report_date" required>
                            <option value="">Chọn ngày</option><?php foreach ($reportDates as $date): ?><option value="<?php echo (int)$date->id; ?>" <?php echo $isEdit ? ((int)$record->inpatient_report_date === (int)$date->id ? 'selected' : '') : ($date->full_date === date('Y-m-d') ? 'selected' : ''); ?>><?php echo ief(date('d/m/Y', strtotime($date->full_date))); ?></option><?php endforeach; ?>
                        </select></div>
                    <div class="form-group"><label class="form-label">Khoa điều trị <span class="req">*</span></label><select class="form-select" name="inpatient_department_id" required>
                            <option value="">Chọn khoa</option><?php foreach ($departments as $d): ?><option value="<?php echo (int)$d->id; ?>" data-bed="<?php echo (int)$d->department_bed; ?>" <?php echo $isEdit && (int)$record->inpatient_department_id === (int)$d->id ? 'selected' : ''; ?>><?php echo ief($d->department_name); ?></option><?php endforeach; ?>
                        </select></div>
                    <div class="form-group"><label class="form-label">Đối tượng KCB <span class="req">*</span></label><select class="form-select" name="payer_type_id" required><option value="">Chọn đối tượng</option><?php foreach ($payerTypes as $p): ?><option value="<?php echo (int)$p->id; ?>" <?php echo $isEdit && (int)$record->payer_type_id === (int)$p->id ? 'selected' : ''; ?>><?php echo ief($p->payer_type_name); ?></option><?php endforeach; ?></select></div>
                    <?php foreach ($fields as $key => $label): ?><div class="form-group"><label class="form-label"><?php echo $label; ?></label><input class="form-control inpatient-number" type="number" min="0" step="1" name="<?php echo $key; ?>" value="<?php echo $isEdit ? (int)$record->$key : 0; ?>"></div><?php endforeach; ?>
                    <div class="form-group"><label class="form-label">Giường thực kê (theo khoa)</label><input class="form-control" disabled id="department-bed-display" type="number" value="<?php echo $isEdit ? (int)$record->inpatient_actual_beds : 0; ?>" readonly></div>
                    <div class="form-group"><label class="form-label">Người bệnh cuối ngày (tự tính)</label><input class="form-control" type="number" name="inpatient_closing_patient_count" value="<?php echo $isEdit ? (int)$record->inpatient_closing_patient_count : 0; ?>" readonly></div>
                    <div class="form-group"><label class="form-label">Công suất giường (tự tính)</label><input class="form-control" type="number" min="0" step="0.01" name="bed_occupancy_percent" value="<?php echo $isEdit && $record->bed_occupancy_percent !== null ? ief($record->bed_occupancy_percent) : ''; ?>" readonly></div>
                    <div class="form-group"><label class="form-label">Ngày điều trị TB (tự tính)</label><input class="form-control" type="number" min="0" step="0.01" name="avg_length_of_stay" value="<?php echo $isEdit && $record->avg_length_of_stay !== null ? ief($record->avg_length_of_stay) : ''; ?>" readonly></div>
                    <div class="form-actions" style="grid-column:1/-1"><a class="btn btn-outline" href="<?php echo XC_URL; ?>/backend/inpatient">Hủy</a><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> <?php echo $isEdit ? 'Cập nhật' : 'Lưu số liệu'; ?></button></div>
                </form>
            </div>
        </div>
    </section>
</main>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function inpatientResult(res, redirect) {
        Swal.fire({
            icon: res.success ? 'success' : 'error',
            title: res.success ? 'Thành công' : 'Không thành công',
            text: res.message,
            timer: res.success ? 1500 : 0,
            showConfirmButton: !res.success
        }).then(function() {
            if (res.success && redirect) location.href = '<?php echo XC_URL; ?>/backend/inpatient';
        });
    }

    function inpatientRequest(form, action, multipart) {
        if (!multipart && !form.checkValidity()) {
            form.reportValidity();
            return;
        }
        var button = $(form).find('button').prop('disabled', true),
            options = {
                url: '<?php echo XC_URL; ?>/api/' + action,
                type: 'POST',
                dataType: 'json',
                data: multipart ? new FormData(form) : $(form).serialize()
            };
        if (multipart) {
            options.processData = false;
            options.contentType = false;
        }
        $.ajax(options).done(function(res) {
            inpatientResult(res, !multipart || res.success);
            if (multipart && res.success) $(form).find('input[type=file]').val('');
        }).fail(function(xhr) {
            Swal.fire('Không thành công', (xhr.responseJSON && xhr.responseJSON.message) || 'Không thể kết nối máy chủ. Vui lòng thử lại.', 'error');
        }).always(function() {
            button.prop('disabled', false);
        });
    }
    $('#inpatientForm').on('submit', function(e) {
        e.preventDefault();
        inpatientRequest(this, 'saveInpatient', false);
    });
    function calculateInpatient() {
        var value = function(name) { return parseInt($('[name=\"' + name + '\"]').val(), 10) || 0; };
        var closing = value('inpatient_opening_patient_count') + value('inpatient_admission_count') + value('inpatient_transfer_count') - value('inpatient_inpatient_daily_discharge_count') - value('inpatient_hospital_transfer_count') - value('inpatient_death_count');
        $('[name=\"inpatient_closing_patient_count\"]').val(Math.max(0, closing));
        var discharge = value('inpatient_inpatient_daily_discharge_count'), days = 0, beds = parseInt($('#inpatientForm select[name=\"inpatient_department_id\"] option:selected').data('bed'), 10) || 0, occupied = value('inpatient_occupied_beds');
        $('#department-bed-display').val(beds);
        $('[name=\"avg_length_of_stay\"]').val(discharge ? (days / discharge).toFixed(2) : '');
        $('[name=\"bed_occupancy_percent\"]').val(beds ? (occupied / beds * 100).toFixed(2) : '');
    }
    $('#inpatientForm').on('input change', '.inpatient-number, select[name=\"inpatient_department_id\"]', calculateInpatient);
    calculateInpatient();
    $('#inpatientImportForm').on('submit', function(e) {
        e.preventDefault();
        var file = this.import_file.files[0];
        if (!file || !/\.(xlsx|csv)$/i.test(file.name) || file.size > 5 * 1024 * 1024) {
            Swal.fire('Tệp không hợp lệ', 'Chọn tệp .xlsx/.csv không quá 5 MB.', 'error');
            return;
        }
        inpatientRequest(this, 'importInpatient', true);
    });
</script><?php require_once 'footer.php'; ?>
