# E-Commerce Web Application Project

This project is a PHP-based e-commerce website developed for educational purposes. It demonstrates the implementation of a basic online bookstore with user authentication, shopping cart functionality, and an administrative panel for book management. The application showcases fundamental web development concepts including server-side scripting, database interactions, and client-side AJAX for dynamic user experiences.


- **Server Environment**: Apache web server (via WAMP)
- **Database**: MySQL for data storage and retrieval

## Setup Instructions


### Installation Steps
1. Place the project folder in your WAMP server's www directory 
2. Create a new database in MySQL (via phpMyAdmin or command line).
using file "schema.sql"(you will find it in database folder)

3. Import the `database/schema.sql` file into the newly created database to set up the required tables.
4. Verify and update database connection details in `src/config/Database.php` if necessary (default settings: localhost, root user, no password).

accounts : 
admin:  username : admin , password : admin123
user:   usname : user , password : user123


## Project Structure
- `database/`: SQL schema file for database initialization.
- `public/`: Publicly accessible web files including the main entry point (index.php), user pages, CSS stylesheets, JavaScript files, and images.
- `src/classes/`: PHP class files encapsulating business logic for authentication, books, cart, and user management.
- `src/config/`: Configuration file for database connection settings.
- `api/`: Server-side scripts for AJAX-based interactions.

