-- Menu quản trị kết nối API cho trang Admin.
INSERT INTO ioc_site_menus
    (site_code, menu_code, menu_name, parent_id, route, icon, sort_order, is_active)
VALUES
    ('admin', 'admin.system_api', 'Quản trị hệ thống API', NULL, '/system_api', 'ph-plugs-connected', 66, 1)
ON DUPLICATE KEY UPDATE
    menu_name = VALUES(menu_name),
    parent_id = VALUES(parent_id),
    route = VALUES(route),
    icon = VALUES(icon),
    sort_order = VALUES(sort_order),
    is_active = 1,
    updated_at = CURRENT_TIMESTAMP(6);
