-- ClinAI v2.0 — Migration: add notes column to assessments
-- Run this in phpMyAdmin if it doesn't already exist
ALTER TABLE `assessments`
  ADD COLUMN `notes` TEXT DEFAULT NULL AFTER `top_confidence`;
