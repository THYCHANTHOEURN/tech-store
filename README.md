# Tech Store - E-commerce Platform

A modern e-commerce platform built with Laravel and Vue.js, designed to provide a seamless online shopping experience for tech products.

## Technologies Used

- **Backend:** Laravel 10+
- **Frontend:** Vue.js with Inertia.js
- **UI Framework:** Vuetify
- **Styling:** CSS with utility classes
- **Charts:** ApexCharts
- **Data Export:** Maatwebsite Excel

## Project Overview

Tech Store is a full-featured e-commerce platform specializing in tech products. It includes both customer-facing storefront and administrative backend capabilities.

### Key Features

- **User Management**: Authentication, authorization, and role-based access control
- **Product Management**: Full CRUD operations for products with categories and attributes
- **Order Processing**: Shopping cart, checkout, and order management
- **Review System**: Customer product reviews
- **Wishlist**: User product wishlists
- **Admin Dashboard**: Comprehensive dashboard with analytics
- **Settings Management**: Site-wide configuration management
- **Export Functionality**: Data export to Excel

## Getting Started

### Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js and NPM
- MySQL or compatible database

### Installation

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
   Update the `.env` file with your database credentials

6. **Run migrations and seed the database**
   ```bash
   php artisan migrate --seed
   ```

7. **Link storage**
   ```bash
   php artisan storage:link
   ```

8. **Download demo assets** (optional)
   ```bash
   php artisan app:download-assets
   ```

9. **Build frontend assets**
   ```bash
   npm run dev
   ```

10. **Start the development server**
    ```bash
    php artisan serve
    ```

## Project Structure

### Key Directories

- **app/**: Contains core application code
  - **Http/Controllers/**: All controllers organized by module
  - **Models/**: Database models
  - **Enums/**: Enumeration classes for roles and permissions
  - **Exports/**: Excel export classes
  - **Providers/**: Service providers
- **resources/**: Frontend resources
  - **js/Pages/**: Vue components organized by section
  - **js/Components/**: Reusable Vue components
  - **js/Plugins/**: Vue plugins like Vuetify
  - **css/**: CSS styles
- **database/**: Database migrations, seeders, and factories

## Development Workflow

### Branching Strategy

- **main**: Production-ready code
- **develop**: Development branch, stable features
- **feature/**: Feature-specific branches (branch off develop)

### Commit Guidelines

- Use descriptive commit messages
- Reference issue numbers when applicable
- Group related changes in single commits

### Pull Request Process

1. Create a feature branch from develop
2. Make your changes and test thoroughly
3. Submit a PR to develop branch
4. PR needs approval before merging

## User Roles

- **Super Admin**: Complete system access
- **Admin**: Administrative access with some restrictions
- **Manager**: Product and order management
- **Customer**: Standard user access

```

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

---

## Acknowledgments

- Built with [Laravel](https://laravel.com/)
- UI components by [Vuetify](https://vuetifyjs.com/)
- Charts by [ApexCharts](https://apexcharts.com/)
- Excel exports by [Laravel Excel](https://laravel-excel.com/)
