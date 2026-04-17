# BD Payment Gateway API (BD PGW API)

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](https://opensource.org/licenses/MIT)
[![Contributors](https://img.shields.io/badge/Contributors-1-brightgreen.svg?style=for-the-badge)](#-developer)

A standalone, easily deployable custom PHP MVC microservice that acts as a unified Payment Gateway API for Bangladeshi Mobile Financial Services (MFS).

## 📑 Table of Contents
- [🚀 Features](#-features)
- [📋 Requirements](#-requirements)
- [⚙️ Installation & Setup](#️-installation--setup)
- [🛣️ API Endpoints](#️-api-endpoints)
- [🤝 Contributing](#-contributing)
- [🙏 Acknowledgments](#-acknowledgments)
- [👨‍💻 Developer](#-developer)
- [📄 License](#-license)

## 🚀 Features

- **bKash Integration**: Fully implements the latest bKash Tokenized Checkout, including token generation, checkout creation, execution, and automatic token refresh functionality.
- **Nagad Integration**: Secure implementation of Nagad payment protocol utilizing asymmetric cryptography (RSA public/private keys) and digital signatures.
- **Micro-Framework**: Built on an extremely lightweight, zero-dependency custom core framework to maximize performance and minimize security overhead.
  - Custom Routing Engine (`Core\Routing\Route`)
  - Lightweight PDO Database Layer & Query Builder
  - Built-in Schema Migrator
  - Input Validation & CSRF Protection

## 📋 Requirements

- PHP 8.0 or higher
- MySQL / MariaDB
- Web Server (Apache/Nginx) with URL Rewriting enabled
- cURL extension enabled

## ⚙️ Installation & Setup

1. **Clone the repository:**
   ```bash
   git clone <repository_url>
   cd bd-pgw-api
   ```

2. **Environment Configuration:**
   Copy the example environment file and update your variables:
   ```bash
   cp .env.example .env
   ```
   Add your bKash and Nagad credentials, along with your Database settings, into `.env`.

3. **Database Migration:**
   The project features a custom migration runner. Run the following command from the root to create the `PaymentTokens` table:
   ```bash
   php migrate.php migrate
   ```

4. **Web Server Configuration:**
   Point your web server's document root to the `public/` directory ensuring that `.htaccess` handling is enabled to allow `public/index.php` to handle all routing.

## 🛣️ API Endpoints

### 🩺 System
* `POST /api/site/health` - Check health status of the API.

### 🦅 bKash
* `POST /api/bkash/pay-now`
  * **Payload (`JSON`)**: `invoice`, `phone`, `amount`, `callbackUrl`
  * **Description**: Initializes the payment transaction.
* `GET /api/bkash/verify/{paymentId}`
  * **Description**: Executes and verifies a successful payment.

### 🟢 Nagad
* `POST /api/nagad/pay-now`
  * **Payload (`JSON`)**: `invoice`, `phone`, `amount`, `callbackUrl`
  * **Description**: Initializes the Nagad secure payment session.
* `GET /api/nagad/verify/{paymentId}`
  * **Description**: Verifies the completed Nagad transaction.

## 🤝 Contributing

I welcome contributions to improve this library. Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 🙏 Acknowledgments

- bKash Limited for their payment API
- Nagad Limited for their payment API
- PHP community for excellent documentation and resources

## 👨‍💻 Developer

**Mehedi Hasan**

- **Email**: mehedi01616720009@gmail.com
- **GitHub**: [Mehedi01616720009](https://github.com/Mehedi01616720009)

If this project helps you, please consider giving it a star ⭐ on GitHub.

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
