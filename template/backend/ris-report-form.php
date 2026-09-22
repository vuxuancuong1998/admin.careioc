<?php
require_once 'header.php';
function rrfe($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
$isEdit = !empty($record);
$fields = array('ris_bhyt_bn_count'=>'Số bệnh nhân BHYT','inpatient_ris_bn_count'=>'Số bệnh nhân nội trú','outpatient_ris_bn_count'=>'Số bệnh nhân ngoại trú','ris_bn_self_pay_count'=>'Số bệnh nhân viện phí','ris_total_fim'=>'Tổng số phim đã chụp');
?>
<main class="content-body"><section class="page-section">
  <div class="section-title-bar"><div class="section-title"><i class="fa-solid fa-x-ray"></i> <?php echo $isEdit ? 'Sửa số liệu CĐHA' : 'Nhập số liệu CĐHA'; ?></div><a href="<?php echo XC_URL; ?>/backend/risReport" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Quay lại</a></div>
  <?php if (!$isEdit): ?><div class="card" style="margin-bottom:18px"><div class="card-header"><div class="card-title"><i class="fa-solid fa-file-import"></i> Import số liệu</div></div><div class="card-body"><form id="risReportImportForm" enctype="multipart/form-data" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
    <input type="hidden" name="csrf" value="<?php echo rrfe($csrf); ?>"><input type="file" name="import_file" accept=".xlsx,.csv" required><a class="btn btn-outline" href="<?php echo XC_URL; ?>/backend/risReport/template"><i class="fa-solid fa-download"></i> Tải file mẫu</a><span style="font-size:12px;color:var(--text-muted)">Bắt buộc: Ngày ghi nhận và Mã nhóm CĐHA. Tối đa 5 MB.</span><button class="btn btn-success"><i class="fa-solid fa-upload"></i> Import</button>
  </form></div></div><?php endif; ?>
  <div class="card"><div class="card-body"><form id="risReportForm" class="form-grid" novalidate>
    <input type="hidden" name="csrf" value="<?php echo rrfe($csrf); ?>"><input type="hidden" name="id" value="<?php echo $isEdit ? (int) $record->id : ''; ?>">
    <div class="form-group"><label class="form-label">Ngày ghi nhận <span class="req">*</span></label><select class="form-select" name="report_date" required><option value="">Chọn ngày</option><?php foreach ($reportDates as $date): ?><option value="<?php echo (int) $date->id; ?>" <?php echo $isEdit ? ((int) $record->report_date === (int) $date->id ? 'selected' : '') : ($date->full_date === date('Y-m-d') ? 'selected' : ''); ?>><?php echo rrfe(date('d/m/Y', strtotime($date->full_date))); ?></option><?php endforeach; ?></select></div>
    <div class="form-group"><label class="form-label">Nhóm CĐHA <span class="req">*</span></label><select class="form-select" name="ris_group_code" required><option value=""><?php echo $categories ? 'Chọn nhóm CĐHA' : 'Chưa có danh mục nhóm đang hoạt động'; ?></option><?php foreach ($categories as $category): ?><option value="<?php echo (int) $category->id; ?>" <?php echo $isEdit && (int) $record->ris_group_code === (int) $category->id ? 'selected' : ''; ?>><?php echo rrfe($category->category_ris_code . ' - ' . $category->ris_category_name); ?></option><?php endforeach; ?></select></div>
    <?php foreach ($fields as $key=>$label): ?><div class="form-group"><label class="form-label"><?php echo $label; ?></label><input class="form-control" type="number" min="0" max="<?php echo $key === 'ris_total_fim' ? '2147483647' : '4294967295'; ?>" step="1" name="<?php echo $key; ?>" value="<?php echo $isEdit ? (int) $record->$key : 0; ?>" required></div><?php endforeach; ?>
    <div class="form-group" style="grid-column:1/-1"><label class="form-label">Ghi chú</label><textarea class="form-control" name="note" rows="3" maxlength="5000" placeholder="Nội dung điều chỉnh hoặc ghi chú nguồn số liệu"><?php echo $isEdit ? rrfe($record->note) : ''; ?></textarea></div>
    <div class="form-actions" style="grid-column:1/-1"><a class="btn btn-outline" href="<?php echo XC_URL; ?>/backend/risReport">Hủy</a><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> <?php echo $isEdit ? 'Cập nhật' : 'Lưu số liệu'; ?></button></div>
  </form></div></div>
</section></main>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function($){
  function showResult(response,redirect){Swal.fire({icon:response.success?'success':'error',title:response.success?'Thành công':'Không thành công',text:response.message,timer:response.success?1500:0,showConfirmButton:!response.success}).then(function(){if(response.success&&redirect)location.href='<?php echo XC_URL; ?>/backend/risReport';});}
  function send(form,action,multipart){if(!multipart&&!form.checkValidity()){form.reportValidity();return;}var button=$(form).find('button').prop('disabled',true),options={url:'<?php echo XC_URL; ?>/api/'+action,type:'POST',dataType:'json',data:multipart?new FormData(form):$(form).serialize()};if(multipart){options.processData=false;options.contentType=false;}$.ajax(options).done(function(response){showResult(response,!multipart||response.success);if(multipart&&response.success)$(form).find('input[type=file]').val('');}).fail(function(xhr){Swal.fire('Không thành công',(xhr.responseJSON&&xhr.responseJSON.message)||'Không thể kết nối máy chủ. Vui lòng thử lại.','error');}).always(function(){button.prop('disabled',false);});}
  $('#risReportForm').on('submit',function(event){event.preventDefault();send(this,'<?php echo $isEdit ? 'editRisReport' : 'saveRisReport'; ?>',false);});
  $('#risReportImportForm').on('submit',function(event){event.preventDefault();var file=this.import_file.files[0];if(!file||!/\.(xlsx|csv)$/i.test(file.name)||file.size<1||file.size>5*1024*1024){Swal.fire('Tệp không hợp lệ','Chọn tệp .xlsx hoặc .csv không quá 5 MB.','error');return;}send(this,'importRisReport',true);});
})(jQuery);
</script>
<?php require_once 'footer.php'; ?>
