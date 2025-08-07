# Credit System API

A modern credit lending system built with Laravel 11, implementing Domain-Driven Design (DDD), Clean Architecture, and SOLID principles. The system provides a comprehensive solution for managing client registration, credit eligibility assessment, and credit application processing.

## Features

- **Client Management**: Register and manage client information with validation
- **Credit Products**: Support for multiple credit products with configurable terms
- **Configurable Business Rules**: Flexible rule-based credit approval system via environment variables
- **Application Processing**: Complete credit application workflow with automated notifications
- **Audit Trail**: Full tracking of application status and decision reasons
- **Environment-Based Configuration**: Easy adjustment of business rules without code changes

## Architecture

The project follows Clean Architecture principles with clear separation of concerns:

- **Domain Layer**: Contains business entities, value objects, and domain services
- **Application Layer**: Implements use cases and application services
- **Infrastructure Layer**: Handles data persistence and external integrations
- **Web Layer**: REST API controllers and request validation

### Key Design Patterns

- **Domain-Driven Design (DDD)**: Rich domain models with business logic encapsulation
- **SOLID Principles**: Single responsibility, dependency inversion, and interface segregation
- **Strategy Pattern**: Pluggable credit approval rules
- **Repository Pattern**: Abstraction for data access
- **Command Query Separation**: Clear distinction between commands and queries

## Requirements

- PHP 8.3+
- Composer
- Laravel 11
- Docker (optional)

### Required PHP Extensions
- mbstring
- fileinfo
- json
- openssl

### Development Dependencies
- PHPStan (static analysis)
- Psalm (static analysis)
- PHP CS Fixer (code style)
- PHPUnit (testing)

## Installation

### Local Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd credit-system
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   # Edit .env file to adjust credit system settings if needed
   ```

4. **Create storage directories and configure environment**
   ```bash
   mkdir -p storage/app/private
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   
   ```

5. **Generate application key**
   ```bash
   php artisan key:generate
   ```

6**Start the development server**
   ```bash
   php artisan serve
   ```

The API will be available at `http://localhost:8000`

### Quick Setup with Makefile

```bash
# Complete setup in one command
make dev-setup

# Then start the server
php artisan serve
```

### Docker Installation

1. **Build and start containers**
   ```bash
   docker compose up -d --build
   ```

2. **Install dependencies inside container**
   ```bash
   docker compose exec app composer install
   docker compose exec app php artisan key:generate
   ```

The API will be available at `http://localhost:8080`

### Docker Quick Setup
```bash
# Complete Docker setup
make docker-setup
```

## API Endpoints

### Clients

- `POST /api/v1/clients` - Create a new client

### Credits

- `POST /api/v1/credits/check-eligibility` - Check credit eligibility
- `POST /api/v1/credits/apply` - Submit credit application

### Example Requests

#### Create Client
```bash
curl -X POST http://localhost:8000/api/v1/clients \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "age": 35,
    "region": "PR",
    "income": 1500,
    "score": 650,
    "pin": "123-45-6789",
    "email": "john.doe@example.com",
    "phone": "+420123456789"
  }'
```

#### Check Credit Eligibility
```bash
curl -X POST http://localhost:8000/api/v1/credits/check-eligibility \
  -H "Content-Type: application/json" \
  -d '{
    "client_id": "client-uuid-here",
    "credit_id": "personal-loan"
  }'
```

#### Apply for Credit
```bash
curl -X POST http://localhost:8000/api/v1/credits/apply \
  -H "Content-Type: application/json" \
  -d '{
    "client_id": "client-uuid-here",
    "credit_id": "personal-loan"
  }'
```

## Data Storage

The system uses JSON files for data persistence, stored in `storage/app/private/`:

- `clients.json` - Client information
- `credits.json` - Available credit products
- `credit_applications.json` - Credit applications and their status

Default credit products are automatically created on first run.

## Testing

### Run Unit Tests
```bash
php artisan test --testsuite=Unit
```

### Run Feature Tests
```bash
php artisan test --testsuite=Feature
```

### Run All Tests
```bash
php artisan test
```

### Docker Testing
```bash
docker compose exec app php artisan test
```

### Using Makefile (Optional)
If you prefer using make commands:
```bash
# Setup project
make install

# Run tests
make test

# Run all quality checks
make quality

# Fix code style
make cs-fix
```

## Code Quality

### Run All Quality Checks
```bash
composer run quality
```

### Individual Quality Tools

#### Static Analysis with PHPStan
```bash
composer run phpstan
# or directly: ./vendor/bin/phpstan analyse
```

