Flight Ticket Booking System

A robust, web-based flight reservation and management system designed to streamline the ticketing process for both passengers and airline administrators. This platform solves the complexities of flight inventory management by implementing a priority-based booking engine, waitlist handling, and a comprehensive administrative dashboard for real-time tracking of domestic and international flights.
💻 Tech Stack

Frontend

    HTML5 & CSS3: Responsive structuring and styling for user and admin interfaces.

    PHP: Server-side HTML templating and dynamic data rendering.

Backend

    PHP (Core): Handles routing, session-based authentication, and business logic (e.g., seat allocation algorithms, priority calculations).

Database

    MySQL: Relational database management using prepared statements (mysqli) to prevent SQL injection and ensure secure data handling.

✨ Key Features

    Role-Based Access Control (RBAC): Secure session management separating standard passenger profiles from administrative accounts.

    Priority Booking Engine: Implements a tiered booking system where users are assigned priority levels (1-5). New reservations default to a 'WAITLISTED' status until seat allocation logic is processed.

    Comprehensive Admin Dashboard: A centralized hub for administrators to:

        View real-time statistics (total flights, active users, total/pending bookings).

        Monitor and manage domestic and international flights separately.

        Track the exact capacity (seats total vs. seats booked) across the entire fleet.

    Flight Categorization & Search: Automatically separates inventory into 'Domestic' and 'International' routes, making it easier for users to query specific departure and arrival dates.

    Data Integrity & Security: Enforces cascading deletions on relational data and utilizes bcrypt password hashing (as seen in the database seed file) for user security.

🗄️ Database Design / Architecture

The system relies on a normalized relational database (flight_booking) consisting of three primary tables:

    users: Manages both passengers and admins. Stores authentication credentials (hashed), role identification (is_admin), and a priority_level used for waitlist resolution.

    flights: The core inventory table. Tracks flight_code, source, destination, scheduling (flight_date, departure_time, arrival_time, arrival_date), and capacity metrics (seats_total, seats_booked). Includes a flight_type enum (Domestic/International).

    bookings: The transactional table linking users and flights. Tracks the reservation status (CONFIRMED, WAITLISTED, CANCELLED) and inherits the user's priority at the time of booking. Features foreign keys with ON DELETE CASCADE to maintain data integrity.

🚀 Setup Instructions

Follow these steps to run the project locally on your machine:

Prerequisites:
You will need a local server environment with PHP and MySQL installed (e.g., XAMPP, WAMP, MAMP, or LAMP).

    Clone the Repository
    Bash

    git clone https://github.com/YourUsername/flight-ticket-booking.git
    cd flight-ticket-booking

    Database Setup

        Open your database management tool (e.g., phpMyAdmin).

        Create a new database or simply import the provided SQL file.

        Run the SQL script included in the repository to generate the schema and populate the database with dummy users and 200+ seed flights.

    Configure Environment Variables

        Locate the config.php file in the root directory (create one if it does not exist based on your local settings).

        Update the database connection credentials to match your local setup:
        PHP

        <?php
        $mysqli = new mysqli("localhost", "root", "", "flight_booking");
        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }
        ?>

    Run the Application

        Move the project folder into your local server's document root (e.g., htdocs for XAMPP or www for WAMP).

        Start Apache and MySQL from your control panel.

        Open your web browser and navigate to http://localhost/flight-ticket-booking/.

Test Credentials:

    Admin Login: admin@example.com | Password: admin123

    Passenger Login: alice@example.com | Password: password123
