# PHP Developer Practical Exam

## Overview
Build a simple Article Management API using any PHP framework of your choice.  
This exam focuses on backend development skills, clean code structure, and basic application security.  
Estimated duration: **4 hours**

---

## Allowed Frameworks
You may use any of the following:

- Laravel
- Symfony
- CodeIgniter
- Slim
- Lumen
- Yii2
- Laminas / Zend
- Vanilla PHP (no framework)

Before starting, declare:

- **Framework Chosen:** __________________________  
- **Experience Level:** ☐ Beginner ☐ Intermediate ☐ Advanced

---

# Project Requirements

## 1. Authentication
Implement basic user authentication:

- User registration  
- User login  
- Password hashing  
- Protect article **create/update/delete** routes  
- No advanced token/session features required

---

## 2. Article Management
Implement full CRUD functionality.

### Create Article
- Authentication required  
- Auto-generate **slug** from title  
- Assign current user as **author**  
- Accept `category_ids[]`

### List Articles
- Return **only published** articles  
- 10 per page pagination

### View Article
- Lookup by **slug**  
- Include **author name**

### Update Article
- Authentication required  
- Only the **author** of the article may update

### Delete Article
- Soft delete using `deleted_at`

---

## 3. Categories (Minimal Feature)
No CRUD endpoints needed.

Implement:

- Assign categories through `category_ids[]` during create/update  
- Retrieve all published articles in a category:  
  `GET /categories/{slug}`

Seed 3–4 default categories.

---

# Database Schema

## users
- id  
- name  
- email (unique)  
- password  
- created_at  
- updated_at  

## articles
- id  
- title  
- slug (unique)  
- content  
- author_id  
- status (draft or published)  
- deleted_at (nullable)  
- created_at  
- updated_at  

## categories
- id  
- name  
- slug (unique)  
- created_at  
- updated_at  

## article_categories
- article_id  
- category_id  

---

# Required Endpoints
## Authentication
- POST /auth/register
- POST /auth/login
## Articles
- GET /articles
- GET /articles/{slug}
- POST /articles
- PUT /articles/{id}
- DELETE /articles/{id}
## Categories
- GET /categories/{slug}


Technical Requirements:Must Have
● Proper input validation
● Password hashing
● SQL injection-safe queries (ORM or prepared statements)
● Authorization checks on update/delete
● Clean, readable code following framework conventions
● Soft deletes implemented correctly


Optional Bonus (If Time Allows)
● /articles/search?q= endpoint
● Seeders for users, categories, and sample articles
● Unit tests (registration, article creation, permission checks)
