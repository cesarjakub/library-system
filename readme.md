# Nette Library Web Project
A full-featured library management system built with [Nette Framework](https://nette.org/). It supports user registration/login, book catalog management, loan tracking, and RESTful APIs for external integrations.

---
## Features

- **Authentication & Roles** – User registration/login with admin-only actions.
- **Book Management** – Add, edit, filter, and delete books with cover upload and search filters (author, year range, query).
- **Loan System** – Admins can create and manage book loans, with email notifications for borrowers.
- **REST API** – Endpoints for books and loans (secured with API keys).
- **Pagination & Filtering** – Efficient navigation of large data sets using `Nette\Utils\Paginator`.
- **Latte Templates** – Clean, fast templating with Latte.
- **Developer Tools** – Tracy debugger, Faker for seed data, PhpStan, Nette Tester.

---
## Tech Stack

- **Backend:** Nette Framework (Application, DI, Database, Forms, Security, Http, Mail)
- **Templating:** Latte
- **Database Layer:** Nettrine (Doctrine ORM + DBAL)
- **Caching & Assets:** Nette Caching, Nette Assets
- **Debugging:** Tracy
- **Testing:** Nette Tester, PhpStan
- **Other:** Contributte Console, Contributte Elasticsearch integration
---

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/nette-library-web.git
   cd nette-library-web
   ```
   
2. **Install dependencies**
    ```bash
    composer install
    ```
   
3. **Run database migrations & seed data (if applicable)**

    ```bash
    vendor/bin/doctrine orm:schema-tool:update --force
    ```
4. **Start the development server**
    ```bash
    php -S localhost:8000 -t www
    ```
   - Visit http://localhost:8000.

## API Overview

### Authentication
All API requests require a valid API key in the header:
```dotenv
API_KEY_BOOKS=tajny-apikey-123
API_KEY_LOANS=tajny-apikey-124
```


### Books
| Method | Endpoint              | Description           |
|--------|----------------------|----------------------|
| GET    | `/api/books`         | List all books        |
| GET    | `/api/books/{id}`    | Get book detail       |
| POST   | `/api/books`         | Create a new book     |
| PUT    | `/api/books/{id}`    | Update book           |
| DELETE | `/api/books/{id}`    | Delete book           |

### Loans
| Method | Endpoint              | Description              |
|--------|----------------------|-------------------------|
| GET    | `/api/loans`         | List all loans           |
| GET    | `/api/loans/{id}`    | Loan detail              |
| POST   | `/api/loans`         | Create new loan          |
| PUT    | `/api/loans/{id}`    | Mark loan as returned    |

---

## User Roles

- **Guest** – Can register or log in.
- **User** – Can view books and loan history.
- **Admin** – Full access: manage books, loans, and users.

---

## Testing
- **Unit tests**:
    ```bash
    php vendor/bin/tester tests 
    ```
- **Debugging**: Tracy is enabled in development mode.

## Author

- **Jakub César**
- GitHub: [https://github.com/cesarjakub](https://github.com/cesarjakub)
