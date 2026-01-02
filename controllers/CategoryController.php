<?php
require_once __DIR__ . '/../config/config.php';

class CategoryController {
    public function allCategories() {
        include __DIR__ . '/../views/user/all_category.php';
    }
}
?>

