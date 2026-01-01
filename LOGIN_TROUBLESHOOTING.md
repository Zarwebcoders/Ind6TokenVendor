# 🔧 Login Not Working - Troubleshooting Guide

## Problem
Login page stays on the same page even with correct credentials. Not redirecting to dashboard.

## Quick Test

**Visit this URL first:**
```
http://localhost:8888/Ind6TokenVendor/test_login.php
```

This test page will show you exactly what's wrong.

---

## Common Causes & Solutions

### 1. ❌ Session Not Working

**Symptoms:**
- Login page reloads without error message
- No redirect happens

**Solution:**
Check if session directory is writable:
```bash
cd /Applications/MAMP/htdocs/Ind6TokenVendor
ls -la writable/session/
chmod -R 777 writable/
```

---

### 2. ❌ Database Not Connected

**Symptoms:**
- Page shows error about database
- Or page stays blank

**Solution:**
Verify database connection:
```bash
/Applications/MAMP/Library/bin/mysql80/bin/mysql -uroot -proot -P8889 -h127.0.0.1 ind6token_admin -e "SELECT * FROM vendors WHERE email='admin@ind6token.com';"
```

**Expected Output:**
```
+----+------------+---------------------+------------+-------------+...
| id | name       | email               | phone      | password    |...
+----+------------+---------------------+------------+-------------+...
|  1 | Admin User | admin@ind6token.com | 1234567890 | password123 |...
+----+------------+---------------------+------------+-------------+...
```

If user not found, create it:
```bash
/Applications/MAMP/Library/bin/mysql80/bin/mysql -uroot -proot -P8889 -h127.0.0.1 ind6token_admin -e "INSERT INTO vendors (name, email, phone, password, wallet_address, created_at, updated_at) VALUES ('Admin User', 'admin@ind6token.com', '1234567890', 'password123', '0x0000000000000000000000000000000000000000', NOW(), NOW());"
```

---

### 3. ❌ Wrong Password in Database

**Symptoms:**
- Error message: "Invalid email or password"
- User exists but login fails

**Solution:**
Update the password in database:
```bash
/Applications/MAMP/Library/bin/mysql80/bin/mysql -uroot -proot -P8889 -h127.0.0.1 ind6token_admin -e "UPDATE vendors SET password='password123' WHERE email='admin@ind6token.com';"
```

Verify:
```bash
/Applications/MAMP/Library/bin/mysql80/bin/mysql -uroot -proot -P8889 -h127.0.0.1 ind6token_admin -e "SELECT email, password FROM vendors WHERE email='admin@ind6token.com';"
```

---

### 4. ❌ Base URL Misconfigured

**Symptoms:**
- Redirect goes to wrong URL
- 404 error after login

**Check:**
Open `app/Config/App.php` and verify:
```php
public string $baseURL = 'http://localhost:8888/Ind6TokenVendor/';
public string $indexPage = '';
```

---

### 5. ❌ Auth Filter Blocking

**Symptoms:**
- Redirects back to login immediately
- Infinite redirect loop

**Check:**
The dashboard route has an auth filter. Make sure session is being set correctly.

View `app/Config/Routes.php` line 12:
```php
$routes->get('/', 'Home::index', ['filter' => 'auth']);
```

This requires `isLoggedIn` to be true in session.

---

## Debug Steps

### Step 1: Check Logs
After trying to login, check the log file:
```bash
tail -f /Applications/MAMP/htdocs/Ind6TokenVendor/writable/logs/log-$(date +%Y-%m-%d).php
```

Look for lines like:
- `Login attempt for email: admin@ind6token.com`
- `User found: admin@ind6token.com`
- `Password matched` or `Password mismatch`
- `Session set for user`

### Step 2: Test Database Connection
```bash
/Applications/MAMP/bin/php/php8.5.0/bin/php -r "
\$db = new mysqli('localhost', 'root', 'root', 'ind6token_admin', 8889);
if (\$db->connect_error) die('Failed: ' . \$db->connect_error);
echo 'Connected!' . PHP_EOL;
\$result = \$db->query(\"SELECT email, password FROM vendors WHERE email='admin@ind6token.com'\");
\$row = \$result->fetch_assoc();
echo 'Email: ' . \$row['email'] . PHP_EOL;
echo 'Password: ' . \$row['password'] . PHP_EOL;
"
```

### Step 3: Test Session
Create a test file `public/test_session.php`:
```php
<?php
session_start();
$_SESSION['test'] = 'working';
echo 'Session ID: ' . session_id() . '<br>';
echo 'Session Test: ' . $_SESSION['test'] . '<br>';
echo 'Session working: ' . (isset($_SESSION['test']) ? 'YES' : 'NO');
```

Visit: `http://localhost:8888/Ind6TokenVendor/test_session.php`

