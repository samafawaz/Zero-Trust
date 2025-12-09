# New Features Implemented

## 1. Transaction Password System
- Users must set a 6-digit transaction password before sending money
- First time accessing send money page redirects to set transaction password
- Transaction password is required for every money transfer

## 2. OTP Verification for Send Money
- After entering transaction details and password, user receives OTP via email
- Must verify OTP to complete the transfer
- OTP expires in 2 minutes

## 3. OTP Verification for Account Settings
- Changing username or password requires OTP verification
- User must enter current password first
- OTP sent to email before changes are applied

## Files Created/Modified

### New Files:
- `set_tx_password_action.php` - Handles setting transaction password
- `send_money_action.php` - Initiates money transfer and sends OTP
- `send_money_otp_verify.php` - Verifies OTP and completes transfer
- `account_settings_action.php` - Initiates account changes and sends OTP
- `account_settings_otp_verify.php` - Verifies OTP and applies changes
- `UPDATE_DATABASE.sql` - SQL to update existing database

### Modified Files:
- `set_tx_password.php` - Added form functionality
- `send_money.php` - Added transaction password check and form
- `account_settings.php` - Added OTP flow
- `otp.php` - Added routing for new OTP purposes

## Database Changes

Run `UPDATE_DATABASE.sql` to:
1. Add `TransactionPasswordHash` column to Users table
2. Update OTPs table Purpose enum to include 'send_money' and 'account_settings'

## How It Works

### Send Money Flow:
1. User clicks "Send Money" from dashboard
2. If no transaction password set → redirect to set it
3. User enters recipient, amount, and transaction password
4. System verifies transaction password
5. OTP sent to user's email
6. User enters OTP to confirm transfer
7. Transfer completed

### Account Settings Flow:
1. User enters new username/password and current password
2. System verifies current password
3. OTP sent to user's email
4. User enters OTP to confirm changes
5. Changes applied to account
