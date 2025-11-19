# Simple Article Management API

A RESTful API for managing articles with user authentication, built with Laravel 12.

## Completed Features

### ✅ Core Requirements

**Authentication**
- User registration with email validation
- User login with session-based authentication
- Password hashing using bcrypt
- Protected routes for article create/update/delete operations
- Custom API authentication middleware that returns JSON responses

**Article Management (Full CRUD)**
- **Create Article**: Authenticated users can create articles with auto-generated slugs
- **List Articles**: Public endpoint returns only published articles with pagination (10 per page)
- **View Article**: Public endpoint to view article by slug with author and category information
- **Update Article**: Authenticated users can only update their own articles (authorization check)
- **Delete Article**: Authenticated users can only delete their own articles (soft delete)
- **Search Articles**: Search functionality via `?q=` query parameter (searches title and content)

**Categories**
- Assign categories to articles during create/update via `category_ids[]`
- Retrieve all published articles in a category by slug: `GET /categories/{slug}`
- 4 default categories seeded: Technology, Science, Business, Lifestyle

**Database**
- Soft deletes implemented using `deleted_at` column
- Proper foreign key constraints
- Unique constraints on slugs and emails

### ✅ Bonus Features

- **Search Endpoint**: Integrated into `/articles` endpoint via `?q=` query parameter
- **Database Seeders**: 
  - `UserSeeder`: 3 test users (john@example.com, jane@example.com, admin@example.com)
  - `CategorySeeder`: 4 default categories
  - `ArticleSeeder`: 7 sample articles (6 published, 1 draft)
- **Unit/Feature Tests**:
  - User registration tests (valid data, duplicate email, validation errors, password hashing)
  - Article creation tests (authenticated creation, slug generation, author assignment, category attachment)
  - Permission tests (author can update/delete, other users cannot, unauthenticated access blocked)
- **Service Layer**: Controllers use service classes for business logic separation
- **Request Validation**: Form Request classes for input validation
- **API Testing Guide**: Comprehensive Insomnia collection guide (`API_TESTING_GUIDE.md`)

## Special Setup Steps

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL 8.0 or higher (or Docker for containerized setup)
- Node.js and npm (for frontend assets, if needed)

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd SimpleArticleManagementApi
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Set up environment file**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Set up database** (choose one option below)

#### Option A: Using Docker (Recommended for quick setup)

4a. **Start Docker containers**
   ```bash
   docker-compose up -d
   ```
   This will start:
   - MySQL 8.0 on port `3307`
   - phpMyAdmin on port `8081`

4b. **Wait for MySQL to be ready** (about 10-15 seconds)
   ```bash
   docker-compose logs mysql
   ```

4c. **Update `.env` file** (already configured for Docker):
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3307
   DB_DATABASE=app
   DB_USERNAME=app
   DB_PASSWORD=app
   ```

#### Option B: Using Local MySQL

4a. **Create MySQL database**
   ```sql
   CREATE DATABASE app;
   CREATE USER 'app'@'localhost' IDENTIFIED BY 'app';
   GRANT ALL PRIVILEGES ON app.* TO 'app'@'localhost';
   FLUSH PRIVILEGES;
   ```

4b. **Update `.env` file**:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=app
   DB_USERNAME=app
   DB_PASSWORD=app
   ```
   Or use your existing MySQL credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run database migrations**
   ```bash
   php artisan migrate
   ```

6. **Seed the database**
   ```bash
   php artisan db:seed
   ```
   This creates:
   - 3 test users (password: `password123`)
   - 4 categories
   - 7 sample articles

7. **Start the development server**
   ```bash
   php artisan serve
   ```
   API will be available at `http://localhost:8000`

### Database Configuration

**For Docker setup:**
- `DB_PORT=3307` (Docker port mapping)
- `DB_DATABASE=app`
- `DB_USERNAME=app`
- `DB_PASSWORD=app`

**For Local MySQL:**
- `DB_PORT=3306` (default MySQL port)
- Use your own database name, username, and password

### Testing

Run the test suite:
```bash
php artisan test
```

Or with PHPUnit directly:
```bash
vendor/bin/phpunit
```

### Accessing phpMyAdmin (Docker only)

If you're using Docker, phpMyAdmin is available at:
- URL: `http://localhost:8081`
- Server: `mysql`
- Username: `app`
- Password: `app`

For local MySQL, use your preferred MySQL client (MySQL Workbench, phpMyAdmin, DBeaver, etc.)

## API Endpoints

### Authentication
- `POST /api/auth/register` - Register a new user
- `POST /api/auth/login` - Login user

### Articles (Public)
- `GET /api/articles` - List all published articles (supports `?q=`, `?sortBy=`, `?sortOrder=`, `?perPage=`)
- `GET /api/articles/{slug}` - Get article by slug

### Articles (Protected - Requires Authentication)
- `POST /api/articles` - Create new article
- `PUT /api/articles/{id}` - Update article (author only)
- `DELETE /api/articles/{id}` - Delete article (author only, soft delete)

### Categories
- `GET /api/categories/{slug}` - Get category with published articles

For detailed API documentation and testing instructions, see [API_TESTING_GUIDE.md](API_TESTING_GUIDE.md).

## Project Structure

```
app/
├── Http/
│   ├── Controllers/     # API controllers
│   ├── Middleware/      # Custom authentication middleware
   │   └── Requests/      # Form request validation classes
│   └── Services/        # Business logic layer
│       ├── AuthService.php
│       ├── ArticleService.php
│       └── CategoryService.php
├── Models/              # Eloquent models
database/
├── migrations/          # Database migrations
└── seeders/            # Database seeders
routes/
└── api.php             # API routes
tests/
└── Feature/            # Feature tests
```

## Known Limitations

1. **Session-Based Authentication**: Uses Laravel's default session authentication. For production, consider implementing token-based authentication (JWT, Sanctum, or Passport).

2. **Search Functionality**: 
   - Uses simple `LIKE` queries (not full-text search)
   - Case-insensitive but may be slow on large datasets
   - No search ranking or relevance scoring

3. **Pagination**: Fixed at 10 items per page by default (configurable via `perPage` query parameter).

4. **Category Management**: No CRUD endpoints for categories - they must be managed directly in the database or via seeders.

5. **Article Status**: Only two statuses supported: `draft` and `published`. No additional statuses like `archived` or `scheduled`.

6. **File Uploads**: No support for article images or attachments.

7. **User Roles**: No role-based access control (RBAC). All authenticated users have the same permissions.

8. **Rate Limiting**: No rate limiting implemented on API endpoints.

9. **API Versioning**: No API versioning strategy implemented.

10. **Caching**: No caching layer implemented for frequently accessed data.

## Security Considerations

- ✅ Password hashing using bcrypt
- ✅ SQL injection protection via Eloquent ORM
- ✅ Input validation via Form Requests
- ✅ Authorization checks on update/delete operations
- ✅ Soft deletes to prevent data loss
- ⚠️ No CSRF protection on API routes (by design for API usage)
- ⚠️ No rate limiting (should be added for production)

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
