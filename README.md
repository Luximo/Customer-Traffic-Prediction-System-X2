# Customer Traffic Prediction System (CTPS)

![Version](https://img.shields.io/badge/version-1.0--prototype-blue)
![PHP](https://img.shields.io/badge/PHP-8.3-purple)
![Laravel](https://img.shields.io/badge/Laravel-12.x-red)
![Status](https://img.shields.io/badge/status-prototype-orange)

A local-first, Laravel-based system for forecasting customer foot traffic at retail locations. This prototype uses a hybrid prediction approach combining synthetic logic and historical data analysis to provide accurate traffic forecasts.

## 📋 Overview

This prototype was built for **Dorothy Lane Market** to help store managers optimize operations through data-driven decision-making. The system forecasts customer traffic patterns to support better:

- **Staffing allocation** during peak/off-peak hours
- **Inventory management** based on expected customer volume
- **Promotional planning** to maximize foot traffic conversion

The dashboard provides visualizations through dynamic charts, interactive heatmaps, and supports data import/export functionality.

## 🚀 Features

| Feature | Status | Description |
|---------|--------|-------------|
| Hourly Prediction Chart | ✅ | Visualizes predicted traffic from 8AM–9PM |
| Weekly Heatmap | ✅ | Calendar-style heat visualization by hour/day |
| Weekly Trend Analysis | ✅ | Aggregates total & average predicted customers |
| Manual Override System | ✅ | Allows managers to input expected values |
| Override Reset | ✅ | One-click reset of manual override data |
| CSV Data Import | ✅ | Upload & process historical traffic data |
| Prediction Source Indicator | ✅ | Transparent labeling of prediction methodology |
| Responsive Design | ✅ | Optimized for both desktop and mobile interfaces |
| Offline Capability | ✅ | Fully functional without internet connection |
| SQLite Integration | ✅ | Lightweight database for local operation |
| CSV Export | 🔜 | Export predictions (planned feature) |

## 🔧 Technical Architecture

### Core Components

```
┌─────────────────┐      ┌───────────────────┐      ┌──────────────────┐
│ Controllers     │      │ Services          │      │ Models           │
│ - Prediction    │─────▶│ - Prediction      │─────▶│ - TrafficData    │
│ - CsvImport     │      │ - DataProcessor   │      │                  │
└─────────────────┘      └───────────────────┘      └──────────────────┘
         │                        │                          │
         │                        │                          │
         ▼                        ▼                          ▼
┌─────────────────────────────────────────────────────────────────────┐
│                          Database Layer                              │
│                        (SQLite / Migration)                          │
└─────────────────────────────────────────────────────────────────────┘
         │                        │                          │
         │                        │                          │
         ▼                        ▼                          ▼
┌─────────────────────────────────────────────────────────────────────┐
│                          Frontend (Blade)                            │
│             - Charts - Heatmaps - Forms - Responsive UI             │
└─────────────────────────────────────────────────────────────────────┘
```

### File Structure

| Path | Purpose |
|------|---------|
| `routes/web.php` | Route definitions and middleware configuration |
| `app/Http/Controllers/PredictionController.php` | Handles prediction requests and view rendering |
| `app/Services/PredictionService.php` | Core algorithmic logic for traffic predictions |
| `app/Http/Controllers/CsvImportController.php` | Processes CSV uploads and data validation |
| `resources/views/predictions.blade.php` | Main dashboard UI with interactive elements |
| `database/database.sqlite` | Local data storage (no cloud dependency) |
| `public/traffic_data.csv` | Sample data for testing |
| `app/Models/TrafficData.php` | Eloquent model with relation definitions |

## 🧠 Prediction Algorithm

The system employs a three-tier prediction strategy:

1. **Historical Data Analysis**: When sufficient historical data exists (3+ records for same hour, weekday, month), the system calculates a weighted average based on recency.

2. **Synthetic Fallback Logic**: In absence of historical data, predictions are generated using:
   - Hour of day weight coefficients
   - Day of week patterns
   - Seasonal adjustments
   - Weather condition modifiers
   - Promotional event impact

3. **Manual Override**: Store managers can input expected values that always take precedence over algorithmic predictions.

All predictions are clearly labeled with their source methodology on the dashboard.

## 💻 Installation

### Prerequisites

- PHP 8.3+
- Composer
- Node.js & NPM
- SQLite

### Setup Instructions

1. **Clone the repository**

```bash
git clone git@github.com:Luximo/Customer-Traffic-Prediction-System-X2.git
cd customer-traffic-prediction
```

2. **Install dependencies**

```bash
composer install
npm install
```

3. **Configure environment**

```bash
cp .env.example .env
php artisan key:generate
```

4. **Setup database**

```bash
touch database/database.sqlite
php artisan migrate
```

5. **Seed with sample data (optional)**

```bash
php artisan db:seed
```

6. **Compile assets and start server**

```bash
npm run dev
php artisan serve
```

7. **Access the dashboard**

Open your browser and navigate to `http://localhost:8000/predictions`

## 📊 Data Import Format

The system accepts CSV files in the following format:

```csv
MM/DD/YYYY,Hour24,CustomerCount
12/31/2023,13,55
12/31/2023,14,50
12/31/2023,15,45
```

- Date format: MM/DD/YYYY
- Hour format: 24-hour (0-23)
- Count: Integer value representing customer count

Upload via the form at the top of the dashboard.

## 🛣️ Development Journey

The project evolved from a comprehensive plan to a focused implementation, prioritizing core functionality and user experience.

### Original Plan vs. Implementation

| Phase | Original Plan | Implementation Status |
|-------|--------------|---------------------|
| **Project Setup** | Laravel + Python/Flask ML setup | ✅ Laravel-only implementation with local prediction logic |
| **Data Model** | Complex schema with traffic, sales, weather, promotions | ✅ Implemented all tables but focused on traffic as primary model |
| **Prediction Logic** | Machine learning with Random Forest | ✅ Hybrid approach using historical data + synthetic fallback |
| **Backend API** | Flask ML integration | ✅ Self-contained Laravel prediction service |
| **Frontend** | Dashboard visualizations | ✅ Complete with charts, heatmaps, and manual overrides |
| **Testing** | End-to-end workflow testing | ✅ Basic functionality testing |
| **Documentation** | System docs and user guide | ✅ Basic README documentation |

### Future Development (v1.1)
- 🔜 CSV export functionality
- 🔜 Enhanced prediction accuracy with additional weather/promotion factors
- 🔜 Multiple store support
- 🔜 Role-based access (managers vs. staff)
- 🔜 API integration points

## 📝 License

This project is proprietary software developed for Dorothy Lane Market.

## 📅 Last Updated

May 4, 2025