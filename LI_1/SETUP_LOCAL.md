# BookEase - Local Development Setup (Without Docker)

## Prerequisites

Before starting, install the following on your machine:

1. **PHP 8.2+** - [Download](https://www.php.net/downloads)
2. **Composer** - [Download](https://getcomposer.org/download/)
3. **Node.js 20+** - [Download](https://nodejs.org/)
4. **PostgreSQL 15** - [Download](https://www.postgresql.org/download/)
5. **Redis** - [Download](https://redis.io/download) or use WSL on Windows

## Step 1: Database Setup

### Create PostgreSQL Database

```bash
# Connect to PostgreSQL (use your system's terminal)
psql -U postgres

# In psql shell:
CREATE DATABASE bookease;
```

### Initialize Database Schema

```bash
# From the project root directory
psql -U postgres -d bookease -f backend/database/migrations.sql
```

### Verify Database Connection

```bash
psql -U postgres -d bookease -c "\dt"
```

## Step 2: Backend Setup

### Install PHP Dependencies

```bash
cd backend
composer install
```

### Configure Environment

Create a `.env` file in the `backend` directory:

```
DB_HOST=localhost
DB_PORT=5432
DB_NAME=bookease
DB_USER=postgres
DB_PASS=postgres
REDIS_HOST=localhost
REDIS_PORT=6379
JWT_SECRET=your-secret-key-change-in-production
JWT_TTL=86400
APP_ENV=development
```

### Run PHP Development Server

```bash
# From the backend directory
php -S localhost:8080 -t public
```

The backend will be available at `http://localhost:8080`

## Step 3: Frontend Setup

### Install Node Dependencies

```bash
cd frontend
npm install
```

### Run Development Server

```bash
# From the frontend directory
npm run dev
```

The frontend will be available at `http://localhost:5173`

## Step 4: Start Redis

### Windows (using WSL or native Redis):

```bash
# If using WSL:
wsl redis-server

# Or if installed natively:
redis-server
```

### macOS:

```bash
# Using Homebrew
brew services start redis
```

### Linux:

```bash
sudo systemctl start redis-server
```

## Running the Application

1. **Start PostgreSQL** (if not running as a service)
2. **Start Redis** in a separate terminal
3. **Start Backend** in a separate terminal: `cd backend && php -S localhost:8080 -t public`
4. **Start Frontend** in a separate terminal: `cd frontend && npm run dev`

Open `http://localhost:5173` in your browser.

## Troubleshooting

### PHP Extensions Missing

If you get errors about missing PHP extensions (pdo_pgsql):

**Windows:**
- Edit `php.ini` and uncomment `;extension=pdo_pgsql`
- Uncomment `;extension=pdo`
- Restart PHP development server

**macOS (Homebrew):**
```bash
brew install php@8.2
# Install pdo_pgsql extension
```

### PostgreSQL Connection Issues

```bash
# Test connection
psql -U postgres -d bookease -c "SELECT 1"

# If password issues:
# Edit postgresql.conf to use md5 or scram-sha-256 authentication
```

### Redis Connection Issues

```bash
# Test Redis connection
redis-cli ping
# Should respond with PONG
```

### Port Already in Use

- Backend running on port 8080: Change with `php -S localhost:9000`
- Frontend running on port 5173: Vite will auto-increment to 5174

### Node Modules Issues

```bash
# Clear cache and reinstall
cd frontend
rm -rf node_modules package-lock.json
npm install
```

## Production Deployment

For production, consider:

1. Use Apache/Nginx instead of PHP development server
2. Set `APP_ENV=production`
3. Use strong JWT_SECRET
4. Enable HTTPS
5. Set proper database backups
6. Use a process manager for Redis and PHP-FPM