### Step 4: Clear All Sessions
```bash
rm -rf /Applications/MAMP/htdocs/Ind6TokenVendor/writable/session/*
```

Then try logging in again.

---

## Manual Login Test

Try this direct database test:

```bash
/Applications/MAMP/bin/php/php8.5.0/bin/php -r "
require '/Applications/MAMP/htdocs/Ind6TokenVendor/vendor/autoload.php';

// Test credentials
\$email = 'admin@ind6token.com';
\$password = 'password123';

// Connect to database
\$db = new mysqli('localhost', 'root', 'root', 'ind6token_admin', 8889);

// Find user
\$stmt = \$db->prepare('SELECT id, name, email, password FROM vendors WHERE email = ?');
\$stmt->bind_param('s', \$email);
\$stmt->execute();
\$result = \$stmt->get_result();
\$user = \$result->fetch_assoc();

if (\$user) {
    echo 'User found: ' . \$user['email'] . PHP_EOL;
    echo 'Stored password: [' . \$user['password'] . ']' . PHP_EOL;
    echo 'Input password: [' . \$password . ']' . PHP_EOL;
    
    if (trim(\$password) === trim(\$user['password'])) {
        echo 'PASSWORD MATCH! Login should work.' . PHP_EOL;
    } else {
        echo 'PASSWORD MISMATCH! This is the problem.' . PHP_EOL;
        echo 'Stored length: ' . strlen(\$user['password']) . PHP_EOL;
        echo 'Input length: ' . strlen(\$password) . PHP_EOL;
    }
} else {
    echo 'User not found!' . PHP_EOL;
}
"
```

---

## Check These Files

### 1. Database Config
`app/Config/Database.php` should have:
```php
'hostname' => 'localhost',
'username' => 'root',
'password' => 'root',
'database' => 'ind6token_admin',
'port'     => 8889,
```

### 2. App Config
`app/Config/App.php` should have:
```php
public string $baseURL = 'http://localhost:8888/Ind6TokenVendor/';
public string $indexPage = '';
```

### 3. Session Config
`app/Config/Session.php` should have:
```php
public string $driver = FileHandler::class;
public string $savePath = WRITEPATH . 'session';
```

---

## Nuclear Option: Reset Everything

If nothing works, reset everything:

```bash
cd /Applications/MAMP/htdocs/Ind6TokenVendor

# 1. Clear sessions
rm -rf writable/session/*

# 2. Clear logs
rm -rf writable/logs/*

# 3. Recreate user
/Applications/MAMP/Library/bin/mysql80/bin/mysql -uroot -proot -P8889 -h127.0.0.1 ind6token_admin -e "DELETE FROM vendors WHERE email='admin@ind6token.com';"

/Applications/MAMP/Library/bin/mysql80/bin/mysql -uroot -proot -P8889 -h127.0.0.1 ind6token_admin -e "INSERT INTO vendors (name, email, phone, password, wallet_address, created_at, updated_at) VALUES ('Admin User', 'admin@ind6token.com', '1234567890', 'password123', '0x0000000000000000000000000000000000000000', NOW(), NOW());"

# 4. Fix permissions
chmod -R 777 writable/

# 5. Clear browser cache and cookies
# Do this manually in your browser
```

Then try logging in again.

---

## Expected Behavior

### When Login Works:
1. Enter credentials on login page
2. Click "Sign in"
3. Page redirects to: `http://localhost:8888/Ind6TokenVendor/`
4. Dashboard loads with statistics
5. No errors in browser console

### When Login Fails:
1. Enter credentials
2. Click "Sign in"
3. Page reloads to login page
4. Error message shows: "Invalid email or password"
5. Email field is pre-filled (from withInput())

---

## Still Not Working?

If you've tried everything above and it still doesn't work:

1. **Visit the test page:**
   ```
   http://localhost:8888/Ind6TokenVendor/test_login.php
   ```

2. **Check the logs:**
   ```bash
   tail -100 /Applications/MAMP/htdocs/Ind6TokenVendor/writable/logs/log-$(date +%Y-%m-%d).php
   ```

3. **Enable debugging:**
   Edit `app/Config/Boot/development.php` and ensure:
   ```php
   ini_set('display_errors', '1');
   error_reporting(E_ALL);
   ```

4. **Check browser console:**
   - Open Developer Tools (F12)
   - Go to Console tab
   - Try logging in
   - Look for JavaScript errors

5. **Check Network tab:**
   - Open Developer Tools (F12)
   - Go to Network tab
   - Try logging in
   - Check the POST request to `auth/login`
   - See what response you get

---

## Contact Information

If you need help, provide:
1. Output from test_login.php
2. Last 50 lines from log file
3. Browser console errors
4. Network tab response from login POST request
