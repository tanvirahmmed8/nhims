# NHIMS

## National Health Information Management System for Bangladesh

NHIMS is a Laravel 13 healthcare information management prototype built with Blade, Bootstrap 5, Bootstrap Icons, Vite, Vanilla JavaScript, and MySQL.

The application demonstrates a connected care workflow across fictional facilities using synthetic data only.

## Features

- Role-based authentication for administrators, receptionists, doctors, laboratory staff, pharmacy staff, and patients
- Facility membership and scoped access control
- Patient registration, search, duplicate checks, emergency contacts, and profile history
- Appointment booking, filtering, rescheduling, cancellation, and status tracking
- Doctor consultation notes, diagnosis, follow-up, and authorized history review
- Electronic prescriptions with multiple medicine items
- Laboratory test orders, result entry, and result publication
- Pharmacy prescription verification and dispensing
- Patient portal for appointments, prescriptions, dispensing status, and published results
- Aggregate reports and searchable audit logs
- Responsive Bootstrap interface for desktop, tablet, and mobile screens
- Server-side validation, policies, active-account checks, and denied-access audit events

## Technology

- PHP 8.3 or later
- Laravel 13
- Blade and Laravel controllers
- Bootstrap 5 and Bootstrap Icons
- Vite and Sass
- MySQL 8 or compatible MariaDB
- Vanilla JavaScript
- PHPUnit

## Clone the repository

```powershell
git clone https://github.com/tanvirahmmed8/nhims.git
Set-Location nhims
```

The repository root is the Laravel `prototype` application. Run all commands from this directory.

## Prerequisites

Install and verify the following before setup.

- PHP 8.3 or later with the MySQL and mbstring extensions
- Composer
- Node.js and npm
- MySQL 8 or MariaDB
- WAMP or another local MySQL service on Windows

Check versions.

```powershell
php -v
composer --version
node -v
npm.cmd --version
```

## Database setup

Start the MySQL service and create a database named `nhims` with `utf8mb4` support.

```sql
CREATE DATABASE nhims CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

You can use phpMyAdmin, MySQL Workbench, or the MySQL command line.

## Application setup

Run these commands from the cloned repository directory.

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
```

Open `.env` and confirm the database values.

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nhims
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations and load deterministic fictional demo records.

```powershell
php artisan migrate --seed
```

Install frontend dependencies and compile production assets.

```powershell
npm.cmd install
npm.cmd run build
```

On Windows PowerShell, use `npm.cmd` when the `npm.ps1` execution-policy shim is unavailable.

## Run the application

Start the Laravel server.

```powershell
php artisan serve
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000).

For frontend development with Vite, use a second terminal.

```powershell
npm.cmd run dev
```

## Run tests

```powershell
php artisan test
```

The feature suite covers the connected clinical workflow, validation, facility scope, patient privacy, inactive accounts, denied-access audit events, and patient portal visibility.

## Demo accounts

The seeder creates these synthetic accounts. Every demo account uses the password `password`.

| Email | Role | Facility context |
| --- | --- | --- |
| `admin@nhims.test` | Administrator | Dhaka Central Community Hospital |
| `receptionist@nhims.test` | Receptionist | Dhaka Central Community Hospital |
| `doctor.dhaka@nhims.test` | Doctor | Dhaka Central Community Hospital |
| `doctor.chattogram@nhims.test` | Doctor | Chattogram Coastal Diagnostic Centre |
| `lab@nhims.test` | Laboratory staff | Chattogram Coastal Diagnostic Centre |
| `pharmacy@nhims.test` | Pharmacy staff | Dhaka Central Community Hospital |
| `patient@nhims.test` | Patient | Ayesha Rahman demo profile |

When `APP_ENV=local` and `APP_DEBUG=true`, the login page displays role-based demo login buttons for these accounts.

## Registration policy

Public registration is for patients only. Staff, doctors, laboratory staff, pharmacy staff, and administrator accounts are created and assigned through the administrator-controlled staff workflow.

In the demonstration flow, an authorized receptionist links a patient account to the full patient profile before portal access is enabled.

## Demo workflow

1. Sign in as the receptionist and register or search a patient.
2. Book an appointment with a doctor and facility.
3. Sign in as the doctor and complete the consultation.
4. Issue a prescription and create a laboratory order.
5. Sign in as laboratory staff and publish a result.
6. Sign in as pharmacy staff and record dispensing.
7. Sign in as the patient and review the portal.
8. Sign in as the Chattogram doctor to demonstrate authorized cross-facility history access.
9. Attempt an unauthorized patient-record or admin URL and review the denied audit event.

## Project structure

```text
app/                 Controllers, models, policies, requests, middleware
bootstrap/            Laravel bootstrap configuration
config/               Application configuration
database/             Migrations, factories, and deterministic seeders
resources/views/      Blade screens and layouts
resources/sass/       Bootstrap variables and NHIMS design system
resources/js/         Bootstrap and small Vanilla JavaScript setup
routes/web.php        Web routes and role-protected route groups
tests/                PHPUnit feature and unit tests
```

## Security and data policy

- Use synthetic patients, fictional facilities, and demo credentials only.
- Keep `.env` out of source control. Use `.env.example` for setup.
- Do not use the seeded password outside local demonstration environments.
- Authorization is enforced server-side through middleware, policies, and scoped queries.
- Sensitive reads, writes, and denied access attempts are recorded in the audit log.

## Prototype limitations

This coursework prototype does not connect to live national health exchanges, SMS or email delivery, payment gateways, real identity verification, clinical devices, or production hosting.

## License

This project is an educational prototype based on Laravel and is intended for coursework demonstration.
