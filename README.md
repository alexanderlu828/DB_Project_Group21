# DB_Project_Group21

## **Project Introduction**

Our online vaccine appointment platform, "VaxHub" is designed to address the chaos in vaccination site scheduling caused by the increased public demand for vaccines. Users can use this platform to view different vaccination locations and vaccine inventory, and book the most suitable vaccination time slot. The system also provides various functionalities for administrators, such as inventory management and vaccination site management.

## **Installation and Setup**

Please go through the following steps to set up your environment.

### **Step 1: System Requirements** 

- PHP 7.4+
- XAMPP
- Composer
- PostgreSQL

### **Step 2: XAMPP Installation**

Download and install XAMPP from the [official website](https://www.apachefriends.org/index.html).

### **Step 3: Composer Installation**

Install Composer from the [official website](https://getcomposer.org/download/).

### **Step 4: Project Deployment**

Unzip the provided project archive into the **`htdocs`** directory of XAMPP.

### **Step 5: Composer Dependencies**

Open your system console, navigate to the project folder, and execute **`composer install`** to install the required PHP packages, including Eloquent ORM. 

### **Step 6: Database Configuration** (skip this step if you have done so before)

Create a PostgreSQL database named **`Vaccination_system`**. Import the provided **`.sql`** file to populate your database with the necessary tables and data.

### **Step 7: Eloquent Configuration**

Configure the database connection in **`eloquent.php`** with your PostgreSQL credentials. Put your password in **`db_password.txt`**. 

### **Step 8: PHP Connection Settings**

Modify the database connection settings in **`user.php`**, **`admin.php`** and **`admin2.php`** files to match your PostgreSQL credentials. Put your password in **`db_password.txt`**. 

### **Step 9: Installing PostgreSQL driver for PHP**

Go to your PHP directory (e.g., at **`C:\xampp\php`**) to edit php.ini using any plain text editor. Uncomment **`;extension=pdo_pgsql`** and **`;extension=pgsql'** by removing the semicolons. 

## **Example** ##

After completing the above steps, enter the URL **`http://localhost/DB_Project_Group21-main/index.php`** to access our system's homepage. If you are a user, please click on "User" to make inquiries and appointments. If you are an administrator, please click on "Administrator" for managing vaccination sites and inventory. The query function for administrators is located under "Administrator search". These web pages offer a user-friendly interface, enjoy it!
