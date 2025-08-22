# Project Overview

This is a logistics and shipping application built with CodeIgniter 4. It helps manage various aspects of the shipping process, including master data (categories, items, couriers, customers), transactions (shipments), and reporting. The application also features a QR code system for tracking shipments, which can be integrated with a separate Android application for couriers.

## Key Technologies

*   **Backend:** CodeIgniter 4, PHP 8
*   **Frontend:** Bootstrap 3, JQuery, AdminLTE
*   **Database:** MySQL
*   **Dependencies:**
    *   `phpoffice/phpspreadsheet`: For handling spreadsheet data.
    *   `endroid/qr-code`: For generating QR codes.

## Architecture

The application follows a standard MVC (Model-View-Controller) architecture:

*   **Models:** Located in `app/Models`, these handle database interactions.
*   **Views:** Located in `app/Views`, these are responsible for the presentation layer.
*   **Controllers:** Located in `app/Controllers`, these handle user requests and business logic.

## Building and Running

### Prerequisites

*   PHP 8 or higher
*   Composer
*   MySQL database

### Installation

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/trionoputra/Aplikasi-Pengiriman-Barang-CI.git
    ```
2.  **Install dependencies:**
    ```bash
    composer install
    ```
3.  **Database Setup:**
    *   Create a MySQL database named `puninar_logistic`.
    *   Import the `db/pengiriman.sql` file into the database.
4.  **Environment Configuration:**
    *   Rename the `.env.example` file to `.env`.
    *   Update the database credentials in the `.env` file.

### Running the Application

To run the application, you can use the built-in CodeIgniter development server:

```bash
php spark serve
```

The application will be available at `http://localhost:8080`.

## Testing

To run the test suite, use the following command:

```bash
composer test
```

This will execute the PHPUnit tests located in the `tests` directory.

## Views

The application's user interface is built using CodeIgniter's view templates, located in the `app/Views` directory. The main layout is defined in `app/Views/layouts/main.php`, which includes the header, sidebar, and footer. The application uses Bootstrap 5 and AdminLTE for styling.

Key views include:

*   **`app/Views/auth/login.php`:** The login page.
*   **`app/Views/dashboard/index.php`:** The main dashboard.
*   **`app/Views/barang/index.php`:** The item master data page.
*   **`app/Views/kategori/index.php`:** The item category master data page.
*   **`app/Views/kurir/index.php`:** The courier master data page.
*   **`app/Views/pelanggan/index.php`:** The customer master data page.
*   **`app/Views/pengiriman/index.php`:** The shipment listing page.
*   **`app/Views/pengiriman/manage.php`:** The form for creating and editing shipments.
*   **`app/Views/pengiriman/detail.php`:** The shipment detail page.
*   **`app/Views/pengiriman/delivery_note.php`:** The printable delivery note.

## Controllers

The application's business logic is handled by the controllers located in the `app/Controllers` directory. Here are some of the key controllers:

*   **AuthController.php:** Handles user authentication, including login, logout, and session management.
*   **PengirimanController.php:** Manages the core shipping functionality, including creating, editing, deleting, and tracking shipments. It has been updated with more specific methods like `create`, `store`, `edit`, `update`, and `delete`. It also has new methods for `detail`, `deliveryNote`, `generateQR`, `track`, `duplicate`, and `export`.
*   **BarangController.php:** Manages the master data for items.
*   **KategoriController.php:** Manages the master data for item categories.
*   **KurirController.php:** Manages the master data for couriers.
*   **PelangganController.php:** Manages the master data for customers.

## Database Schema

The database schema is defined in the `db/pengiriman.sql` file. It consists of the following tables:

*   **barang:** Stores information about the items that can be shipped.
*   **detail_pengiriman:** Stores the details of each shipment, including the items and quantities.
*   **kategori:** Stores the categories for the items.
*   **kurir:** Stores information about the couriers.
*   **pelanggan:** Stores information about the customers.
*   **pengiriman:** Stores the main information about each shipment.
*   **user:** Stores the user accounts for the application.

## Development Conventions

*   **Coding Style:** The project follows the PSR-12 coding style guide.
*   **Database Migrations:** Database changes should be managed through migrations, located in `app/Database/Migrations`.
*   **Routing:** Routes are defined in `app/Config/Routes.php`.
*   **Authentication:** The application uses a custom authentication filter (`app/Filters/AuthFilter.php`) to protect routes.
*   **API:** The application provides a set of API endpoints for interacting with the QR code system.
