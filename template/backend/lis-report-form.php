<?php
require_once 'header.php';
function lrfe($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
$isEdit = !empty($record);
$fields = array(
    'lis_bhyt_count' => 'Số xét nghiệm bệnh nhân BHYT',
    'inpatient_lis_count' => 'Số xét nghiệm bệnh nhân nội trú',
    'outpatient_lis_count' => 'Số xét nghiệm bệnh nhân ngoại trú',
    'lis_self_pay_count' => 'Số xét nghiệm bệnh nhân viện phí'
);
?>
<main class="content-body"><section class="page-section">
  <div class="section-title-bar"><div class="section-title"><i class="fa-solid fa-vials"></i> <?php echo $isEdit ? 'Sửa số liệu xét nghiệm' : 'Nhập số liệu xét nghiệm'; ?></div><a href="<?php echo XC_URL; ?>/backend/lisReport" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Quay lại</a></div>

  <?php if (!$isEdit): ?><div class="card" style="margin-bottom:18px">
    <div class="card-header"><div class="card-title"><i class="fa-solid fa-file-import"></i> Import số liệu</div></div>
    <div class="card-body"><form id="lisReportImportForm" enctype="multipart/form-data" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
      <input type="hidden" name="csrf" value="<?php echo lrfe($csrf); ?>">
      <input type="file" name="import_file" accept=".xlsx,.csv" required>
      <a class="btn btn-outline" href="<?php echo XC_URL; ?>/backend/lisReport/template"><i class="fa-solid fa-download"></i> Tải file mẫu</a>
      <span style="font-size:12px;color:var(--text-muted)">Bắt buộc: Ngày ghi nhận và Mã nhóm xét nghiệm. Tối đa 5 MB.</span>
      <button class="btn btn-success"><i class="fa-solid fa-upload"></i> Import</button>
    </form></div>
  </div><?php endif; ?>

  <div class="card"><div class="card-body">
    <form id="lisReportForm" class="form-grid" novalidate>
      <input type="hidden" name="csrf" value="<?php echo lrfe($csrf); ?>"><input type="hidden" name="id" value="<?php echo $isEdit ? (int) $record->id : ''; ?>">
      <div class="form-group"><label class="form-label">Ngày ghi nhận <span class="req">*</span></label><select class="form-select" name="report_date" required><option value="">Chọn ngày</option><?php foreach ($reportDates as $date): ?><option value="<?php echo (int) $date->id; ?>" <?php echo $isEdit ? ((int) $record->report_date === (int) $date->id ? 'selected' : '') : ($date->full_date === date('Y-m-d') ? 'selected' : ''); ?>><?php echo lrfe(date('d/m/Y', strtotime($date->full_date))); ?></option><?php endforeach; ?></select></div>
      <div class="form-group"><label class="form-label">Nhóm xét nghiệm <span class="req">*</span></label><select class="form-select" name="lis_group_code" required><option value=""><?php echo $categories ? 'Chọn nhóm xét nghiệm' : 'Chưa có danh mục nhóm đang hoạt động'; ?></option><?php foreach ($categories as $category): ?><option value="<?php echo (int) $category->id; ?>" <?php echo $isEdit && (int) $record->lis_group_code === (int) $category->id ? 'selected' : ''; ?>><?php echo lrfe($category->category_lis_code . ' - ' . $category->lis_category_name); ?></option><?php endforeach; ?></select></div>
      <?php foreach ($fields as $key => $label): ?><div class="form-group"><label class="form-label"><?php echo $label; ?></label><input class="form-control lis-report-number" type="number" min="0" max="4294967295" step="1" name="<?php echo $key; ?>" value="<?php echo $isEdit ? (int) $record->$key : 0; ?>" required></div><?php endforeach; ?>
      <div class="form-group"><label class="form-label">Tổng theo nội/ngoại trú</label><input class="form-control" id="lisPatientTotal" type="number" value="0" readonly disabled></div>
      <div class="form-group"><label class="form-label">Tổng theo đối tượng chi trả</label><input class="form-control" id="lisPayerTotal" type="number" value="0" readonly disabled ></div>
      <div class="form-group" style="grid-column:1/-1"><label class="form-label">Ghi chú</label><textarea class="form-control" name="note" rows="3" maxlength="5000" placeholder="Nội dung điều chỉnh hoặc ghi chú nguồn số liệu"><?php echo $isEdit ? lrfe($record->note) : ''; ?></textarea></div>
      <div class="form-actions" style="grid-column:1/-1"><a class="btn btn-outline" href="<?php echo XC_URL; ?>/backend/lisReport">Hủy</a><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> <?php echo $isEdit ? 'Cập nhật' : 'Lưu số liệu'; ?></button></div>
    </form>
  </div></div>
</section></main>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function ($) {
  function showResult(response, redirect) {
    Swal.fire({icon:response.success?'success':'error',title:response.success?'Thành công':'Không thành công',text:response.message,timer:response.success?1500:0,showConfirmButton:!response.success}).then(function(){if(response.success&&redirect)location.href='<?php echo XC_URL; ?>/backend/lisReport';});
  }
  function send(form, action, multipart) {
    if (!multipart && !form.checkValidity()) { form.reportValidity(); return; }
    var button=$(form).find('button').prop('disabled',true), options={url:'<?php echo XC_URL; ?>/api/'+action,type:'POST',dataType:'json',data:multipart?new FormData(form):$(form).serialize()};
    if(multipart){options.processData=false;options.contentType=false;}
    $.ajax(options).done(function(response){showResult(response,!multipart||response.success);if(multipart&&response.success)$(form).find('input[type=file]').val('');}).fail(function(xhr){Swal.fire('Không thành công',(xhr.responseJSON&&xhr.responseJSON.message)||'Không thể kết nối máy chủ. Vui lòng thử lại.','error');}).always(function(){button.prop('disabled',false);});
  }
  function updateTotals(){var number=function(name){return parseInt($('[name="'+name+'"]').val(),10)||0;};$('#lisPatientTotal').val(number('inpatient_lis_count')+number('outpatient_lis_count'));$('#lisPayerTotal').val(number('lis_bhyt_count')+number('lis_self_pay_count'));}
  $('#lisReportForm').on('submit',function(event){event.preventDefault();send(this,'<?php echo $isEdit ? 'editLisReport' : 'saveLisReport'; ?>',false);}).on('input','.lis-report-number',updateTotals);
  updateTotals();
  $('#lisReportImportForm').on('submit',function(event){event.preventDefault();var file=this.import_file.files[0];if(!file||!ftest(file)){Swal.fire('Tệp không hợp lệ','Chọn tệp .xlsx hoặc .csv không quá 5 MB.','error');return;}send(this,'importLisReport',true);});
  function ftest(file){return /\.(xlsx|csv)$/i.test(file.name)&&file.size>0&&file.size<=5*1024*1024;}
})(jQuery);
</script>
<?php require_once 'footer.php'; ?>
