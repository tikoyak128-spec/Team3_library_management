<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Database/db.php';

// --- HELPER FUNCTION TO GET FULL BASE URL ---
function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    
    // Get the script's root folder path relative to the domain root
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    // Move up one level if inside a subdirectory (e.g. /members/)
    $projectDir = preg_replace('#/[^/]+$#', '', $scriptDir);

    return rtrim($protocol . $host . $projectDir, '/');
}

// --- PROCESS FORM SUBMISSION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create' || $action === 'update') {
        $id    = $_POST['id'] ?? null;
        $name  = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        
        // Default fallback to existing image URL if updating, or empty string
        $profile_image = $_POST['existing_profile_image'] ?? '';

        // -------------------------------------------------------------
        // METHOD 1: DIRECT URL INPUT (Priority 1)
        // -------------------------------------------------------------
        if (!empty($_POST['image_url']) && filter_var($_POST['image_url'], FILTER_VALIDATE_URL)) {
            $profile_image = trim($_POST['image_url']);
        } 
        // -------------------------------------------------------------
        // METHOD 2: LOCAL FILE UPLOAD (Priority 2)
        // -------------------------------------------------------------
        elseif (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            
            $targetDir = BASE_PATH . '/Assets/images/';

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $fileTmpPath   = $_FILES['profile_image']['tmp_name'];
            $fileName      = $_FILES['profile_image']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (in_array($fileExtension, $allowedExtensions)) {
                $newFileName    = time() . '_' . uniqid() . '.' . $fileExtension;
                $targetFilePath = $targetDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                    // Cleanup old local file if updating
                    if ($action === 'update' && !empty($_POST['existing_profile_image'])) {
                        $oldFilename = basename($_POST['existing_profile_image']);
                        $oldFilePath = $targetDir . $oldFilename;
                        if (file_exists($oldFilePath) && !is_dir($oldFilePath)) {
                            @unlink($oldFilePath);
                        }
                    }

                    // Save full accessible URL in database
                    $profile_image = getBaseUrl() . '/Assets/images/' . $newFileName;
                }
            }
        }

        // --- EXECUTE DATABASE QUERY ---
        if ($action === 'create') {
            $sql = "INSERT INTO members (name, phone, email, profile_image) VALUES (:name, :phone, :email, :profile_image)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':name'          => $name,
                ':phone'         => $phone,
                ':email'         => $email,
                ':profile_image' => $profile_image
            ]);

            header("Location: index.php?status=success");
            exit;

        } elseif ($action === 'update' && $id) {
            $sql = "UPDATE members SET name = :name, phone = :phone, email = :email, profile_image = :profile_image WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':name'          => $name,
                ':phone'         => $phone,
                ':email'         => $email,
                ':profile_image' => $profile_image,
                ':id'            => $id
            ]);

            header("Location: index.php?status=updated");
            exit;
        }
    }
}

// Default fallback preview image for UI
$profileImgPath = '/Assets/images/user-placeholder.jpg';
?>

<?php include '../includes/header.php'; ?>

