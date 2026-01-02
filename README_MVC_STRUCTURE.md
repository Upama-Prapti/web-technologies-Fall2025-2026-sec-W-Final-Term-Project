# MVC Structure Documentation

## Project Structure

```
blogging website/
├── config/              # Configuration files
│   ├── config.php      # Main config
│   └── database.php    # Database connection
├── models/             # Data models
│   ├── UserModel.php
│   ├── AdminModel.php
│   ├── PostModel.php
│   ├── CommentModel.php
│   └── LikeModel.php
├── controllers/        # Business logic controllers
│   ├── AuthController.php
│   ├── PostController.php
│   ├── UserController.php
│   ├── CategoryController.php
│   └── AuthorController.php
├── views/              # View templates
│   ├── user/          # User-facing views
│   └── admin/         # Admin views
├── components/         # Reusable components
│   ├── connect.php
│   ├── user_header.php
│   ├── admin_header.php
│   └── footer.php
├── css/               # Stylesheets
├── js/                # JavaScript files
├── uploaded_img/      # Uploaded images
└── [Entry Points]     # PHP files that route to controllers
    ├── index.php
    ├── home.php
    ├── login.php
    ├── register.php
    ├── posts.php
    ├── view_post.php
    ├── category.php
    ├── update.php
    ├── user_likes.php
    ├── user_comments.php
    ├── authors.php
    ├── author_posts.php
    ├── all_category.php
    └── search.php
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

