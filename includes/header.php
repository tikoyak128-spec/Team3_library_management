<?php
if (!defined('BASE_URL')) {
    // Dynamically matches your exact folder name 'php +20larevel/TEAM3-library_management/'
    define('BASE_URL', 'http://localhost/php%20+20larevel/TEAM3-library_management/');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Pro - Management System</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Primary Global Stylesheet Layout Core -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>Assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>Assets/css/dashboard.css">
    <link rel="stylesheet" href="../Assets/css/style.css">
    <link rel="stylesheet" href="../Assets/css/dashboard.css">
    <!-- Dynamic Page Stylesheet Injection -->
    <?php if (isset($page_styles) && is_array($page_styles)): ?>
        <?php foreach ($page_styles as $style): ?>
            <link rel="stylesheet" href="<?php echo BASE_URL . $style; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
<div class="app-container">
    <!-- Sidebar will be included right below this in the page template -->