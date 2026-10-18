# ÉLEVÉ — Every Step, Elevated.
> Minimalist Luxury Footwear E-Commerce Platform & Full-Stack Atelier Management Suite

---

## 🏛️ Project Overview

**ÉLEVÉ** is a full-stack minimalist luxury footwear e-commerce ecosystem consisting of:

1. **Mobile Application (`Eleve-Mobile-App`)**:
   - Built with **React Native** & **Expo Router** strictly in **`.jsx`**.
   - Luxury Obsidian & Ivory aesthetic (`#171717`, `#F7F5F0`, `#C5A880`, `#FFFFFF`).
   - Seamless **Dual Ambience (Dark / Light)** theme engine with zero flicker.
   - Slide-Over Drawer Navigation with tactile spring animations.
   - Real-Time Atelier Feed with live order status progression (3-second state machine).
   - Mandatory client authentication gate before purchase with automated bag preservation and cart merging.
   - Real Philippine Peso (`₱` PHP) pricing and currency formatting.

2. **Backend REST API & Admin Suite (`Eleve-backend`)**:
   - Built with **Laravel 12** utilizing strict OOP PDO architecture, Repository Pattern, and Form Requests.
   - Atomic transactions with PDO row-level locking (`lockForUpdate`) for concurrency-safe inventory decrements.
   - Sanctum Token Authentication & Guest Session Token (`X-Session-Token`) support.
   - Complete Blade + Tailwind CSS Admin Management Suite (Dashboard, Categories, Product Variant Matrix, Inventory, Order Fulfillment with Printable Packing Slip, Customer Directory, and Real-Time Notification Bell with Web Audio Synthesized Chime).

---

## 📁 Repository Structure

```
ELEVE/
├── Eleve-Mobile-App/        # React Native / Expo Router Mobile App (.jsx)
│   ├── src/
│   │   ├── app/             # File-based navigation (tabs, product, checkout, order, auth)
│   │   ├── components/      # Luxury UI components & drawer navigation
│   │   ├── constants/       # Design tokens, theme palettes & currency helpers
│   │   ├── context/         # Auth, Cart, Wishlist, Notifications & Theme providers
│   │   └── services/        # API client & AsyncStorage persistent storage
│   ├── package.json
│   └── app.json
│
├── Eleve-backend/           # Laravel 12 REST API & Blade Admin Dashboard
│   ├── app/
│   │   ├── Http/Controllers # API & Admin Controllers
│   │   ├── Models/          # Eloquent Models (Product, Order, Variant, Cart, etc.)
│   │   ├── Repositories/    # OOP Repository Pattern Implementations
│   │   └── Services/        # Business logic & 3-second Auto-Progression Engine
│   ├── database/            # Migrations & Seeders
│   ├── resources/views/     # Blade Admin templates with Dark/Light mode
│   ├── routes/              # api.php & web.php
│   └── composer.json
│
├── .gitignore               # Root git ignore
└── README.md                # Documentation
```

---

## 🚀 Quick Start Guide

### Prerequisites
- Node.js >= 18
- PHP >= 8.2 & Composer
- MySQL (e.g. XAMPP on localhost:3306, database: `eleve_db`)
- Android Studio / Android Emulator (or Expo Go app on a physical device)

---

### 1. Backend Setup (`Eleve-backend`)

```bash
cd Eleve-backend

# Install PHP dependencies
composer install

# Configure environment
cp .env.example .env
php artisan key:generate

# Run database migrations and luxury seeders
php artisan migrate:fresh --seed

# Start backend server
php artisan serve --host=0.0.0.0 --port=8000
```

- **Admin Portal**: `http://localhost:8000/admin/login`
  - Email: `admin@eleve.com`
  - Password: `password`
- **REST API Base URL**: `http://localhost:8000/api/v1`

---

### 2. Mobile App Setup (`Eleve-Mobile-App`)

```bash
cd Eleve-Mobile-App

# Install dependencies
npm install

# Port forwarding for Android Emulator (optional, runs automatically)
adb reverse tcp:8000 tcp:8000

# Start Expo Development Server
npm run android
# Or for interactive menu:
npx expo start
```

- **Test Customer Login**:
  - Email: `customer@eleve.com`
  - Password: `password`

---

## 🛡️ License & Atelier Copyright

Designed and developed for **ÉLEVÉ Paris** — *Every Step, Elevated.*  
All rights reserved.
