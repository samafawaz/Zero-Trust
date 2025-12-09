-- Remove lockout-related columns from users table
USE zerotrustdb;

-- Check and drop columns if they exist
SET @dbname = DATABASE();
SET @tablename = 'users';

-- Drop FailedOtpAttempts
SET @columnname = 'FailedOtpAttempts';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE (TABLE_SCHEMA = @dbname)
        AND (TABLE_NAME = @tablename)
        AND (COLUMN_NAME = @columnname)
    ) > 0,
    CONCAT('ALTER TABLE ', @tablename, ' DROP COLUMN ', @columnname, ';'),
    'SELECT 1'
));

PREPARE alterIfExists FROM @preparedStatement;
EXECUTE alterIfExists;
DEALLOCATE PREPARE alterIfExists;

-- Drop LockoutUntil
SET @columnname = 'LockoutUntil';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE (TABLE_SCHEMA = @dbname)
        AND (TABLE_NAME = @tablename)
        AND (COLUMN_NAME = @columnname)
    ) > 0,
    CONCAT('ALTER TABLE ', @tablename, ' DROP COLUMN ', @columnname, ';'),
    'SELECT 1'
));

PREPARE alterIfExists FROM @preparedStatement;
EXECUTE alterIfExists;
DEALLOCATE PREPARE alterIfExists;

-- Drop IsPermanentlyLocked
SET @columnname = 'IsPermanentlyLocked';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE (TABLE_SCHEMA = @dbname)
        AND (TABLE_NAME = @tablename)
        AND (COLUMN_NAME = @columnname)
    ) > 0,
    CONCAT('ALTER TABLE ', @tablename, ' DROP COLUMN ', @columnname, ';'),
    'SELECT 1'
));

PREPARE alterIfExists FROM @preparedStatement;
EXECUTE alterIfExists;
DEALLOCATE PREPARE alterIfExists;
