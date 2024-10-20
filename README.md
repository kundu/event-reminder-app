<details>
<summary>📄 Documentation of Event Management System Development</summary>

## Overview

This document outlines the development of an Event Management System using Laravel, showcasing various features such as user registration, event creation, updating, and deletion. The system is designed to manage events efficiently while ensuring a smooth user experience. The implementation adheres to best practices in Laravel development, including the use of controllers, services, and request validation.

## Features Implemented

### 1. User Registration

The system allows users to register through a dedicated route. The registration process includes validation for required fields such as name, email, and password. The following features were implemented:

- **Validation**: Ensures that all required fields are filled out correctly, including checks for duplicate emails.
- **Database Interaction**: Successfully stores user information in the database upon registration.

### 2. Event Management

The core functionality of the system revolves around managing events. Users can create, update, delete, and view events. The following features were implemented:

#### a. Event Creation

- **Route**: A POST route (`/events`) is defined to handle event creation.
- **Validation**: The system validates event data, ensuring that required fields such as title, start time, and end time are provided.
- **Database Interaction**: Events are stored in the database with the associated user ID.

#### b. Event Updating

- **Route**: A PUT route (`/events/{id}`) is defined to handle event updates.
- **Authorization**: The system checks if the user is authorized to update the event using Laravel's Gate functionality.
- **Validation**: Similar to event creation, the update process includes validation for required fields and ensures that the end time is after the start time.
- **Database Interaction**: Updates the event details in the database.

#### c. Event Deletion

- **Route**: A DELETE route (`/events/{id}`) is defined to handle event deletion.
- **Authorization**: Ensures that only the owner of the event can delete it.
- **Database Interaction**: Removes the event from the database.

### 3. Event Listing

The system provides a view for users to see their upcoming and completed events. This feature includes:

- **Data Retrieval**: Fetches events associated with the authenticated user.
- **View Rendering**: Displays events in a user-friendly format.

### 4. Request Validation

Custom request classes (`EventStoreRequest` and `EventUpdateRequest`) were created to handle validation logic for event creation and updating. This approach keeps the controller clean and adheres to the Single Responsibility Principle.

### 5. Service Layer

A service class (`EventService`) was implemented to encapsulate the business logic related to event management. This includes methods for creating, updating, and retrieving events. This separation of concerns enhances code maintainability and testability.

### 6. Testing

Comprehensive feature tests were written to ensure the functionality of the application. The tests cover:

- User registration
- Event creation, updating, and deletion
- Validation rules for events
- Authorization checks

These tests utilize Laravel's built-in testing capabilities, ensuring that the application behaves as expected.

