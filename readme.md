# Nette Library Web Project

[![License](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.1-777BB4.svg)](https://www.php.net/)

A full-featured **library management system** built with [Nette Framework](https://nette.org/).  
Supports user registration/login, book catalog management, loan tracking, and RESTful APIs for external integrations.

---

## Table of Contents

1. [Features](#features)
2. [Tech Stack](#tech-stack)
3. [Installation](#installation)
4. [Database & Seed Data](#database--seed-data)
5. [Running the Project](#running-the-project)
6. [API Documentation](#api-documentation)
7. [User Roles & Permissions](#user-roles--permissions)
8. [Testing & Debugging](#testing--debugging)
9. [Deployment](#deployment)
10. [Contributing](#contributing)
11. [License](#license)
12. [Author](#author)

---

## Features

- **Authentication & Roles** – Secure registration and login with role-based access (Guest, User, Admin).
- **Book Management** – Add, edit, delete books with cover images, search, and filters by author, year, or keyword.
- **Loan System** – Admins can manage loans, send email notifications for due books, and mark returns.
- **REST API** – CRUD endpoints for books and loans, secured with API keys.
- **Pagination & Filtering** – Optimized for large datasets using `Nette\Utils\Paginator`.
- **Latte Templates** – Clean and maintainable frontend with Latte templates.
- **Developer Tools** – Tracy debugger, PhpStan static analysis, Nette Tester, Faker seed data, and console commands.
- **Search** – Optional Contributte Elasticsearch integration for full-text search.

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | [Nette Framework](https://nette.org/) (DI, Forms, Security, Database, HTTP, Mail) |
| ORM & Database | [Nettrine ORM](https://nettrine.com/) (Doctrine ORM + DBAL) |
| Templating | Latte |
| Caching | Nette Caching |
| Assets | Nette Assets |
| Search | Contributte Elasticsearch |
| Debugging | Tracy Debugger |
| Testing | Nette Tester, PhpStan, Mockery |
| Data Seeding | FakerPHP |
| Console & CLI | Contributte Console |

---

## Installation

### 1. Clone the repository
```bash
    git clone https://github.com/cesarjakub/library-system.git
    cd library-system
```

### 2. Install dependencies
```bash
  composer install
```

## Running the Project

### Local development server
```bash
  php -S localhost:8000 -t www
```
- Visit http://localhost:8000

### Contributte Console
```bash
    php bin/console list
```

## API Documentation

### Authentication
All API requests require an API key header:

```http
Books
X-API-KEY: tajny-apikey-123
Loans
X-API-KEY: tajny-apikey-124
```

## Books
| Method | Endpoint          | Description       |
|--------|-----------------|-----------------|
| GET    | `/api/books`     | List all books   |
| GET    | `/api/books/{id}`| Get book detail  |
| POST   | `/api/books`     | Create a book    |
| PUT    | `/api/books/{id}`| Update book      |
| DELETE | `/api/books/{id}`| Delete book      |

## Loans
| Method | Endpoint          | Description       |
|--------|-----------------|-----------------|
| GET    | `/api/loans`     | List all loans   |
| GET    | `/api/loans/{id}`| Loan detail      |
| POST   | `/api/loans`     | Create new loan  |
| PUT    | `/api/loans/{id}`| Mark loan returned |

---

## User Roles & Permissions

- **Guest** – Can register or log in.
- **User** – Can view books and their loan history.
- **Admin** – Full access: manage books, loans, and users.

---

## Testing & Debugging

### Unit & Functional Tests
```bash
  php vendor/bin/tester tests
```

### Debugging
Tracy panel enabled in development mode for errors, logs, and database queries.

## License
This project is licensed under **MIT**.

## Author
**Jakub César**  
GitHub: [https://github.com/cesarjakub](https://github.com/cesarjakub)
