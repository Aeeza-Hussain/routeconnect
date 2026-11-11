# RouteConnect 🚗📍

> **Know your route. Know your time. Travel smarter.**  
> An intelligent inter-city ride sharing, private vehicle charter, and travel request management platform built for modern regional transit.

---

## 🌟 Key Features

### 1. 💺 Shared Ride-Pooling with Segment-Based Seating
- **Scheduled Trips:** Verified drivers publish rides with origin, destination, intermediate stops, and departure times.
- **Segment Booking:** Passengers can book specific seats between any origin and destination stops along a route.
- **Atomic Inventory Control:** Real-time seat inventory calculation with pessimistic row locking prevents overbooking and race conditions.

### 2. 🚙 Private Vehicle Rental & Fleet Management
- **Full-Vehicle Charters:** Dedicated catalog for passengers and tourist groups to charter entire vehicles (Sedans, SUVs, Hiace Vans).
- **Calendar Conflict Prevention:** Automated scheduling engine verifies vehicle availability against active bookings, scheduled trips, and driver-blocked dates.

### 3. 📋 Travel Request Marketplace (Reverse Bidding)
- **Custom Travel Requests:** Passengers publish specific travel needs (route, preferred datetime, passenger count, vehicle type).
- **Driver Applications:** Verified drivers submit competitive bids with proposed fares.
- **Atomic Driver Selection:** Accepting a bid confirms the arrangement and rejects competing bids in a single atomic transaction.

### 4. 💬 In-Page Messaging & Live Chat Overlay Drawer
- **Zero Page Navigation Loss:** Seamless sliding chat drawer accessible from the navbar and listings.
- **Real-Time Coordination:** Direct messaging between passenger and driver for trip details and pickup coordination.
- **Unread Counters:** Dynamic navbar badge tracking for unread messages and system notifications.

### 5. 🛡️ Verification & Administrative Moderation
- **Driver Screening:** CNIC and driver's license document upload and verification workflows.
- **Vehicle Roadworthiness:** Registration and inspection approval gate before vehicles can be scheduled.
- **Role-Based Access Control:** Strict permission guards for **Guest**, **Passenger**, **Driver**, and **Admin**.

### 6. 📱 Flutter-Ready RESTful API
- **Laravel Sanctum Auth:** Stateless Bearer token authentication for cross-platform mobile apps (Android & iOS).
- **Comprehensive API Suite:** 10 RESTful controllers providing full parity with web features under `/api/*`.

---

## 🛠️ Technology Stack

| Layer | Technology | Details |
| :--- | :--- | :--- |
| **Backend Framework** | Laravel 12.0 | PHP 8.2+ MVC, Eloquent ORM, Migrations, Policies |
| **Database** | MySQL 8.0+ | ACID transactions, foreign keys, row-level pessimistic locking |
| **Authentication** | Laravel Sanctum | Session cookies for web; Bearer tokens for mobile APIs |
| **Frontend UI** | Blade & Tailwind CSS | Responsive UI, dark/light theme toggle, custom drawers |
| **Client Scripting** | Vanilla JS (ES6+) / AJAX | Asynchronous messaging drawer, dynamic stop recalculation |
| **Testing Suite** | PHPUnit 11.5 | 404 automated feature/unit tests, 1,931 assertions (100% pass) |
| **Asset Pipeline** | Vite 5.x | Modern frontend compilation and asset bundling |

---

## 🚀 Getting Started

### Prerequisites
- **PHP** >= 8.2 (with `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json` extensions)
- **Composer** >= 2.0
- **Node.js** >= 18.x & **NPM**
- **MySQL** >= 8.0 or MariaDB

### Installation Steps

1. **Clone the repository:**
   ```bash
   git clone https://github.com/Aeeza-Hussain/routeconnect.git
   cd routeconnect
   ```

2. **Install backend dependencies:**
   ```bash
   composer install
   ```

3. **Install frontend dependencies:**
   ```bash
   npm install
   npm run build
   ```

4. **Environment setup:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database in `.env`:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=travel_app
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Run migrations and seeders:**
   ```bash
   php artisan migrate --seed
   ```

7. **Create storage symlink:**
   ```bash
   php artisan storage:link
   ```

8. **Start development server:**
   ```bash
   php artisan serve
   ```
   Access the application at `http://127.0.0.1:8000`.

---

## 📡 RESTful API Overview (Flutter Integration)

All API endpoints are prefixed with `/api` and return standardized JSON responses.

### Public Endpoints
- `POST /api/auth/register` — Register a new Passenger or Driver account.
- `POST /api/auth/login` — Authenticate and receive a Sanctum `plainTextToken`.
- `GET /api/stops` & `GET /api/routes` — Reference transit stops and corridors.
- `GET /api/trips` & `GET /api/trips/{id}` — Search and inspect scheduled shared rides.
- `GET /api/vehicles` & `GET /api/vehicles/{id}` — Browse private charter fleet.

### Protected Endpoints (`Authorization: Bearer <token>`)
- `POST /api/auth/logout` — Revoke token and terminate session.
- `GET /api/auth/me` & `PUT /api/auth/profile` — View/update authenticated profile.
- `POST /api/trips/{trip}/book` — Reserve seats on a shared ride.
- `POST /api/vehicles/{vehicle}/book` — Charter a private vehicle.
- `GET /api/bookings` & `POST /api/bookings/{id}/cancel` — Manage active bookings.
- `GET/POST /api/travel-requests` — Publish and browse custom travel requests.
- `POST /api/travel-requests/{id}/apply` — Driver bid submission.
- `POST /api/travel-requests/{id}/applications/{app_id}/select` — Accept driver bid.
- `GET/POST /api/conversations/*` — Full messaging, in-app negotiation, and unread counts.
- `GET /api/activity/passenger` & `GET /api/activity/driver` — Role-based activity records.

---

## 🧪 Testing & Verification

RouteConnect is thoroughly tested with automated feature and unit tests covering authorization, business logic atomicity, API responses, and database integrity.

```bash
php artisan test
```

**Results:**
- **Tests:** 404 passed
- **Assertions:** 1,931 verified
- **Pass Rate:** 100.0%

---

## 👥 Roles & Permissions

- **Guest:** Browse landing page, search marketplace rides and vehicle rentals.
- **Passenger:** Book shared seats, charter private vehicles, post travel requests, chat with drivers, view My Activity.
- **Driver:** Register vehicles, manage availability calendar, schedule shared trips, bid on passenger requests, manage passenger manifests.
- **Admin:** Moderate platform users, approve/reject driver credentials and vehicle registrations, orchestrate routes and intermediate stops, monitor bookings.

---

## 📄 License

This project is licensed under the [MIT License](LICENSE).
