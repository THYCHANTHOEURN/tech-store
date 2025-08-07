# Tech Store – Modern Laravel & Vue.js E-commerce Platform

A robust, scalable e-commerce solution for tech products, built with **Laravel** (PHP) and **Vue.js** (SPA via Inertia.js). Includes advanced admin dashboard, notifications, messaging, role-based access, and flexible settings.

---

## 🚀 Features

- **Authentication & Authorization**: Secure login, registration, email verification, password reset, and role/permission management (Spatie).
- **User Management**: Admin, Super Admin, Manager, Customer roles. CRUD for users and customers.
- **Product Catalog**: Categories, brands, products, banners, reviews, wishlists, inventory management.
- **Order Processing**: Cart, checkout, payment, order tracking, invoices.
- **Messaging System**: Customer support threads, admin replies, attachments, status (active/closed).
- **Notifications**: Real-time notifications for orders, messages, system events (database + email).
- **Settings Management**: Site-wide configuration, dynamic options, grouped settings.
- **Data Export/Import**: Excel/CSV export for products, users, orders, brands, categories.
- **Dashboard Analytics**: Sales, orders, inventory alerts, unread messages/notifications.
- **Responsive UI**: Vuetify-based SPA, mobile-friendly, fast navigation.
- **Extensible**: Modular codebase, easy to add new features.

---

## 🛠️ Tech Stack

- **Backend**: Laravel 10+, PHP 8.1+
- **Frontend**: Vue.js 3, Inertia.js, Vuetify
- **Database**: MySQL (or compatible)
- **Notifications**: Laravel Notifications (database, mail)
- **Authorization**: Spatie Laravel-Permission
- **Excel Export**: Maatwebsite Laravel-Excel
- **Charts**: ApexCharts
- **Testing**: Pest, PHPUnit

---

## 📦 Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd tech-store
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   Edit `.env` with your DB credentials.

6. **Run migrations & seeders**
   ```bash
   php artisan migrate --seed
   # or for a fresh start
   php artisan migrate:fresh --seed
   ```

7. **Link storage**
   ```bash
   php artisan storage:link
   ```

8. **(Optional) Download demo assets**
   ```bash
   php artisan assets:download
   ```

9. **Build frontend assets**
   ```bash
   npm run dev
   ```

10. **Start development server**
    ```bash
    php artisan serve
    ```

---

## 🗂️ Project Structure

- **app/**: Core Laravel code (Controllers, Models, Policies, Providers, Enums, Notifications, Exports)
- **resources/js/**: Vue SPA (Pages, Components, Composables, Plugins)
- **database/**: Migrations, Seeders, Factories
- **routes/**: Web, dashboard, API, auth routes
- **config/**: Laravel configuration files

---

## 👤 User Roles & Permissions

- **Super Admin**: Full system access
- **Admin**: Manage users, products, orders, settings
- **Manager**: Product & order management
- **Customer**: Shopping, reviews, messaging

Role/permission logic powered by Spatie Laravel-Permission. Easily extendable for custom roles.

---

## 📈 Dashboard & Admin Features

- **Notifications**: Real-time, mark as read, unread count, dropdown, pagination
- **Messaging**: Customer support threads, admin replies, attachments
- **Settings**: Dynamic, grouped, option fields
- **Exports**: Download data as Excel/CSV
- **Inventory Alerts**: Low stock, bulk updates

---

## 🧑‍💻 Development Workflow

- **Branching**: `main` (production), `develop` (staging), `feature/*` (features)
- **Commits**: Descriptive messages, reference issues
- **Pull Requests**: Feature branch → develop, review required

---

## 🤝 Contributing

1. Fork the repo
2. Create a feature branch (`git checkout -b feature/my-feature`)
3. Commit & push (`git commit -m 'Add feature'`)
4. Open a Pull Request

---

## 📄 License

MIT License – see LICENSE file.

---

## 🙏 Acknowledgments

- [Laravel](https://laravel.com/)
- [Vue.js](https://vuejs.org/)
- [Vuetify](https://vuetifyjs.com/)
- [ApexCharts](https://apexcharts.com/)
- [Laravel Excel](https://laravel-excel.com/)
- [Spatie Laravel-Permission](https://spatie.be/docs/laravel-permission/v5/introduction)
## Acknowledgments

- Built with [Laravel](https://laravel.com/)
- UI components by [Vuetify](https://vuetifyjs.com/)
- Charts by [ApexCharts](https://apexcharts.com/)
- Excel exports by [Laravel Excel](https://laravel-excel.com/)
