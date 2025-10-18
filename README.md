# HNG Stage 0 Task: Dynamic Profile Endpoint

## Overview

This project is a solution for the **Stage 0 Backend task**. It consists of a dynamic RESTful API endpoint (`/me`) built with **vanilla PHP**.
The endpoint returns a JSON object containing static user profile information, a dynamically generated UTC timestamp, and a random cat fact fetched from an external API.

The project is structured with a focus on **separation of concerns**, **security**, and **portability**, without relying on external frameworks or dependency managers like Composer.

---

## Features

* **RESTful Endpoint:** A clean `GET /me` endpoint.
* **Dynamic Data:** Fetches a new random cat fact from the Cat Facts API on every request.
* **Live Timestamp:** Includes the current UTC time in ISO 8601 format, updated with each call.
* **Structured JSON Response:** Follows the required schema for the task.
* **Environment-Based Configuration:** User details are safely managed using a `.env` file.
* **Robust Security:**

  * **CORS Handling:** Includes headers and handles preflight `OPTIONS` requests.
  * **Rate Limiting:** Simple session-based protection against abuse (15 requests per minute).
  * **Error Handling:** Gracefully handles potential failures from the external Cat Facts API.

---

## Requirements

* PHP 8.0 or higher
* A web server (Apache with `mod_rewrite` enabled, Nginx, or PHP built-in server)
* Internet connection to fetch cat facts

---

## Folder Structure

The project is organized to separate routing, endpoint logic, and core application services.

```
/
├── .htaccess         # Apache rewrite rules for clean URLs
├── .env              # Your local environment variables (private)
├── .env.example      # Example environment file
├── index.php         # Main router / entry point
├── me.php            # Logic for the /me endpoint
└── assets/
    ├── header.php    # Handles security (CORS, rate limiting) and common headers
    └── Profile.php   # Data service class to fetch user info and cat facts
```

---

## Installation and Setup

Follow these steps to get the project running on your local machine.

### Step 1: Clone the Repository

```bash
git clone <your-repository-url>
cd <repository-folder>
```

### Step 2: Create Your Environment File

Copy the example environment file to create your own local configuration.

```bash
cp .env.example .env
```

Now open the `.env` file and replace the placeholder values with your actual details:

```env
USER_EMAIL="your.email@example.com"
USER_NAME="Your Full Name"
USER_STACK="PHP"
```

### Step 3: Run the Server

Use PHP’s built-in server for quick testing:

```bash
php -S localhost:8000
```

Your API is now running and accessible at **[http://localhost:8000](http://localhost:8000)**

### Step 4: Test the API

Use `curl` to test the endpoint:

```bash
curl -i http://localhost:8000/me
```

Or use Postman / your browser to visit
**[http://localhost:8000/me](http://localhost:8000/me)**

---

## API Response Examples

### ✅ Successful Response

```json
{
    "status": "success",
    "user": {
        "email": "your.email@example.com",
        "name": "Your Full Name",
        "stack": "PHP"
    },
    "timestamp": "2025-10-18T15:56:12.345Z",
    "fact": "A cat's brain is more similar to a human's brain than a dog's."
}
```

### ⚠️ Response When Cat Facts API Fails

```json
{
    "status": "success",
    "user": {
        "email": "your.email@example.com",
        "name": "Your Full Name",
        "stack": "PHP"
    },
    "timestamp": "2025-10-18T15:57:01.456Z",
    "fact": "Could not fetch cat fact due to a network error."
}
```

---

## How the System Works

1. **Request Routing:**
   The `.htaccess` file rewrites all requests to `/me` into `index.php?endpoint=me`.
   `index.php` acts as a router and loads the corresponding files.

2. **Security First:**
   `assets/header.php` is loaded for `/me`. It checks CORS preflight requests and enforces rate limiting.
   If the rate limit is exceeded, it sends a **429 Too Many Requests** response.

3. **Data Processing:**
   `me.php` calls `Profile::getProfileData()` from `assets/Profile.php`.
   This class reads your `.env` details, fetches the cat fact, and assembles the final response.

4. **Response:**
   The data array is encoded to JSON and returned with a **200 OK** status.

---

## Troubleshooting

| Issue                     | Possible Cause                                | Solution                                                                |
| ------------------------- | --------------------------------------------- | ----------------------------------------------------------------------- |
| 404 Not Found             | `.htaccess` not read / `mod_rewrite` disabled | Ensure Apache allows `.htaccess` overrides or use PHP’s built-in server |
| 500 Internal Server Error | Syntax error in PHP code                      | Check PHP error logs for the specific issue                             |
| 429 Too Many Requests     | Exceeded rate limit (15 req/min)              | Wait one minute before retrying                                         |
| User data is empty        | Missing or unreadable `.env` file             | Create `.env` from example and ensure correct permissions               |

---

## Submission Information

**Full Name:** Your Full Name
**Email:** [your.email@example.com](mailto:your.email@example.com)
**Stack:** PHP
