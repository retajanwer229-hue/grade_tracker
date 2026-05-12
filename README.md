# GradeTracker 📊

A web-based grade management application where students can log their marks per subject, view running averages, and track academic progress across a semester.

---

## Features

- **User Authentication** — Register and login with session-based security
- **Add Grades** — Log marks per subject and assessment with decimal support
- **View Grades** — Interactive table with sortable columns and subject filter
- **Edit & Delete** — Full CRUD operations on grade entries
- **Subject Summary** — Sidebar showing average and letter grade per subject
- **Overall Average** — GPA block showing overall semester performance
- **Performance Chart** — Bar chart visualizing averages per subject (green = pass, red = fail)
- **Responsive Design** — Works on mobile, tablet, and desktop

---

## Tech Stack

| Layer | Technology |
|---|---|
| Frontend | HTML5, CSS3, JavaScript (ES6+) |
| Backend | PHP 8, PDO |
| Database | MySQL |
| Charts | Chart.js |
| Server | Apache (XAMPP) |

---

## Project Structure

```
grade_tracker/
├── backend/
│   ├── db.php              # PDO database connection
│   ├── addGrade.php        # Add a new grade entry
│   ├── getGrades.php       # Fetch all grades as JSON
│   ├── editGrade.php       # Update an existing grade
│   ├── deleteGrade.php     # Delete a grade entry
│   ├── subjectSummary.php  # Average per subject as JSON
│   ├── login.php           # User login with session
│   ├── register.php        # User registration
│   └── logout.php          # Destroy session and redirect
├── index.html              # Main application page
├── style.css               # All styles (Grid, Flexbox, media queries)
└── app.js                  # All frontend logic (Fetch, sort, render)
```

---

## Setup Instructions

### Requirements
- XAMPP (Apache + MySQL)
- A modern web browser

### Steps

**1. Install XAMPP**
Download from [apachefriends.org](https://www.apachefriends.org) and install it.

**2. Start Apache and MySQL**
Open XAMPP Control Panel and start both Apache and MySQL.

**3. Copy project files**
Place the project folder inside:
```
C:\xampp\htdocs\grade_tracker\
```

**4. Create the database**
Open your browser and go to:
```
http://localhost/phpmyadmin
```
Create a new database named `grade_score`, then run this SQL:

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject VARCHAR(100) NOT NULL,
    assessment VARCHAR(100) NOT NULL,
    mark FLOAT NOT NULL,
    total FLOAT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

**5. Configure database connection**
Open `backend/db.php` and confirm these settings match your setup:
```php
$host = 'localhost';
$db   = 'grade_score';
$user = 'root';
$pass = '';
```

**6. Register and login**
Go to:
```
http://localhost/grade_tracker/backend/register.php
```
Create an account, then login at:
```
http://localhost/grade_tracker/backend/login.php
```

**7. Open the app**
After login you will be redirected automatically to:
```
http://localhost/grade_tracker/index.html
```

---

## Responsive Design

Tested on the following screen sizes:

| Screen | Size |
|---|---|
| Mobile | 375px (iPhone SE) |
| Tablet | 768px (iPad) |
| Desktop | 1440px (Full HD) |

---

## Screenshots

> Add screenshots of your app here after testing.

---

## License

This project was built as an academic submission for a Web Programming course.
