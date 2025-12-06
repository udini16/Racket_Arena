Racket Arena - Sports Court Booking System 🏸

A robust web-based application designed to streamline the management of sports court reservations. This system features separate portals for customers and staff, real-time availability checking, and an efficient workflow for assigning courts to booking requests.

🚀 Key Features

👤 Customer Portal

Smart Booking Interface: Interactive form to select date, start time, and duration.

Real-Time Availability: The system automatically disables time slots that are fully booked across all courts to prevent double-booking.

Booking History: Customers can track the status of their requests (Pending, Confirmed, Cancelled).

Automated Cost Calculation: Estimates pricing based on booking duration.

👔 Staff/Employee Dashboard

Request Management: View incoming booking requests in a unified table.

Court Assignment: Assign specific courts (e.g., Grass, Clay, Hard) to confirmed bookings.

Status Control: Approve, Reject, or Mark bookings as Complete.

Conflict Detection: Visual indicators prevent assigning a court that is already occupied during a specific time slot.

Dashboard Stats: Quick overview of daily activity (placeholders implemented).

⚙️ Backend Logic (Laravel)

Robust Validation: Prevents overlapping bookings at the database level.

Timezone Handling: Ensures consistent scheduling regardless of user local time (UTC handling).

API-First Design: Backend serves JSON data to the frontend via RESTful API endpoints.

🛠️ Tech Stack

Backend: Laravel 10+, PHP 8.1+

Frontend: Blade Templates, Vanilla JavaScript (Fetch API), Tailwind CSS

Database: MySQL

Icons: FontAwesome

📦 Installation

Clone the repository

git clone [https://github.com/yourusername/racket-arena.git](https://github.com/yourusername/racket-arena.git)


Install Dependencies

composer install
npm install


Environment Setup

cp .env.example .env
php artisan key:generate


Database Migration

php artisan migrate --seed


Run the Server

php artisan serve


📝 Usage

Customer: Register/Login to view available slots and submit a booking request.

Staff: Login to the staff panel to view requests. Use the "Assign" button to allocate a court to a pending request.

🤝 Contributing

Contributions, issues, and feature requests are welcome!

📄 License

MIT
