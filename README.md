# 🐝 Buzzly - PHP Social Media Platform

Buzzly is a lightweight, custom-built social media website developed using **PHP**, **MySQL**, and **AJAX**. It allows users to register, create profiles, upload multimedia content, and interact with other users in real-time without page reloads.

## 🚀 Key Features

*   **User Authentication:** Secure user registration, login, logout, and profile management.
*   **Media Uploads:** Support for uploading images and videos directly to the feed.
*   **Interactive AJAX Actions:** Dynamic like/unlike system, comment sections, and user follow/unfollow functionality.
*   **Modular Architecture:** Clean and structured PHP layout using reusable includes and dedicated AJAX handlers.
*   **Database Integration:** Ready-to-import MySQL database schema included (`buzzly.sql`).

## 📁 Project Structure

```text
buzzly/
├── index.php             # Main news feed page
├── login.php             # User login page
├── register.php          # User registration page
├── profile.php           # User profile page
├── upload.php            # Post creation page
├── assets/               # Frontend stylesheets and JS
├── includes/             # Database connection and core functions
├── ajax/                 # Background handlers for likes, follows, and comments
└── uploads/              # Storage directory for user media
```

## 🛠️ Tech Stack

*   **Backend:** PHP (Procedural/OOP)
*   **Database:** MySQL
*   **Frontend:** HTML5, CSS3, JavaScript (AJAX / jQuery)
