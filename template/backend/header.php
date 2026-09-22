<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trung Tâm Giám Sát & Điều Hành Y Tế Số </title>
  <!-- Google Fonts & Font Awesome 6 -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="<?php echo XC_URL;?>/template/backend/asset/css/style.css">
</head>
<body>

  <!-- Loading State: Spinner tinh tế + Dấu chấm nhảy nhịp nhàng -->
  <div class="loading-overlay" id="loadingOverlay">
    <div class="spinner-ring"></div>
    <div class="loading-text-wrapper">
      <span id="loadingBaseText">Đang xử lý</span>
      <div class="loading-dots">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
  </div>

  <div class="app-container">

    <!-- ====== SIDEBAR (BÊN TRÁI - THU GỌN CHỈ CÒN 1 NÚT) ====== -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <a href="#" class="brand">
          <div class="brand-icon"><i class="fa-solid fa-heart-pulse"></i></div>
          <span class="brand-text">MED-IOC PORTAL</span>
        </a>
        <button class="sidebar-toggle-btn" id="sidebarToggleBtn" title="Đóng / Mở Menu">
          <i class="fa-solid fa-bars"></i>
        </button>
      </div>

      <ul class="sidebar-menu">
        <li class="menu-title">Chỉ huy & Điều hành</li>
        
        <li class="menu-item active">
          <a class="menu-link" onclick="triggerPageLoad('Trung tâm IOC')">
            <i class="icon fa-solid fa-chart-pie"></i>
            <span class="menu-text">Trung tâm IOC</span>
          </a>
        </li>

        <li class="menu-item has-dropdown">
          <div class="menu-link dropdown-toggle">
            <i class="icon fa-solid fa-hospital-user"></i>
            <span class="menu-text">Báo cáo số liệu</span>
            <i class="fa-solid fa-chevron-down arrow-icon"></i>
          </div>
          <ul class="submenu">
            <li><a href="<?php echo XC_URL; ?>/backend/outpatient"><i class="fa-solid fa-caret-right"></i> Số liệu khám ngoại trú</a></li>
            <li><a href="<?php echo XC_URL; ?>/backend/inpatient"><i class="fa-solid fa-caret-right"></i> Điều trị nội trú</a></li>
            <li><a href="<?php echo XC_URL; ?>/backend/lisReport"><i class="fa-solid fa-caret-right"></i> Số liệu xét nghiệm</a></li>
            <li><a href="<?php echo XC_URL; ?>/backend/risReport"><i class="fa-solid fa-caret-right"></i> Số liệu CĐHA</a></li>
            <li><a href="<?php echo XC_URL; ?>/backend/emrReport"><i class="fa-solid fa-caret-right"></i> Số liệu EMR</a></li>
          </ul>
        </li>

        <li class="menu-item has-dropdown">
          <div class="menu-link dropdown-toggle">
            <i class="icon fa-solid fa-pills"></i>
            <span class="menu-text">Dược & Vật Tư</span>
            <i class="fa-solid fa-chevron-down arrow-icon"></i>
          </div>
          <ul class="submenu">
            <li><a onclick="triggerPageLoad('Danh mục thuốc BHYT')"><i class="fa-solid fa-caret-right"></i> Danh mục thuốc BHYT</a></li>
            <li><a onclick="triggerPageLoad('Tồn kho dược thông minh')"><i class="fa-solid fa-caret-right"></i> Cảnh báo tồn kho & hạn dùng</a></li>
          </ul>
        </li>

        <li class="menu-title">Liên thông & Dữ liệu</li>

        <li class="menu-item">
          <a class="menu-link" onclick="triggerPageLoad('Cổng BHYT XML 130')">
            <i class="icon fa-solid fa-network-wired"></i>
            <span class="menu-text">Cổng BHYT (XML QĐ 130)</span>
          </a>
        </li>

        <li class="menu-item">
          <a class="menu-link" onclick="triggerPageLoad('Cấu hình tham số HIS')">
            <i class="icon fa-solid fa-sliders"></i>
            <span class="menu-text">Cấu hình tham số HIS</span>
          </a>
        </li>

        <li class="menu-item">
          <a class="menu-link" href="<?php echo XC_URL; ?>/backend/lisIntegration">
            <i class="icon fa-solid fa-flask-vial"></i>
            <span class="menu-text">Đồng bộ dữ liệu LIS</span>
          </a>
        </li>
      </ul>
    </aside>

    <!-- ====== MAIN CONTENT ====== -->
    <div class="main-wrapper">
      
      <!-- Top Navbar -->
      <header class="navbar">
        <div class="breadcrumb">
          <i class="fa-solid fa-house-medical"></i>
          <span>/</span>
          <a href="#">Hệ thống điều hành</a>
          <span>/</span>
          <span id="currentBreadcrumb">Quản lý tiếp nhận số</span>
        </div>

        <div class="nav-actions">
          <button class="sidebar-toggle-btn" title="Thông báo hệ thống">
            <i class="fa-regular fa-bell"></i>
          </button>

          <!-- User Profile Dropdown (Top Right) -->
          <div class="user-profile">
            <button class="user-btn" id="userMenuBtn">
              <div class="avatar">BS</div>
              <div class="user-info">
                <div class="user-name">BS. CKII Lê Hoàng</div>
                <div class="user-role">Quản trị viên hệ thống</div>
              </div>
              <i class="fa-solid fa-angle-down" style="font-size: 12px; color: var(--text-muted);"></i>
            </button>

            <ul class="user-menu-dropdown" id="userMenuDropdown">
              <li><a href="#"><i class="fa-regular fa-id-badge"></i> Thông tin cá nhân</a></li>
              <li><a href="javascript:void(0)" id="btnOpenChangePass"><i class="fa-solid fa-key"></i> Đổi mật khẩu</a></li>
              <li><a href="#"><i class="fa-solid fa-shield-halved"></i> Phân quyền dữ liệu</a></li>
              <li class="dropdown-divider"></li>
              <li><a href="javascript:void(0)" onclick="handleLogout()" style="color: var(--danger);"><i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng xuất</a></li>
            </ul>
          </div>
        </div>
      </header>
