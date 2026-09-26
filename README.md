Multi-User Blog Application (Laravel 12)
A feature-rich, responsive Multi-User Blog Application built with Laravel 12, Tailwind CSS, and MySQL. This application features role-based access control (Admin & Author/User), full post CRUD management, dynamic filtering/searching, an image storage system, and an interactive comment module.

🚀 Features
Public Features
Dynamic Homepage: Browse recent blog posts with pagination.

Search & Filters: Search posts by title/content and filter by Categories or Tags.

Single Post View: Detailed post reader page with author meta, dynamic tags, and interactive comment section.

Author Portal
Post Management (CRUD): Authors can create, view, edit, and delete their own blog posts.

Featured Image Uploads: Image upload support integrated with Laravel's public storage link.

Draft/Publish Workflow: Set posts as draft or published.

Tag & Category Assignment: Sync multiple tags and assign posts to specific categories.

Interactive & Security Features
Policy Authorization: Enforced post ownership using PostPolicy—users can only edit/delete their own content.

Comment System: Authenticated users can leave comments. Post authors and comment owners can delete comments.

Role-Based Access Control (RBAC): Distinct permissions for admin and user/author roles.

Admin Panel
Protected Middleware: Dedicated /admin route group restricted exclusively to users with the admin role.

Dashboard Stats: High-level overview displaying Total Posts, Users, Categories, and Comments.

Category & User Management: Full control over blog categories and user permissions.

🛠️ Tech Stack
Framework: Laravel 12

Language: PHP 8.2+

Database: MySQL

Frontend: Blade Templates + Tailwind CSS (via Vite)

Authentication: Laravel Breeze

📋 Prerequisites
Ensure you have the following installed on your machine:

PHP (>= 8.2)

Composer

Node.js (>= 18.x) & NPM

MySQL (via XAMPP, Laragon, or standalone service)

📦 Installation & Setup Guide
Follow these steps to set up the project locally:

1. Clone the Repository
Bash
git clone https://github.com/your-username/blog-project.git
cd blog-project
2. Install PHP Dependencies
Bash
composer install
3. Install Node.js Dependencies
Bash
npm install
4. Environment Configuration
Duplicate the .env.example file to create your .env configuration file:

Bash
cp .env.example .env
Open .env and configure your database credentials:

Code snippet
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_db
DB_USERNAME=root
DB_PASSWORD=
5. Generate Application Key
Bash
php artisan key:generate
6. Run Migrations & Database Seeders
Run database migrations along with predefined seeders (creates an Admin user, default categories, tags, and dummy posts):

Bash
php artisan migrate --seed
Default Admin Credentials:

Email: admin@example.com

Password: password

7. Link Storage Folder
Create the symbolic link required for image uploads to be accessible publicly:

Bash
php artisan storage:link
(If php is not recognized globally on Windows, use C:\xampp\php\php artisan storage:link)


💻 Running the Application
1. Compile Frontend Assets (Vite)
Bash
npm run dev
2. Start Local Development Server
Open a new terminal window/tab and run:

Bash
php artisan serve
Access the application in your browser at:
👉 [http://127.0.0.1:8000](http://127.0.0.1:8000)


📂 Project Structure Overview
Plaintext
blog-project/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/PostController.php       # Admin Management
│   │   │   ├── Author/PostController.php      # Author CRUD Controller
│   │   │   ├── CommentController.php          # Comments Controller
│   │   │   └── PostController.php             # Public Controller
│   │   └── Middleware/
│   │       └── CheckRole.php                  # Admin Role Middleware
│   ├── Models/                                # User, Post, Category, Tag, Comment
│   └── Policies/
│       └── PostPolicy.php                     # Ownership & Auth Policies
├── database/
│   ├── factories/                             # Model Factories
│   ├── migrations/                            # DB Schemas
│   └── seeders/                               # Database Seeders
├── resources/
│   └── views/
│       ├── admin/                             # Admin Dashboard Views
│       ├── author/                            # Author Portal Views
│       ├── layouts/                           # Master App Layouts
│       └── posts/                             # Public Blog Views
└── routes/
    └── web.php                                # Web Application Routes
    
🔒 License
This project is open-sourced software licensed under the MIT license.
