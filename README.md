# World Spices — Online Store

World Spices is a full-stack web application for selling spices online. It follows the MVC design pattern with PHP on the server side, a MySQL database for data storage, and a responsive front-end built with HTML, CSS, JavaScript, and Bootstrap.

## Features

### Public Area
- **Home Page** — Displays a catalog of featured spices with images and descriptions.
- **Registration** — Users can create an account by providing personal details, selecting a profile photo, and setting a password with validation.
- **Login** — Authentication system with role-based redirection (Member or Admin).

### Member Area
- **Profile Management** — Members can update personal details (except email) and change their password.
- **Spice Catalog** — Browse all available spices and add items to a shopping cart.
- **Shopping Cart** — Tracks selected spices, updates quantities, and simulates a purchase with a generated invoice.
- **Logout** — Ends the session and redirects to the home page.

### Admin Area
- **Spice Management (CRUD)** — Add, list, search, update, and delete spices, including uploading images.
- **Member Management** — View all registered members and activate/deactivate accounts.
- **Asynchronous Operations** — All admin actions use AJAX/Fetch for dynamic updates without full page reloads.

## Architecture
- **MVC Structure** — Clear separation of Models, Views, and Controllers.
- **DAO Pattern** — Data Access Object layer handles all database interactions.
- **Singleton Pattern** — Ensures a single database connection instance.
- **Asynchronous Front-End** — Uses Fetch API with JSON responses for smooth user experience.

## Technologies Used
- **Backend** — PHP (MVC, DAO, Singleton), MySQL
- **Frontend** — HTML5, CSS3, JavaScript (ES6+), Bootstrap 5
- **Client-Side Storage** — LocalStorage for cart management
- **Data Formats** — JSON for server-client communication

## Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/World-Spices.git
   ```
2. Import the provided database schema into MySQL.
3. Update database connection settings in the configuration file.
4. Deploy the project to a PHP-enabled server (e.g., Apache with PHP module).
5. Access the site in your browser.

## Default Admin Credentials
- **Email:** `admin@epices.com`
- **Password:** `12345`

*(Change these credentials after first login for security.)*

## Project Structure
```
client/              # Frontend resources (HTML, CSS, JS, images)
serveur/             # PHP backend (Controllers, Models, DAO)
public/              # Public-facing assets
routes.php           # Request routing
index.php            # Entry point / Home page
```

## License
This project is provided as-is for demonstration purposes.
