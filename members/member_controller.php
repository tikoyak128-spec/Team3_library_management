<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Database/db.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Helper function to convert uploaded file into a Base64 Data URL
function handleImageToDataUrl($existingImage = '') {
    // 1. Direct Image URL Input takes highest priority
    if (!empty($_POST['image_url']) && filter_var($_POST['image_url'], FILTER_VALIDATE_URL)) {
        return trim($_POST['image_url']);
    }

    // 2. Convert uploaded file directly to Base64 Data URL
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath   = $_FILES['profile_image']['tmp_name'];
        $fileExtension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $imageData = file_get_contents($fileTmpPath);
            $mimeType  = ($fileExtension === 'jpg') ? 'image/jpeg' : 'image/' . $fileExtension;

            // Notice the comma right after base64:
            return 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
        }
    }

    return $existingImage;
}

switch ($action) {
    case 'create':
        $name  = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');

        // Convert uploaded image directly into Base64 URL string
        $profile_image = handleImageToDataUrl();

        if (!empty($name)) {
            $sql = "INSERT INTO members (name, phone, email, profile_image) VALUES (:name, :phone, :email, :profile_image)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':name'          => $name,
                ':phone'         => $phone,
                ':email'         => $email,
                ':profile_image' => $profile_image
            ]);
        }

        header('Location: index.php?status=success');
        exit;

    case 'update':
        $id            = $_POST['id'] ?? null;
        $name          = trim($_POST['name'] ?? '');
        $phone         = trim($_POST['phone'] ?? '');
        $email         = trim($_POST['email'] ?? '');
        $existingImage = $_POST['existing_profile_image'] ?? '';

        // Convert uploaded image to Base64 URL or keep existing string
        $profile_image = handleImageToDataUrl($existingImage);

        if ($id && !empty($name)) {
            $sql = "UPDATE members SET name = :name, phone = :phone, email = :email, profile_image = :profile_image WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':name'          => $name,
                ':phone'         => $phone,
                ':email'         => $email,
                ':profile_image' => $profile_image,
                ':id'            => $id
            ]);
        }

        header('Location: index.php?status=updated');
        exit;

    case 'delete':
        $id = $_GET['id'] ?? null;

        if ($id) {
            $sql = "DELETE FROM members WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);
        }

        header('Location: index.php?status=deleted');
        exit;

    default:
        header('Location: index.php');
        exit;
}