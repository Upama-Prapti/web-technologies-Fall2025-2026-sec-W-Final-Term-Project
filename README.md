# MVC Structure Documentation
# Project Structure

```
blog/
├── assets/                    # Static assets
│   ├── css/
│   │   ├── admin_style.css   # Admin panel styles
│   │   └── style.css         # User panel styles
│   └── js/
│       ├── admin_script.js   # Admin panel scripts
│       └── script.js         # User panel scripts
│
├── components/                # Reusable PHP components
│   ├── add_cart.php
│   ├── admin_header.php      # Admin panel header
│   ├── admin_logout.php
│   ├── connect.php           # Database connection (legacy)
│   ├── db_helper.php
│   ├── footer.php            # Site footer
│   ├── like_post.php         # Like/unlike functionality
│   ├── user_header.php       # User panel header
│   └── user_logout.php
│
├── config/                    # Configuration files
│   ├── config.php            # Main configuration (BASE_URL, constants)
│   └── database.php          # Database connection setup
│
├── controllers/               # MVC Controllers
│   ├── AdminController.php   # Admin panel operations
│   ├── AuthController.php    # Authentication (login, register, logout)
│   ├── AuthorController.php  # Author-related operations
│   ├── CategoryController.php # Category operations
│   ├── PostController.php    # Post operations (home, view, search, etc.)
│   └── UserController.php    # User operations (profile, likes, comments, create post)
│
├── models/                    # Data models
│   ├── AdminModel.php
│   ├── CommentModel.php
│   ├── LikeModel.php
│   ├── PostModel.php
│   └── UserModel.php
│
├── views/                     # View templates
│   ├── admin/                 # Admin panel views
│   │   ├── add_posts.php
│   │   ├── admin_accounts.php
│   │   ├── comments.php
│   │   ├── dashboard.php
│   │   ├── edit_post.php
│   │   ├── login.php
│   │   ├── read_post.php
│   │   ├── register_admin.php
│   │   ├── update_profile.php
│   │   ├── users_accounts.php
│   │   └── view_posts.php
│   └── user/                  # User panel views
│       ├── all_category.php
│       ├── author_posts.php
│       ├── authors.php
│       ├── category.php
│       ├── create_post.php    # User post creation
│       ├── home.php           # Home page
│       ├── login.php          # Login page (with tabs)
│       ├── posts.php
│       ├── register.php
│       ├── search.php
│       ├── update.php
│       ├── user_comments.php
│       ├── user_likes.php
│       └── view_post.php
│
├── uploaded_img/              # User uploaded images
│
├── blog_db.sql               # Database schema
├── index.php                 # Main entry point (Router)
└── README_MVC_STRUCTURE.md   # This file
```


## How It Works

1. **Entry Points** (root PHP files) - Route requests to appropriate controllers
2. **Controllers** - Handle business logic, interact with models, load views
3. **Models** - Handle database operations
4. **Views** - Display HTML/PHP templates

## Path Updates Needed

All view files should use relative paths from their location:
- `../../components/` for components
- `../../css/` for stylesheets
- `../../js/` for JavaScript
- `../../uploaded_img/` for images

## Database

- Uses mysqli with port 3307
- Database: blog_db
- All queries use prepared statements for security

