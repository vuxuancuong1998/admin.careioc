CREATE TABLE IF NOT EXISTS ioc_lis_dates (
 report_date DATE PRIMARY KEY,
 day_number TINYINT UNSIGNED NOT NULL,
 month_number TINYINT UNSIGNED NOT NULL,
 year_number SMALLINT UNSIGNED NOT NULL,
 status VARCHAR(16) NOT NULL DEFAULT 'pending',
 last_attempt_at DATETIME NULL,
 next_attempt_at DATETIME NULL,
 last_error VARCHAR(255) NULL,
 KEY pending_days(status,next_attempt_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Staging survives PHP requests; it is not a connection-scoped TEMPORARY TABLE.
CREATE TABLE IF NOT EXISTS ioc_lis_staging (
 report_date DATE PRIMARY KEY,
 total_count BIGINT UNSIGNED NOT NULL,
 outpatient_count BIGINT UNSIGNED NOT NULL,
 inpatient_count BIGINT UNSIGNED NOT NULL,
 insurance_count BIGINT UNSIGNED NOT NULL,
 self_pay_count BIGINT UNSIGNED NOT NULL,
 source VARCHAR(64) NOT NULL DEFAULT 'LIS:get_tk_hd_khoa_xn',
 synced_at DATETIME NOT NULL,
 status VARCHAR(16) NOT NULL DEFAULT 'temporary',
 CONSTRAINT fk_lis_staging_date FOREIGN KEY(report_date) REFERENCES ioc_lis_dates(report_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ioc_lis_daily LIKE ioc_lis_staging;

CREATE TABLE IF NOT EXISTS ioc_lis_service_staging (
 report_date DATE NOT NULL,
 service_key CHAR(64) NOT NULL,
 service_code VARCHAR(128) NULL,
 service_group VARCHAR(255) NOT NULL,
 service_name VARCHAR(512) NOT NULL,
 total_count BIGINT UNSIGNED NOT NULL,
 outpatient_count BIGINT UNSIGNED NOT NULL,
 inpatient_count BIGINT UNSIGNED NOT NULL,
 insurance_count BIGINT UNSIGNED NOT NULL,
 self_pay_count BIGINT UNSIGNED NOT NULL,
 PRIMARY KEY(report_date,service_key),
 CONSTRAINT fk_lis_service_staging FOREIGN KEY(report_date) REFERENCES ioc_lis_staging(report_date) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ioc_lis_service_daily LIKE ioc_lis_service_staging;

CREATE TABLE IF NOT EXISTS ioc_lis_sync_log (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 report_date DATE NOT NULL,
 sync_mode VARCHAR(16) NOT NULL,
 success TINYINT NOT NULL,
 message VARCHAR(255) NOT NULL,
 created_at DATETIME NOT NULL,
 KEY by_date(report_date,created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
