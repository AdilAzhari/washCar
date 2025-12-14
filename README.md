# WashyWashy Pro - Car Wash Management System

A comprehensive car wash management system built with Laravel 12, Inertia.js, and Vue 3. Manage branches, bays, customers, packages, staff, inventory, and queue operations efficiently.

![Laravel](https://img.shields.io/badge/Laravel-12.x-red)
![Vue](https://img.shields.io/badge/Vue-3.x-green)
![Inertia](https://img.shields.io/badge/Inertia.js-2.x-blue)
![TypeScript](https://img.shields.io/badge/TypeScript-5.x-blue)

## Features

### 🏢 Branch Management
- Create and manage multiple branch locations
- Track branch performance and statistics
- Generate QR codes for customer queue joining
- View detailed branch analytics and revenue trends
- Staff assignment to branches

### 🚿 Bay Management
- Monitor bay status (idle, active, maintenance)
- Real-time bay availability tracking
- Quick status updates
- Bay activity logging
- Performance metrics per bay

### 👥 Customer Management
- Customer profiles with vehicle information
- Membership tiers (Regular, Silver, Gold, Platinum)
- Wash history tracking
- Customer loyalty tracking
- Contact management

### 📦 Package Management
- Customizable wash packages
- Pricing and duration configuration
- Color-coded packages for easy identification
- Package popularity tracking
- Active/inactive status management

### 📋 Queue Management
- Two-view queue system:
  - **Queue Management**: Add customers to queue, manage positions
  - **View Queue**: Live operations view with waiting and in-progress sections
- Real-time queue position tracking
- Automatic bay assignment
- Queue statistics and wait time tracking
- Public QR code-based queue joining

### 👨‍💼 Staff Management
- Staff profiles with role assignment
- Branch assignment for staff members
- Role-based permissions (Admin, Manager, Staff)
- Staff activity tracking

### 📊 Inventory Management
- Track supplies and consumables
- Low stock alerts
- Category-based organization
- Branch-specific inventory
- Usage tracking and reporting

### 💰 Transaction Management
- Complete wash transaction history
- Revenue tracking and reporting
- Payment processing
- Transaction search and filtering

### 🔔 Notifications
- System-wide notification center
- Important updates and alerts
- Activity notifications
- Staff assignments and updates

### 📈 Dashboard & Analytics
- Real-time business metrics
- Revenue trends and forecasts
- Bay utilization statistics
- Customer growth tracking
- Package performance analysis
- Branch comparison tools

## Technology Stack

### Backend
- **Laravel 12** - PHP framework
- **Inertia.js** - Server-driven single-page app framework
- **SQLite/MySQL** - Database (SQLite for dev, MySQL for production)
- **PHP 8.2+** - Programming language

### Frontend
- **Vue 3** - JavaScript framework
- **TypeScript** - Type-safe JavaScript
- **Tailwind CSS** - Utility-first CSS framework
- **Vite** - Frontend build tool
- **Radix Vue** - Headless UI components
- **Lucide Icons** - Icon library
- **Vue Sonner** - Toast notifications
- **QRCode** - QR code generation

### Additional Libraries
- **class-variance-authority** - Component variants
- **clsx** - Class name utility
- **tailwind-merge** - Tailwind class merging
- **Simple QR Code** - Server-side QR generation

## Requirements

- PHP >= 8.2
- Composer
- Node.js >= 18.x
- npm or yarn
- MySQL 5.7+ or SQLite 3.x
- Git

## Installation

### Local Development

1. **Clone the repository**
   ```bash
   git clone https://github.com/YOUR_USERNAME/washywashy-pro.git
   cd washywashy-pro
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   - For SQLite (default):
     ```bash
     touch database/database.sqlite
     ```
   - For MySQL: Update `.env` with your database credentials

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Seed the database (optional)**
   ```bash
   php artisan db:seed
   ```

8. **Build frontend assets**
   ```bash
   npm run build
   # or for development with hot reload
   npm run dev
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   ```

10. **Visit the application**
    - Open your browser to `http://localhost:8000`
    - Default login: Use seeded credentials or register a new account

## Production Deployment

See [DEPLOYMENT.md](DEPLOYMENT.md) for detailed cPanel deployment instructions.

### Quick Production Setup

1. Clone repository to server
2. Run the deployment script:
   ```bash
   chmod +x deploy.sh
   ./deploy.sh
   ```
3. Configure `.env` for production
4. Point domain to `public` directory
5. Create admin user via tinker

## Project Structure

```
washywashy-pro/
├── app/
│   ├── Http/Controllers/      # Application controllers
│   ├── Models/                # Eloquent models
│   └── ...
├── database/
│   ├── migrations/            # Database migrations
│   └── seeders/               # Database seeders
├── resources/
│   ├── js/
│   │   ├── Components/        # Vue components
│   │   ├── Layouts/          # Layout components
│   │   └── Pages/            # Inertia pages
│   └── css/                  # Stylesheets
├── routes/
│   └── web.php               # Web routes
├── public/                   # Public assets
├── .env.example              # Environment template
├── deploy.sh                 # Deployment script
└── DEPLOYMENT.md             # Deployment guide
```

## Key Features Explained

### Queue System

The queue system has two interfaces:

1. **Management View** (`/queue`) - For staff to add customers and manage the queue
2. **Operations View** (`/queue-view`) - Live view showing:
   - Left side: Customers waiting in queue
   - Right side: Active washes in progress
   - Top: Real-time statistics

### QR Code Queue Joining

1. Each branch has a unique QR code
2. Customers scan the code to join the queue
3. They fill in vehicle details and select a package
4. They receive a queue number and can track status

### Branch Analytics

Branch detail pages show:
- Today's statistics (revenue, completed washes, etc.)
- 6-month revenue trend with visual graphs
- Staff roster and management
- Branch-specific settings

## API Routes

Main routes structure:

```
/dashboard              - Main dashboard
/branches              - Branch management
/bays                  - Bay management
/queue                 - Queue management
/queue-view            - Live queue operations
/customers             - Customer management
/packages              - Package management
/staff                 - Staff management
/inventory             - Inventory management
/transactions          - Transaction history
/notifications         - Notification center
```

## Development

### Running Tests
```bash
php artisan test
```

### Code Style
```bash
# PHP
./vendor/bin/pint

# JavaScript/Vue
npm run lint
```

### Database Reset
```bash
php artisan migrate:fresh --seed
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## Security

If you discover any security-related issues, please email security@yourdomain.com instead of using the issue tracker.

## License

This project is proprietary software. All rights reserved.

## Credits

- Built with [Laravel](https://laravel.com)
- UI powered by [Inertia.js](https://inertiajs.com) and [Vue 3](https://vuejs.org)
- Styled with [Tailwind CSS](https://tailwindcss.com)
- Icons by [Lucide](https://lucide.dev)

## Support

For support and questions:
- Check the [documentation](DEPLOYMENT.md)
- Create an issue on GitHub
- Contact: support@yourdomain.com

---

Made with ❤️ for efficient car wash management