![Test Case Screenshot](https://i.ibb.co.com/sggd3Pm/Screenshot-from-2024-10-21-00-03-07.png)

### 7. Cron Job Setup

To ensure that scheduled tasks run automatically, we need to set up a cron job on the server. This cron job will run the Laravel scheduler every minute, allowing it to execute any scheduled tasks defined in the application.

1. Open the crontab file for editing:
   ```
   crontab -e
   ```

2. Add the following line to run the Laravel scheduler every minute:
   ```
   * * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
   ```
   Replace `/path/to/your/project` with the actual path to your Laravel project.

3. Save and exit the crontab file.

This setup ensures that the Laravel scheduler runs regularly, executing any scheduled tasks such as sending event reminders.

### 8. Queue Configuration

For testing purposes, we are currently using the `sync` queue driver. This means that queued jobs are executed synchronously in the foreground. While this is suitable for testing, it's important to note that in a production environment, you would typically use a more robust queue driver like Redis or database for better performance and reliability.

To configure the `sync` queue driver for testing:

1. In your `.env` file, ensure the following line is present:
   ```
   QUEUE_CONNECTION=sync
   ```

2. This configuration allows you to test queued jobs immediately without setting up a separate queue worker.

When moving to production, you should consider switching to a more scalable queue driver and running queue workers to process jobs in the background.



## Challenges Faced

During the development process, I encountered challenges related to:

- **Authorization Logic**: Implementing the authorization checks required a good understanding of Laravel's Gate and Policy features.
- **Validation**: Ensuring that all validation rules were correctly applied and that appropriate error messages were returned to the user.

## Limitations

It is important to note that the offline data saving functionality was not implemented in this version of the application. The focus was primarily on online event management, ensuring that all data interactions occur in real-time with the database.

</details>

<details>
<summary>🛠️ Future Development Enhancements for the Event Management System</summary>

## Introduction

As the Event Management System continues to evolve, there are several enhancements that can be implemented to improve debugging, performance, and overall user experience. This document outlines the potential integration of Laravel Telescope for debugging, Laravel Horizon for managing job queues, and Redis for caching to optimize query performance.

## 1. Integrating Laravel Telescope for Debugging

### Overview

Laravel Telescope is an elegant debug assistant for Laravel applications. It provides insights into requests, exceptions, database queries, and more, making it easier to monitor and debug applications during development.

### Benefits

- **Real-time Monitoring**: Telescope allows developers to monitor requests and responses in real-time, providing immediate feedback on application performance.
- **Detailed Insights**: It offers detailed information about database queries, cache operations, and scheduled tasks, helping identify bottlenecks and optimize performance.
- **Error Tracking**: Telescope captures exceptions and logs them, making it easier to debug issues as they arise.

### Implementation Steps

1. Install Telescope via Composer:
   ```
   composer require laravel/telescope
   ```

2. Publish the Telescope configuration:
   ```
   php artisan telescope:install
   ```

3. Run the migrations to create the necessary tables:
   ```
   php artisan migrate
   ```

4. Configure Telescope in the `config/telescope.php` file to suit the application's needs.

5. Access Telescope through the `/telescope` route to monitor application performance and debug issues.

## 2. Implementing Laravel Horizon for Job Queue Management

### Overview

Laravel Horizon provides a beautiful dashboard and code-driven configuration for managing Laravel's job queues. It allows developers to monitor job processing in real-time and manage queues effectively.

### Benefits

- **Real-time Monitoring**: Horizon provides a dashboard to monitor job processing, including failed jobs, job throughput, and processing times.
- **Queue Management**: It allows for easy management of job queues, including prioritization and configuration of different queue connections.
- **Notifications**: Horizon can send notifications for failed jobs, ensuring that developers are alerted to issues promptly.

### Implementation Steps

1. Install Horizon via Composer:
   ```
   composer require laravel/horizon
   ```

2. Publish the Horizon configuration:
   ```
   php artisan horizon:install
   ```

3. Run the migrations to create the necessary tables:
   ```
   php artisan migrate
   ```

4. Configure Horizon in the `config/horizon.php` file to define the queues and their settings.

5. Start Horizon using the command:
   ```
   php artisan horizon
   ```

6. Access the Horizon dashboard through the `/horizon` route to monitor job processing.

## 3. Utilizing Redis for Caching

### Overview

Redis is an in-memory data structure store that can be used as a database, cache, and message broker. Integrating Redis into the Event Management System can significantly enhance performance by caching frequently accessed data.

### Benefits

- **Faster Query Performance**: Caching results in Redis reduces the need for repeated database queries, leading to faster response times.
- **Scalability**: Redis can handle a large number of requests, making it suitable for applications with high traffic.
- **Session Management**: Redis can be used to manage user sessions efficiently, improving the overall user experience.

### Implementation Steps

1. Install the Redis PHP extension and the predis/predis package via Composer:
   ```
   composer require predis/predis
   ```

2. Configure the Redis connection in the `config/database.php` file.

3. Use Redis for caching by implementing the `Cache` facade in the application:
   ```php
   use Illuminate\Support\Facades\Cache;

   // Caching an event query
   $events = Cache::remember('events', 60, function () {
       return Event::all();
   });
   ```

4. Monitor Redis performance and adjust caching strategies as needed to optimize application performance.

## Conclusion

By integrating Laravel Telescope, Horizon, and Redis into the Event Management System, we can significantly enhance debugging capabilities, improve job queue management, and optimize query performance. These enhancements will lead to a more robust and efficient application, ultimately providing a better experience for users and developers alike.

</details>


<details>
<summary>🎸 Running the Event Management System Locally</summary>

# Running the Event Management System Locally

To run the Event Management System locally, follow these steps to set up your environment, configure the database, and run the application. This guide assumes you have PHP, Composer, and a web server (like Apache or Nginx) installed on your machine.

## Prerequisites

1. **PHP**: Ensure you have PHP 8.0 or higher installed.
2. **Composer**: Make sure Composer is installed for managing PHP dependencies.
3. **Database**: You should have MySQL or another compatible database server running.
4. **SMTP Email**: Set up SMTP email for testing using Mailtrap.

To set up SMTP email for testing, we will use Mailtrap, a service that allows you to test email sending without actually sending emails to real addresses.

- **Create a Mailtrap Account**: Go to [Mailtrap](https://mailtrap.io/) and sign up for a free account.

- **Get SMTP Credentials**: Once you have created an account, navigate to the "Inboxes" section and find the SMTP settings. You will need the following credentials:
   - `MAIL_HOST`
   - `MAIL_PORT`
   - `MAIL_USERNAME`
   - `MAIL_PASSWORD`

## Step 1: Clone the Repository

Clone the repository to your local machine:

```bash
git clone https://github.com/kundu/event-reminder-app.git
cd event-reminder-app
```

## Step 2: Install Dependencies

Run the following command to install the required PHP packages:

```bash
composer install
```

## Step 3: Set Up the Environment File

1. Copy the `.env.example` file to create your `.env` file:

```bash
cp .env.example .env
```

2. Open the `.env` file in a text editor and configure the database settings. Set the `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` to match your local database configuration. For example:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_test_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

Now, you can run the application and test the email sending functionality using Mailtrap.

```
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
```

## Step 4: Create the Test Database

1. Log in to your MySQL server:

```bash
mysql -u your_database_username -p
```

2. Create a new database for testing:

```sql
CREATE DATABASE your_test_database_name;
```

3. Exit the MySQL prompt:

```sql
EXIT;
```

## Step 5: Generate Application Key

Run the following command to generate the application key:

```bash
php artisan key:generate
```

## Step 6: Run Migrations

Run the migrations to create the necessary tables in your database:

```bash
php artisan migrate
```

## Step 7: Seed the Database (Optional)

If you have seeders set up and want to populate your database with initial data, run:

```bash
php artisan db:seed
```

## Step 8: Start the Local Development Server

You can start the built-in PHP development server using the following command:

```bash
php artisan serve
```

This will start the server at `http://localhost:8000` by default.

## Step 9: Run Tests

To run the tests and ensure everything is functioning correctly, use the following command:

```bash
php artisan test
```
This will execute all the tests defined in your application.

**Note: Without proper SMTP configuration, the project will not run correctly. For local development, the OTP will be set to 123456.**



## Conclusion

You should now have the Event Management System running locally. You can access the application in your web browser at `http://localhost:8000`. Make sure to test the various functionalities, including user registration, event creation, updating, and deletion, to ensure everything is working as expected.

</details>
