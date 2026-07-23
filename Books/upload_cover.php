<?php
require_once __DIR__ . '/../Authentication/auth_check.php';
require_once __DIR__ . '/../Database/db.php';

if (!isset($conn) && isset($pdo)) { $conn = $pdo; }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cover_image'])) {
    $book_id = (int)($_POST['book_id'] ?? 0);
    $file    = $_FILES['cover_image'];

    if ($book_id > 0 && $file['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        
        if (in_array(mime_content_type($file['tmp_name']), $allowedTypes)) {
            $ext          = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFileName  = 'book_' . $book_id . '_' . time() . '.' . $ext;
            $uploadFolder = __DIR__ . '/../Assets/images/';

            // Create target directory if missing
            if (!is_dir($uploadFolder)) {
                mkdir($uploadFolder, 0777, true);
            }

            $destination = $uploadFolder . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                try {
                    // Update book cover_image column in database
                    $stmt = $conn->prepare("UPDATE books SET cover_image = ? WHERE id = ?");
                    $stmt->execute([$newFileName, $book_id]);
                } catch (PDOException $e) {
                    // Log error safely if SQL fails
                    error_log("Upload Cover Error: " . $e->getMessage());
                }
            }
        }
    }
}

// Redirect back to Dashboard
header('Location: ' . BASE_URL . 'Dashboard/index.php');
exit;