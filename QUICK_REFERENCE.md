# 🚀 Quick Reference - Ind6TokenVendor

## Login Credentials
```
URL:      http://localhost:8888/Ind6TokenVendor/auth/login
Email:    admin@ind6token.com
Password: password123
```

## Database Connection
```
Host:     localhost
Port:     8889
Username: root
Password: root
Database: ind6token_admin
```

## MAMP Commands

### MySQL CLI
```bash
/Applications/MAMP/Library/bin/mysql80/bin/mysql -uroot -proot -P8889 -h127.0.0.1 ind6token_admin
```

### PHP CLI
```bash
/Applications/MAMP/bin/php/php8.5.0/bin/php
```

### Run Migrations
```bash
cd /Applications/MAMP/htdocs/Ind6TokenVendor
/Applications/MAMP/bin/php/php8.5.0/bin/php spark migrate
```

## Important URLs
- **Login**: http://localhost:8888/Ind6TokenVendor/auth/login
- **Dashboard**: http://localhost:8888/Ind6TokenVendor/
- **phpMyAdmin**: http://localhost:8888/phpMyAdmin/
- **Payment Test**: http://localhost:8888/Ind6TokenVendor/payment/test

## Files Modified
1. `app/Config/Database.php` - Port: 8889, Password: root
2. `app/Config/App.php` - indexPage: ''
3. `app/Controllers/Auth.php` - Fixed redirects

## Tables Created
- admins
- vendors
- payments
- bank_details
- migrations

## Troubleshooting
If login doesn't work:
1. Clear browser cache
2. Check MAMP is running
3. Verify database exists: `SHOW DATABASES;`
4. Verify user exists: `SELECT * FROM vendors;`
