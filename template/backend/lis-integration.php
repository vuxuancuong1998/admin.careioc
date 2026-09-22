<?php require_once 'header.php'; ?>
<main class="content-body"><section class="page-section">
<div class="section-title-bar"><div class="section-title">Quản lý dữ liệu LIS</div></div>
<div class="card"><div class="card-body">
<p>Dữ liệu trong ngày làm mới mỗi <strong>30 phút</strong>. Chốt ngày lúc <strong>23:59:59</strong> (giờ Việt Nam). Ngày chưa chốt được đồng bộ bù khi máy chủ hoạt động trở lại.</p>
<p style="color:var(--text-muted)">Đơn vị: <strong>lượt dịch vụ xét nghiệm</strong>. BHYT và viện phí là số lượt theo đối tượng thanh toán; một phiếu có thể có nhiều dịch vụ. Mã đơn vị: <?php echo (int)$hospitalCode; ?>.</p>
<form id="lisFilter"><div class="form-grid">
<div class="form-group"><label class="form-label" for="lisFromDate">Từ ngày</label><input class="form-control" id="lisFromDate" type="date" min="2024-01-01" max="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars($fromDate,ENT_QUOTES,'UTF-8'); ?>" required></div>
<div class="form-group"><label class="form-label" for="lisToDate">Đến ngày</label><input class="form-control" id="lisToDate" type="date" min="2024-01-01" max="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars($toDate,ENT_QUOTES,'UTF-8'); ?>" required></div>
</div><div class="form-actions" style="justify-content:flex-start;flex-wrap:wrap">
<button class="btn btn-primary" type="submit">Xem dữ liệu</button>
<button class="btn btn-outline" type="button" id="syncLis">Đồng bộ khoảng ngày</button>
<button class="btn btn-outline" type="button" id="checkLisStatus">Kiểm tra kết nối LIS</button>
</div></form>
<div id="lisMessage" role="status" aria-live="polite" style="padding:12px 0"></div>
<div style="overflow-x:auto"><table class="table" style="width:100%;min-width:940px"><thead><tr><th>Ngày</th><th>Tổng lượt</th><th>Ngoại trú</th><th>Nội trú</th><th>BHYT</th><th>Viện phí</th><th>Trạng thái</th><th>Cập nhật</th><th>Thao tác</th></tr></thead><tbody id="lisRows"></tbody></table></div>
<div class="form-actions" style="justify-content:flex-start"><button class="btn btn-outline" id="lisPrev" type="button">Trước</button><span id="lisPages"></span><button class="btn btn-outline" id="lisNext" type="button">Sau</button></div>
</div></div>
<div class="card" id="lisDetails" style="display:none;margin-top:20px"><div class="card-body"><h3 id="lisDetailsTitle" tabindex="-1"></h3>
<div style="overflow-x:auto"><table class="table" style="width:100%;min-width:750px"><thead><tr><th>Nhóm</th><th>Dịch vụ</th><th>Tổng lượt</th><th>Ngoại trú</th><th>Nội trú</th><th>BHYT</th><th>Viện phí</th></tr></thead><tbody id="lisServiceRows"></tbody></table></div>
</div></div></section></main>
<script src="<?php echo XC_URL; ?>/template/eoffice/assets/js/jquery-3.6.0.min.js"></script>
<script>
(function ($) {
'use strict';
var base=<?php echo json_encode(XC_URL); ?>, csrf=<?php echo json_encode($csrf); ?>;
var page=1,busy=false,requestId=0,detailDate=null;
var initial=<?php echo json_encode($saved,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
var error=<?php echo json_encode($loadError,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
function message(text,ok){$('#lisMessage').text(text).css('color',ok?'var(--success)':'var(--danger)');}
function count(v){return v===null||v===undefined?'—':Number(v).toLocaleString('vi-VN');}
function range(){if(!document.getElementById('lisFilter').reportValidity())return null;var from=$('#lisFromDate').val(),to=$('#lisToDate').val();if(from>to){message('Ngày bắt đầu phải trước hoặc bằng ngày kết thúc.',false);return null;}return {from_date:from,to_date:to};}
function render(data){
page=data.page;$('#lisRows').empty();
(data.rows||[]).forEach(function(r){
var tr=$('<tr>');$('<td>').text(r.report_date).appendTo(tr);
['total_count','outpatient_count','inpatient_count','insurance_count','self_pay_count'].forEach(function(k){$('<td>').text(count(r[k])).appendTo(tr);});
$('<td>').text((r.status==='final'?'Đã chốt':r.status==='temporary'?'Tạm':'Chờ đồng bộ')+(r.last_error?' · Lỗi lần gần nhất':'')).attr('title',r.last_error||'').appendTo(tr);
$('<td>').text(r.synced_at||'—').appendTo(tr);
var actions=$('<td>');$('<button type="button" class="btn btn-outline">').text('Chi tiết').on('click',function(){details(r.report_date);}).appendTo(actions);
$('<button type="button" class="btn btn-outline lis-resync">').text('Đồng bộ lại').prop('disabled',busy).on('click',function(){syncDates([r.report_date]);}).appendTo(actions);
actions.appendTo(tr);$('#lisRows').append(tr);
});
if(!data.rows.length)$('#lisRows').append($('<tr>').append($('<td colspan="9">').text('Chưa có ngày trong khoảng này. Kiểm tra tác vụ tạo lịch ngày.')));
var pages=Math.max(1,Math.ceil(data.total/31));$('#lisPages').text('Trang '+page+'/'+pages+' · '+data.total+' ngày');$('#lisPrev').prop('disabled',page<=1);$('#lisNext').prop('disabled',page>=pages);
}
function load(targetPage,announce){var filters=range();if(!filters)return;var id=++requestId;$.getJSON(base+'/backend/lisIntegration',$.extend(filters,{format:'json',page:targetPage})).done(function(r){if(id!==requestId)return;if(r.success){render(r.data);if(announce)message('Đã tải dữ liệu đã lưu.',true);}else message(r.message,false);}).fail(function(){message('Không tải được dữ liệu. Kiểm tra kết nối hoặc đăng nhập lại.',false);});}
function details(date){
detailDate=date;$('#lisDetails').show();$('#lisDetailsTitle').text('Chi tiết dịch vụ ngày '+date);$('#lisServiceRows').empty();
$.getJSON(base+'/backend/lisIntegration',{format:'json',details:date}).done(function(r){if(detailDate!==date)return;if(!r.success){message(r.message,false);return;}$('#lisServiceRows').empty();
r.data.forEach(function(s){var tr=$('<tr>');$('<td>').text(s.service_group).appendTo(tr);$('<td>').text(s.service_name).appendTo(tr);['total_count','outpatient_count','inpatient_count','insurance_count','self_pay_count'].forEach(function(k){$('<td>').text(count(s[k])).appendTo(tr);});$('#lisServiceRows').append(tr);});
if(!r.data.length)$('#lisServiceRows').append($('<tr>').append($('<td colspan="7">').text('Chưa có dịch vụ hoặc LIS trả báo cáo rỗng cho ngày này.')));$('#lisDetailsTitle').trigger('focus');
}).fail(function(){message('Không tải được chi tiết dịch vụ. Vui lòng thử lại.',false);});
}
function setBusy(value){busy=value;$('#syncLis,#checkLisStatus,.lis-resync').prop('disabled',value);}
function syncDates(dates){if(busy)return;setBusy(true);var completed=0;
function next(){message('Đang đồng bộ '+dates[completed]+' ('+(completed+1)+'/'+dates.length+')…',true);
$.ajax({url:base+'/api/lisSyncDay',method:'POST',dataType:'json',timeout:120000,data:{csrf:csrf,report_date:dates[completed]}}).done(function(r){
if(!r.success){setBusy(false);load(page,false);message(r.message+' Đã hoàn thành '+completed+'/'+dates.length+' ngày.',false);return;}
completed++;if(completed<dates.length){next();}else{setBusy(false);load(page,false);if(detailDate)details(detailDate);message('Đã lưu dữ liệu '+completed+' ngày.',true);}
}).fail(function(xhr){setBusy(false);load(page,false);message((xhr.responseJSON&&xhr.responseJSON.message)||'Đồng bộ bị gián đoạn. Có thể đồng bộ lại an toàn.',false);});
}next();}
$('#lisFilter').on('submit',function(e){e.preventDefault();load(1,true);});$('#lisPrev').on('click',function(){load(page-1,false);});$('#lisNext').on('click',function(){load(page+1,false);});
$('#syncLis').on('click',function(){var r=range();if(!r)return;var d=new Date(r.from_date+'T00:00:00Z'),end=new Date(r.to_date+'T00:00:00Z'),dates=[];while(d<=end){dates.push(d.toISOString().slice(0,10));d.setUTCDate(d.getUTCDate()+1);}if(dates.length>31){message('Mỗi lần đồng bộ thủ công tối đa 31 ngày. Tác vụ nền tự đồng bộ bù từ năm 2024.',false);return;}syncDates(dates);});
$('#checkLisStatus').on('click',function(){if(busy)return;setBusy(true);$.ajax({url:base+'/api/externalSystemStatus',method:'POST',dataType:'json',data:{csrf:csrf,system:'lis',login_if_needed:1}}).done(function(r){message(r.message,r.success);}).fail(function(){message('Không kiểm tra được kết nối LIS. Vui lòng thử lại.',false);}).always(function(){setBusy(false);});});
render(initial);if(error)message(error,false);
setInterval(function(){if(!busy)load(page,false);},60000);
})(jQuery);
</script>
<?php require_once 'footer.php'; ?>
