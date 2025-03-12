# ✨ Inventory Management System

A robust Inventory Management System built with **Laravel 10** and **MySQL**, designed to streamline your inventory tracking, sales, and purchasing processes.

![Dashboard](https://github.com/user-attachments/assets/1df45f1d-aaed-4299-9b90-35e7f47dc7ea)

## 🗂️ Database Design

The system is structured using a clear and efficient database schema:

![Database Diagram](https://github.com/fajarghifar/inventory-management-system/assets/71541409/0c7d4163-96f5-4724-8741-4615e52ecf98)

## 🌟 Key Features

-   **POS (Point of Sale)**
-   **Orders**
    -   Pending Orders
    -   Complete Orders
    -   Pending Payments
-   **Purchases**
    -   All Purchases
    -   Approval Process
    -   Purchase Reports
-   **Products Management**
-   **Customer Records**
-   **Supplier Management**

## 🚀 Quick Start

Follow these steps to set up the project locally:

1. **Clone the repository:**

    ```bash
    git clone https://github.com/fajarghifar/inventory-management-system
    ```

2. **Navigate to the project folder:**

    ```bash
    cd inventory-management-system
    ```

3. **Install PHP dependencies:**

    ```bash
    composer install
    ```

4. **Copy `.env` configuration:**

    ```bash
    cp .env.example .env
    ```

5. **Generate application key:**

    ```bash
    php artisan key:generate
    ```

6. **Configure the database in the `.env` file** with your local credentials.

7. **Run database migrations and seed sample data:**

    ```bash
    php artisan migrate:fresh --seed
    ```

8. **Link storage for media files:**

    ```bash
    php artisan storage:link
    ```

9. **Install JavaScript and CSS dependencies:**

    ```bash
    npm install && npm run dev
    ```

10. **Start the Laravel development server:**

    ```bash
    php artisan serve
    ```

11. **Login using the default admin credentials:**

    - **Email:** `admin@admin.com`
    - **Password:** `password`

## 🐋 Docker set up

With Docker, you can easily start both the app and MySQL database without needing to install anything locally.

In the _docker-compose.yml_ file, you’ll find the configuration for MySQL.

_Note: The app uses the environment variables from .env.example.docker. If you want to change any configurations in this environment file, create a new .env file, make your changes, and then rebuild the Docker image._

**To pull and run the Docker images:**

This command will pull the _mysql:5.7_ and _inert/laravel-app_ images and start the containers:

_Note: You don't need to have the repository on your local machine, you just need the docker-compose.yml file and run:_

```bash
docker-compose up -d
```

Once the containers are up, the app will be available at http://localhost:8000/.

**To build the Docker image after code changes:**

If you’ve made changes to the code and want to update the Docker image, use the following command:

_Note: This command will reset your database. Make sure to back it up._

```bash
docker build -t inert/laravel-app .
```

**To tag the Docker image:**

```bash
docker tag inert/laravel-app inert/laravel-app:"tagname"
```

**To push the Docker image:**

```bash
docker push inert/laravel-app:"tagname"
```

## 💾 Backup the Database to a Safe Location

"In case you delete the MySQL Docker container by mistake"

**To export the database from the Docker container and save it to your desktop:**

Note: Change the path to your own.

powershell

```bash
docker exec mysql-db mysqldump -u root -p'examplepassword' inventory_management_system > "C:\Users\User\Desktop\backup.sql"
```

**To import the database from the desktop back into the Docker container:**

powershell

```bash
Get-Content "C:\Users\User\Desktop\backup.sql" | docker exec -i mysql-db mysql -u root -p'examplepassword' inventory_management_system
```

⚡ **To clean the database, remove example data, and create a new admin with a fresh database, run:**

```bash
docker exec -it laravel-app bash
```

```bash
php artisan migrate:fresh
```

```bash
php artisan tinker
```

```bash
use App\Models\User;
User::create([
    'name' => 'Super Admin',                // edit this
    'username' => 'name',
    'email' => 'email@email.al',            // edit this
    'password' => bcrypt('password'),       // edit this
    'role' => 'admin',
]);
```

## 🔧 Configuration

### Configuring Cart Settings:

-   To customize tax rates, number formatting, and more, open `./config/cart.php`.
-   For more details, check out the [hardevine/shoppingcart](https://packagist.org/packages/hardevine/shoppingcart) package.

## 💡 Contributing

Have ideas to improve the system? Feel free to:

-   Submit a **Pull Request (PR)**
-   Create an **Issue** for feature requests or bugs

## 📄 License

Licensed under the [MIT License](LICENSE).

---

> Find me on [GitHub](https://github.com/fajarghifar) &nbsp;&middot;&nbsp; [YouTube](https://www.youtube.com/@fajarghifar) &nbsp;&middot;&nbsp; [Instagram](https://instagram.com/fajarghifar) &nbsp;&middot;&nbsp; [LinkedIn](https://www.linkedin.com/in/fajarghifar/)

---
