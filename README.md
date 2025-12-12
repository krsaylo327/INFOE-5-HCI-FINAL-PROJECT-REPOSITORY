# Tech Ticket System

A full-stack IT support ticket management system built with Laravel 12, Inertia.js, Vue 3, and TypeScript.

## Features

### Ticket Management
- Create tickets with subject, description, and priority
- Upload photo attachments (JPG/PNG/GIF, max 2MB)
- Track status: Open → In Progress → Resolved → Closed
- Set priority levels: Low, Medium, High
- Add comments for collaboration

### Role-Based Access Control
- **Students** - Create and view their own tickets
- **Faculty** - Create tickets and manage assigned tickets
- **Admins** - Full access to all tickets and user management

### Advanced Features
- Search and filter tickets by subject, description, creator, status, or priority
- Smart sorting with high priority tickets first and closed tickets at the bottom
- Real-time updates
- Dark mode support
- Responsive design for desktop, tablet, and mobile

### Security
- Laravel Sanctum for SPA authentication
- Policy-based authorization
- CSRF protection
- Secure session management

## Quick Start

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js 18 or higher
- SQLite (or MySQL/PostgreSQL)

### Installation

1. Clone the repository
```bash
git clone https://github.com/elijaspen/tech-ticket-sys.git
cd tech-ticket-system
```

2. Install PHP dependencies
```bash
composer install
```

3. Install Node dependencies
```bash
npm install
```

4. Setup environment
```bash
cp .env.example .env
php artisan key:generate
```

5. Create database
```bash
touch database/database.sqlite
```

6. Run migrations
```bash
php artisan migrate
```

7. Seed database (needed for admin)
```bash
php artisan db:seed
```

8. Create storage link
```bash
php artisan storage:link
```

9. Build frontend assets
```bash
npm run build
```

10. Start development server
```bash
php artisan serve
```

Visit http://127.0.0.1:8000

### Development Mode

For hot module replacement during development:

```bash
# Terminal 1: Start Vite dev server
npm run dev

# Terminal 2: Start Laravel server
php artisan serve
```

## Screenshots

Screenshots will be added by the team.

## Tech Stack

### Backend
- Laravel 12 - PHP Framework
- Laravel Sanctum - API Authentication
- SQLite - Database (easily switchable to MySQL/PostgreSQL)

### Frontend
- Vue 3 - Progressive JavaScript Framework
- TypeScript - Type-safe JavaScript
- Inertia.js - Modern monolith
- Tailwind CSS - Utility-first CSS
- shadcn/ui - Component library
- Lucide Icons - Icon set

### Development
- Vite - Fast build tool
- ESLint - Code linting

## User Roles and Permissions

### Student
- Create tickets
- View own tickets
- Add comments
- Edit priority while ticket is Open
- Cannot delete tickets
- Cannot change status

### Faculty
- All Student permissions
- View assigned tickets
- Change ticket status for assigned tickets
- Manage assigned tickets

### Admin
- Full access to all tickets
- Manage user roles
- Delete tickets
- Change any ticket status or priority
- Assign tickets to faculty

## Project Structure

```
tech-ticket-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── TicketController.php
│   │   │   ├── CommentController.php
│   │   │   └── AdminUserController.php
│   │   └── Middleware/
│   │       └── AdminOnly.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Ticket.php
│   │   └── Comment.php
│   └── Policies/
│       └── TicketPolicy.php
├── resources/
│   ├── js/
│   │   ├── components/
│   │   ├── layouts/
│   │   ├── pages/
│   │   │   ├── Dashboard.vue
│   │   │   ├── Tickets/
│   │   │   ├── Admin/
│   │   │   └── auth/
│   │   └── app.ts
│   └── css/
│       └── app.css
├── routes/
│   ├── web.php
│   └── api.php
└── database/
    ├── migrations/
    └── seeders/
```

## Configuration

### Database
Edit .env to change database:
```env
DB_CONNECTION=sqlite
# Or use MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=tech_ticket
# DB_USERNAME=root
# DB_PASSWORD=
```

### File Uploads
Files are stored in storage/app/public/tickets/
Maximum file size: 2MB

### Session Domain
For deployment, update:
```env
SESSION_DOMAIN=your-domain.com
SANCTUM_STATEFUL_DOMAINS=your-domain.com
```

## Deployment

### Using ngrok for Development/Demo

1. Start your Laravel server
```bash
php artisan serve
```

2. In another terminal, start ngrok
```bash
ngrok http 8000
```

3. Update your .env
```env
APP_URL=https://your-ngrok-url.ngrok.io
SESSION_DOMAIN=your-ngrok-url.ngrok.io
SANCTUM_STATEFUL_DOMAINS=your-ngrok-url.ngrok.io
```

4. Clear cache
```bash
php artisan config:clear
php artisan cache:clear
```

### Production Deployment

See Laravel Deployment Documentation for production deployment options:
- Laravel Forge - Automated deployment
- DigitalOcean - VPS hosting
- Heroku - Platform as a Service
- AWS - Enterprise cloud

## Testing

```bash
php artisan test
```

## Contributing

This is a group project. Contributions from all team members are welcome.

### How to Contribute

1. Clone the repository
2. Create or switch to your assigned branch (git checkout ui or git checkout docs)
3. Make your changes
4. Commit your changes (git commit -m 'Add feature description')
5. Push to your branch (git push origin your-branch)
6. Open a Pull Request

### Branch Structure
- master - Main production branch (protected)
- ui - UI/UX improvements
- docs - Documentation and screenshots
- test - Testing and quality assurance

### Areas for Contribution

- UI/UX Improvements
- Documentation
- Screenshots
- Tests
- Bug Fixes
- New Features

## License

This project is created for educational purposes as a final project.

## Team Members



- [Eli Jaspen Faderguya] - Full Stack Developer
- [Ralph Louise Seguera] - UI/UX Designer & Front-End Developer
- [Kristen Modesto] - Product Manager
- [Marc Joseff Umiten] - QA(Quality Assurance) & DevOps
- [Hezane Kate Agustin] - Documentation & Technical Writer
- [Artaxerxes Garcia] - Technical Analyst & Support

## Acknowledgments

- Built with Laravel 12
- UI Components from shadcn/ui
- Icons from Lucide
- Powered by Inertia.js

## Support

For issues or questions, please open a GitHub issue.
