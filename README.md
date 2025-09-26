# POS System 🛒

A simple Point-of-Sale system built with PHP, CSS, JS for managing sales, products, categories, customers, and users.

---

## 🔍 Table of Contents

- [Features](#features)  
- [Tech Stack](#tech-stack)  
- [Installation](#installation)  
- [Usage](#usage)  
- [Project Structure](#project-structure)  
- [Contributing](#contributing)  
- [License](#license)

---

## ✅ Features

- Add, edit, delete **products**  
- Add, edit, delete **categories**  
- Add, edit, delete **customers**  
- Manage **sales** (add sales, report, manage)  
- User management (login, logout, permissions)  
- Simple report of sales  
- (Optional: search, sort, filtering by date / category if implemented)  

---

## 🛠 Tech Stack

| Component | Technology |
|-----------|-------------|
| Backend / Language | PHP |
| Frontend | HTML, CSS, JavaScript |
| Database | (e.g. MySQL OR SQLite OR what you're using) |
| Assets | CSS files, JS files, Images |
| UI / Templates | Plain PHP with includes, CSS styles |

---

## 🚀 Installation

These are general steps to run the project locally. Adjust based on your environment.

```bash
# Clone the repo
git clone https://github.com/justrhey/pos-system.git

# Move into project folder
cd pos-system

# Copy or configure database
# For example: create a database, import schema from `Database/` folder if present

# Configure your database credentials in the PHP config file
# (you might have a config file or inside include files)

# Start local server
# If using built-in PHP server:
php -S localhost:8000

# Or put files into Apache / Nginx root if you have a web server setup
