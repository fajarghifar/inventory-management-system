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

```bash
git clone https://github.com/tauseedzaman/inventory-management-system
```

2. Go into the repository 

```bash
cd inventory-management-system
```

3. Install Packages 

```bash
composer install
```


4. Copy `.env` file 

```bash

cp .env.example .env

```

5. Generate app key 

```bash
php artisan key:generate
```

6. Setting up your database credentials in your `.env` file.
7. Seed Database: 

```bash

php artisan migrate:fresh --seed

```
8. Create Storage Link

```bash
php artisan storage:link
```

9. Install NPM dependencies 

```bash

npm install && npm run dev

```
10. Run 

```bash

php artisan serve

```
11. Try login with email: 

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

### if you wana contribute to this project and add some features into it, here are some possable features you can add into this project..;)
[https://chat.openai.com/share/48776dd2-3b91-4b93-b7b5-8a728d39df4f](https://chat.openai.com/share/48776dd2-3b91-4b93-b7b5-8a728d39df4f)
# 🤝 License

### [MIT](LICENSE)

> Github [@fajarghifar](https://github.com/fajarghifar) &nbsp;&middot;&nbsp;
> Instagram [@fajarghifar](https://instagram.com/fajarghifar)
