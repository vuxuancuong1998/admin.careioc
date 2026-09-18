-- Phan quyen tai khoan theo site va menu cho CARE IOC.
-- Super admin (ioc_users.is_admin = 1) duoc bypass toan bo bang quyen.

ALTER TABLE ioc_users
    ADD UNIQUE INDEX IF NOT EXISTS ux_ioc_users_username (username),
    ADD UNIQUE INDEX IF NOT EXISTS ux_ioc_users_email (email);

CREATE TABLE IF NOT EXISTS ioc_user_site_access (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    site_code ENUM('admin', 'backend', 'dashboard') NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_by INT DEFAULT NULL,
    updated_by INT DEFAULT NULL,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at DATETIME(6) DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY ux_ioc_user_site (user_id, site_code),
    KEY ix_ioc_site_active (site_code, is_active),
    CONSTRAINT fk_ioc_site_access_user FOREIGN KEY (user_id) REFERENCES ioc_users (id),
    CONSTRAINT fk_ioc_site_access_created_by FOREIGN KEY (created_by) REFERENCES ioc_users (id) ON DELETE SET NULL,
    CONSTRAINT fk_ioc_site_access_updated_by FOREIGN KEY (updated_by) REFERENCES ioc_users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Quyen truy cap doc lap theo admin, backend va dashboard.';

CREATE TABLE IF NOT EXISTS ioc_site_menus (
    id INT NOT NULL AUTO_INCREMENT,
    site_code ENUM('admin', 'backend', 'dashboard') NOT NULL,
    menu_code VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    menu_name VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    parent_id INT DEFAULT NULL,
    route VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    icon VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    UNIQUE KEY ux_ioc_site_menu_code (site_code, menu_code),
    KEY ix_ioc_site_menu_order (site_code, is_active, sort_order),
    KEY ix_ioc_site_menu_parent (parent_id),
    CONSTRAINT fk_ioc_site_menu_parent FOREIGN KEY (parent_id) REFERENCES ioc_site_menus (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Danh muc menu phan quyen cua ba khu vuc.';

CREATE TABLE IF NOT EXISTS ioc_user_menu_permissions (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    menu_id INT NOT NULL,
    can_view TINYINT(1) NOT NULL DEFAULT 0,
    can_create TINYINT(1) NOT NULL DEFAULT 0,
    can_update TINYINT(1) NOT NULL DEFAULT 0,
    can_delete TINYINT(1) NOT NULL DEFAULT 0,
    can_import TINYINT(1) NOT NULL DEFAULT 0,
    can_export TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_by INT DEFAULT NULL,
    updated_by INT DEFAULT NULL,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    UNIQUE KEY ux_ioc_user_menu_permission (user_id, menu_id),
    KEY ix_ioc_menu_permission_active (menu_id, is_active),
    CONSTRAINT fk_ioc_menu_permission_user FOREIGN KEY (user_id) REFERENCES ioc_users (id),
    CONSTRAINT fk_ioc_menu_permission_menu FOREIGN KEY (menu_id) REFERENCES ioc_site_menus (id),
    CONSTRAINT fk_ioc_menu_permission_created_by FOREIGN KEY (created_by) REFERENCES ioc_users (id) ON DELETE SET NULL,
    CONSTRAINT fk_ioc_menu_permission_updated_by FOREIGN KEY (updated_by) REFERENCES ioc_users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Quyen thao tac chi tiet cua tai khoan tren tung menu.';

INSERT INTO ioc_site_menus (site_code, menu_code, menu_name, route, icon, sort_order) VALUES
('admin', 'admin.overview', 'Tổng quan IOC', '/', 'ph-squares-four', 10),
('admin', 'admin.outpatient', 'Khám bệnh và tiếp đón', '/outpatient', 'ph-stethoscope', 20),
('admin', 'admin.inpatient', 'Nội trú và giường bệnh', '/inpatient', 'ph-bed', 30),
('admin', 'admin.categories', 'Quản lý danh mục', NULL, 'ph-list-dashes', 40),
('admin', 'admin.departments', 'Khoa phòng', '/departments', 'ph-buildings', 41),
('admin', 'admin.beds', 'Giường bệnh', '/beds', 'ph-bed', 42),
('admin', 'admin.staff', 'Nhân viên', '/staff', 'ph-users', 43),
('admin', 'admin.infrastructure', 'Hạ tầng CNTT và Server', '/infrastructure', 'ph-hard-drives', 50),
('admin', 'admin.security', 'An toàn thông tin', '/security', 'ph-shield-check', 60),
('admin', 'admin.accounts', 'Quản lý tài khoản', NULL, 'ph-user-gear', 70),
('admin', 'admin.accounts.admin', 'Tài khoản trang Admin', '/accounts/admin', 'ph-user-circle-gear', 71),
('admin', 'admin.accounts.backend', 'Tài khoản trang Backend', '/accounts/backend', 'ph-database', 72),
('admin', 'admin.accounts.dashboard', 'Tài khoản trang Dashboard', '/accounts/dashboard', 'ph-chart-line-up', 73),
('backend', 'backend.overview', 'Tổng quan nhập liệu', '/backend', 'fa-gauge-high', 10),
('backend', 'backend.outpatient', 'Số liệu khám ngoại trú', '/backend/outpatient', 'fa-stethoscope', 20),
('backend', 'backend.inpatient', 'Điều trị nội trú', '/backend/inpatient', 'fa-bed-pulse', 30),
('backend', 'backend.lis_report', 'Số liệu xét nghiệm', '/backend/lisReport', 'fa-flask-vial', 40),
('backend', 'backend.ris_report', 'Số liệu chẩn đoán hình ảnh', '/backend/risReport', 'fa-x-ray', 50),
('backend', 'backend.emr_report', 'Số liệu EMR', '/backend/emrReport', 'fa-file-waveform', 60),
('backend', 'backend.lis_integration', 'Đồng bộ dữ liệu LIS', '/backend/lisIntegration', 'fa-arrows-rotate', 70),
('dashboard', 'dashboard.overview', 'Tổng quan IOC', '/dashboard', 'fa-chart-line', 10),
('dashboard', 'dashboard.admin_portal', 'Cổng quản trị và nhập liệu', '/', 'fa-gear', 20)
ON DUPLICATE KEY UPDATE
    menu_name = VALUES(menu_name), route = VALUES(route), icon = VALUES(icon),
    sort_order = VALUES(sort_order), is_active = 1, updated_at = CURRENT_TIMESTAMP(6);

UPDATE ioc_site_menus child
JOIN ioc_site_menus parent
    ON parent.site_code = 'admin' AND parent.menu_code = 'admin.categories'
SET child.parent_id = parent.id
WHERE child.site_code = 'admin'
  AND child.menu_code IN ('admin.departments', 'admin.beds', 'admin.staff');

UPDATE ioc_site_menus child
JOIN ioc_site_menus parent
    ON parent.site_code = 'admin' AND parent.menu_code = 'admin.accounts'
SET child.parent_id = parent.id
WHERE child.site_code = 'admin'
  AND child.menu_code IN ('admin.accounts.admin', 'admin.accounts.backend', 'admin.accounts.dashboard');

INSERT INTO ioc_user_site_access (user_id, site_code, is_active, created_by, updated_by)
SELECT id, site.site_code, 1, id, id
FROM ioc_users
CROSS JOIN (
    SELECT 'admin' AS site_code
    UNION ALL SELECT 'backend'
    UNION ALL SELECT 'dashboard'
) site
WHERE is_admin = 1
ON DUPLICATE KEY UPDATE is_active = 1, deleted_at = NULL, updated_by = VALUES(updated_by);

-- Dùng literal HEX để tên tiếng Việt không phụ thuộc code page của terminal khi chạy migration.
UPDATE ioc_site_menus SET menu_name = CONVERT(0x54E1BB956E67207175616E20494F43 USING utf8mb4) WHERE menu_code = 'admin.overview';
UPDATE ioc_site_menus SET menu_name = CONVERT(0x4B68C3A16D2062E1BB876E682076C3A0207469E1BABF7020C491C3B36E USING utf8mb4) WHERE menu_code = 'admin.outpatient';
UPDATE ioc_site_menus SET menu_name = CONVERT(0x4EE1BB9969207472C3BA2076C3A0206769C6B0E1BB9D6E672062E1BB876E68 USING utf8mb4) WHERE menu_code = 'admin.inpatient';
UPDATE ioc_site_menus SET menu_name = CONVERT(0x5175E1BAA36E206CC3BD2064616E68206DE1BBA563 USING utf8mb4) WHERE menu_code = 'admin.categories';
UPDATE ioc_site_menus SET menu_name = CONVERT(0x4B686F61207068C3B26E67 USING utf8mb4) WHERE menu_code = 'admin.departments';
UPDATE ioc_site_menus SET menu_name = CONVERT(0x4769C6B0E1BB9D6E672062E1BB876E68 USING utf8mb4) WHERE menu_code = 'admin.beds';
UPDATE ioc_site_menus SET menu_name = CONVERT(0x4E68C3A26E207669C3AA6E USING utf8mb4) WHERE menu_code = 'admin.staff';
UPDATE ioc_site_menus SET menu_name = CONVERT(0x48E1BAA12074E1BAA76E6720434E54542076C3A020536572766572 USING utf8mb4) WHERE menu_code = 'admin.infrastructure';
UPDATE ioc_site_menus SET menu_name = CONVERT(0x416E20746FC3A06E207468C3B46E672074696E USING utf8mb4) WHERE menu_code = 'admin.security';
UPDATE ioc_site_menus SET menu_name = CONVERT(0x5175E1BAA36E206CC3BD2074C3A069206B686FE1BAA36E USING utf8mb4) WHERE menu_code = 'admin.accounts';
UPDATE ioc_site_menus SET menu_name = CONVERT(0x54C3A069206B686FE1BAA36E207472616E672041646D696E USING utf8mb4) WHERE menu_code = 'admin.accounts.admin';
UPDATE ioc_site_menus SET menu_name = CONVERT(0x54C3A069206B686FE1BAA36E207472616E67204261636B656E64 USING utf8mb4) WHERE menu_code = 'admin.accounts.backend';
UPDATE ioc_site_menus SET menu_name = CONVERT(0x54C3A069206B686FE1BAA36E207472616E672044617368626F617264 USING utf8mb4) WHERE menu_code = 'admin.accounts.dashboard';
UPDATE ioc_site_menus SET menu_name = CONVERT(0x54E1BB956E67207175616E206E68E1BAAD70206C69E1BB8775 USING utf8mb4) WHERE menu_code = 'backend.overview';
UPDATE ioc_site_menus SET menu_name = CONVERT(0x53E1BB91206C69E1BB8775206B68C3A16D206E676FE1BAA169207472C3BA USING utf8mb4) WHERE menu_code = 'backend.outpatient';
UPDATE ioc_site_menus SET menu_name = CONVERT(0xC49069E1BB8175207472E1BB8B206EE1BB9969207472C3BA USING utf8mb4) WHERE menu_code = 'backend.inpatient';
UPDATE ioc_site_menus SET menu_name = CONVERT(0x53E1BB91206C69E1BB87752078C3A974206E676869E1BB876D USING utf8mb4) WHERE menu_code = 'backend.lis_report';
UPDATE ioc_site_menus SET menu_name = CONVERT(0x53E1BB91206C69E1BB8775206368E1BAA96E20C4916FC3A16E2068C3AC6E6820E1BAA36E68 USING utf8mb4) WHERE menu_code = 'backend.ris_report';
UPDATE ioc_site_menus SET menu_name = CONVERT(0x53E1BB91206C69E1BB877520454D52 USING utf8mb4) WHERE menu_code = 'backend.emr_report';
UPDATE ioc_site_menus SET menu_name = CONVERT(0xC490E1BB936E672062E1BB992064E1BBAF206C69E1BB8775204C4953 USING utf8mb4) WHERE menu_code = 'backend.lis_integration';
UPDATE ioc_site_menus SET menu_name = CONVERT(0x54E1BB956E67207175616E20494F43 USING utf8mb4) WHERE menu_code = 'dashboard.overview';
UPDATE ioc_site_menus SET menu_name = CONVERT(0x43E1BB956E67207175E1BAA36E207472E1BB8B2076C3A0206E68E1BAAD70206C69E1BB8775 USING utf8mb4) WHERE menu_code = 'dashboard.admin_portal';
