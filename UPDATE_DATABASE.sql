-- Run this SQL to update your existing database

USE zerotrustdb;

-- 1. Add TransactionPasswordHash column to Users table
ALTER TABLE Users 
ADD COLUMN TransactionPasswordHash VARCHAR(255) NULL AFTER PasswordHash;

-- 2. Add columns for exponential account lockout
ALTER TABLE Users 
ADD COLUMN FailedOtpAttempts INT DEFAULT 0 AFTER TransactionPasswordHash,
ADD COLUMN LockoutUntil DATETIME NULL AFTER FailedOtpAttempts,
ADD COLUMN IsPermanentlyLocked BOOLEAN DEFAULT FALSE AFTER LockoutUntil;

-- 3. Update OTPs table Purpose enum to include new purposes
ALTER TABLE otps 
MODIFY COLUMN Purpose ENUM('signup', 'login', 'send_money', 'account_settings') NOT NULL;
