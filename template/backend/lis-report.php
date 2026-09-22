<?php
require_once 'header.php';
function lre($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
$sumPatient = (int) $totals->inpatient_lis_count + (int) $totals->outpatient_lis_count;
$sumPayer = (int) $totals->lis_bhyt_count + (int) $totals->lis_self_pay_count;
?>
<main class="content-body">
  <section class="page-section">
    <div class="section-title-bar">
      <div class="section-title"><i class="fa-solid fa-vials"></i> Báo cáo số liệu xét nghiệm</div>
      <a class="btn btn-outline" href="<?php echo XC_URL; ?>/backend/lisReport/template"><i class="fa-solid fa-download"></i> File mẫu</a>
      <a class="btn btn-primary" href="<?php echo XC_URL; ?>/backend/lisReport/add"><i class="fa-solid fa-plus"></i> Nhập số liệu</a>
    </div>

    <div class="filter-card">
      <form method="get" class="filter-grid" id="lisReportFilter">
        <div class="form-group"><label class="form-label">Từ ngày</label><input class="form-control" type="date" name="from_date" value="<?php echo lre($filterFromDate); ?>" required></div>
        <div class="form-group"><label class="form-label">Đến ngày</label><input class="form-control" type="date" name="to_date" value="<?php echo lre($filterToDate); ?>" required></div>
        <div class="form-group"><label class="form-label">Nhóm xét nghiệm</label><select class="form-select" name="lis_group_code"><option value="">Tất cả</option><?php foreach ($groupOptions as $group): ?><option value="<?php echo (int) $group->lis_group_code; ?>" <?php echo $filterGroupCode === (int) $group->lis_group_code ? 'selected' : ''; ?>><?php echo lre($group->category_lis_code . ' - ' . $group->lis_category_name); ?></option><?php endforeach; ?></select></div>
        <button class="btn btn-primary" style="align-self:end"><i class="fa-solid fa-filter"></i> Lọc</button>
      </form>
    </div>

    <div class="card" style="margin-bottom:18px">
      <div class="card-header"><div class="card-title">Tổng hợp theo bộ lọc</div></div>
      <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:16px">
        <div><div style="color:var(--text-muted);font-size:12px">Nội trú</div><strong style="font-size:22px"><?php echo number_format($totals->inpatient_lis_count); ?></strong></div>
        <div><div style="color:var(--text-muted);font-size:12px">Ngoại trú</div><strong style="font-size:22px"><?php echo number_format($totals->outpatient_lis_count); ?></strong></div>
        <div><div style="color:var(--text-muted);font-size:12px">BHYT</div><strong style="font-size:22px"><?php echo number_format($totals->lis_bhyt_count); ?></strong></div>
        <div><div style="color:var(--text-muted);font-size:12px">Viện phí</div><strong style="font-size:22px"><?php echo number_format($totals->lis_self_pay_count); ?></strong></div>
        <div><div style="color:var(--text-muted);font-size:12px">Tổng theo người bệnh</div><strong style="font-size:22px"><?php echo number_format($sumPatient); ?></strong></div>
        <div><div style="color:var(--text-muted);font-size:12px">Tổng theo chi trả</div><strong style="font-size:22px"><?php echo number_format($sumPayer); ?></strong></div>
      </div>
    </div>

    <div class="card"><div class="table-container"><table class="custom-table">
      <thead><tr><th>Ngày</th><th>Nhóm xét nghiệm</th><th style="text-align:right">Nội trú</th><th style="text-align:right">Ngoại trú</th><th style="text-align:right">BHYT</th><th style="text-align:right">Viện phí</th><th>Ghi chú</th><th style="text-align:center">Thao tác</th></tr></thead>
      <tbody>
      <?php if (!$records): ?><tr><td colspan="8" style="text-align:center;padding:38px;color:var(--text-muted)"><i class="fa-regular fa-folder-open" style="display:block;font-size:28px;margin-bottom:10px"></i>Chưa có số liệu phù hợp.</td></tr><?php endif; ?>
      <?php foreach ($records as $record): ?><tr>
        <td style="font-weight:600;white-space:nowrap"><?php echo date('d/m/Y', strtotime($record->full_date)); ?></td>
        <td><?php echo $record->lis_category_name ? lre($record->category_lis_code . ' - ' . $record->lis_category_name) : lre('Mã #' . $record->lis_group_code); ?></td>
        <td style="text-align:right"><?php echo number_format($record->inpatient_lis_count); ?></td>
        <td style="text-align:right"><?php echo number_format($record->outpatient_lis_count); ?></td>
        <td style="text-align:right"><?php echo number_format($record->lis_bhyt_count); ?></td>
        <td style="text-align:right"><?php echo number_format($record->lis_self_pay_count); ?></td>
        <td style="max-width:260px;white-space:normal"><?php echo $record->note !== null && $record->note !== '' ? lre($record->note) : '—'; ?></td>
        <td><div class="action-btns" style="justify-content:center"><a class="btn-icon" title="Sửa" href="<?php echo XC_URL; ?>/backend/lisReport/edit/<?php echo (int) $record->id; ?>"><i class="fa-solid fa-pen"></i></a><button type="button" class="btn-icon delete" title="Xóa" data-id="<?php echo (int) $record->id; ?>"><i class="fa-solid fa-trash"></i></button></div></td>
      </tr><?php endforeach; ?>
      </tbody>
    </table></div></div>
  </section>
</main>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function ($) {
  $('#lisReportFilter').on('submit', function (event) {
    var from = this.from_date.value, to = this.to_date.value;
    if (from && to && from > to) {
      event.preventDefault();
      Swal.fire('Khoảng ngày không hợp lệ', 'Từ ngày phải nhỏ hơn hoặc bằng đến ngày.', 'error');
    }
  });
  $('.btn-icon.delete').on('click', function () {
    var button = $(this), id = button.data('id');
    Swal.fire({title:'Xóa số liệu?',text:'Thao tác này sẽ xóa bản ghi xét nghiệm.',icon:'warning',showCancelButton:true,confirmButtonText:'Xóa',cancelButtonText:'Hủy'}).then(function (answer) {
      if (!answer.isConfirmed) return;
      button.prop('disabled', true);
      $.ajax({url:'<?php echo XC_URL; ?>/api/deleteLisReport',type:'POST',dataType:'json',data:{csrf:'<?php echo lre($csrf); ?>',id:id}}).done(function (response) {
        Swal.fire({icon:response.success?'success':'error',title:response.success?'Thành công':'Không thành công',text:response.message,timer:response.success?1500:0,showConfirmButton:!response.success}).then(function(){if(response.success)location.reload();});
      }).fail(function (xhr) {
        Swal.fire('Không thành công',(xhr.responseJSON&&xhr.responseJSON.message)||'Không thể xóa bản ghi. Vui lòng thử lại.','error');
      }).always(function(){button.prop('disabled', false);});
    });
  });
})(jQuery);
</script>
<?php require_once 'footer.php'; ?>
