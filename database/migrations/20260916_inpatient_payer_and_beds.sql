ALTER TABLE ioc_inpatient_daily
    ADD COLUMN payer_type_id TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER inpatient_department_id,
    ADD COLUMN inpatient_closing_patient_count INT UNSIGNED NOT NULL DEFAULT 0 AFTER inpatient_death_count,
    ADD COLUMN inpatient_actual_beds SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER inpatient_occupied_beds,
  DROP INDEX uq_ioc_inpatient_daily,
  ADD UNIQUE KEY uq_ioc_inpatient_daily (inpatient_report_date, inpatient_department_id, payer_type_id),
  ADD KEY ix_ioc_inpatient_payer (payer_type_id);
