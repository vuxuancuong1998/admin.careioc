ALTER TABLE ioc_inpatient_daily
  ADD COLUMN status TINYINT(2) NOT NULL DEFAULT 1 COMMENT '1 = đang sử dụng, 99 = đã xóa mềm' AFTER id,
  ADD KEY ix_ioc_inpatient_status (status);
