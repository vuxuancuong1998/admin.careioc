<?php
// Run once with PHP CLI. Original timestamps remain in report_date_legacy.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
ini_set('session.save_path', sys_get_temp_dir());
require_once dirname(__DIR__) . '/config.php';
session_write_close();
$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASSWORD, array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
$type = $pdo->query("SHOW COLUMNS FROM ioc_outpatient_daily LIKE 'report_date'")->fetch(PDO::FETCH_ASSOC)['Type'];
if (strpos($type, 'int') !== false) { echo "report_date already stores ioc_date.id.\n"; exit; }
if ($type !== 'datetime') throw new RuntimeException('Unexpected report_date type; migration stopped.');
$missing = $pdo->query('SELECT COUNT(*) FROM ioc_outpatient_daily o LEFT JOIN ioc_date d ON d.full_date=DATE(o.report_date) WHERE d.id IS NULL')->fetchColumn();
if ($missing) throw new RuntimeException('Some report dates are absent from ioc_date. Complete the date catalogue before migrating.');
$duplicates = $pdo->query('SELECT 1 FROM ioc_outpatient_daily GROUP BY DATE(report_date),department_id,payer_type_id HAVING COUNT(*)>1 LIMIT 1')->fetchColumn();
if ($duplicates) throw new RuntimeException('Multiple records exist for the same calendar day, department and payer. Resolve them before migrating.');
$pending = $pdo->query("SHOW COLUMNS FROM ioc_outpatient_daily LIKE 'report_date_id'")->fetch();
if (!$pending) $pdo->exec('ALTER TABLE ioc_outpatient_daily ADD COLUMN report_date_id INT NULL AFTER report_date');
$pdo->exec('UPDATE ioc_outpatient_daily o JOIN ioc_date d ON d.full_date=DATE(o.report_date) SET o.report_date_id=d.id');
if ($pdo->query('SELECT COUNT(*) FROM ioc_outpatient_daily WHERE report_date_id IS NULL')->fetchColumn()) throw new RuntimeException('Date mapping incomplete; original dates are unchanged.');
$pdo->exec("ALTER TABLE ioc_outpatient_daily
    DROP INDEX uq_ioc_outpatient_daily,
    CHANGE COLUMN report_date report_date_legacy DATETIME NULL DEFAULT NULL COMMENT 'Original timestamp retained during date ID migration',
    CHANGE COLUMN report_date_id report_date INT NOT NULL COMMENT 'References ioc_date.id',
    ADD UNIQUE KEY uq_ioc_outpatient_daily (report_date,department_id,payer_type_id),
    ADD CONSTRAINT fk_ioc_outpatient_date FOREIGN KEY (report_date) REFERENCES ioc_date(id) ON UPDATE CASCADE");
echo 'Migrated ' . $pdo->query('SELECT COUNT(*) FROM ioc_outpatient_daily')->fetchColumn() . " records to ioc_date.id; original timestamps retained.\n";
