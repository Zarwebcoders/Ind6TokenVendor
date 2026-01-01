# Login Redirect and Error Display Fix

## Issues Identified

### 1. **URL Redirect Loop Problem**
When login failed, the page was redirecting to `auth/login/` (with trailing slash), which was then being processed by `.htaccess` rules, causing URL malformation and redirect loops.

### 2. **Error Messages Not Displaying**
Flash data (error messages) were being lost during the redirect process because `redirect()->back()` was causing issues with the URL rewriting.

### 3. **Configuration Issues**
- `indexPage` was set to `'index.php'` which was interfering with clean URLs
- Incorrect model reference (`AdminModel` instead of `VendorModel`)

## Changes Made

### 1. **app/Config/App.php** (Line 43)
**Before:**
```php
public string $indexPage = 'index.php';
```

**After:**
```php
public string $indexPage = '';
```

**Why:** This enables clean URLs and prevents the redirect loop caused by `index.php` being added to URLs.

---

### 2. **app/Controllers/Auth.php** (Line 28)
**Before:**
```php
return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
```

**After:**
```php
return redirect()->to('auth/login')->withInput()->with('errors', $this->validator->getErrors());
```

**Why:** Using explicit route instead of `->back()` prevents URL malformation and ensures flash data is preserved.

---

### 3. **app/Controllers/Auth.php** (Line 50)
**Before:**
```php
return redirect()->back()->withInput()->with('error', 'Invalid email or password.');
```

**After:**
```php
return redirect()->to('auth/login')->withInput()->with('error', 'Invalid email or password.');
```

**Why:** Same as above - explicit routing prevents redirect issues.

---

### 4. **app/Controllers/Auth.php** (Line 60)
**Before:**
```php
$adminModel = new AdminModel();
```

**After:**
```php
$adminModel = new VendorModel();
```

**Why:** Fixed incorrect model reference to match the imported model class.

---

## How to Test

1. **Clear Browser Cache and Cookies** (important!)
   - Open your browser's developer tools (F12)
   - Go to Application/Storage tab
   - Clear all cookies and cache for localhost

2. **Test Invalid Login:**
   - Go to: `http://localhost:8888/Ind6TokenVendor/auth/login`
   - Enter incorrect credentials
   - Click "Sign in"
   - **Expected Result:** 
     - URL should remain `http://localhost:8888/Ind6TokenVendor/auth/login`
     - Error message "Invalid email or password." should display in red box

3. **Test Validation Errors:**
   - Go to: `http://localhost:8888/Ind6TokenVendor/auth/login`
   - Enter invalid email format (e.g., "notanemail")
   - Enter password less than 6 characters
   - Click "Sign in"
   - **Expected Result:**
     - URL should remain `http://localhost:8888/Ind6TokenVendor/auth/login`
     - Validation errors should display in red box

4. **Test Successful Login:**
   - Go to: `http://localhost:8888/Ind6TokenVendor/auth/login`
   - Enter valid credentials (admin@ind6token.com / password123)
   - Click "Sign in"
   - **Expected Result:**
     - Should redirect to dashboard at `http://localhost:8888/Ind6TokenVendor/`

## Additional Notes

### Session Storage
- Sessions are stored in `writable/session/` directory
- Make sure this directory exists and is writable
- If you still have issues, check folder permissions:
  ```bash
  chmod -R 777 writable/
  ```

### Debugging Tips
If errors still don't show:
1. Check if `writable/session/` directory exists
2. Enable debugging in `.env` file:
   ```
   CI_ENVIRONMENT = development
   ```
3. Check browser console for JavaScript errors
4. Check MAMP error logs

### URL Structure
With the fixes, your URLs should now be clean:
- ✅ `http://localhost:8888/Ind6TokenVendor/auth/login`
- ❌ `http://localhost:8888/Ind6TokenVendor/auth/login/` (no trailing slash)
- ❌ `http://localhost:8888/Ind6TokenVendor/index.php/auth/login` (no index.php)

## What Was Happening Before

1. User submits invalid login
2. Controller tries to redirect back with `->back()`
3. URL becomes `auth/login/` (with trailing slash)
4. `.htaccess` processes this and adds `index.php`
5. URL becomes malformed like `auth/login/index.php/auth/login`
6. Flash data gets lost in the redirect chain
7. User sees no error message

## What Happens Now

1. User submits invalid login
2. Controller redirects to explicit route `'auth/login'`
3. URL stays clean: `auth/login`
4. Flash data is preserved
5. Error message displays correctly
