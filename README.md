# Startup-Investor Platform MVP

A comprehensive platform connecting startups with investors, built with vanilla PHP following MVC architecture.

## Features Implemented

### Core MVP Features
- **User Authentication**: Secure registration, login, and session management
- **User Types**: Support for both startup founders and investors
- **Profile Management**: Detailed profiles for startups and investors
- **Matching Algorithm**: Intelligent matching based on industry, stage, funding size, and location
- **Dashboard**: Role-specific dashboards with statistics and recent activity

### Technical Features
- **Security**: CSRF protection, input validation, password hashing, brute force protection
- **Database**: MySQL with proper indexing and relationships
- **Architecture**: Clean MVC pattern with services layer
- **Responsive UI**: Bootstrap-based responsive design
- **Performance**: Optimized queries and caching-ready structure

## Project Structure

```
startup-investor-platform/
├── public/                 # Web root
│   ├── index.php          # Main entry point
│   ├── .htaccess          # URL rewriting
│   └── assets/            # Static assets
├── src/
│   ├── Core/              # Core framework classes
│   ├── Models/            # Data models
│   ├── Controllers/       # Request handlers
│   ├── Services/          # Business logic
│   └── Views/             # Templates
├── config/                # Configuration files
├── database/              # Migrations and seeds
└── scripts/               # Utility scripts
```

## Installation

1. **Prerequisites**
   - PHP 7.4+ with PDO MySQL extension
   - MySQL 5.7+ or MariaDB
   - Web server (Apache/Nginx) or XAMPP

2. **Setup**
   ```bash
   # Clone or download the project to your web directory
   cd c:/xampp/htdocs/startup
   
   # Configure database settings
   # Edit config/database.php with your database credentials
   
   # Run database migrations
   c:\xampp\php\php.exe scripts/migrate.php
   ```

3. **Configuration**
   - Update `config/database.php` with your database credentials
   - Modify `config/config.php` for your environment settings
   - Ensure proper file permissions for uploads and storage directories

## Database Schema

### Core Tables
- **users**: User accounts and basic information
- **startups**: Startup company profiles and details
- **investors**: Investor profiles and investment criteria
- **industries**: Industry categories for matching
- **matches**: Matching results and status tracking

### Key Features
- JSON fields for flexible data storage (investment preferences, portfolio)
- Full-text search indexes for company and description searches
- Optimized indexes for matching queries
- Foreign key constraints for data integrity

## Matching Algorithm

The platform uses a sophisticated scoring system:

- **Industry Match (30 points)**: Alignment between startup industry and investor preferences
- **Stage Match (25 points)**: Compatibility of startup stage with investor focus
- **Investment Size (20 points)**: Funding goal within investor's range
- **Geographic Proximity (15 points)**: Location-based matching
- **Track Record (10 points)**: Investor experience in startup's industry

Matches with scores above 60% are considered high-quality and displayed to users.

## Security Features

- **Authentication**: Secure password hashing with PHP's password_hash()
- **CSRF Protection**: All forms protected against cross-site request forgery
- **Input Validation**: Comprehensive server-side validation and sanitization
- **Brute Force Protection**: Login attempt limiting with lockout periods
- **Session Security**: Secure session configuration and regeneration
- **File Upload Security**: Type validation and secure storage

## API Endpoints

The platform includes RESTful endpoints for:
- User authentication (`/login`, `/register`, `/logout`)
- Profile management (`/profile/create`, `/profile/edit`)
- Matching system (`/api/match`, `/matches`)
- Search functionality (`/search/startups`, `/search/investors`)

## User Workflows

### Startup Founder Journey
1. Register and choose "Startup" user type
2. Complete startup profile (company details, funding needs)
3. View dashboard with match statistics
4. Browse potential investor matches
5. Express interest in investors
6. Communicate through the platform

### Investor Journey
1. Register and choose "Investor" user type
2. Complete investor profile (investment criteria, portfolio)
3. View dashboard with startup opportunities
4. Browse potential startup matches
5. Express interest in startups
6. Connect with founders

## Performance Optimizations

- **Database Indexing**: Strategic indexes for common query patterns
- **Query Optimization**: Efficient joins and prepared statements
- **Caching Ready**: File-based caching system implemented
- **Pagination**: Built-in pagination for large result sets
- **Lazy Loading**: Related data loaded only when needed

## Development Notes

### Architecture Decisions
- **Vanilla PHP**: No external frameworks for maximum control and learning
- **MVC Pattern**: Clear separation of concerns
- **Service Layer**: Business logic separated from controllers
- **Singleton Database**: Efficient connection management
- **Template System**: Simple but effective view rendering

### Code Quality
- **PSR-4 Autoloading**: Namespace-based class loading
- **Error Handling**: Comprehensive error management
- **Input Sanitization**: All user input properly sanitized
- **SQL Injection Prevention**: Prepared statements throughout
- **XSS Protection**: Output escaping in templates

## Future Enhancements

The MVP provides a solid foundation for additional features:
- Real-time messaging system
- Document sharing and due diligence
- Investment tracking and portfolio management
- Advanced search and filtering
- Email notifications and alerts
- Mobile app development
- Integration with external services (CRM, payment processing)

## Testing

The platform includes basic error handling and validation. For production deployment, consider adding:
- Unit tests for models and services
- Integration tests for user workflows
- Security penetration testing
- Performance load testing
- Cross-browser compatibility testing

## Support

This MVP demonstrates a production-ready architecture that can scale with your business needs. The modular design allows for easy feature additions and modifications.
