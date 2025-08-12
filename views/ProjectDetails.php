<?php
require_once('./layouts/header.php');
include BASE_PATH . '/models/Logs.php';
include BASE_PATH . '/models/Users.php';
include BASE_PATH . '/models/ProjectImageModel.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: project_logs.php');
    exit();
}

$project_id = $_GET['id'];
$project_logs = new Logs();
$project_details = $project_logs->getById($project_id);

if (!$project_details) {
    header('Location: project_logs.php');
    exit();
}

$user_details = new User();
$assigned_user = $user_details->getById($project_details['user_id']);

// Get project images
$project_imageModel = new ProjectImageModel();
$project_images = $project_imageModel->getImagebyProjectId($project_id);
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Project Details</h5>
                    <a href="../views/admin/Logs.php" class="btn btn-sm btn-outline-secondary">
                        <i class="bx bx-arrow-back"></i> Back to Projects
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Project Name</label>
                                <p><?= htmlspecialchars($project_details['project_name'] ?? 'N/A') ?></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Assigned To</label>
                                <p><?= htmlspecialchars($assigned_user['full_name'] ?? 'N/A') ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <p>
                                    <?php if ($project_details['status'] == 'finished'): ?>
                                        <span class="badge bg-success">Finished</span>
                                    <?php elseif ($project_details['status'] == 'in_progress'): ?>
                                        <span class="badge bg-primary">In Progress</span>
                                    <?php elseif ($project_details['status'] == 'idle'): ?>
                                        <span class="badge bg-dark">Idle</span>
                                    <?php elseif ($project_details['status'] == 'cancelled'): ?>
                                        <span class="badge bg-danger">Cancelled</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Last Updated</label>
                                <p><?= date('F j, Y H:i', strtotime($project_details['last_updated'])) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Screenshots Section -->
                    <div class="mb-4">
                        <label class="form-label fw-bold mb-4">Project Screenshots</label>
                        <?php if (!empty($project_images)): ?>
                            <div class="row g-3" id="imageGallery">
                                <?php foreach ($project_images as $index => $image): ?>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="card h-100">
                                            <img 
                                                src="../services/uploads/projects/<?= htmlspecialchars($image['file_path']) ?>" 
                                                class="card-img-top cursor-pointer gallery-image"
                                                alt="Screenshot <?= $index + 1 ?>"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#imageModal"
                                                data-image="<?= htmlspecialchars($image['file_path']) ?>"
                                                data-title="Screenshot <?= $index + 1 ?>">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">No screenshots available for this project.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalImageTitle">Screenshot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImageView" src="" class="img-fluid" style="max-height: 70vh;">
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-outline-secondary" id="prevImage">
                    <i class="bx bx-chevron-left"></i> Previous
                </button>
                <div>
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                        <i class="bx bx-x"></i> Close
                    </button>
                    <a id="downloadImage" href="#" class="btn btn-primary" download>
                        <i class="bx bx-download"></i> Download
                    </a>
                </div>
                <button type="button" class="btn btn-outline-secondary" id="nextImage">
                    Next <i class="bx bx-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const galleryImages = document.querySelectorAll('.gallery-image');
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    const modalTitle = document.getElementById('modalImageTitle');
    const modalImg = document.getElementById('modalImageView');
    const downloadLink = document.getElementById('downloadImage');
    const prevBtn = document.getElementById('prevImage');
    const nextBtn = document.getElementById('nextImage');
    
    let currentIndex = 0;
    let images = [];
    
    // Initialize gallery
    if (galleryImages.length > 0) {
        images = Array.from(galleryImages).map(img => ({
            src: '../services/uploads/projects/' + img.getAttribute('data-image'),
            title: img.getAttribute('data-title')
        }));
        
        // Set up click handlers for each image
        galleryImages.forEach((img, index) => {
            img.addEventListener('click', () => {
                currentIndex = index;
                updateModal();
            });
        });
        
        // Navigation handlers
        prevBtn.addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            updateModal();
        });
        
        nextBtn.addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % images.length;
            updateModal();
        });
        
        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (!modal._isShown) return;
            
            if (e.key === 'ArrowLeft') {
                currentIndex = (currentIndex - 1 + images.length) % images.length;
                updateModal();
            } else if (e.key === 'ArrowRight') {
                currentIndex = (currentIndex + 1) % images.length;
                updateModal();
            }
        });
    }
    
    function updateModal() {
        const currentImage = images[currentIndex];
        modalImg.src = currentImage.src;
        modalTitle.textContent = currentImage.title;
        downloadLink.href = currentImage.src;
        downloadLink.download = currentImage.src.split('/').pop();
        
        // Update button states
        prevBtn.disabled = images.length <= 1;
        nextBtn.disabled = images.length <= 1;
    }
});
</script>

<?php
require_once('./layouts/footer.php');
?>