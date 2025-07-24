# PHP Simple Authentication App

This is a lightweight, dependency-free PHP application that demonstrates a basic user authentication system. It includes user login, a protected dashboard page, and a simple routing mechanism. It is designed for learning purposes with a focus on a simple setup.

## Project Structure

- `public/`: The web server's document root. All requests are handled by `index.php`.
- `pages/`: Contains the different pages of the application (e.g., `home.php`, `login.php`, `dashboard.php`).
- `lib/`: Contains core functionalities like the database connection script.
- `database_setup.sql`: The SQL script to set up the necessary database table.

---

## Getting Started on XAMPP

Follow these steps to get the project running on your local machine using XAMPP.

### 1\. Database Setup

1.  Make sure you have a MySQL server running from your XAMPP Control Panel.
2.  Create a new database named `my_app_db`. You can do this using phpMyAdmin.
3.  Import the `database_setup.sql` file to create the `users` table and insert a sample user.

<!-- end list -->

- **Sample User Credentials:**
  - **Email:** `test@example.com`
  - **Password:** `password123`

### 2\. Configure Database Connection

The database connection settings are located in `lib/database.php`. If your database credentials are different from the defaults below, please update the file accordingly. The default XAMPP setup usually has a `root` user with no password.

```php
return [
    'host' => '127.0.0.1',
    'dbname' => 'my_app_db',
    'user' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];
```

### 3\. Configure the Application URL

This is the most important step for making the app work in a subdirectory.

1.  **Place the project folder** inside your `C:/xampp/htdocs/` directory.
2.  **Open the `public/index.php` file**.
3.  At the top of the file, find the configuration variable named `$urlPrefix`.
4.  You must set this variable to match your folder path in the URL. For example, if your project folder is named `sia-project`, your URL will look like `http://localhost/sia-project/public/login`. Therefore, you must set the variable like this:
    ```php
    // The part of the URL path that comes before your routes.
    $urlPrefix = '/sia-project/public';
    ```

### 4\. Run the Project

You can now access the application by navigating to your local server URL in your web browser, making sure to include `/public/` at the end of the path.

- **Example:** `http://localhost/your-project-folder/public/`

---

### Troubleshooting

If you click on links like "Login" and get a "Not Found" error from Apache (not the application's 404 page), it means `mod_rewrite` may not be configured correctly in XAMPP.

1.  In the XAMPP Control Panel, go to Apache `Config` -\> `httpd.conf`.
2.  Make sure the line `LoadModule rewrite_module modules/mod_rewrite.so` does **not** have a `#` at the beginning.
3.  In the same file, find the `<Directory "C:/xampp/htdocs">` section and ensure it says `AllowOverride All`.
4.  **Restart Apache** after making any changes.