<!-- Google Fonts & FontAwesome -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    :root {
        --primary: #6f42c1;
        --primary-hover: #5b2ef0;
        --bg-surface: #f8fafc;
        --card-bg: #ffffff;
        --text-dark: #0f172a;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
        --radius-lg: 16px;
        --radius-md: 12px;
        --radius-sm: 8px;
    }

    body {
        background-color: var(--bg-surface);
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: var(--text-dark);
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--text-dark);
    }

    .page-subtitle {
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .member-card {
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        overflow: hidden;
        background: var(--card-bg);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04);
    }

    .card-header-custom {
        background: var(--primary);
        color: white;
        padding: 1.75rem 2rem;
    }

    .header-icon-box {
        width: 48px;
        height: 48px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(4px);
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #ffffff;
    }

    .avatar-preview-wrapper {
        display: flex;
        align-items: flex-start;
        gap: 1.25rem;
        padding: 1.25rem;
        border: 1px dashed var(--border-color);
        border-radius: var(--radius-md);
        background: #f8fafc;
    }

    .avatar-preview-img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--primary);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        flex-shrink: 0;
    }

    .form-label {
        font-weight: 700;
        font-size: 0.85rem;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
    }

    .input-group-custom {
        position: relative;
    }

    .input-group-custom i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 0.95rem;
        z-index: 10;
    }

    .form-control-custom {
        height: 48px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        padding-left: 2.75rem;
        padding-right: 1rem;
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--text-dark);
        background-color: #ffffff;
        transition: all 0.2s ease;
    }

    .form-control-custom-file {
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
        width: 100%;
        background-color: #ffffff;
    }

    .form-control-custom:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(111, 66, 193, 0.12);
        outline: none;
    }

    .btn-action-primary {
        background: var(--primary);
        color: #ffffff !important;
        border: none;
        border-radius: var(--radius-md);
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 12px rgba(111, 66, 193, 0.25);
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .btn-action-primary:hover {
        background: var(--primary-hover);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(111, 66, 193, 0.35);
    }

    .btn-action-secondary {
        background: #ffffff;
        color: var(--text-dark) !important;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .btn-action-secondary:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }
</style>

<div class="container-fluid py-4 px-4">

    <!-- Top Navigation Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="page-title mb-1">Add New Member</h2>
            <p class="page-subtitle mb-0">Fill in the details below to register a new member</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="../index.php" class="btn-action-secondary">
               <i class="fa-solid fa-house"></i> Dashboard
            </a>
            <a href="index.php" class="btn-action-secondary">
               <i class="fa-solid fa-arrow-left"></i> Members List
            </a>
        </div>
    </div>

    <!-- Create Form Section -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card member-card">
                <div class="card-header-custom">
                    <div class="d-flex align-items-center gap-3">
                        <div class="header-icon-box">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold">Member Details</h4>
                            <small class="opacity-75">Enter information and select a profile photo</small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">

                    <!-- Form posts to itself -->
                    <form action="" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="create">

                        <!-- Profile Photo Section -->
                        <div class="mb-4">
                            <label class="form-label">Profile Image</label>
                            <div class="avatar-preview-wrapper">
                                <img id="previewImg" 
                                     src="<?= htmlspecialchars($profileImgPath); ?>" 
                                     alt="Preview" 
                                     class="avatar-preview-img"
                                     onerror="this.onerror=null; this.src='/Assets/images/user-placeholder.jpg';">
                                
                                <div class="flex-grow-1 w-100">
                                    <!-- Option 1: Direct Web Image URL -->
                                    <div class="mb-2">
                                        <div class="input-group-custom">
                                            <i class="fa-solid fa-link"></i>
                                            <input type="url" 
                                                   name="image_url" 
                                                   id="image_url_input" 
                                                   class="form-control form-control-custom" 
                                                   placeholder="Paste web image URL (https://...)" 
                                                   oninput="previewUrl(this.value)">
                                        </div>
                                    </div>

                                    <div class="text-center text-muted my-1" style="font-size: 0.75rem; font-weight: 700;">— OR UPLOAD FILE —</div>

                                    <!-- Option 2: Local File Upload -->
                                    <div>
                                        <input type="file" 
                                               name="profile_image" 
                                               id="profile_image_input" 
                                               class="form-control-custom-file" 
                                               accept="image/*" 
                                               onchange="previewFile(this)">
                                        <small class="text-muted d-block mt-1">Uploaded to Assets/images (JPG, PNG, WEBP)</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Member Name Input -->
                        <div class="mb-4">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <div class="input-group-custom">
                                <i class="fa-regular fa-user"></i>
                                <input type="text" name="name" class="form-control form-control-custom" placeholder="Enter full name" required>
                            </div>
                        </div>

                        <!-- Phone Input -->
                        <div class="mb-4">
                            <label class="form-label">Phone Number</label>
                            <div class="input-group-custom">
                                <i class="fa-solid fa-phone"></i>
                                <input type="text" name="phone" class="form-control form-control-custom" placeholder="Enter phone number">
                            </div>
                        </div>

                        <!-- Email Input -->
                        <div class="mb-4">
                            <label class="form-label">Email Address</label>
                            <div class="input-group-custom">
                                <i class="fa-regular fa-envelope"></i>
                                <input type="email" name="email" class="form-control form-control-custom" placeholder="Enter email address">
                            </div>
                        </div>

                        <hr class="my-4" style="border-color: var(--border-color);">

                        <!-- Action Buttons -->
                        <div class="d-flex align-items-center gap-2 justify-content-end">
                            <a href="index.php" class="btn-action-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn-action-primary">
                                <i class="fa-solid fa-user-plus"></i> Save
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
const defaultPlaceholder = '/Assets/images/user-placeholder.jpg';

// Live Image Preview for uploaded local file
function previewFile(input) {
    const file = input.files[0];
    if (file) {
        document.getElementById('image_url_input').value = '';
        
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
        }
        reader.readAsDataURL(file);
    } else if (!document.getElementById('image_url_input').value.trim()) {
        document.getElementById('previewImg').src = defaultPlaceholder;
    }
}

// Live Image Preview for pasted URL
function previewUrl(url) {
    if (url.trim() !== '') {
        document.getElementById('profile_image_input').value = '';
        document.getElementById('previewImg').src = url;
    } else if (!document.getElementById('profile_image_input').files.length) {
        document.getElementById('previewImg').src = defaultPlaceholder;
    }
}
</script>

<?php include '../includes/footer.php'; ?>