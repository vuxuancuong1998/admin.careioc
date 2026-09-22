<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>IOC Mini - Y tế Đăk Tô | Dashboard mẫu</title>
<style>
:root{
  --bg:#07111f; --panel:#0d1b2b; --panel2:#102338; --line:#1d3550;
  --text:#eaf2ff; --muted:#8fa9c4; --ok:#2ecc71; --warn:#f1c40f;
  --bad:#ff5c5c; --info:#39a0ff; --accent:#20c6b7; --purple:#9b7cff;
}
*{box-sizing:border-box}
html,body{margin:0;padding:0;overflow-x:hidden;max-width:100%}
body{background:linear-gradient(180deg,#06101d,#091827 55%,#07111f);color:var(--text);font-family:Segoe UI,Arial,sans-serif}
.wrap{max-width:1920px;margin:auto;padding:18px 22px 28px;width:100%}
.header{display:flex;align-items:center;justify-content:space-between;gap:18px;margin-bottom:14px}
.brand{display:flex;align-items:center;gap:14px}
.logo{width:54px;height:54px;border-radius:14px;background:linear-gradient(135deg,#20c6b7,#39a0ff);display:grid;place-items:center;font-weight:800;font-size:18px;box-shadow:0 0 28px #20c6b744}
.title h1{font-size:24px;margin:0 0 3px}.title p{margin:0;color:var(--muted);font-size:13px}
.live{display:flex;align-items:center;gap:8px;color:#cfe7ff;font-size:13px}.dot{width:9px;height:9px;border-radius:50%;background:var(--ok);box-shadow:0 0 12px var(--ok)}
.tabs{display:flex;gap:8px;margin-bottom:14px}
.tab{border:1px solid var(--line);background:#0b1726;color:#bdd2e8;padding:9px 14px;border-radius:10px;cursor:pointer}
.tab.active{background:linear-gradient(180deg,#173350,#10263f);color:white;border-color:#315b7e}
.page{display:none}.page.active{display:block}
.grid{display:grid;grid-template-columns:repeat(12,1fr);gap:12px}
.card{background:linear-gradient(180deg,rgba(16,35,56,.96),rgba(12,27,44,.96));border:1px solid var(--line);border-radius:14px;padding:14px;box-shadow:0 10px 30px #00000022}
.kpi{grid-column:span 2;min-height:120px;position:relative;overflow:hidden}
.kpi .label{font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.4px}
.kpi .value{font-size:30px;font-weight:750;margin:12px 0 6px}
.kpi .sub{font-size:12px;color:#b8cce0}.trend{position:absolute;right:12px;bottom:12px;font-size:12px;padding:4px 7px;border-radius:7px;background:#0a1725}
.green{color:var(--ok)}.yellow{color:var(--warn)}.red{color:var(--bad)}.blue{color:var(--info)}
.section-title{font-size:15px;font-weight:700;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center}
.section-title small{font-weight:400;color:var(--muted);font-size:11px}
.c7{grid-column:span 7}.c5{grid-column:span 5}.c4{grid-column:span 4}.c8{grid-column:span 8}.c12{grid-column:span 12}
.chart{height:220px;position:relative}
svg{width:100%;height:100%}.axis{stroke:#24415e;stroke-width:1}.gridline{stroke:#18314a;stroke-width:1}.area{fill:#20c6b71f}.line{fill:none;stroke:#20c6b7;stroke-width:3}.line2{fill:none;stroke:#39a0ff;stroke-width:2.5}.bar{fill:#39a0ff}.bar2{fill:#9b7cff}
.legend{display:flex;gap:16px;font-size:11px;color:var(--muted);margin-top:4px}.lg i{display:inline-block;width:9px;height:9px;border-radius:3px;margin-right:5px}
.bed{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.gauge{height:170px;display:grid;place-items:center;position:relative}
.ring{width:140px;height:140px;border-radius:50%;background:conic-gradient(var(--accent) 0 78%,#173149 78% 100%);display:grid;place-items:center}
.ring:after{content:"";width:100px;height:100px;background:#0d1b2b;border-radius:50%;position:absolute}
.gnum{position:absolute;z-index:2;text-align:center}.gnum b{font-size:28px}.gnum span{display:block;color:var(--muted);font-size:11px}
.status-list{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.status{display:flex;align-items:center;justify-content:space-between;background:#0b1827;border:1px solid #19354f;border-radius:10px;padding:10px 11px}
.status .name{font-size:12px}.pill{font-size:10px;border-radius:999px;padding:4px 7px;background:#143047}
.pill.ok{color:#9df3bd}.pill.warn{color:#ffe27a}.pill.bad{color:#ff9b9b}
.alerts{display:flex;flex-direction:column;gap:8px}
.alert{display:grid;grid-template-columns:90px 1fr 110px;gap:10px;align-items:center;padding:9px 10px;border-radius:10px;background:#0b1827;border-left:3px solid var(--warn)}
.alert.bad{border-left-color:var(--bad)}.alert.info{border-left-color:var(--info)}
.alert .time{font-size:11px;color:var(--muted)}.alert .msg{font-size:12px}.alert .tag{font-size:10px;text-align:right}
.table{width:100%;border-collapse:collapse;font-size:12px}.table th,.table td{padding:9px;border-bottom:1px solid #193149;text-align:left}.table th{color:#8fa9c4;font-weight:600}
.progress{height:7px;background:#173149;border-radius:9px;overflow:hidden}.progress > span{display:block;height:100%;background:linear-gradient(90deg,#20c6b7,#39a0ff)}
.infra-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px}
.device{background:#0b1827;border:1px solid #19354f;border-radius:12px;padding:12px}
.device .top{display:flex;justify-content:space-between;align-items:center}.device b{font-size:13px}.device .meta{margin-top:10px;font-size:11px;color:var(--muted);line-height:1.7}
.spark{height:36px;margin-top:8px}
.badge{display:inline-flex;align-items:center;gap:5px;font-size:10px;padding:4px 7px;border-radius:999px;background:#10273c}
.badge:before{content:"";width:7px;height:7px;border-radius:50%;background:var(--ok)}
.badge.warn:before{background:var(--warn)}.badge.bad:before{background:var(--bad)}

/* ===== MÀN HÌNH SỐ LIỆU KHÁM CHỮA BỆNH HẰNG NGÀY ===== */
.kcb-head{text-align:center;margin:2px 0 14px}
.kcb-head h2{margin:0;color:#ff7777;font-size:30px;letter-spacing:.5px}
.kcb-head .date{font-size:20px;font-weight:700;margin-top:4px;color:#f7fbff}
.kcb-head .updated{font-size:11px;color:var(--muted);margin-top:5px}
.kcb-kpi{grid-column:span 2;min-height:126px;text-align:center;display:flex;flex-direction:column;justify-content:center}
.kcb-kpi .label{font-size:12px;color:#a9bfd6;text-transform:uppercase}
.kcb-kpi .value{font-size:38px;font-weight:800;margin:8px 0 2px;color:#ffffff}
.kcb-kpi .note{font-size:11px;color:var(--muted)}
.kcb-table-wrap{overflow:auto;border-radius:12px;border:1px solid #28445f}
.kcb-table{width:100%;border-collapse:collapse;min-width:1280px;font-size:14px}
.kcb-table th,.kcb-table td{border:1px solid #28445f;padding:11px 9px;text-align:center;vertical-align:middle}
.kcb-table th{background:#7f3e46;color:#fff;font-weight:700}
.kcb-table th.group{background:#914850}
.kcb-table td:first-child,.kcb-table td:nth-child(2){text-align:left}
.kcb-table tbody tr:nth-child(odd){background:#0b1827}
.kcb-table tbody tr:nth-child(even){background:#0d1d2e}
.kcb-table .total-row{background:#132a40!important;font-weight:800}
.kcb-table .strong{font-size:18px;font-weight:800;color:#ff7777}
.kcb-table .sum{font-size:18px;font-weight:800;color:#fff}
.kcb-table .service-note{text-align:left;line-height:1.65;white-space:nowrap}
.kcb-mini-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
.kcb-mini{background:#0b1827;border:1px solid #1f3b57;border-radius:12px;padding:14px}
.kcb-mini h3{font-size:12px;color:#9fb7cf;text-transform:uppercase;margin:0 0 10px}
.kcb-mini .big{font-size:30px;font-weight:800}
.kcb-mini .rows{font-size:12px;line-height:1.9;color:#d8e6f4}
.kcb-mini .rows b{font-size:15px;color:#fff;float:right}
.source-chip{display:inline-block;padding:5px 9px;border-radius:999px;background:#102a40;border:1px solid #24506d;color:#acd4ef;font-size:10px}
@media(max-width:1100px){
  .kcb-kpi{grid-column:span 4}
  .kcb-mini-grid{grid-template-columns:1fr 1fr}
}

.footer{margin-top:10px;color:#6f8aa6;font-size:10px;text-align:right}

/* BỘ LỌC CHỌN NGÀY & POPUP LƯỚI LỊCH TẤT CẢ MÀN HÌNH */
.filter-bar{display:flex;align-items:center;justify-content:space-between;background:linear-gradient(90deg,#0b1929,#0d2035);border:1px solid #1c3857;border-radius:12px;padding:9px 16px;margin-bottom:14px;gap:12px;flex-wrap:wrap;box-shadow:0 4px 18px rgba(0,0,0,0.25);position:relative}
.filter-group{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.filter-label{font-size:12.5px;color:var(--text);font-weight:700;display:flex;align-items:center;gap:6px}
.cal-picker-wrap{position:relative;display:inline-flex;align-items:center}
.cal-icon-btn{background:#0e243a;border:1px solid #234c75;color:#20c6b7;border-radius:8px;padding:6px 12px;font-size:13px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:all .2s;white-space:nowrap}
.cal-icon-btn:hover,.cal-icon-btn.active{background:linear-gradient(135deg,#20c6b7,#39a0ff);color:#040914;border-color:transparent;box-shadow:0 0 14px rgba(32,198,183,0.4)}
.filter-date-input{background:#06101d;border:1px solid #234c75;border-radius:8px;color:#fff;padding:6px 10px;font-size:13px;font-family:inherit;outline:none;cursor:pointer;transition:border-color .2s}
.filter-date-input:focus{border-color:var(--accent);box-shadow:0 0 10px rgba(32,198,183,0.3)}
.btn-filter{background:#0e243a;border:1px solid #234c75;color:#bcd3eb;padding:6px 14px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s;white-space:nowrap}
.btn-filter:hover,.btn-filter.active{background:linear-gradient(135deg,#20c6b7,#39a0ff);color:#040914;border-color:transparent;box-shadow:0 0 14px rgba(32,198,183,0.4);font-weight:700}
.date-view-badge{font-size:12.5px;padding:6px 12px;border-radius:8px;background:#102b44;border:1px solid #23527a;color:#a3e9c1;font-weight:600;display:inline-flex;align-items:center;gap:6px;white-space:nowrap}

/* POPUP LƯỚI LỊCH (CALENDAR GRID POPUP) */
.calendar-grid-popup{position:absolute;top:calc(100% + 8px);left:0;z-index:9999;background:#091a2c;border:1px solid #20456b;border-radius:14px;padding:14px;width:300px;box-shadow:0 18px 45px rgba(0,0,0,0.85),0 0 25px rgba(32,198,183,0.2);display:none;animation:calPopIn .2s cubic-bezier(0.16,1,0.3,1)}
.calendar-grid-popup.show{display:block}
@keyframes calPopIn{from{opacity:0;transform:translateY(-6px) scale(0.97)}to{opacity:1;transform:translateY(0) scale(1)}}
.cal-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid #1a3857}
.cal-head-title{font-size:13.5px;font-weight:800;color:#f0f6fc;letter-spacing:.3px}
.cal-nav-btn{background:#0e243a;border:1px solid #20456b;color:#a5c7eb;border-radius:6px;width:28px;height:28px;cursor:pointer;display:grid;place-items:center;font-size:14px;font-weight:700;transition:all .15s}
.cal-nav-btn:hover{background:#20c6b7;color:#040914;border-color:#20c6b7}
.cal-weekdays{display:grid;grid-template-columns:repeat(7,1fr);gap:4px;text-align:center;font-size:11px;font-weight:700;color:#7e9bb9;margin-bottom:6px}
.cal-days{display:grid;grid-template-columns:repeat(7,1fr);gap:4px}
.cal-day-cell{aspect-ratio:1;border-radius:8px;display:grid;place-items:center;font-size:12px;font-weight:600;color:#d5e5f5;cursor:pointer;background:#06121f;border:1px solid transparent;transition:all .15s}
.cal-day-cell:hover{background:#153757;border-color:#20c6b7;color:#fff}
.cal-day-cell.other-month{color:#32506e;background:transparent}
.cal-day-cell.today{border:1px solid #20c6b7;color:#20c6b7}
.cal-day-cell.selected{background:linear-gradient(135deg,#20c6b7,#39a0ff);color:#040914;font-weight:800;box-shadow:0 0 10px rgba(32,198,183,0.5)}
.cal-foot{display:flex;justify-content:space-between;align-items:center;margin-top:10px;padding-top:8px;border-top:1px solid #1a3857;font-size:11.5px}
.cal-today-link{color:#20c6b7;font-weight:700;cursor:pointer;text-decoration:none}
.cal-today-link:hover{text-decoration:underline}

/* ===== TỐI ƯU HOÁ GIAO DIỆN MOBILE & TABLET TOÀN DIỆN ===== */
@media (max-width: 992px) {
  .wrap{padding:12px 14px 24px}
  .kpi{grid-column:span 4}
  .c7,.c5,.c4,.c8,.c12,.c6{grid-column:span 12}
  .infra-grid{grid-template-columns:1fr 1fr}
  .kcb-kpi{grid-column:span 4}
  .kcb-mini-grid{grid-template-columns:1fr 1fr}
  .bed{grid-template-columns:1fr 1fr}
}

@media (max-width: 768px) {
  .header{flex-direction:column;align-items:flex-start;gap:12px}
  .brand{width:100%;align-items:flex-start;gap:10px}
  .logo{width:42px;height:42px;font-size:15px;border-radius:10px;flex-shrink:0}
  .title h1{font-size:16px;line-height:1.35;margin-bottom:2px}
  .title p{font-size:11.5px}
  .live{width:100%;justify-content:space-between;flex-wrap:wrap;gap:8px}
  
  .tabs{overflow-x:auto;-webkit-overflow-scrolling:touch;padding-bottom:6px;gap:6px;flex-wrap:nowrap;width:100%}
  .tab{white-space:nowrap;font-size:12px;padding:8px 12px;flex-shrink:0}
  
  .filter-bar{flex-direction:column;align-items:stretch;gap:10px;padding:10px 12px}
  .filter-group{width:100%;justify-content:flex-start}
  .calendar-grid-popup{width:calc(100vw - 40px);max-width:300px;left:0}
  
  .kpi{grid-column:span 6;min-height:105px;padding:11px}
  .kpi .value{font-size:24px;margin:8px 0 3px}
  .kpi .label{font-size:11px}
  .kpi .sub{font-size:11px}
  
  .kcb-head h2{font-size:22px}
  .kcb-head .date{font-size:16px}
  .kcb-kpi{grid-column:span 6;min-height:105px;padding:10px}
  .kcb-kpi .value{font-size:28px;margin:4px 0 2px}
  
  .infra-grid{grid-template-columns:1fr}
  .kcb-mini-grid{grid-template-columns:1fr}
  .status-list{grid-template-columns:1fr}
  .bed{grid-template-columns:1fr}
  
  .chart{height:180px}
  .alert{grid-template-columns:55px 1fr auto;gap:8px;padding:8px}
  .alert .time{font-size:10px}
  .alert .msg{font-size:11.5px}
  .alert .tag{font-size:9.5px}
}

@media (max-width: 480px) {
  .wrap{padding:8px 10px 20px}
  .title h1{font-size:14.5px}
  .filter-date-input{width:100%;min-width:unset}
  .kpi{grid-column:span 6;padding:9px;min-height:96px}
  .kpi .value{font-size:20px}
  .kpi .label{font-size:10px;letter-spacing:0}
  .kpi .trend{font-size:9.5px;padding:2px 5px;right:6px;bottom:6px}
  .kcb-kpi{grid-column:span 6}
  .kcb-kpi .value{font-size:24px}
  .kcb-kpi .label{font-size:10px}
  .calendar-grid-popup{width:calc(100vw - 20px);left:-6px}
}

/* ===== TIẾN TRÌNH XỬ LÝ LOAD DỮ LIỆU ĐẶC TRƯNG IOC (IOC HUD CIRCULAR LOADER) ===== */
.ioc-loader-overlay{position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,0.48);backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);display:flex;flex-direction:column;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .25s ease}
.ioc-loader-overlay.active{opacity:1;pointer-events:auto}
.ioc-loader-hud{position:relative;width:140px;height:140px;display:grid;place-items:center}
.ioc-pulse-wave{position:absolute;width:100%;height:100%;border-radius:50%;border:1px solid rgba(0,242,254,0.4);animation:iocWave 2s cubic-bezier(0.2,0.8,0.4,1) infinite}
.ioc-ring-outer{position:absolute;inset:0;border-radius:50%;border:2px dashed rgba(32,198,183,0.4);animation:iocRotateCW 8s linear infinite}
.ioc-ring-scanner{position:absolute;inset:6px;border-radius:50%;border:3px solid transparent;border-top-color:#00f2fe;border-right-color:#39a0ff;box-shadow:0 0 22px rgba(0,242,254,0.5);animation:iocRotateCW 1.4s cubic-bezier(0.5,0.1,0.5,0.9) infinite}
.ioc-ring-reverse{position:absolute;inset:18px;border-radius:50%;border:2px dotted rgba(57,160,255,0.65);animation:iocRotateCCW 2.5s linear infinite}
.ioc-core-pulse{width:60px;height:60px;border-radius:50%;background:rgba(6,16,28,0.75);backdrop-filter:blur(6px);border:1.5px solid rgba(0,242,254,0.7);display:grid;place-items:center;box-shadow:0 0 25px rgba(32,198,183,0.5),inset 0 0 15px rgba(0,242,254,0.3);animation:iocCoreBeat 1.5s ease-in-out infinite alternate}
.ioc-core-logo{font-size:14px;font-weight:900;letter-spacing:1.5px;background:linear-gradient(135deg,#00f2fe,#fff);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.ioc-loader-title{margin-top:22px;font-size:13.5px;font-weight:800;letter-spacing:1.2px;color:#f0f8ff;text-transform:uppercase;display:flex;align-items:center;gap:8px;text-shadow:0 0 14px rgba(0,242,254,0.6)}
.ioc-loader-desc{margin-top:6px;font-size:11.5px;color:#8ab4db;font-weight:500;text-align:center}
.ioc-loader-bar{width:180px;height:3px;background:rgba(255,255,255,0.12);border-radius:999px;margin-top:14px;overflow:hidden;position:relative}
.ioc-loader-bar::after{content:"";position:absolute;left:-50%;height:100%;width:50%;background:linear-gradient(90deg,transparent,#00f2fe,#39a0ff,transparent);animation:iocProgress 1.4s ease-in-out infinite}

@keyframes iocRotateCW{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}
@keyframes iocRotateCCW{from{transform:rotate(360deg)}to{transform:rotate(0deg)}}
@keyframes iocCoreBeat{0%{transform:scale(0.92);box-shadow:0 0 15px rgba(32,198,183,0.3)}100%{transform:scale(1.06);box-shadow:0 0 30px rgba(0,242,254,0.7)}}
@keyframes iocWave{0%{transform:scale(0.6);opacity:0.8}100%{transform:scale(1.35);opacity:0}}
@keyframes iocProgress{0%{left:-50%;width:30%}50%{width:60%}100%{left:100%;width:30%}}

/* USER AVATAR & DROPDOWN MENU */
.user-menu-wrap{position:relative;display:inline-flex;align-items:center}
.user-avatar-btn{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#20c6b7,#39a0ff);color:#040914;font-weight:800;font-size:13px;display:grid;place-items:center;cursor:pointer;border:1.5px solid rgba(0,242,254,0.4);box-shadow:0 0 12px rgba(0,242,254,0.25);transition:all .2s ease}
.user-avatar-btn:hover{transform:scale(1.08);box-shadow:0 0 18px rgba(0,242,254,0.6);border-color:#00f2fe}
.user-dropdown-menu{position:absolute;top:calc(100% + 8px);right:0;z-index:9999;width:250px;background:#091a2e;border:1px solid #1f456e;border-radius:14px;padding:10px;box-shadow:0 16px 40px rgba(0,0,0,0.85),0 0 25px rgba(0,242,254,0.15);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);display:none;animation:userDropIn .2s cubic-bezier(0.16,1,0.3,1)}
.user-dropdown-menu.show{display:block}
@keyframes userDropIn{from{opacity:0;transform:translateY(-8px) scale(0.96)}to{opacity:1;transform:translateY(0) scale(1)}}
.user-menu-header{display:flex;align-items:center;gap:10px;padding:8px 8px 12px;border-bottom:1px solid #1a3857;margin-bottom:6px}
.user-menu-avatar{width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#00f2fe,#39a0ff);color:#040914;font-weight:800;font-size:15px;display:grid;place-items:center;box-shadow:0 0 12px rgba(0,242,254,0.4);flex-shrink:0}
.user-menu-info .user-name{font-size:13px;font-weight:700;color:#f0f6fc}
.user-menu-info .user-role{font-size:11px;color:#8fa9c4;margin-top:1px}
.user-menu-list{list-style:none;padding:0;margin:0}
.user-menu-link{display:flex;align-items:center;gap:9px;padding:8px 10px;border-radius:8px;color:#d5e5f5;font-size:12.5px;font-weight:500;text-decoration:none;cursor:pointer;transition:all .15s;background:transparent;border:none;width:100%;text-align:left;box-sizing:border-box}
.user-menu-link:hover{background:#132b45;color:#20c6b7}
.user-menu-link.logout{color:#ff7777;margin-top:4px;border-top:1px solid #1a3857;padding-top:9px;border-radius:0 0 8px 8px}
.user-menu-link.logout:hover{background:rgba(239,68,68,0.15);color:#ff5c5c}
</style>
</head>
<body>

<!-- TIẾN TRÌNH XỬ LÝ LOAD DỮ LIỆU ĐẶC TRƯNG IOC -->
<div class="ioc-loader-overlay" id="ioc-loader-overlay">
  <div class="ioc-loader-hud">
    <div class="ioc-pulse-wave"></div>
    <div class="ioc-ring-outer"></div>
    <div class="ioc-ring-scanner"></div>
    <div class="ioc-ring-reverse"></div>
    <div class="ioc-core-pulse">
      <span class="ioc-core-logo">IOC</span>
    </div>
  </div>
  <div class="ioc-loader-title">
    <span class="dot"></span>
    <span id="ioc-loader-msg">ĐANG ĐỒNG BỘ DỮ LIỆU ĐIỀU HÀNH...</span>
  </div>
  <div class="ioc-loader-desc" id="ioc-loader-sub">Trung tâm Điều hành Y tế Thông minh BVĐK KV Đăk Tô</div>
  <div class="ioc-loader-bar"></div>
</div>

<div class="wrap">
  <div class="header">
    <div class="brand">
      <div class="logo">IOC</div>
      <div class="title"><h1>TRUNG TÂM ĐIỀU HÀNH THÔNG MINH – BỆNH VIỆN ĐA KHOA KHU VỰC ĐĂK TÔ</h1><p>Giám sát hoạt động bệnh viện & hạ tầng công nghệ thông tin</p></div>
    </div>
    <div class="live" style="display:flex;align-items:center;gap:12px">
      <div style="display:flex;align-items:center;gap:8px;color:#cfe7ff;font-size:13px"><span class="dot"></span><span>Đang cập nhật thời gian thực</span></div>
      
      <a href="<?php echo XC_URL; ?>/admin/accounts/admin" style="display:inline-flex;align-items:center;gap:6px;background:linear-gradient(135deg,#20c6b7,#39a0ff);color:#040914;text-decoration:none;font-size:12px;font-weight:700;padding:6px 14px;border-radius:8px;box-shadow:0 0 16px rgba(32,198,183,0.3);transition:all 0.2s">
        <span>⚙️ Quản trị & Nhập liệu</span>
      </a>

      <!-- USER AVATAR & DROPDOWN HEADER -->
      <div class="user-menu-wrap">
        <div class="user-avatar-btn" onclick="toggleUserDropdown('user-dropdown-index')" title="Nhấp vào avatar để xem menu & Đăng xuất">
          AD
        </div>

        <!-- USER DROPDOWN MENU -->
        <div class="user-dropdown-menu" id="user-dropdown-index">
          <div class="user-menu-header">
            <div class="user-menu-avatar">AD</div>
            <div class="user-menu-info">
              <div class="user-name">Trực ban Điều hành</div>
              <div class="user-role">Quản trị viên Hệ thống IOC</div>
            </div>
          </div>
          <ul class="user-menu-list">
            <li><a class="user-menu-link" href="<?php echo XC_URL; ?>/admin/accounts/admin"><i class="fa-solid fa-pen-to-square"></i> Quản lý tài khoản</a></li>
            <li><button class="user-menu-link logout" onclick="handleLogout()"><i class="fa-solid fa-arrow-right-from-bracket"></i> <b>Đăng xuất</b></button></li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- BỘ LỌC CHỌN NGÀY DÙNG CHUNG TẤT CẢ MÀN HÌNH -->
  <div class="filter-bar">
    <div class="filter-group">
      <div class="cal-picker-wrap">
        <button class="cal-icon-btn" id="btn-toggle-cal-grid" onclick="toggleCalendarGrid('cal-popup-index', 'global-date-input')" title="Nhấp để hiển thị Lưới Lịch chọn ngày">
          <span style="font-size:15px">📅</span>
          <span>Chọn ngày</span>
        </button>

        <!-- LƯỚI LỊCH POPUP -->
        <div class="calendar-grid-popup" id="cal-popup-index">
          <div class="cal-head">
            <button class="cal-nav-btn" onclick="calNavigateMonth(-1, 'cal-popup-index', 'global-date-input')">‹</button>
            <span class="cal-head-title" id="cal-month-title-index">Tháng 08/2026</span>
            <button class="cal-nav-btn" onclick="calNavigateMonth(1, 'cal-popup-index', 'global-date-input')">›</button>
          </div>
          <div class="cal-weekdays">
            <span>T2</span><span>T3</span><span>T4</span><span>T5</span><span>T6</span><span>T7</span><span style="color:#ff7777">CN</span>
          </div>
          <div class="cal-days" id="cal-days-grid-index">
            <!-- Rendered by JS -->
          </div>
          <div class="cal-foot">
            <span class="cal-today-link" onclick="selectCalendarDate(new Date(), 'cal-popup-index', 'global-date-input')">Hôm nay</span>
            <button class="btn-filter" style="padding:2px 8px;font-size:11px" onclick="document.getElementById('cal-popup-index').classList.remove('show')">Đóng</button>
          </div>
        </div>
      </div>

      <input type="date" id="global-date-input" class="filter-date-input" onchange="onGlobalDateChange(this.value)">
      <button class="btn-filter active" id="btn-filter-today" onclick="filterByPreset('today')">Hôm nay</button>
      <button class="btn-filter" id="btn-filter-yesterday" onclick="filterByPreset('yesterday')">Hôm qua</button>
    </div>
    <div class="filter-group">
      <span class="date-view-badge"><span class="dot"></span>Số liệu ngày: <b id="lbl-viewing-date">--/--/----</b></span>
      <span class="source-chip" id="kcb-source" style="margin:0">Đang tải dữ liệu...</span>
    </div>
  </div>

  <div class="tabs">
    <button class="tab active" onclick="showPage('overview',this)">Tổng quan điều hành</button>
    <button class="tab" onclick="showPage('dailykcb',this)">Số liệu KCB hằng ngày</button>
    <button class="tab" onclick="showPage('infra',this)">Hạ tầng CNTT</button>
    <button class="tab" onclick="showPage('alerts',this)">Cảnh báo & ATTT</button>
  </div>

  <section id="overview" class="page active">
    <div class="grid">
      <div class="card kpi"><div class="label">Tổng số lượt khám bệnh tháng</div><div class="value" id="kpi-overview-kham-thang">3.486</div><div class="sub">Lượt khám ngày</div><div class="trend green" id="kpi-overview-kham-ngay">120</div></div>
      <div class="card kpi"><div class="label">Ra viện</div><div class="value" id="kpi-overview-ra-vien">37</div><div class="sub">Trong ngày</div><div class="trend yellow">Cần theo dõi</div></div>
      <div class="card kpi"><div class="label">Đang điều trị</div><div class="value" id="kpi-overview-dang-dieu-tri">82</div><div class="sub" id="kpi-overview-giuong-kh">150 giường kế hoạch</div><div class="trend blue" id="kpi-overview-rate">82%</div></div>
      <div class="card kpi"><div class="label">Công suất giường</div><div class="value" id="kpi-overview-cong-suat">78%</div><div class="sub">Theo giường thực kê</div><div class="trend green">Ổn định</div></div>
      <div class="card kpi"><div class="label">Hồ sơ EMR</div><div class="value" id="kpi-overview-emr">91.6%</div><div class="sub">Trong ngày</div><div class="trend green">▲ 55.1%</div></div>
      <div class="card kpi"><div class="label">Hệ thống CNTT</div><div class="value" id="kpi-overview-cntt">97.8%</div><div class="sub">Availability 24h</div><div class="trend green">Bình thường</div></div>

      <div class="card c7">
        <div class="section-title">Xu hướng hoạt động khám chữa bệnh <small>Theo chu kỳ lọc ngày</small></div>
        <div class="chart">
          <svg viewBox="0 0 760 220" preserveAspectRatio="none">
            <line x1="40" y1="20" x2="40" y2="190" class="axis"/><line x1="40" y1="190" x2="740" y2="190" class="axis"/>
            <g class="gridline"><line x1="40" y1="55" x2="740" y2="55"/><line x1="40" y1="95" x2="740" y2="95"/><line x1="40" y1="135" x2="740" y2="135"/></g>
            <path d="M40 170 L140 125 L240 145 L340 88 L440 108 L540 62 L640 80 L740 52 L740 190 L40 190 Z" class="area"/>
            <polyline points="40,170 140,125 240,145 340,88 440,108 540,62 640,80 740,52" class="line"/>
            <polyline points="40,181 140,169 240,174 340,154 440,160 540,145 640,150 740,137" class="line2"/>
          </svg>
        </div>
        <div class="legend"><span class="lg"><i style="background:#20c6b7"></i>Lượt khám</span><span class="lg"><i style="background:#39a0ff"></i>Nhập viện</span></div>
      </div>

      <div class="card c5">
        <div class="section-title">Công suất giường & điều trị nội trú <small>Toàn viện</small></div>
        <div class="bed">
          <div class="gauge"><div class="ring" id="overview-gauge-ring"></div><div class="gnum"><b id="overview-gauge-val">78%</b><span>Công suất</span></div></div>
          <div style="padding-top:10px">
            <div style="font-size:12px;color:var(--muted);margin-bottom:6px">Giường đang sử dụng</div><div class="progress"><span id="overview-bed-bar" style="width:78%"></span></div>
            <div style="display:flex;justify-content:space-between;margin-top:8px;font-size:11px"><span id="overview-bed-using">Đang dùng: 82</span><span id="overview-bed-empty">Trống: 23</span></div>
            <div style="margin-top:18px;font-size:12px;color:var(--muted)">Ngày điều trị TB</div><div style="font-size:24px;font-weight:700;margin-top:5px"><span id="overview-avg-days">6.4</span> <span style="font-size:11px;color:var(--muted)">ngày</span></div>
            <div style="font-size:11px;color:#ffe27a;margin-top:6px">Khoa PHCN: 92% – gần ngưỡng cảnh báo</div>
          </div>
        </div>
      </div>

      <div class="card c4">
        <div class="section-title">Chuyển đổi số & chất lượng</div>
        <table class="table">
          <tr><th>Chỉ số</th><th>Hiện tại</th></tr>
          <tr><td>Hồ sơ bệnh án điện tử</td><td class="green" id="cds-table-emr">94.2%</td></tr>
          <tr><td>Ký số biểu mẫu</td><td class="green" id="cds-table-sign">91.6%</td></tr>
          <tr><td>Liên thông dữ liệu</td><td class="green" id="cds-table-sync">98.7%</td></tr>
          <tr><td>Thanh toán không tiền mặt</td><td class="blue" id="cds-table-cashless">63.4%</td></tr>
          <tr><td>Hài lòng người bệnh</td><td class="green" id="cds-table-satisfaction">96.1%</td></tr>
        </table>
      </div>

      <div class="card c4">
        <div class="section-title">Trạng thái hệ thống nghiệp vụ</div>
        <div class="status-list">
          <div class="status"><span class="name">Chỉ số tiếp nhận CCCD</span><span class="pill ok" id="nv-pill-cccd">134</span></div>
          <div class="status"><span class="name">Liên thông Khám sức khoẻ Lái xe</span><span class="pill ok" id="nv-pill-laixe">25</span></div>
          <div class="status"><span class="name">Tỷ lệ cấp chế độ BHXH (TT25/2025)</span><span class="pill ok" id="nv-pill-bhxh">60%</span></div>
          <div class="status"><span class="name">Liên thông sức khoẻ bà mẹ trẻ em (eMHC)</span><span class="pill ok" id="nv-pill-emhc">13</span></div>
        </div>
      </div>

      <div class="card c4">
        <div class="section-title">Cảnh báo cần xử lý <small>Ưu tiên cao</small></div>
        <div class="alerts">
          <div class="alert bad"><div class="time">14:12</div><div class="msg">Ổ đĩa máy chủ PACS đạt 88%</div><div class="tag red">Nghiêm trọng</div></div>
          <div class="alert"><div class="time">13:48</div><div class="msg">Thời gian chờ khám > 35 phút tại PK01</div><div class="tag yellow">Cảnh báo</div></div>
          <div class="alert info"><div class="time">13:25</div><div class="msg">Đường truyền Internet 2 tăng độ trễ</div><div class="tag blue">Theo dõi</div></div>
        </div>
      </div>
    </div>
  </section>

  <section id="dailykcb" class="page">
    <div class="kcb-head">
      <h2>SỐ LIỆU KHÁM CHỮA BỆNH</h2>
      <div class="date">Ngày <span id="kcb-current-date">--/--/----</span></div>
      <div class="updated">Cập nhật lúc <span id="kcb-updated-time">--:--</span> • <span class="source-chip" id="kcb-source-daily">Dữ liệu theo ngày chọn</span></div>
    </div>

    <div class="grid" style="margin-bottom:12px">
      <div class="card kcb-kpi"><div class="label">Tổng BN điều trị</div><div class="value" id="kpi-tong-dieu-tri">114</div><div class="note">Cơ sở I + Cơ sở II</div></div>
      <div class="card kcb-kpi"><div class="label">Khám</div><div class="value" id="kpi-kham">17</div><div class="note">Lượt trong ngày</div></div>
      <div class="card kcb-kpi"><div class="label">BN ngoại trú</div><div class="value" id="kpi-ngoai-tru">61</div><div class="note">Đang điều trị</div></div>
      <div class="card kcb-kpi"><div class="label">BN nội trú</div><div class="value" id="kpi-noi-tru">36</div><div class="note">Đang điều trị</div></div>
      <div class="card kcb-kpi"><div class="label">Nội trú ban ngày</div><div class="value" id="kpi-noi-tru-ban-ngay">17</div><div class="note">YHCT/PHCN</div></div>
      <div class="card kcb-kpi"><div class="label">Thủ thuật</div><div class="value" id="kpi-thu-thuat">343</div><div class="note">Tổng trong ngày</div></div>
    </div>

    <div class="card c12" style="margin-bottom:12px">
      <div class="section-title">Bảng chi tiết hoạt động khám chữa bệnh <small>Thiết kế tối ưu cho màn hình 65 inch</small></div>
      <div class="kcb-table-wrap">
        <table class="kcb-table">
          <thead>
            <tr>
              <th rowspan="3" style="width:45px">TT</th>
              <th rowspan="3" style="width:125px">Cơ sở</th>
              <th rowspan="3" style="width:75px">ĐVT</th>
              <th rowspan="3" style="width:95px">Tổng BN<br>điều trị</th>
              <th colspan="7" class="group">Số liệu</th>
              <th rowspan="3" style="width:185px">Ghi chú</th>
            </tr>
            <tr>
              <th colspan="3">Khoa Khám bệnh</th>
              <th colspan="2">Khoa Nội TH - Nhi - An dưỡng</th>
              <th rowspan="2">Khoa XN -<br>CĐHA - TDCN</th>
              <th rowspan="2">Thủ thuật</th>
            </tr>
            <tr>
              <th>Khám</th>
              <th>Kê đơn<br>thuốc thang</th>
              <th>BN ngoại trú</th>
              <th>BN nội trú</th>
              <th>Nội trú<br>ban ngày</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td><td><b>Cơ sở I</b></td><td>Lượt</td>
              <td class="strong" id="cs1-tong">90</td>
              <td id="cs1-kham">11</td><td id="cs1-kedon">1</td><td id="cs1-ngoai">51</td>
              <td id="cs1-noi">25</td><td id="cs1-banngay">14</td><td id="cs1-cls">5</td><td id="cs1-thuthuat">273</td>
              <td class="service-note">+ DV YHCT: <b id="cs1-yhct">67</b><br>+ DV PHCN: <b id="cs1-phcn">204</b><br>+ Cấy chỉ KK: <b id="cs1-caychi">00</b></td>
            </tr>
            <tr>
              <td>2</td><td><b>Cơ sở II</b></td><td>Lượt</td>
              <td class="strong" id="cs2-tong">24</td>
              <td id="cs2-kham">6</td><td id="cs2-kedon">4</td><td id="cs2-ngoai">10</td>
              <td id="cs2-noi">11</td><td id="cs2-banngay">3</td><td id="cs2-cls">0</td><td id="cs2-thuthuat">70</td>
              <td class="service-note">+ DV YHCT: <b id="cs2-yhct">33</b><br>+ DV PHCN: <b id="cs2-phcn">37</b><br>+ Cấy chỉ KK: <b id="cs2-caychi">02</b></td>
            </tr>
            <tr class="total-row">
              <td colspan="3" style="text-align:center">TỔNG</td>
              <td class="strong" id="sum-tong">114</td>
              <td class="sum" id="sum-kham">17</td><td class="sum" id="sum-kedon">5</td><td class="sum" id="sum-ngoai">61</td>
              <td class="sum" id="sum-noi">36</td><td class="sum" id="sum-banngay">17</td><td class="sum" id="sum-cls">5</td><td class="sum" id="sum-thuthuat">343</td>
              <td></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="kcb-mini-grid">
      <div class="kcb-mini">
        <h3>Dịch vụ Y học cổ truyền</h3>
        <div class="big" id="mini-yhct">100</div>
        <div class="rows">Cơ sở I <b id="mini-yhct1">67</b><br>Cơ sở II <b id="mini-yhct2">33</b></div>
      </div>
      <div class="kcb-mini">
        <h3>Dịch vụ Phục hồi chức năng</h3>
        <div class="big" id="mini-phcn">241</div>
        <div class="rows">Cơ sở I <b id="mini-phcn1">204</b><br>Cơ sở II <b id="mini-phcn2">37</b></div>
      </div>
      <div class="kcb-mini">
        <h3>Điều trị Phong - Da liễu</h3>
        <div class="big" id="mini-phong-quanly">43</div>
        <div class="rows">BN đang quản lý <b id="mini-phong-quanly2">43</b><br>BN nội trú <b id="mini-phong-noitru">14</b></div>
      </div>
      <div class="kcb-mini">
        <h3>Kiểm soát nhiễm khuẩn</h3>
        <div class="big"><span id="mini-racthai">293</span> <span style="font-size:14px;color:var(--muted)">Kg</span></div>
        <div class="rows">Rác thải y tế trong ngày<br>Cấy chỉ: <b id="mini-caychi">02</b></div>
      </div>
    </div>
  </section>

  <section id="infra" class="page">
    <div class="grid">
      <div class="card kpi"><div class="label">Thiết bị đang giám sát</div><div class="value" id="kpi-infra-monitored">128</div><div class="sub">Server, switch, firewall, AP, PC</div></div>
      <div class="card kpi"><div class="label">Online</div><div class="value green" id="kpi-infra-online">124</div><div class="sub">96.9% thiết bị</div></div>
      <div class="card kpi"><div class="label">Cảnh báo</div><div class="value yellow" id="kpi-infra-warning">3</div><div class="sub">2 hiệu năng, 1 đường truyền</div></div>
      <div class="card kpi"><div class="label">Mất kết nối</div><div class="value red" id="kpi-infra-offline">1</div><div class="sub">Máy trạm Khoa PHCN</div></div>
      <div class="card kpi"><div class="label">MTTR tháng</div><div class="value" id="kpi-infra-mttr">18'</div><div class="sub">Thời gian xử lý TB</div></div>
      <div class="card kpi"><div class="label">Uptime tháng</div><div class="value green" id="kpi-infra-uptime">99.72%</div><div class="sub">Dịch vụ trọng yếu</div></div>

      <div class="card c12">
        <div class="section-title">Giám sát tập trung hạ tầng CNTT <small>Trạng thái theo thời gian thực</small></div>
        <div class="infra-grid">
          <div class="device"><div class="top"><b>Server HIS</b><span class="badge">Online</span></div><div class="meta" id="dev-meta-his">CPU 34% · RAM 61%<br>Disk 58% · Uptime 47d</div><svg class="spark" viewBox="0 0 120 36"><polyline points="0,28 15,20 30,22 45,15 60,19 75,12 90,17 105,10 120,13" class="line"/></svg></div>
          <div class="device"><div class="top"><b>Server PACS</b><span class="badge warn">Warning</span></div><div class="meta" id="dev-meta-pacs">CPU 41% · RAM 72%<br>Disk 88% · Uptime 47d</div><svg class="spark" viewBox="0 0 120 36"><polyline points="0,26 15,25 30,22 45,20 60,18 75,17 90,14 105,12 120,9" class="line2"/></svg></div>
          <div class="device"><div class="top"><b>Firewall</b><span class="badge">Online</span></div><div class="meta" id="dev-meta-fw">Sessions 1,428<br>WAN1 62Mbps · WAN2 31Mbps</div><svg class="spark" viewBox="0 0 120 36"><polyline points="0,24 15,16 30,19 45,10 60,13 75,18 90,12 105,15 120,11" class="line"/></svg></div>
          <div class="device"><div class="top"><b>Core Switch</b><span class="badge">Online</span></div><div class="meta">24/24 ports up<br>Traffic 412Mbps · PoE 0%</div><svg class="spark" viewBox="0 0 120 36"><polyline points="0,25 15,22 30,23 45,20 60,18 75,19 90,17 105,16 120,15" class="line2"/></svg></div>
          <div class="device"><div class="top"><b>Database HIS</b><span class="badge">Online</span></div><div class="meta">Connections 84<br>Avg query 31ms · Backup OK</div><svg class="spark" viewBox="0 0 120 36"><polyline points="0,22 15,17 30,21 45,14 60,16 75,12 90,18 105,13 120,16" class="line"/></svg></div>
          <div class="device"><div class="top"><b>Internet 1</b><span class="badge">Online</span></div><div class="meta">Latency 12ms<br>Loss 0.0% · Utilization 48%</div><svg class="spark" viewBox="0 0 120 36"><polyline points="0,20 15,19 30,18 45,20 60,17 75,18 90,16 105,17 120,16" class="line2"/></svg></div>
          <div class="device"><div class="top"><b>Internet 2</b><span class="badge warn">Latency</span></div><div class="meta">Latency 68ms<br>Loss 1.7% · Utilization 29%</div><svg class="spark" viewBox="0 0 120 36"><polyline points="0,28 15,25 30,19 45,12 60,17 75,9 90,14 105,8 120,12" class="line2"/></svg></div>
          <div class="device"><div class="top"><b>Backup NAS</b><span class="badge">Online</span></div><div class="meta">Last job 02:10 - Success<br>Used 71% · Restore test OK</div><svg class="spark" viewBox="0 0 120 36"><polyline points="0,26 15,24 30,23 45,21 60,20 75,18 90,16 105,15 120,14" class="line"/></svg></div>
        </div>
      </div>

      <div class="card c8">
        <div class="section-title">Băng thông mạng & đường truyền Internet <small>24 giờ</small></div>
        <div class="chart">
          <svg viewBox="0 0 760 220" preserveAspectRatio="none">
            <line x1="40" y1="20" x2="40" y2="190" class="axis"/><line x1="40" y1="190" x2="740" y2="190" class="axis"/>
            <g class="gridline"><line x1="40" y1="55" x2="740" y2="55"/><line x1="40" y1="95" x2="740" y2="95"/><line x1="40" y1="135" x2="740" y2="135"/></g>
            <polyline points="40,160 95,145 150,150 205,120 260,135 315,95 370,110 425,72 480,86 535,62 590,77 645,56 700,70 740,60" class="line"/>
            <polyline points="40,178 95,170 150,172 205,165 260,160 315,148 370,151 425,138 480,144 535,130 590,136 645,126 700,133 740,128" class="line2"/>
          </svg>
        </div>
        <div class="legend"><span class="lg"><i style="background:#20c6b7"></i>WAN1</span><span class="lg"><i style="background:#39a0ff"></i>WAN2</span></div>
      </div>
      <div class="card c4">
        <div class="section-title">Phân bố sự cố tháng</div>
        <table class="table">
          <tr><th>Nhóm</th><th>Số sự cố</th><th>MTTR</th></tr>
          <tr><td>Mạng</td><td>7</td><td>16'</td></tr>
          <tr><td>Máy chủ</td><td>3</td><td>24'</td></tr>
          <tr><td>Ứng dụng</td><td>5</td><td>19'</td></tr>
          <tr><td>Máy trạm</td><td>11</td><td>12'</td></tr>
          <tr><td>ATTT</td><td>2</td><td>31'</td></tr>
        </table>
      </div>
    </div>
  </section>

  <section id="alerts" class="page">
    <div class="grid">
      <div class="card kpi"><div class="label">Cảnh báo 24h</div><div class="value" id="kpi-alerts-24h">18</div><div class="sub">Tất cả mức độ</div></div>
      <div class="card kpi"><div class="label">Nghiêm trọng</div><div class="value red" id="kpi-alerts-critical">2</div><div class="sub">Cần xử lý ngay</div></div>
      <div class="card kpi"><div class="label">Đã xác nhận</div><div class="value blue" id="kpi-alerts-ack">13</div><div class="sub">72% tổng cảnh báo</div></div>
      <div class="card kpi"><div class="label">Đã khắc phục</div><div class="value green" id="kpi-alerts-resolved">11</div><div class="sub">Trong 24 giờ</div></div>
      <div class="card kpi"><div class="label">Đăng nhập thất bại</div><div class="value" id="kpi-alerts-failed-login">23</div><div class="sub">Từ 7 nguồn</div></div>
      <div class="card kpi"><div class="label">Backup lỗi</div><div class="value green">0</div><div class="sub">7 ngày gần nhất</div></div>

      <div class="card c8">
        <div class="section-title">Nhật ký cảnh báo & xử lý sự cố <small>Ưu tiên theo mức độ</small></div>
        <table class="table">
          <tr><th>Thời gian</th><th>Mức độ</th><th>Sự kiện</th><th>Đối tượng</th><th>Trạng thái</th></tr>
          <tr><td>14:12</td><td class="red">Nghiêm trọng</td><td>Dung lượng ổ đĩa > 85%</td><td>Server PACS</td><td class="yellow">Đang xử lý</td></tr>
          <tr><td>13:48</td><td class="yellow">Cảnh báo</td><td>Thời gian chờ khám > ngưỡng</td><td>Phòng khám 01</td><td class="blue">Đã xác nhận</td></tr>
          <tr><td>13:25</td><td class="yellow">Cảnh báo</td><td>Độ trễ Internet tăng cao</td><td>WAN2</td><td class="blue">Theo dõi</td></tr>
          <tr><td>12:54</td><td class="red">Nghiêm trọng</td><td>Máy trạm mất kết nối</td><td>Khoa PHCN - PC07</td><td class="green">Đã khắc phục</td></tr>
          <tr><td>11:42</td><td class="blue">Thông tin</td><td>5 lần đăng nhập thất bại</td><td>EMR Admin</td><td class="green">Đóng</td></tr>
        </table>
      </div>

      <div class="card c4">
        <div class="section-title">Kênh gửi cảnh báo</div>
        <div class="status-list">
          <div class="status"><span class="name">Email</span><span class="pill ok">Hoạt động</span></div>
          <div class="status"><span class="name">Zalo OA</span><span class="pill ok">Hoạt động</span></div>
        </div>
      </div>

      <div class="card c6" style="grid-column:span 6">
        <div class="section-title">An toàn thông tin</div>
        <table class="table">
          <tr><td>Sự kiện đăng nhập bất thường</td><td class="yellow">3</td></tr>
          <tr><td>Dò quét tài khoản</td><td class="yellow">1</td></tr>
          <tr><td>Mã độc phát hiện</td><td class="green">0</td></tr>
          <tr><td>Thay đổi cấu hình trái phép</td><td class="green">0</td></tr>
          <tr><td>Sự kiện Firewall/IPS</td><td class="blue" id="alerts-fw-events">37</td></tr>
        </table>
      </div>
      <div class="card c6" style="grid-column:span 6">
        <div class="section-title">Sao lưu & khả năng phục hồi</div>
        <table class="table">
          <tr><td>Backup HIS</td><td class="green">Thành công 02:05</td></tr>
          <tr><td>Backup LIS</td><td class="green">Thành công 02:08</td></tr>
          <tr><td>Backup PACS</td><td class="green">Thành công 02:10</td></tr>
          <tr><td>Kiểm tra restore gần nhất</td><td class="green">Đạt</td></tr>
          <tr><td>Dung lượng kho backup</td><td class="yellow">71%</td></tr>
        </table>
      </div>
    </div>
  </section>

  <div class="footer">Dashboard IOC Mini • Đã bổ sung bộ lọc ngày trên toàn hệ thống • Sẵn sàng kết nối API HIS</div>
</div>
<script>
let CURRENT_SELECTED_DATE = "";

function showPage(id,btn){
 document.querySelectorAll('.page').forEach(x=>x.classList.remove('active'));
 document.querySelectorAll('.tab').forEach(x=>x.classList.remove('active'));
 document.getElementById(id).classList.add('active'); btn.classList.add('active');
}

/*
  KẾT NỐI API / CỔNG QUẢN TRỊ:
  Mặc định trỏ đến api-kcb.php, hỗ trợ fallback LocalStorage.
*/
const KCB_API_URL = "api-kcb.php";

function fmtDate(d){
  return String(d.getDate()).padStart(2,'0') + '/' +
         String(d.getMonth()+1).padStart(2,'0') + '/' + d.getFullYear();
}
function fmtDateIso(d){
  return d.getFullYear() + '-' +
         String(d.getMonth()+1).padStart(2,'0') + '-' +
         String(d.getDate()).padStart(2,'0');
}
function fmtTime(d){
  return String(d.getHours()).padStart(2,'0') + ':' +
         String(d.getMinutes()).padStart(2,'0');
}
function setText(id,val){
  const el=document.getElementById(id);
  if(el && val !== undefined && val !== null) el.textContent=val;
}

// Bộ lọc ngày nhanh (Hôm nay / Hôm qua)
function filterByPreset(type){
  document.querySelectorAll('.btn-filter').forEach(b=>b.classList.remove('active'));
  const now = new Date();
  if(type === 'yesterday'){
    now.setDate(now.getDate() - 1);
    document.getElementById('btn-filter-yesterday')?.classList.add('active');
  } else {
    document.getElementById('btn-filter-today')?.classList.add('active');
  }
  const iso = fmtDateIso(now);
  const datePicker = document.getElementById('global-date-input');
  if(datePicker) datePicker.value = iso;
  onGlobalDateChange(iso);
}

function onGlobalDateChange(isoVal){
  if(!isoVal) return;
  const parts = isoVal.split('-');
  CURRENT_SELECTED_DATE = parts[2] + '/' + parts[1] + '/' + parts[0];
  setText('lbl-viewing-date', CURRENT_SELECTED_DATE);
  setText('kcb-current-date', CURRENT_SELECTED_DATE);
  loadKcbFromHis(CURRENT_SELECTED_DATE);
}

function renderKcb(data){
  if (!data) return;
  const a=data.coso1||{}, b=data.coso2||{};
  const sum=(k)=>Number(a[k]||0)+Number(b[k]||0);

  const displayDate = data.date || CURRENT_SELECTED_DATE || fmtDate(new Date());
  setText('kcb-current-date', displayDate);
  setText('lbl-viewing-date', displayDate);
  if(data.updated_at) setText('kcb-updated-time', data.updated_at);

  const keys=['tong','kham','kedon','ngoai','noi','banngay','cls','thuthuat','yhct','phcn','caychi'];
  keys.forEach(k=>setText('cs1-'+k,a[k]));
  keys.forEach(k=>setText('cs2-'+k,b[k]));

  ['tong','kham','kedon','ngoai','noi','banngay','cls','thuthuat'].forEach(k=>setText('sum-'+k,sum(k)));

  // KCB KPIs
  setText('kpi-tong-dieu-tri',sum('tong'));
  setText('kpi-kham',sum('kham'));
  setText('kpi-ngoai-tru',sum('ngoai'));
  setText('kpi-noi-tru',sum('noi'));
  setText('kpi-noi-tru-ban-ngay',sum('banngay'));
  setText('kpi-thu-thuat',sum('thuthuat'));

  setText('mini-yhct',sum('yhct')); setText('mini-yhct1',a.yhct); setText('mini-yhct2',b.yhct);
  setText('mini-phcn',sum('phcn')); setText('mini-phcn1',a.phcn); setText('mini-phcn2',b.phcn);
  setText('mini-caychi',String(sum('caychi')).padStart(2,'0'));

  if(data.phong_dalieu){
    setText('mini-phong-quanly',data.phong_dalieu.dang_quan_ly);
    setText('mini-phong-quanly2',data.phong_dalieu.dang_quan_ly);
    setText('mini-phong-noitru',data.phong_dalieu.noi_tru);
  }
  if(data.rac_thai_y_te_kg !== undefined) setText('mini-racthai',data.rac_thai_y_te_kg);

  // Cập nhật phân hệ Tổng quan điều hành nếu có trong payload
  if(data.overview){
    const o = data.overview;
    if(o.kham_thang) setText('kpi-overview-kham-thang', Number(o.kham_thang).toLocaleString('vi-VN'));
    if(o.kham_ngay) setText('kpi-overview-kham-ngay', o.kham_ngay);
    if(o.ra_vien) setText('kpi-overview-ra-vien', o.ra_vien);
    if(o.dang_dieu_tri) setText('kpi-overview-dang-dieu-tri', o.dang_dieu_tri);
    if(o.cong_suat_giuong){
      setText('kpi-overview-cong-suat', o.cong_suat_giuong + '%');
      setText('overview-gauge-val', o.cong_suat_giuong + '%');
      const bar = document.getElementById('overview-bed-bar');
      if(bar) bar.style.width = o.cong_suat_giuong + '%';
    }
    if(o.emr_rate) setText('kpi-overview-emr', o.emr_rate + '%');
    if(o.it_availability) setText('kpi-overview-cntt', o.it_availability + '%');
    if(o.emr_digital) setText('cds-table-emr', o.emr_digital + '%');
    if(o.digital_sign) setText('cds-table-sign', o.digital_sign + '%');
    if(o.data_link) setText('cds-table-sync', o.data_link + '%');
    if(o.cashless) setText('cds-table-cashless', o.cashless + '%');
    if(o.satisfaction) setText('cds-table-satisfaction', o.satisfaction + '%');
    if(o.cccd_count) setText('nv-pill-cccd', o.cccd_count);
    if(o.driving_license_count) setText('nv-pill-laixe', o.driving_license_count);
    if(o.bhxh_tt25_rate) setText('nv-pill-bhxh', o.bhxh_tt25_rate + '%');
    if(o.emhc_count) setText('nv-pill-emhc', o.emhc_count);
    if(o.ngay_dt_tb) setText('overview-avg-days', o.ngay_dt_tb);
  }

  // Cập nhật phân hệ Hạ tầng
  if(data.infra){
    const inf = data.infra;
    if(inf.monitored) setText('kpi-infra-monitored', inf.monitored);
    if(inf.online) setText('kpi-infra-online', inf.online);
    if(inf.warning) setText('kpi-infra-warning', inf.warning);
    if(inf.offline) setText('kpi-infra-offline', inf.offline);
    if(inf.mttr) setText('kpi-infra-mttr', inf.mttr);
    if(inf.uptime) setText('kpi-infra-uptime', inf.uptime);
  }

  // Cập nhật phân hệ Cảnh báo
  if(data.alerts_security){
    const sec = data.alerts_security;
    if(sec.alerts_24h) setText('kpi-alerts-24h', sec.alerts_24h);
    if(sec.critical) setText('kpi-alerts-critical', sec.critical);
    if(sec.acknowledged) setText('kpi-alerts-ack', sec.acknowledged);
    if(sec.resolved) setText('kpi-alerts-resolved', sec.resolved);
    if(sec.failed_logins) setText('kpi-alerts-failed-login', sec.failed_logins);
    if(sec.firewall_events) setText('alerts-fw-events', sec.firewall_events);
  }
}

function showIocLoader(msg = "ĐANG ĐỒNG BỘ DỮ LIỆU ĐIỀU HÀNH...") {
  const overlay = document.getElementById('ioc-loader-overlay');
  const msgEl = document.getElementById('ioc-loader-msg');
  if (msgEl) msgEl.textContent = msg;
  if (overlay) overlay.classList.add('active');
}

function hideIocLoader() {
  const overlay = document.getElementById('ioc-loader-overlay');
  if (overlay) overlay.classList.remove('active');
}

async function loadKcbFromHis(specificDate, isSilent = false){
  const now = new Date();
  if(!CURRENT_SELECTED_DATE) {
    CURRENT_SELECTED_DATE = fmtDate(now);
    setText('lbl-viewing-date', CURRENT_SELECTED_DATE);
    setText('kcb-current-date', CURRENT_SELECTED_DATE);
    const datePicker = document.getElementById('global-date-input');
    if(datePicker && !datePicker.value) datePicker.value = fmtDateIso(now);
  }

  const queryDate = specificDate || CURRENT_SELECTED_DATE;
  const url = KCB_API_URL ? `${KCB_API_URL}?date=${encodeURIComponent(queryDate)}` : "";

  if(!isSilent) {
    showIocLoader(`ĐANG TẢI SỐ LIỆU NGÀY ${queryDate}...`);
  }

  const startTime = Date.now();

  if(url){
    try{
      const r=await fetch(url, {cache:'no-store'});
      if(!r.ok) throw new Error('HTTP '+r.status);
      const res=await r.json();
      const data = res.data ? res.data : res;
      renderKcb(data);
      setText('kcb-source','Đã đồng bộ ngày ' + queryDate);
      setText('kcb-source-daily','Đã đồng bộ từ API');
    }catch(e){
      // Fallback sang LocalStorage nếu không có PHP server
      const local = localStorage.getItem('ioc_dakto_data');
      if (local) {
        try {
          const data = JSON.parse(local);
          renderKcb(data);
          setText('kcb-source','Dữ liệu lưu trình duyệt (LocalStorage)');
          setText('kcb-source-daily','Dữ liệu lưu trình duyệt (LocalStorage)');
        } catch(err){}
      } else {
        setText('kcb-source','Dữ liệu minh họa • Sẵn sàng kết nối');
      }
      console.warn(e);
    } finally {
      if(!isSilent) {
        const elapsed = Date.now() - startTime;
        const delay = Math.max(0, 450 - elapsed);
        setTimeout(hideIocLoader, delay);
      }
    }
  }
}

// State Lưới Lịch & Lựa chọn
let CAL_VIEW_DATE = new Date();

function toggleCalendarGrid(popupId, inputId) {
  const popup = document.getElementById(popupId);
  if (!popup) return;
  const isShow = popup.classList.contains('show');
  document.querySelectorAll('.calendar-grid-popup').forEach(p => p.classList.remove('show'));
  if (!isShow) {
    const inputVal = document.getElementById(inputId)?.value;
    if (inputVal) {
      const parts = inputVal.split('-');
      CAL_VIEW_DATE = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
    } else {
      CAL_VIEW_DATE = new Date();
    }
    renderCalendarGrid(popupId, inputId);
    popup.classList.add('show');
  }
}

function calNavigateMonth(step, popupId, inputId) {
  CAL_VIEW_DATE.setMonth(CAL_VIEW_DATE.getMonth() + step);
  renderCalendarGrid(popupId, inputId);
}

function renderCalendarGrid(popupId, inputId) {
  const titleEl = document.getElementById('cal-month-title-index');
  const gridEl = document.getElementById('cal-days-grid-index');
  if (!titleEl || !gridEl) return;

  const year = CAL_VIEW_DATE.getFullYear();
  const month = CAL_VIEW_DATE.getMonth();
  
  titleEl.textContent = `Tháng ${String(month + 1).padStart(2, '0')}/${year}`;

  const firstDayIndex = new Date(year, month, 1).getDay();
  const startOffset = firstDayIndex === 0 ? 6 : firstDayIndex - 1;
  const lastDate = new Date(year, month + 1, 0).getDate();
  const prevMonthLastDate = new Date(year, month, 0).getDate();

  const selectedInputVal = document.getElementById(inputId)?.value;
  const todayStr = fmtDateIso(new Date());

  let html = '';

  // Các ngày của tháng trước
  for (let i = startOffset; i > 0; i--) {
    const d = prevMonthLastDate - i + 1;
    html += `<div class="cal-day-cell other-month" onclick="selectCalendarDate(new Date(${year}, ${month - 1}, ${d}), '${popupId}', '${inputId}')">${d}</div>`;
  }

  // Các ngày trong tháng hiện tại
  for (let i = 1; i <= lastDate; i++) {
    const curIso = `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
    const isSelected = selectedInputVal === curIso;
    const isToday = todayStr === curIso;

    let cls = 'cal-day-cell';
    if (isSelected) cls += ' selected';
    if (isToday) cls += ' today';

    html += `<div class="${cls}" onclick="selectCalendarDate(new Date(${year}, ${month}, ${i}), '${popupId}', '${inputId}')">${i}</div>`;
  }

  // Các ngày của tháng kế tiếp
  const totalRendered = startOffset + lastDate;
  const remaining = (7 - (totalRendered % 7)) % 7;
  for (let i = 1; i <= remaining; i++) {
    html += `<div class="cal-day-cell other-month" onclick="selectCalendarDate(new Date(${year}, ${month + 1}, ${i}), '${popupId}', '${inputId}')">${i}</div>`;
  }

  gridEl.innerHTML = html;
}

function selectCalendarDate(dateObj, popupId, inputId) {
  const iso = fmtDateIso(dateObj);
  const input = document.getElementById(inputId);
  if (input) {
    input.value = iso;
    onGlobalDateChange(iso);
  }
  document.getElementById(popupId)?.classList.remove('show');
}

// Dropdown Menu Người dùng & Đăng xuất
function toggleUserDropdown(menuId) {
  const menu = document.getElementById(menuId);
  if (!menu) return;
  const isShow = menu.classList.contains('show');
  document.querySelectorAll('.user-dropdown-menu').forEach(m => m.classList.remove('show'));
  if (!isShow) {
    menu.classList.add('show');
  }
}

// Xử lý Đăng xuất
function handleLogout() {
  showIocLoader('ĐANG ĐĂNG XUẤT HỆ THỐNG...');
  setTimeout(() => {
    window.location.href = <?php echo json_encode(XC_URL . '/admin/logout'); ?>;
  }, 600);
}

// Đóng lịch và menu user khi nhấp chuột ra ngoài
document.addEventListener('click', (e) => {
  if (!e.target.closest('.cal-picker-wrap') && !e.target.closest('.calendar-grid-popup')) {
    document.querySelectorAll('.calendar-grid-popup').forEach(p => p.classList.remove('show'));
  }
  if (!e.target.closest('.user-menu-wrap') && !e.target.closest('.user-dropdown-menu')) {
    document.querySelectorAll('.user-dropdown-menu').forEach(m => m.classList.remove('show'));
  }
});

// Khởi chạy ban đầu
const initialIso = fmtDateIso(new Date());
const globalDateInput = document.getElementById('global-date-input');
if(globalDateInput) globalDateInput.value = initialIso;
onGlobalDateChange(initialIso);

setInterval(() => {
  // Chỉ tự động tải lại nếu đang xem ngày hôm nay
  const todayFormatted = fmtDate(new Date());
  if(CURRENT_SELECTED_DATE === todayFormatted) {
    loadKcbFromHis(CURRENT_SELECTED_DATE, true);
  }
}, 20000);
</script>
</body>
</html>
