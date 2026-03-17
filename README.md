# ✨ Comprehensive Inventory Management System

A robust, enterprise-grade **Inventory Management System** built with **Laravel 12** and **Livewire**. Designed specifically to streamline inventory tracking, sales, purchasing processes, and financial ledger management with dynamic localization.

![Dashboard Preview](public/images/screenshot.png)

## 🌟 Key Modules & Features

- **📊 Advanced Analytics Dashboard**
  - Real-time Total Sales & Net Cash Flow tracking.
  - Interactive ApexCharts for Sales & Cash Flow trends.
  - Quick insights: Top Selling Products, Top Customers, and Low Stock Alerts.

- **💳 Sales & POS (Point of Sale)**
  - Fast, intuitive POS interface designed for rapid checkouts.
  - Support for Global Discounts, Exact Cash computation, and Change tracking.
  - Direct integration with Invoice/Receipt printing.
  - Persistent cart state across sessions.

- **📦 Purchases & Receiving**
  - End-to-end Purchase Order workflow.
  - Seamless "Receive Items" action that updates real inventory balances automatically.
  - Supplier tracking and history filtering.

- **🗃️ Master Data Management**
  - **Products**: Manage stock, pricing (Buy/Sell margins), and associations.
  - **Categories & Units**: Structured tagging for efficient reporting.
  - **Customers & Suppliers**: Comprehensive contact books integrated globally.

- **💰 Finance Ledger & Cash Flow**
  - Integrated Double-entry style tracking for all Income and Expenses.
  - Dynamic Cash Flow reporting mapping POS sales to Income and Purchases to Expenses automatically.
  - Custom Income/Expense categorization.

- **⚙️ Dynamic Localization & Settings**
  - Global Store Information management.
  - **Fully Dynamic Currency Framework**: Customizable currency symbols, positions (left/right), thousands separators, decimal separators, and fractional precision. Changes apply globally to charts, tables, inputs, and receipts instantly.

## 🛠️ Tech Stack & Library Used

- **Framework**: Laravel 12.x
- **Frontend/Reactivity**: Laravel Livewire 3 + Alpine.js
- **Styling**: Tailwind CSS (Shadcn-inspired components)
- **Data Tables**: Livewire PowerGrid (with customized AJAX filters)
- **Charts**: ApexCharts
- **Icons**: Blade Heroicons
- **Database**: MySQL

## 🚀 Quick Start

Follow these steps to set up the project locally for development or testing.

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL Database

### Installation Steps

1. **Clone the repository:**
    ```bash
    git clone https://github.com/fajarghifar/inventory-management-system.git
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

6. **Configure your Database:**
    Open the `.env` file and set up your MySQL connection credentials:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=root
    DB_PASSWORD=
    ```

7. **Run database migrations and seeders:**
    This command will migrate all tables and inject default users, settings, products, and categories.
    ```bash
    php artisan migrate:fresh --seed
    ```

8. **Link storage for media/image files:**
    ```bash
    php artisan storage:link
    ```

9. **Install node modules and compile assets:**
    ```bash
    npm install
    npm run build
    ```

10. **Start the Laravel development server:**
    ```bash
    php artisan serve
    ```

11. **Login using the default admin credentials:**
    - **Username:** `admin`
    - **Password:** `password`

## 💡 Contributing

Have ideas to improve the system? Architecture enhancements, UI tweaks, or bug reports are welcome!
- Submit a **Pull Request (PR)**
- Create an **Issue** for feature requests or structural bugs

## 📄 License

Licensed under the [MIT License](LICENSE).

---

> Crafted by [Fajar Ghifar](https://github.com/fajarghifar) &nbsp;&middot;&nbsp; [YouTube](https://www.youtube.com/@fajarghifar) &nbsp;&middot;&nbsp; [Instagram](https://instagram.com/fajarghifar) &nbsp;&middot;&nbsp; [LinkedIn](https://www.linkedin.com/in/fajarghifar/)
