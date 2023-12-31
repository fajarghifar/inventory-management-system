## ✨ Inventory Management System

Inventory Management System with Laravel 10 and MySql.

![Dashboard](https://user-images.githubusercontent.com/71541409/236858603-89e4be74-0a8b-4b4b-98b0-24e66ec5602d.png)

## 💀 Design Database
![Diagram Class](https://github.com/fajarghifar/inventory-management-system/assets/71541409/0c7d4163-96f5-4724-8741-4615e52ecf98)

## 😎 Features
- POS
- Orders
  - Pending Orders
  - Complete Orders
  - Pending Due
- Purchases
  - All Purchases
  - Approval Purchases
  - Purchase Report
- Products
- Customers
- Suppliers

## 🚀 How to Use

1. Clone Repository
2. 

```bash
git clone https://github.com/fajarghifar/inventory-management-system
```

4. Go into the repository 

```bash
cd inventory-management-system
```

5. Install Packages 

```bash
composer install
```


6. Copy `.env` file 

```bash

cp .env.example .env

```

7. Generate app key 

```bash
php artisan key:generate
```

8. Setting up your database credentials in your `.env` file.
9. Seed Database: 

```bash

php artisan migrate:fresh ---seed

```
10. Create Storage Link

```bash
php artisan storage:link
```

12. Install NPM dependencies 

```bash

npm install && npm run dev

```
14. Run 

```bash

php artisan serve

```
15. Try login with email: 

```bash

admin@admin.com

```
and password: 

```bash

password

```

## 🚀 Config

1. **Config Chart**

    Open file `./config/cart.php`. You can set a tax, format number, etc.
    > For More details, visit this link [hardevine/shoppingcart](https://packagist.org/packages/hardevine/shoppingcart).

## 📝 Contributing

If you have any ideas to make it more interesting, please send a PR or create an issue for a feature request.

# 🤝 License

### [MIT](LICENSE)

> Github [@fajarghifar](https://github.com/fajarghifar) &nbsp;&middot;&nbsp;
> Instagram [@fajarghifar](https://instagram.com/fajarghifar)
