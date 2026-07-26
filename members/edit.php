<?php include '../includes/header.php'; ?>

<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Database/db.php';

$id = $_GET['id'] ?? null;
$member = null;

if ($id) {
    // Fetch member details safely using PDO
    $sql = "SELECT * FROM members WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $id]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Redirect if member ID doesn't exist
if (!$member) {
    header("Location: index.php");
    exit;
}

// --- DYNAMIC IMAGE RESOLUTION ---
$imageName = $member['profile_image'] ?? '';
$uploadDir = BASE_PATH . '/Assets/images/';

if (!empty($imageName) && filter_var($imageName, FILTER_VALIDATE_URL)) {
    // Case 1: External HTTP/HTTPS URL
    $profileImgPath = $imageName;
} elseif (!empty($imageName) && file_exists($uploadDir . $imageName)) {
    // Case 2: Local file saved in /Assets/images/
    $profileImgPath = '/Assets/images/' . $imageName;
} else {
    // Case 3: Fallback placeholder
    $profileImgPath = '/Assets/images/user-placeholder.jpg';
}
?>

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
            <h2 class="page-title mb-1">Edit Member</h2>
            <p class="page-subtitle mb-0">Update information for member #<?= htmlspecialchars((string)$member['id']); ?></p>
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

    <!-- Edit Form Section -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card member-card">
                <div class="card-header-custom">
                    <div class="d-flex align-items-center gap-3">
                        <div class="header-icon-box">
                            <i class="fa-solid fa-user-pen"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold">Update Details</h4>
                            <small class="opacity-75">Modify member details and profile image</small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">

                    <form action="member_controller.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?= htmlspecialchars((string)$member['id']); ?>">
                        <input type="hidden" name="existing_profile_image" value="<?= htmlspecialchars($member['profile_image'] ?? ''); ?>">

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
                                    <!-- Option A: Direct Web Image URL -->
                                    <div class="mb-2">
                                        <div class="input-group-custom">
                                            <i class="fa-solid fa-link"></i>
                                            <input type="url" 
                                                   name="image_url" 
                                                   id="image_url_input" 
                                                   class="form-control form-control-custom" 
                                                   placeholder="Paste web image URL (https://...)" 
                                                   value="<?= filter_var($member['profile_image'] ?? '', FILTER_VALIDATE_URL) ? htmlspecialchars($member['profile_image']) : ''; ?>"
                                                   oninput="previewUrl(this.value)">
                                        </div>
                                    </div>

                                    <div class="text-center text-muted my-1" style="font-size: 0.75rem; font-weight: 700;">— OR UPLOAD FILE —</div>

                                    <!-- Option B: Local File Upload -->
                                    <div>
                                        <input type="file" 
                                               name="profile_image" 
                                               id="profile_image_input" 
                                               class="form-control-custom-file" 
                                               accept="image/*" 
                                               onchange="previewFile(this)">
                                        <small class="text-muted d-block mt-1">Uploaded to Assets/images (JPG, PNG, WEBP, GIF)</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Member Name Input -->
                        <div class="mb-4">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <div class="input-group-custom">
                                <i class="fa-regular fa-user"></i>
                                <input type="text" name="name" class="form-control form-control-custom" value="<?= htmlspecialchars($member['name'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <!-- Phone Input -->
                        <div class="mb-4">
                            <label class="form-label">Phone Number</label>
                            <div class="input-group-custom">
                                <i class="fa-solid fa-phone"></i>
                                <input type="text" name="phone" class="form-control form-control-custom" value="<?= htmlspecialchars($member['phone'] ?? ''); ?>">
                            </div>
                        </div>

                        <!-- Email Input -->
                        <div class="mb-4">
                            <label class="form-label">Email Address</label>
                            <div class="input-group-custom">
                                <i class="fa-regular fa-envelope"></i>
                                <input type="email" name="email" class="form-control form-control-custom" value="<?= htmlspecialchars($member['email'] ?? ''); ?>">
                            </div>
                        </div>

                        <hr class="my-4" style="border-color: var(--border-color);">

                        <!-- Action Buttons -->
                        <div class="d-flex align-items-center gap-2 justify-content-end">
                            <a href="index.php" class="btn-action-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn-action-primary">
                                <i class="fa-solid fa-floppy-disk"></i> Update Member
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
    }
}

// Live Image Preview for pasted URL
function previewUrl(url) {
    if (url.trim() !== '') {
        document.getElementById('profile_image_input').value = '';
        document.getElementById('previewImg').src = url;
    }
}
</script>

<?php include '../includes/footer.php'; ?>