<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ĐĂNG NHẬP | TRUNG TÂM ĐIỀU HÀNH THÔNG MINH – BỆNH VIỆN ĐA KHOA KHU VỰC ĐĂK TÔ</title>
  
  <!-- Favicon & Meta -->
  <meta name="description" content="Trung tâm Điều hành Thông minh (IOC) - Bệnh viện Đa khoa Khu vực Đăk Tô. Hệ thống giám sát, phân tích dữ liệu y tế và điều hành thời gian thực.">
  <meta name="keywords" content="IOC Đăk Tô, Bệnh viện Đa khoa khu vực Đăk Tô, Intelligent Operation Center, AI Medical Dashboard">
  
  <!-- Google Fonts: Orbitron & Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- FontAwesome 6 Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <style>
    :root {
      --bg-dark: #040914;
      --bg-surface: rgba(10, 24, 46, 0.72);
      --bg-card: rgba(13, 31, 58, 0.65);
      --border-cyan: rgba(0, 242, 254, 0.28);
      --border-glow: #00f2fe;
      --cyan-neon: #00f2fe;
      --blue-accent: #4facfe;
      --emerald-glow: #10b981;
      --warn-amber: #f59e0b;
      --danger-red: #ef4444;
      --purple-ai: #a855f7;
      --text-main: #f0f6fc;
      --text-muted: #8ea3bf;
      --text-dim: #516d8f;
      --glow-shadow: 0 0 35px rgba(0, 242, 254, 0.25);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      user-select: none;
    }

    body {
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
      background-color: var(--bg-dark);
      color: var(--text-main);
      min-height: 100vh;
      overflow-x: hidden;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      position: relative;
    }

    /* ============================================================
       CANVAS & BACKGROUND SCI-FI EFFECTS
    ============================================================ */
    #ai-network-canvas {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      z-index: 0;
      pointer-events: auto;
    }

    .cyber-grid-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: 
        linear-gradient(to right, rgba(0, 242, 254, 0.03) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(0, 242, 254, 0.03) 1px, transparent 1px);
      background-size: 50px 50px;
      pointer-events: none;
      z-index: 1;
    }

    .ambient-glow {
      position: fixed;
      width: 600px;
      height: 600px;
      border-radius: 50%;
      filter: blur(140px);
      pointer-events: none;
      z-index: 1;
      opacity: 0.18;
      animation: ambientPulse 12s infinite alternate ease-in-out;
    }
    .ambient-1 {
      top: -150px;
      left: -100px;
      background: radial-gradient(circle, #00f2fe, #4facfe);
    }
    .ambient-2 {
      bottom: -150px;
      right: -100px;
      background: radial-gradient(circle, #a855f7, #3b82f6);
    }
    .ambient-3 {
      top: 40%;
      right: 25%;
      width: 450px;
      height: 450px;
      background: radial-gradient(circle, #10b981, #00f2fe);
      opacity: 0.12;
    }

    @keyframes ambientPulse {
      0% { transform: scale(1) translate(0, 0); opacity: 0.15; }
      50% { transform: scale(1.15) translate(30px, -20px); opacity: 0.24; }
      100% { transform: scale(0.9) translate(-20px, 30px); opacity: 0.15; }
    }

    /* Scanline horizontal sweep */
    .scanline-beam {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: linear-gradient(90deg, transparent, rgba(0, 242, 254, 0.8), transparent);
      box-shadow: 0 0 15px var(--cyan-neon);
      opacity: 0.4;
      pointer-events: none;
      z-index: 2;
      animation: scanSweep 9s linear infinite;
    }

    @keyframes scanSweep {
      0% { top: -10px; opacity: 0; }
      10% { opacity: 0.5; }
      90% { opacity: 0.5; }
      100% { top: 100vh; opacity: 0; }
    }

    /* ============================================================
       TOP HUD STATUS BAR
    ============================================================ */
    .top-hud {
      position: relative;
      z-index: 10;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 16px 36px;
      background: linear-gradient(180deg, rgba(6, 17, 34, 0.88), rgba(6, 17, 34, 0.2));
      border-bottom: 1px solid rgba(0, 242, 254, 0.15);
      backdrop-filter: blur(16px);
    }

    .hud-left {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .hospital-emblem {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      background: linear-gradient(135deg, rgba(0, 242, 254, 0.2), rgba(79, 172, 254, 0.3));
      border: 1.5px solid var(--cyan-neon);
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 0 20px rgba(0, 242, 254, 0.35);
      position: relative;
    }

    .hospital-emblem i {
      font-size: 22px;
      color: #00f2fe;
      filter: drop-shadow(0 0 8px #00f2fe);
    }

    .hud-title-group h2 {
      font-size: 15px;
      font-weight: 800;
      letter-spacing: 1px;
      background: linear-gradient(90deg, #ffffff, #c7e5ff);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      text-transform: uppercase;
    }

    .hud-title-group p {
      font-size: 11px;
      color: var(--text-muted);
      letter-spacing: 0.5px;
    }

    .hud-center {
      display: flex;
      align-items: center;
      gap: 24px;
    }

    .telemetry-chip {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 6px 14px;
      border-radius: 20px;
      background: rgba(13, 31, 58, 0.7);
      border: 1px solid rgba(0, 242, 254, 0.2);
      font-size: 12px;
      color: var(--text-muted);
      backdrop-filter: blur(8px);
    }

    .live-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background-color: var(--emerald-glow);
      box-shadow: 0 0 10px var(--emerald-glow);
      animation: pulseDot 2s infinite;
    }

    @keyframes pulseDot {
      0%, 100% { transform: scale(1); opacity: 1; }
      50% { transform: scale(1.35); opacity: 0.6; }
    }

    .ecg-mini {
      display: flex;
      align-items: center;
      gap: 6px;
      font-family: 'Orbitron', monospace;
      font-size: 12px;
      color: var(--cyan-neon);
    }

    .ecg-mini svg {
      width: 55px;
      height: 18px;
      stroke: var(--cyan-neon);
      stroke-width: 2;
      fill: none;
    }

    .hud-right {
      display: flex;
      align-items: center;
      gap: 18px;
    }

    .clock-display {
      text-align: right;
      font-family: 'Orbitron', monospace;
    }

    .clock-time {
      font-size: 16px;
      font-weight: 700;
      color: #fff;
      letter-spacing: 1.5px;
      text-shadow: 0 0 10px rgba(0, 242, 254, 0.5);
    }

    .clock-date {
      font-size: 11px;
      color: var(--text-muted);
    }

    .ai-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 5px 12px;
      border-radius: 8px;
      background: linear-gradient(135deg, rgba(168, 85, 247, 0.2), rgba(0, 242, 254, 0.15));
      border: 1px solid rgba(168, 85, 247, 0.4);
      color: #d8b4fe;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 0.8px;
      text-transform: uppercase;
    }

    .ai-badge i {
      color: #c084fc;
      animation: rotateAi 6s linear infinite;
    }

    @keyframes rotateAi {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* ============================================================
       MAIN CONTENT / LOGIN CONTAINER
    ============================================================ */
    .main-wrapper {
      position: relative;
      z-index: 10;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
      flex: 1;
    }

    .login-box-container {
      width: 100%;
      max-width: 1040px;
      display: grid;
      grid-template-columns: 1.15fr 0.95fr;
      border-radius: 24px;
      background: var(--bg-surface);
      border: 1px solid rgba(0, 242, 254, 0.22);
      box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.65),
        0 0 40px rgba(0, 242, 254, 0.12),
        inset 0 0 20px rgba(0, 242, 254, 0.04);
      backdrop-filter: blur(28px);
      overflow: hidden;
      position: relative;
    }

    /* High-tech Glowing Animated Border Trace */
    .login-box-container::before {
      content: '';
      position: absolute;
      inset: -2px;
      border-radius: 26px;
      padding: 2px;
      background: linear-gradient(135deg, rgba(0, 242, 254, 0.7), transparent 30%, rgba(168, 85, 247, 0.6), transparent 70%, rgba(16, 185, 129, 0.5));
      -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
      -webkit-mask-composite: xor;
      mask-composite: exclude;
      pointer-events: none;
      opacity: 0.85;
    }

    /* LEFT PANEL: IOC OVERVIEW & TELEMETRY */
    .ioc-info-panel {
      padding: 44px 40px;
      background: linear-gradient(160deg, rgba(10, 28, 54, 0.8), rgba(5, 16, 32, 0.95));
      border-right: 1px solid rgba(0, 242, 254, 0.15);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      position: relative;
      overflow: hidden;
    }

    .ioc-info-panel::after {
      content: '';
      position: absolute;
      right: -80px;
      bottom: -80px;
      width: 260px;
      height: 260px;
      background: radial-gradient(circle, rgba(0, 242, 254, 0.1), transparent 70%);
      border-radius: 50%;
      pointer-events: none;
    }

    .brand-hero {
      margin-bottom: 24px;
    }

    .brand-tag {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      background: rgba(0, 242, 254, 0.1);
      border: 1px solid rgba(0, 242, 254, 0.35);
      color: var(--cyan-neon);
      font-size: 11px;
      font-weight: 700;
      padding: 4px 12px;
      border-radius: 999px;
      letter-spacing: 1.2px;
      text-transform: uppercase;
      margin-bottom: 14px;
    }

    .brand-tag i {
      font-size: 10px;
    }

    .hero-heading {
      font-size: 23px;
      font-weight: 800;
      line-height: 1.35;
      letter-spacing: 0.3px;
      background: linear-gradient(135deg, #ffffff 0%, #bde4ff 60%, #00f2fe 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 8px;
    }

    .hero-sub {
      font-size: 14px;
      font-weight: 700;
      color: #38bdf8;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      margin-bottom: 16px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .hero-desc {
      font-size: 13px;
      color: var(--text-muted);
      line-height: 1.65;
    }

    /* IOC Mini Telemetry Cards inside Left Panel */
    .telemetry-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
      margin: 24px 0;
    }

    .telemetry-card {
      background: rgba(15, 37, 68, 0.55);
      border: 1px solid rgba(0, 242, 254, 0.12);
      border-radius: 14px;
      padding: 12px 14px;
      position: relative;
      transition: all 0.3s ease;
      backdrop-filter: blur(10px);
    }

    .telemetry-card:hover {
      border-color: rgba(0, 242, 254, 0.4);
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(0, 242, 254, 0.15);
    }

    .card-label {
      font-size: 11px;
      color: var(--text-muted);
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 6px;
    }

    .card-label i {
      font-size: 13px;
      color: var(--cyan-neon);
    }

    .card-val {
      font-family: 'Orbitron', sans-serif;
      font-size: 18px;
      font-weight: 700;
      color: #fff;
    }

    .card-meta {
      font-size: 10px;
      color: var(--emerald-glow);
      margin-top: 2px;
      display: flex;
      align-items: center;
      gap: 4px;
    }

    /* Radar Holo Circle */
    .radar-container {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 12px 16px;
      background: rgba(4, 15, 29, 0.6);
      border-radius: 14px;
      border: 1px solid rgba(0, 242, 254, 0.15);
    }

    .radar-circle {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      border: 1.5px solid rgba(0, 242, 254, 0.4);
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    .radar-circle::before {
      content: '';
      position: absolute;
      width: 100%;
      height: 100%;
      border-radius: 50%;
      background: conic-gradient(from 0deg, rgba(0, 242, 254, 0.45) 0deg, transparent 90deg);
      animation: radarScan 3s linear infinite;
    }

    .radar-circle::after {
      content: '';
      position: absolute;
      width: 6px;
      height: 6px;
      background: var(--cyan-neon);
      border-radius: 50%;
      box-shadow: 0 0 8px var(--cyan-neon);
    }

    @keyframes radarScan {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    .radar-info h4 {
      font-size: 12px;
      font-weight: 700;
      color: #e2e8f0;
      margin-bottom: 2px;
    }

    .radar-info p {
      font-size: 11px;
      color: var(--text-muted);
    }

    /* RIGHT PANEL: LOGIN FORM */
    .ioc-form-panel {
      padding: 44px 38px;
      background: rgba(8, 21, 41, 0.85);
      display: flex;
      flex-direction: column;
      justify-content: center;
      position: relative;
    }

    .form-header {
      text-align: left;
      margin-bottom: 26px;
    }

    .form-header h3 {
      font-size: 20px;
      font-weight: 800;
      color: #fff;
      letter-spacing: 0.5px;
      margin-bottom: 6px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .form-header h3 i {
      color: var(--cyan-neon);
      font-size: 18px;
      filter: drop-shadow(0 0 6px var(--cyan-neon));
    }

    .form-header p {
      font-size: 12.5px;
      color: var(--text-muted);
    }

    /* Input Field Groups */
    .form-group {
      margin-bottom: 18px;
      position: relative;
    }

    .form-label {
      display: block;
      font-size: 12px;
      font-weight: 600;
      color: #b3c7de;
      margin-bottom: 7px;
      letter-spacing: 0.3px;
    }

    .input-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }

    .input-icon {
      position: absolute;
      left: 16px;
      font-size: 15px;
      color: var(--text-dim);
      transition: color 0.3s ease;
      pointer-events: none;
      z-index: 2;
    }

    .cyber-input {
      width: 100%;
      height: 48px;
      background: rgba(13, 29, 52, 0.8);
      border: 1.5px solid rgba(0, 242, 254, 0.2);
      border-radius: 12px;
      padding: 0 46px 0 44px;
      font-size: 13.5px;
      color: #ffffff;
      font-family: inherit;
      outline: none;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.3);
    }

    .cyber-input::placeholder {
      color: var(--text-dim);
      font-size: 12.5px;
    }

    .cyber-input:focus {
      border-color: var(--cyan-neon);
      background: rgba(18, 38, 66, 0.95);
      box-shadow: 
        0 0 20px rgba(0, 242, 254, 0.25),
        inset 0 0 10px rgba(0, 242, 254, 0.1);
    }

    .cyber-input:focus + .input-icon,
    .cyber-input:focus ~ .input-icon {
      color: var(--cyan-neon);
      filter: drop-shadow(0 0 5px var(--cyan-neon));
    }

    .toggle-pwd-btn {
      position: absolute;
      right: 14px;
      background: none;
      border: none;
      color: var(--text-dim);
      font-size: 14px;
      cursor: pointer;
      padding: 6px;
      transition: color 0.25s ease;
      z-index: 2;
    }

    .toggle-pwd-btn:hover {
      color: var(--cyan-neon);
    }

    /* Select Dropdown */
    .cyber-select {
      width: 100%;
      height: 48px;
      background: rgba(13, 29, 52, 0.8);
      border: 1.5px solid rgba(0, 242, 254, 0.2);
      border-radius: 12px;
      padding: 0 38px 0 44px;
      font-size: 13px;
      color: #ffffff;
      font-family: inherit;
      outline: none;
      cursor: pointer;
      appearance: none;
      -webkit-appearance: none;
      transition: all 0.3s ease;
    }

    .cyber-select:focus {
      border-color: var(--cyan-neon);
      box-shadow: 0 0 20px rgba(0, 242, 254, 0.25);
    }

    .select-arrow {
      position: absolute;
      right: 16px;
      color: var(--text-dim);
      font-size: 11px;
      pointer-events: none;
    }

    .cyber-select option {
      background: #08172c;
      color: #f0f6fc;
      padding: 10px;
    }

    /* Remember & Forgot Password row */
    .form-options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 22px;
      font-size: 12px;
    }

    .cyber-checkbox-label {
      display: flex;
      align-items: center;
      gap: 8px;
      color: var(--text-muted);
      cursor: pointer;
      user-select: none;
    }

    .cyber-checkbox {
      appearance: none;
      -webkit-appearance: none;
      width: 16px;
      height: 16px;
      border: 1.5px solid rgba(0, 242, 254, 0.4);
      border-radius: 4px;
      background: rgba(13, 29, 52, 0.8);
      cursor: pointer;
      display: grid;
      place-content: center;
      transition: all 0.2s ease;
    }

    .cyber-checkbox:checked {
      background: var(--cyan-neon);
      border-color: var(--cyan-neon);
      box-shadow: 0 0 10px rgba(0, 242, 254, 0.5);
    }

    .cyber-checkbox:checked::before {
      content: '✓';
      color: #040914;
      font-size: 11px;
      font-weight: 900;
    }

    .forgot-link {
      color: #38bdf8;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.25s ease;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }

    .forgot-link:hover {
      color: #00f2fe;
      text-shadow: 0 0 8px rgba(0, 242, 254, 0.6);
      transform: translateX(2px);
    }

    /* Submit Button - Futuristic Glowing */
    .submit-btn {
      width: 100%;
      height: 50px;
      background: linear-gradient(135deg, #00f2fe 0%, #4facfe 50%, #0072ff 100%);
      border: none;
      border-radius: 12px;
      color: #030e1d;
      font-family: 'Orbitron', 'Plus Jakarta Sans', sans-serif;
      font-size: 14px;
      font-weight: 800;
      letter-spacing: 1.2px;
      cursor: pointer;
      position: relative;
      overflow: hidden;
      box-shadow: 
        0 8px 24px rgba(0, 242, 254, 0.4),
        0 0 12px rgba(0, 242, 254, 0.2);
      transition: all 0.3s cubic-bezier(0.2, 0.8, 0.2, 1);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }

    .submit-btn::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: linear-gradient(
        60deg,
        transparent 30%,
        rgba(255, 255, 255, 0.45) 50%,
        transparent 70%
      );
      transform: rotate(30deg);
      transition: transform 0.75s ease;
    }

    .submit-btn:hover {
      transform: translateY(-2px) scale(1.01);
      box-shadow: 
        0 12px 30px rgba(0, 242, 254, 0.6),
        0 0 20px rgba(0, 242, 254, 0.4);
    }

    .submit-btn:hover::before {
      transform: rotate(30deg) translate(50%, 50%);
    }

    .submit-btn:active {
      transform: translateY(1px);
    }

    /* Fast Access / SSO */
    .divider {
      display: flex;
      align-items: center;
      gap: 12px;
      margin: 20px 0;
      color: var(--text-dim);
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .divider::before, .divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: rgba(0, 242, 254, 0.15);
    }

    .fast-access-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
    }

    .fast-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      height: 40px;
      background: rgba(13, 29, 52, 0.6);
      border: 1px solid rgba(0, 242, 254, 0.18);
      border-radius: 10px;
      color: #cfe1f3;
      font-size: 12px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.25s ease;
    }

    .fast-btn:hover {
      background: rgba(18, 41, 74, 0.9);
      border-color: var(--cyan-neon);
      color: #fff;
      box-shadow: 0 0 14px rgba(0, 242, 254, 0.2);
    }

    .fast-btn i {
      color: var(--cyan-neon);
    }

    /* Toast Notification for Simulated Responses */
    .toast-alert {
      position: fixed;
      bottom: 24px;
      right: 24px;
      padding: 14px 20px;
      border-radius: 12px;
      background: rgba(8, 25, 48, 0.95);
      border: 1px solid var(--cyan-neon);
      color: #fff;
      font-size: 13px;
      display: flex;
      align-items: center;
      gap: 12px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6), 0 0 20px rgba(0, 242, 254, 0.3);
      backdrop-filter: blur(16px);
      z-index: 100;
      transform: translateY(100px);
      opacity: 0;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      pointer-events: none;
    }

    .toast-alert.show {
      transform: translateY(0);
      opacity: 1;
      pointer-events: auto;
    }

    .toast-alert.success {
      border-color: var(--emerald-glow);
    }
    .toast-alert.success i {
      color: var(--emerald-glow);
    }
    .toast-alert.error {
      border-color: var(--danger-red);
    }
    .toast-alert.error i {
      color: var(--danger-red);
    }

    /* ============================================================
       BOTTOM IOC TICKER / FOOTER
    ============================================================ */
    .bottom-hud {
      position: relative;
      z-index: 10;
      padding: 12px 36px;
      background: linear-gradient(0deg, rgba(6, 17, 34, 0.92), rgba(6, 17, 34, 0.3));
      border-top: 1px solid rgba(0, 242, 254, 0.12);
      backdrop-filter: blur(12px);
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 11.5px;
      color: var(--text-dim);
    }

    .ticker-wrap {
      display: flex;
      align-items: center;
      gap: 14px;
      overflow: hidden;
      max-width: 65%;
    }

    .ticker-tag {
      font-family: 'Orbitron', sans-serif;
      font-weight: 700;
      font-size: 10px;
      color: #040914;
      background: var(--cyan-neon);
      padding: 2px 8px;
      border-radius: 4px;
      letter-spacing: 0.8px;
      white-space: nowrap;
    }

    .ticker-content {
      color: var(--text-muted);
      white-space: nowrap;
      animation: tickerSlide 25s linear infinite;
    }

    @keyframes tickerSlide {
      0% { transform: translateX(20%); }
      100% { transform: translateX(-100%); }
    }

    .footer-right {
      display: flex;
      align-items: center;
      gap: 16px;
      white-space: nowrap;
    }

    .secure-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      color: #94a3b8;
    }

    .secure-badge i {
      color: var(--emerald-glow);
    }

    /* ============================================================
       RESPONSIVE DESIGN
    ============================================================ */
    @media (max-width: 960px) {
      .login-box-container {
        grid-template-columns: 1fr;
        max-width: 520px;
      }
      .ioc-info-panel {
        display: none;
      }
      .top-hud {
        padding: 12px 20px;
      }
      .hud-center {
        display: none;
      }
      .bottom-hud {
        padding: 10px 20px;
        flex-direction: column;
        gap: 6px;
        text-align: center;
      }
      .ticker-wrap {
        max-width: 100%;
      }
    }

    @media (max-width: 480px) {
      .main-wrapper {
        padding: 14px;
      }
      .ioc-form-panel {
        padding: 32px 20px;
      }
      .fast-access-grid {
        grid-template-columns: 1fr;
      }
      .hud-title-group h2 {
        font-size: 13px;
      }
    }
  </style>
</head>
<body>

  <!-- Sci-Fi Canvas Background: Interactive Neural Network -->
  <canvas id="ai-network-canvas"></canvas>
  
  <!-- Cyber Grid Pattern & Ambient Lighting -->
  <div class="cyber-grid-overlay"></div>
  <div class="ambient-glow ambient-1"></div>
  <div class="ambient-glow ambient-2"></div>
  <div class="ambient-glow ambient-3"></div>
  <div class="scanline-beam"></div>

  <!-- TOP HUD: REAL-TIME SYSTEM STATUS -->
  <header class="top-hud">
    <div class="hud-left">
      <div class="hospital-emblem">
        <i class="fa-solid fa-hospital-user"></i>
      </div>
      <div class="hud-title-group">
        <h2>BỆNH VIỆN ĐA KHOA KHU VỰC ĐĂK TÔ</h2>
        <p>HỆ THỐNG TRUNG TÂM ĐIỀU HÀNH THÔNG MINH</p>
      </div>
    </div>

    

    <div class="hud-right">
      <div class="clock-display">
        <div class="clock-time" id="live-clock">--:--:--</div>
        <div class="clock-date" id="live-date">--/--/----</div>
      </div>
      <div class="ai-badge">
        <i class="fa-solid fa-brain"></i>
       
      </div>
    </div>
  </header>

  <!-- MAIN LOGIN CONTAINER -->
  <main class="main-wrapper">
    <div class="login-box-container">
      
      <!-- LEFT PANEL: IOC OVERVIEW & TELEMETRY -->
      <div class="ioc-info-panel">
        <div class="brand-hero">
          <div class="brand-tag">
            <i class="fa-solid fa-shield-halved"></i>
            CỔNG ĐIỀU HÀNH TẬP TRUNG
          </div>
          <h1 class="hero-heading">TRUNG TÂM ĐIỀU HÀNH THÔNG MINH</h1>
          <div class="hero-sub">
            <i class="fa-solid fa-square-plus"></i> BỆNH VIỆN ĐA KHOA KHU VỰC ĐĂK TÔ
          </div>
          <p class="hero-desc">
            Nền tảng tích hợp dữ liệu khám chữa bệnh, phân tích chỉ số lâm sàng thời gian thực, quản lý hạ tầng trang thiết bị y tế và hỗ trợ ra quyết định lâm sàng bằng Trí tuệ Nhân tạo (AI).
          </p>
        </div>

        <!-- Mini Telemetry Badges -->
        <div class="telemetry-grid">
          <div class="telemetry-card">
            <div class="card-label">
              <span>CÔNG SUẤT GIƯỜNG</span>
              <i class="fa-solid fa-bed-pulse"></i>
            </div>
            <div class="card-val">92.4%</div>
            <div class="card-meta">
              <i class="fa-solid fa-arrow-trend-up"></i> +1.8% so với hôm qua
            </div>
          </div>

          <div class="telemetry-card">
            <div class="card-label">
              <span>TIẾP NHẬN HÔM NAY</span>
              <i class="fa-solid fa-users"></i>
            </div>
            <div class="card-val">348 BN</div>
            <div class="card-meta">
              <i class="fa-solid fa-circle-check"></i> Luồng khám ổn định
            </div>
          </div>

          <div class="telemetry-card">
            <div class="card-label">
              <span>HỆ THỐNG PHÒNG MỔ</span>
              <i class="fa-solid fa-heart-pulse"></i>
            </div>
            <div class="card-val">4 / 5 SẴN SÀNG</div>
            <div class="card-meta">
              <i class="fa-solid fa-check"></i> 01 ca đang phẫu thuật
            </div>
          </div>

          <div class="telemetry-card">
            <div class="card-label">
              <span>ĐỒNG BỘ DỮ LIỆU EMR</span>
              <i class="fa-solid fa-server"></i>
            </div>
            <div class="card-val">100% LIVE</div>
            <div class="card-meta">
              <i class="fa-solid fa-bolt"></i> Độ trễ: 12ms
            </div>
          </div>
        </div>

        <!-- Radar Status Visual -->
        <div class="radar-container">
          <div class="radar-circle"></div>
          <div class="radar-info">
            <h4>HỆ THỐNG GIÁM SÁT AN NINH & CẢNH BÁO Y TẾ</h4>
            <p>Toàn bộ 18 phân hệ đang hoạt động ở chế độ bảo mật cấp cao (SSL 256-bit).</p>
          </div>
        </div>
      </div>

      <!-- RIGHT PANEL: LOGIN FORM -->
      <div class="ioc-form-panel">
        <div class="form-header">
          <h3>
            <i class="fa-solid fa-fingerprint"></i>
            XÁC THỰC CÁN BỘ
          </h3>
          <p>Nhập thông tin tài khoản được cấp quyền để truy cập hệ thống</p>
        </div>

        <form id="admin-login-form" autocomplete="off">
          
          

          <!-- Mã Cán Bộ / Username -->
          <div class="form-group">
            <label class="form-label" for="login-username">TÊN ĐĂNG NHẬP</label>
            <div class="input-wrapper">
              <i class="fa-solid fa-user-doctor input-icon"></i>
              <input 
                type="text" 
                id="login-username" 
                class="cyber-input" 
                placeholder="Nhập mã định danh cán bộ hoặc email..." 
                required 
                autocomplete="username"
              >
            </div>
          </div>

          <!-- Mật Khẩu -->
          <div class="form-group">
            <label class="form-label" for="login-password">MẬT KHẨU BẢO MẬT</label>
            <div class="input-wrapper">
              <i class="fa-solid fa-lock input-icon"></i>
              <input 
                type="password" 
                id="login-password" 
                class="cyber-input" 
                placeholder="••••••••••••" 
                required 
                autocomplete="current-password"
              >
              <button type="button" class="toggle-pwd-btn" id="toggle-pwd-btn" title="Hiện/ẩn mật khẩu">
                <i class="fa-solid fa-eye" id="eye-icon"></i>
              </button>
            </div>
          </div>

          <!-- Options: Remember & Forgot -->
          <div class="form-options">
            <label class="cyber-checkbox-label">
              <input type="checkbox" id="remember-me" class="cyber-checkbox" checked>
              <span>Ghi nhớ phiên làm việc</span>
            </label>
            <a href="javascript:void(0)" class="forgot-link" id="btn-forgot-pwd">
              <i class="fa-regular fa-circle-question"></i> Quên mật khẩu?
            </a>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="submit-btn" id="btn-submit">
            <i class="fa-solid fa-right-to-bracket"></i>
            <span>ĐĂNG NHẬP HỆ THỐNG IOC</span>
          </button>
        </form>

        <!-- Fast Access Divider -->
       

    </div>
  </main>

  <!-- BOTTOM HUD: LIVE TICKER & FOOTER -->
  <footer class="bottom-hud">
    <div class="ticker-wrap">
      <span class="ticker-tag">LIVE IOC</span>
      <div class="ticker-content">
        [THÔNG BÁO] Hệ thống IOC Bệnh viện Đa khoa Khu vực Đăk Tô đã kích hoạt module AI Phân tích dịch tễ tự động • Các khoa phòng đã hoàn tất đồng bộ dữ liệu lúc 07:00 • Hotline kỹ thuật: (0260) 3862.xxx
      </div>
    </div>
    
    <div class="footer-right">
      <div class="secure-badge">
        <i class="fa-solid fa-shield-check"></i>
        <span>BẢO MẬT SSL 256-BIT ENCRYPTION</span>
      </div>
      <span>&copy; 2026 BV ĐKKV ĐĂK TÔ</span>
    </div>
  </footer>

  <!-- Toast Notification Popup -->
  <div class="toast-alert" id="toast-alert">
    <i class="fa-solid fa-circle-info" id="toast-icon"></i>
    <span id="toast-msg">Đang kết nối đến máy chủ điều hành...</span>
  </div>

  <!-- JAVASCRIPT: PARTICLE CANVAS + AI LOGIC + INTERACTIONS -->
  <script>
    /* ============================================================
       1. REAL-TIME CLOCK AND DATE
    ============================================================ */
    function updateClock() {
      const now = new Date();
      const hours = String(now.getHours()).padStart(2, '0');
      const minutes = String(now.getMinutes()).padStart(2, '0');
      const seconds = String(now.getSeconds()).padStart(2, '0');
      document.getElementById('live-clock').textContent = `${hours}:${minutes}:${seconds}`;

      const days = ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'];
      const dayName = days[now.getDay()];
      const day = String(now.getDate()).padStart(2, '0');
      const month = String(now.getMonth() + 1).padStart(2, '0');
      const year = now.getFullYear();
      document.getElementById('live-date').textContent = `${dayName}, ${day}/${month}/${year}`;
    }
    setInterval(updateClock, 1000);
    updateClock();

    /* ============================================================
       2. PASSWORD SHOW/HIDE TOGGLE
    ============================================================ */
    const togglePwdBtn = document.getElementById('toggle-pwd-btn');
    const pwdInput = document.getElementById('login-password');
    const eyeIcon = document.getElementById('eye-icon');

    togglePwdBtn.addEventListener('click', () => {
      if (pwdInput.type === 'password') {
        pwdInput.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
      } else {
        pwdInput.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
      }
    });

    /* ============================================================
       3. TOAST NOTIFICATION HELPER
    ============================================================ */
    const toast = document.getElementById('toast-alert');
    const toastMsg = document.getElementById('toast-msg');
    const toastIcon = document.getElementById('toast-icon');
    let toastTimeout;

    function showToast(message, type = 'info', duration = 3500) {
      clearTimeout(toastTimeout);
      toastMsg.textContent = message;
      toast.className = `toast-alert show ${type}`;

      if (type === 'success') {
        toastIcon.className = 'fa-solid fa-circle-check';
      } else if (type === 'error') {
        toastIcon.className = 'fa-solid fa-circle-xmark';
      } else {
        toastIcon.className = 'fa-solid fa-circle-info';
      }

      toastTimeout = setTimeout(() => {
        toast.classList.remove('show');
      }, duration);
    }

    /* ============================================================
       4. FORM SUBMISSION WITH SIMULATED AI VERIFICATION
    ============================================================ */
    const loginForm = document.getElementById('admin-login-form');
    const btnSubmit = document.getElementById('btn-submit');

    loginForm.addEventListener('submit', function (e) {
      e.preventDefault();
      const username = document.getElementById('login-username').value.trim();
      const password = document.getElementById('login-password').value;

      if (!username || !password) {
        showToast('Vui lòng nhập đầy đủ mã cán bộ và mật khẩu!', 'error');
        return;
      }

      // Button loading state
      btnSubmit.disabled = true;
      btnSubmit.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i><span>HỆ THỐNG ĐANG XÁC THỰC QUYỀN TRUY CẬP...</span>`;

      showToast('Đang kết nối trung tâm điều hành...', 'info');
      fetch('<?php echo XC_URL; ?>/api/stafflogin', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
        body: new URLSearchParams({username: username, password: password})
      })
        .then(response => response.json().then(data => ({ok: response.ok, data: data})))
        .then(result => {
          if (!result.ok || !result.data.success) {
            throw new Error(result.data.message || 'Tên đăng nhập hoặc mật khẩu không đúng.');
          }
          showToast(result.data.message || 'Đăng nhập thành công.', 'success', 2500);
          setTimeout(() => { window.location.href = result.data.return_url || '<?php echo XC_URL; ?>/backend'; }, 700);
        })
        .catch(error => {
          btnSubmit.disabled = false;
          btnSubmit.innerHTML = `<i class="fa-solid fa-right-to-bracket"></i><span>ĐĂNG NHẬP HỆ THỐNG IOC</span>`;
          showToast(error.message || 'Không thể kết nối máy chủ. Vui lòng thử lại.', 'error');
        });
    });

    document.getElementById('btn-forgot-pwd').addEventListener('click', () => {
      showToast('Vui lòng liên hệ Phòng CNTT - BV Đa khoa Đăk Tô để được cấp lại mật khẩu.', 'info', 4500);
    });

    /* ============================================================
       5. INTERACTIVE 60FPS AI NEURAL NETWORK CANVAS
    ============================================================ */
    const canvas = document.getElementById('ai-network-canvas');
    const ctx = canvas.getContext('2d');

    let width, height;
    let particles = [];
    let mouse = { x: null, y: null, radius: 150 };

    function resizeCanvas() {
      width = canvas.width = window.innerWidth;
      height = canvas.height = window.innerHeight;
      initParticles();
    }
    window.addEventListener('resize', resizeCanvas);

    window.addEventListener('mousemove', (e) => {
      mouse.x = e.x;
      mouse.y = e.y;
    });

    window.addEventListener('mouseout', () => {
      mouse.x = null;
      mouse.y = null;
    });

    class Particle {
      constructor() {
        this.x = Math.random() * width;
        this.y = Math.random() * height;
        this.vx = (Math.random() - 0.5) * 0.75;
        this.vy = (Math.random() - 0.5) * 0.75;
        this.radius = Math.random() * 2.2 + 1;
        this.baseColor = Math.random() > 0.3 ? '#00f2fe' : (Math.random() > 0.5 ? '#4facfe' : '#a855f7');
        this.alpha = Math.random() * 0.5 + 0.3;
      }

      update() {
        this.x += this.vx;
        this.y += this.vy;

        // Bounce on edges
        if (this.x < 0 || this.x > width) this.vx *= -1;
        if (this.y < 0 || this.y > height) this.vy *= -1;

        // Mouse interaction
        if (mouse.x !== null && mouse.y !== null) {
          const dx = mouse.x - this.x;
          const dy = mouse.y - this.y;
          const dist = Math.sqrt(dx * dx + dy * dy);
          if (dist < mouse.radius) {
            const angle = Math.atan2(dy, dx);
            const force = (mouse.radius - dist) / mouse.radius;
            this.x -= Math.cos(angle) * force * 2.5;
            this.y -= Math.sin(angle) * force * 2.5;
          }
        }
      }

      draw() {
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
        ctx.fillStyle = this.baseColor;
        ctx.globalAlpha = this.alpha;
        ctx.shadowBlur = 10;
        ctx.shadowColor = this.baseColor;
        ctx.fill();
        ctx.shadowBlur = 0;
      }
    }

    function initParticles() {
      particles = [];
      const particleCount = Math.floor((width * height) / 14000);
      const count = Math.min(Math.max(particleCount, 45), 110);
      for (let i = 0; i < count; i++) {
        particles.push(new Particle());
      }
    }

    function connectParticles() {
      const maxDistance = 140;
      for (let i = 0; i < particles.length; i++) {
        for (let j = i + 1; j < particles.length; j++) {
          const dx = particles[i].x - particles[j].x;
          const dy = particles[i].y - particles[j].y;
          const dist = Math.sqrt(dx * dx + dy * dy);

          if (dist < maxDistance) {
            const alpha = (1 - dist / maxDistance) * 0.28;
            ctx.beginPath();
            ctx.moveTo(particles[i].x, particles[i].y);
            ctx.lineTo(particles[j].x, particles[j].y);
            ctx.strokeStyle = `rgba(0, 242, 254, ${alpha})`;
            ctx.lineWidth = 1;
            ctx.stroke();
          }
        }
      }
    }

    function animate() {
      ctx.clearRect(0, 0, width, height);

      for (let p of particles) {
        p.update();
        p.draw();
      }
      connectParticles();

      requestAnimationFrame(animate);
    }

    resizeCanvas();
    animate();
  </script>
    </body>

</html>
