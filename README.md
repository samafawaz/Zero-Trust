# Zero Trust Banking Application

A secure banking application implementing Zero Trust security principles for user authentication and transaction verification.

## Overview

This application demonstrates a banking system built with PHP, MySQL, and modern web technologies, focusing on security through Zero Trust principles. It includes features like multi-factor authentication, device fingerprinting, and location-based security checks.

## Features

- **Secure User Authentication**
  - Email/Password login
  - One-Time Password (OTP) verification
  - Session management with timeouts

- **Account Security**
  - Device fingerprinting
  - Location-based access control
  - Secure session handling

- **Banking Operations**
  - View account balance
  - Send money with transaction verification
  - Transaction history

- **User Management**
  - Registration with email verification
  - Profile management
  - Transaction password protection

## Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- SMTP server for email notifications

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/samafawaz/Zero-Trust.git
   cd Zero-Trust
   ```

2. **Set up the database**
   - Import the database schema from `database/zerotrustdb.sql`
   - Configure database credentials in `db.php`

3. **Configure email settings**
   - Copy `email_config.example.php` to `email_config.php`
   - Update with your SMTP server details

4. **Set up the web server**
   - Point your web server to the project directory
   - Ensure mod_rewrite is enabled for clean URLs

5. **Set file permissions**
   ```bash
   chmod 755 -R ./
   chmod 777 -R logs/  # If using file-based logging
   ```

## Configuration

Edit the following files to match your environment:

- `db.php` - Database connection settings
- `email_config.php` - Email server configuration
- `.htaccess` - URL rewriting rules (if using Apache)

## Security Features

- **Multi-Factor Authentication**: Requires both password and OTP for login
- **Location Verification**: Checks login locations against known locations
- **Session Security**: Implements idle and absolute session timeouts
- **Input Validation**: All user inputs are strictly validated
- **CSRF Protection**: Implements anti-CSRF tokens
- **Secure Headers**: Implements security headers for web protection

## Usage

1. Register a new account
2. Verify your email address
3. Log in with your credentials
4. Set up a transaction password
5. Start using the banking features

