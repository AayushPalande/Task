CRUD Application with PHP and MySQL
This project implements a simple CRUD Application using PHP for backend logic and MySQL for data storage. Users can be added, edited, deleted, and viewed through a web interface with pagination.


Features
Add User: Enter details including name, email, mobile, gender, and experiences (company, years, months).
Edit User: Update user details and experiences.
Delete User: Remove users from the database.
View Users: Paginated display of users with total companies served and total experience in years and months.

Prerequisites
XAMPP: Ensure XAMPP is installed on your system. You can download it from https://www.apachefriends.org/index.html.
Web Browser: Any modern web browser (Chrome, Firefox, Edge, etc.).
Before running the application, ensure you have the following installed:
PHP (version 7.x recommended)
MySQL (or MariaDB)
Web server (Apache, Nginx, etc.)

Setup Instructions
Download the Project:
Clone the repository or download the ZIP file.
Move Files:
Place the project files into your web server directory (e.g., htdocs for XAMPP).
Create Database:
Start XAMPP and Open phpMyAdmin
Open XAMPP Control Panel:

Start the Apache and MySQL modules by clicking on the "Start" button next to each.
Open phpMyAdmin:

Open your web browser and go to http://localhost/phpmyadmin/.
Create Database
Create a New Database:
Click on the "Databases" tab at the top.
In the "Create database" field, enter the name crud_app.
Click on the "Create" button.
Create Tables
Table: users
Select Your Database:

Click on the name of the database crudapp in the left sidebar.
Create a New Table:

In the "Create table" section, enter the table name users.
For the number of columns, enter 5.
Define Columns for users Table:

Fill in the columns with the following details:
id: INT, PRIMARY KEY, AUTO_INCREMENT
name: VARCHAR(100)
email: VARCHAR(100)
mobile: VARCHAR(15), UNIQUE
gender: ENUM('Male', 'Female', 'Other')
Click the "Save" button to create the table.
Table: experiences
Create a New Table:

Click on the "Create table" link in the left sidebar.
Enter the table name experiences.
For the number of columns, enter 5.
Define Columns for experiences Table:

Fill in the columns with the following details:
id: INT, PRIMARY KEY, AUTO_INCREMENT
user_id: INT, INDEX
company: VARCHAR(100)
years: INT
months: INT
Click the "Save" button to create the table.

Run the Application
Download or clone the CRUD application code into your XAMPP's htdocs directory.
Access the application:
Open your web browser and go to http://localhost/Your_file_location/.

Usage

Adding a User:
Click on "Add User" on the main page.
Fill out the form with required details.
Optionally add experiences and click "Add User".

Editing a User:
Click "Edit" next to a user on the main page.
Update details in the form and click "Update".

Deleting a User:
Click "Delete" next to a user on the main page.
Confirm deletion when prompted.

Viewing Users:
Users are displayed in a paginated table format.
Navigate through pages using pagination links.

Files Structure
index.php: Main page displaying user list and actions.
add_user.php: Form to add a new user and handle form submission.
edit_user.php: Form to edit user details and handle update.
delete_user.php: Script to delete a user from the database.
db_config.php: Configuration file for database connection.
README.md: Documentation file you are currently reading.
