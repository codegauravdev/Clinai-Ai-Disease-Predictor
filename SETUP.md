# ClinAI — Setup Guide for XAMPP

## Quick Start (5 minutes)

### Step 1 — Place files
Copy the entire `disease-predictor` folder to:
```
C:\xampp\htdocs\disease-predictor\
```

### Step 2 — Start XAMPP
1. Open **XAMPP Control Panel**
2. Click **Start** next to **Apache**
3. Click **Start** next to **MySQL**

### Step 3 — Create the database
1. Open browser → `http://localhost/phpmyadmin`
2. Click **New** (left sidebar)
3. Name: `clinai_db` → click **Create**
4. Select `clinai_db` → click **Import** tab
5. Click **Choose File** → select `disease-predictor/database/clinai_db.sql`
6. Click **Go** — all tables will be created automatically

### Step 4 — Open the app
```
http://localhost/disease-predictor/login.php
```

### Demo login credentials
| Field    | Value               |
|----------|---------------------|
| Email    | admin@clinai.local  |
| Password | Admin@123           |

---

## File Structure
```
disease-predictor/
├── login.php           ← Login & Register page
├── index.php           ← Main AI predictor (requires login)
├── history.php         ← Assessment history & dashboard
│
├── php/
│   ├── config.php      ← Database config & session helpers
│   ├── auth.php        ← Login / register / logout API
│   └── api.php         ← Save assessments, fetch history, stats
│
├── database/
│   └── clinai_db.sql   ← Import this into phpMyAdmin
│
├── css/
│   └── style.css       ← Full stylesheet (glassmorphism + dark mode)
│
└── js/
    ├── diseases.js     ← Disease knowledge base (15 conditions, 80+ symptoms)
    ├── engine.js       ← AI scoring engine
    └── app.js          ← UI controller
```

---

## Changing Database Password
If your XAMPP MySQL has a password, edit `php/config.php`:
```php
define('DB_PASS', 'your_password_here');
```

## Creating More Users
Use the **Register** tab on the login page, or insert directly:
```sql
INSERT INTO users (full_name, email, password, role, department)
VALUES ('Dr. Jane Smith', 'jane@hospital.com',
        '$2y$10$...bcrypt_hash...', 'doctor', 'Cardiology');
```

To generate a bcrypt hash for a password:
```php
echo password_hash('YourPassword123', PASSWORD_BCRYPT);
```

## Default Roles
| Role   | Access                              |
|--------|-------------------------------------|
| doctor | New assessments, own history        |
| nurse  | New assessments, own history        |
| admin  | New assessments, ALL history, stats |
