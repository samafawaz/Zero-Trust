-- Add TransactionPasswordHash column to users table if it doesn't exist
USE zerotrustdb;

-- First check if the column exists
SET @dbname = DATABASE();
SET @tablename = 'users';
SET @columnname = 'TransactionPasswordHash';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE (TABLE_SCHEMA = @dbname)
        AND (TABLE_NAME = @tablename)
        AND (COLUMN_NAME = @columnname)
    ) > 0,
    'SELECT 1',
    CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' VARCHAR(255) NULL AFTER PasswordHash;')
));

PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Also add to Users table (case-sensitive check)
SET @tablename = 'Users';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE (TABLE_SCHEMA = @dbname)
        AND (TABLE_NAME = @tablename)
        AND (COLUMN_NAME = @columnname)
    ) > 0,
    'SELECT 1',
    CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' VARCHAR(255) NULL AFTER PasswordHash;')
));

PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Show the structure of both tables to confirm the changes
DESCRIBE users;
DESCRIBE Users;
