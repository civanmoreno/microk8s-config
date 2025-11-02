# LAMP Stack Application

A well-structured PHP application demonstrating best practices for separation of concerns, following a clean architecture pattern.

## Directory Structure

```
app/
├── config/              # Configuration files
│   └── database.php     # Database configuration
├── src/                 # Source code (Models, Classes)
│   ├── Database.php     # Database connection manager (Singleton)
│   └── Message.php      # Message model for database operations
├── public/              # Public assets
│   └── css/
│       └── style.css    # Application styles
├── index.php            # Application entry point
└── README.md            # This file
```

## Architecture Overview

### Separation of Concerns

This application follows a clean architecture pattern with clear separation of:

1. **Configuration** ([config/database.php](config/database.php))
   - Database credentials from environment variables
   - PDO options and settings
   - Easy to extend with additional config files

2. **Business Logic** ([src/](src/))
   - `Database.php`: Singleton pattern for database connections
   - `Message.php`: Model for handling message CRUD operations
   - Clean, testable, reusable code

3. **Presentation** ([index.php](index.php))
   - Minimal logic in the view
   - Clear separation between data fetching and display
   - HTML with proper escaping using `htmlspecialchars()`

4. **Assets** ([public/css/](public/css/))
   - External CSS for styling
   - No inline styles in PHP/HTML
   - Responsive design

## Key Features

### Database Class (Singleton Pattern)

```php
$database = Database::getInstance();
$pdo = $database->getConnection();
```

- Single database connection throughout the application
- Automatic configuration loading
- Connection health checking
- Thread-safe implementation

### Message Model

```php
$messageModel = new Message($pdo);

// Create a new message
$messageModel->create('Hello World');

// Get latest messages
$messages = $messageModel->getLatest(10);

// Get total count
$count = $messageModel->count();
```

- Clean API for database operations
- Prepared statements for security
- Error handling and logging
- Automatic table creation

## Security Features

- **SQL Injection Protection**: All queries use prepared statements
- **XSS Protection**: All output is escaped using `htmlspecialchars()`
- **Environment Variables**: Sensitive data stored in env vars, not code
- **Error Handling**: Proper try-catch blocks with error logging

## Environment Variables

The application reads configuration from environment variables:

| Variable | Description | Default |
|----------|-------------|---------|
| `MYSQL_HOST` | MySQL server hostname | `mysql` |
| `MYSQL_DATABASE` | Database name | `lamp_db` |
| `MYSQL_USER` | Database username | `lamp_user` |
| `MYSQL_PASSWORD` | Database password | `lamp_password` |

## Database Schema

### Messages Table

```sql
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Best Practices Implemented

1. **Autoloading**: PSR-4 style autoloading for classes
2. **Dependency Injection**: Database passed to models via constructor
3. **Single Responsibility**: Each class has one clear purpose
4. **DRY Principle**: No code duplication
5. **Proper Documentation**: PHPDoc comments for all classes and methods
6. **Error Handling**: Graceful error handling with user-friendly messages
7. **Responsive Design**: Mobile-first CSS approach
8. **Clean Code**: Readable, maintainable, and well-organized

## Extending the Application

### Adding a New Model

1. Create a new file in [src/](src/):

```php
<?php
class YourModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Your methods here
}
```

2. Use it in [index.php](../index.php):

```php
$yourModel = new YourModel($pdo);
```

### Adding New Configuration

Create a new config file in [config/](config/):

```php
<?php
// config/app.php
return [
    'name' => 'My App',
    'version' => '1.0.0',
];
```

Load it in your code:

```php
$appConfig = require __DIR__ . '/config/app.php';
```

## Development Tips

### Enable Error Reporting

Error reporting is enabled by default in [index.php](index.php):

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

For production, disable display_errors and log errors instead.

### Database Debugging

The Database class includes a connection checker:

```php
if ($database->isConnected()) {
    echo "Connected!";
}
```

### Viewing Logs

Check PHP error logs for detailed error information:

```bash
# In Docker container
tail -f /var/log/apache2/error.log
```

## Testing

### Manual Testing Checklist

- [ ] Application loads without errors
- [ ] Database connection is successful
- [ ] Messages are being inserted
- [ ] Messages table displays correctly
- [ ] Total count is accurate
- [ ] PHP extensions are listed
- [ ] Page is responsive on mobile
- [ ] No XSS vulnerabilities (all output escaped)
- [ ] No SQL injection vulnerabilities (prepared statements)

### Testing Database Connection

1. Check if the database is reachable
2. Verify credentials are correct
3. Ensure database exists
4. Check user permissions

## Performance Considerations

- **Connection Pooling**: Singleton pattern ensures one connection
- **Indexed Queries**: Database table has proper indexes
- **Prepared Statements**: Queries are cached by MySQL
- **Minimal Queries**: Only fetch what's needed
- **CSS External**: Browser can cache the stylesheet

## Troubleshooting

### Database Connection Fails

Check the error message displayed on the page. Common issues:

1. MySQL server not running
2. Incorrect credentials
3. Database doesn't exist
4. Network connectivity issues

### CSS Not Loading

Ensure the path is correct relative to index.php:

```html
<link rel="stylesheet" href="public/css/style.css">
```

### Autoloader Not Working

Check that:
- Files are in the correct location
- Class names match file names
- File permissions are correct

## License

This project is open source and available under the MIT License.
