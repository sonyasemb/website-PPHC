ALTER TABLE reservasi
ADD COLUMN layanan VARCHAR(100) NULL AFTER keluhan_kebutuhan;

UPDATE reservasi
SET layanan = psikolog
WHERE (layanan IS NULL OR layanan = '')
  AND psikolog IS NOT NULL
  AND psikolog <> '';