#### Psalm Static Analysis
```bash
composer run psalm
# or directly: ./vendor/bin/psalm
```

#### Auto-fix Code Style
```bash
composer run cs-fix
# or directly: ./vendor/bin/pint
```

## Project Structure

```
app/
├── Application/           # Use cases and application services
│   ├── Client/
│   ├── Credit/
│   └── CreditApplication/
├── Domain/               # Domain entities and business logic
│   ├── Client/
│   ├── Credit/
│   ├── CreditApplication/
│   └── Notification/
├── Http/                 # Web layer (controllers, requests)
│   ├── Controllers/
│   └── Requests/
├── Infrastructure/       # External concerns (persistence, notifications)
│   ├── Notification/
│   └── Persistence/
└── Providers/           # Service providers and DI configuration
```

## Configuration

### Environment Variables

The system is highly configurable through environment variables. Key settings in `.env`:

```env
# Application
APP_NAME="Credit System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Credit System Business Rules
CREDIT_MIN_AGE=18
CREDIT_MAX_AGE=60
CREDIT_MIN_SCORE=500
CREDIT_MIN_INCOME=1000
CREDIT_ALLOWED_REGIONS="PR,BR,OS"
CREDIT_OSTRAVA_RATE_INCREASE=5.0

# Feature Toggles
CREDIT_PRAGUE_RANDOM_REJECTION=true
CREDIT_NOTIFICATIONS_ENABLED=true

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=debug
```

### Business Rules Configuration

You can easily adjust the credit approval criteria by modifying environment variables:

| Variable | Description | Default | Example |
|----------|-------------|---------|---------|
| `CREDIT_MIN_AGE` | Minimum client age | 18 | 21 |
| `CREDIT_MAX_AGE` | Maximum client age | 60 | 65 |
| `CREDIT_MIN_SCORE` | Minimum credit score | 500 | 600 |
| `CREDIT_MIN_INCOME` | Minimum monthly income | 1000 | 1500 |
| `CREDIT_ALLOWED_REGIONS` | Comma-separated regions | "PR,BR,OS" | "PR,BR,OS,PL" |
| `CREDIT_OSTRAVA_RATE_INCREASE` | Rate increase for Ostrava | 5.0 | 3.0 |
| `CREDIT_PRAGUE_RANDOM_REJECTION` | Enable Prague random rejection | true | false |
| `CREDIT_NOTIFICATIONS_ENABLED` | Enable notifications | true | false |

### Service Provider Registration

Add to `config/app.php`:
```php
'providers' => [
    // ...
    App\Providers\CreditSystemServiceProvider::class,
],
```

### Publishing Configuration

To customize advanced settings, publish the configuration file:
```bash
php artisan vendor:publish --tag=credit-config
```

This creates `config/credit.php` where you can define more complex business rules.

## Logs

Application logs are stored in `storage/logs/laravel.log`. Credit application notifications are logged automatically for audit purposes.

## Troubleshooting

### Common Issues

1. **Permission errors**
   ```bash
   sudo chown -R $USER:$USER storage bootstrap/cache
   chmod -R 755 storage bootstrap/cache
   ```

2. **JSON file errors**
   ```bash
   mkdir -p storage/app/private
   chmod 755 storage/app/private
   ```

3. **Configuration cache issues**
   ```bash
   php artisan config:clear
   php artisan config:cache
   ```

4. **Composer dependency issues**
   ```bash
   composer clear-cache
   composer install --no-cache
   ```

5. **Docker permission issues**
   ```bash
   docker compose exec app chown -R www:www /var/www/storage
   ```

### Development Tips

- Use `php artisan log:clear` to clear logs
- Check `storage/logs/laravel.log` for application logs
- Credit notifications are logged for debugging
- JSON files in `storage/app/private/` can be manually inspected
- Use `php artisan config:show credit` to view current configuration
- Modify `.env` variables and restart server to apply changes

### Configuration Examples

#### Stricter Requirements
```env
CREDIT_MIN_AGE=25
CREDIT_MIN_SCORE=650
CREDIT_MIN_INCOME=2000
CREDIT_PRAGUE_RANDOM_REJECTION=false
```

#### Development/Testing Mode
```env
CREDIT_MIN_AGE=18
CREDIT_MIN_SCORE=300
CREDIT_MIN_INCOME=500
CREDIT_NOTIFICATIONS_ENABLED=false
```

### Development Workflow

```bash
# Before committing
make quality          # Run all quality checks
php artisan test     # Run tests
make cs-fix          # Fix code style issues
```
