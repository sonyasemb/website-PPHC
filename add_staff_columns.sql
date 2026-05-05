SET @db_name = DATABASE();

SET @sql = IF(
    EXISTS(
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = @db_name
          AND TABLE_NAME = 'users'
          AND COLUMN_NAME = 'email'
    ),
    'SELECT 1',
    'ALTER TABLE users ADD COLUMN email VARCHAR(120) NULL AFTER nama'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = IF(
    EXISTS(
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = @db_name
          AND TABLE_NAME = 'users'
          AND COLUMN_NAME = 'status'
    ),
    'SELECT 1',
    'ALTER TABLE users ADD COLUMN status ENUM(''active'',''inactive'',''pending'',''suspended'',''banned'') NOT NULL DEFAULT ''active'' AFTER role'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = IF(
    EXISTS(
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = @db_name
          AND TABLE_NAME = 'users'
          AND COLUMN_NAME = 'created_at'
    ),
    'SELECT 1',
    'ALTER TABLE users ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER status'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = IF(
    EXISTS(
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = @db_name
          AND TABLE_NAME = 'users'
          AND COLUMN_NAME = 'last_login_at'
    ),
    'SELECT 1',
    'ALTER TABLE users ADD COLUMN last_login_at DATETIME NULL AFTER created_at'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
