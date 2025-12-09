# Zero Trust Architecture Features

## What is Zero Trust?
**"Never trust, always verify"** - Zero Trust assumes no user or device should be trusted by default, even if they're inside the network.

---

## Our Implementation

### 1. ✅ Device Fingerprinting
**What it does:** Tracks which device you're using based on browser information.

**How it works:**
- Creates a unique ID from your browser type, language, and settings
- Stores trusted devices in the database
- Detects when logging in from a new device
- Automatically trusts the device after successful login

**Files:** `device_fingerprint.php`, `TrustedDevices` table

---

### 2. ✅ IP Address & Location Tracking
**What it does:** Monitors where you're logging in from.

**How it works:**
- Records your IP address on every login
- Detects if you're logging in from a new location
- Keeps a history of all login attempts (successful and failed)
- Tracks patterns of access

**Files:** `location_check.php`, `LoginHistory` table

---

### 3. ✅ Multi-Factor Authentication (2FA)
**What it does:** Requires two forms of verification.

**How it works:**
- Password (something you know)
- OTP code via email (something you have)
- Both required to access your account

**Files:** `login_action.php`, `otp.php`, `email_config.php`

---

### 4. ✅ Session Management
**What it does:** Limits how long you stay logged in.

**How it works:**
- Session expires after 5 minutes of inactivity
- Forces re-login for security
- Prevents unauthorized access if you leave your computer

**Files:** `auth.php`

---

### 5. ✅ Audit Logging
**What it does:** Records everything that happens.

**How it works:**
- Logs all login attempts (successful and failed)
- Tracks IP addresses and devices
- Creates an audit trail for security review

**Files:** `LoginHistory` table

---

## How to Test Zero Trust Features

### Test 1: Device Fingerprinting
1. Login normally from Chrome browser
2. Check `TrustedDevices` table in phpMyAdmin - your device is saved
3. Open in a different browser (Firefox or Edge)
4. Login again - it will be detected as a new device
5. Check `TrustedDevices` table - now you have 2 devices saved

### Test 2: Location Tracking
1. Login from your home network
2. Check `LoginHistory` table - your IP is recorded
3. Login from mobile data or different WiFi
4. Check `LoginHistory` table - new IP address is recorded

### Test 3: Session Timeout
1. Login to dashboard
2. Wait 5 minutes without clicking anything
3. Try to navigate - you'll be logged out automatically

### Test 4: Login History Audit
1. Login from different browsers/devices
2. Try wrong password a few times
3. Check `LoginHistory` table in phpMyAdmin
4. You'll see all attempts with Success=1 (passed) or Success=0 (failed)

---

## Database Tables

### TrustedDevices
Stores devices you've logged in from before.

### LoginHistory
Records every login attempt with IP address and timestamp.

### AuditLog
Logs all sensitive actions for security review.

---

## Why This Demonstrates Zero Trust

| Zero Trust Principle | Our Implementation |
|---------------------|-------------------|
| Verify explicitly | Device + Location + Password + OTP |
| Never trust by default | Check device and location on every login |
| Least privilege access | Session expires after 5 minutes |
| Assume breach | Audit logging of all login attempts |
| Monitor and log | Track all devices, IPs, and login history |

---

## For Your Presentation

**Key Points to Mention:**

1. **"We verify the device and location on every login"**
   - Show the `TrustedDevices` and `LoginHistory` tables in phpMyAdmin
   - Demonstrate logging in from different browsers

2. **"We use multi-factor authentication (2FA)"**
   - Explain password + OTP email
   - Show the OTP verification page

3. **"Sessions expire quickly for security"**
   - Mention the 5-minute timeout
   - Demonstrate automatic logout

4. **"We log everything for audit trails"**
   - Show the LoginHistory table with IP addresses and timestamps
   - Explain how admins can review suspicious activity

5. **"We track devices to detect unauthorized access"**
   - Show how the system remembers trusted devices
   - Explain how new devices are detected

---

## Simple Explanation for Non-Technical People

**Traditional Security:** "Show your ID at the door, then you can go anywhere in the building."

**Zero Trust Security:** "Check your ID at the door, verify which device you're using, check where you're coming from, and track everything."

Our banking app uses Zero Trust by:
- Checking your identity with password + OTP email (2FA)
- Tracking which device you're using (computer/phone fingerprint)
- Monitoring where you're logging in from (IP address)
- Automatically logging you out after 5 minutes
- Recording all login attempts for security review

---

## Installation

1. Run the SQL file: `zero_trust_tables.sql` in phpMyAdmin
2. All PHP files are already in place
3. Login and test the features!

---

## Credits
Built by [Your Names] - First Year University Students
Demonstrating Zero Trust Architecture principles in a banking application.
