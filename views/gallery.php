<?php
require_once __DIR__ . '/../models/ProjectImageModel.php';
require_once __DIR__ . '/../models/Logs.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../helpers/AppManager.php';
require_once __DIR__ . '/../models/BaseModel.php';

// Get project ID from URL
$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;

// Initialize variables
$project = null;
$images = [];
$error = '';

try {
    // Get project details
    $project_logs = new Logs();
    $all_projects = $project_logs->getCompleted() ?: [];
    
    foreach ($all_projects as $proj) {
        if ($proj['id'] == $project_id) {
            $project = $proj;
            break;
        }
    }
    
    if (!$project) {
        $error = "Project not found.";
    } else {
        // Get images for this project
        $imgModel = new ProjectImageModel();
        $all_images = $imgModel->getAll() ?: [];
        
        foreach ($all_images as $img) {
            if ($img['project_id'] == $project_id) {
                $images[] = $img;
            }
        }
        
        if (empty($images)) {
            $error = "No images available for this project.";
        }
    }
} catch (Exception $e) {
    $error = 'Unable to load project data. Please try again later.';
    error_log("Error loading project data: " . $e->getMessage());
}

// If no project found, redirect to main page after 3 seconds
if (!$project) {
    header("Refresh: 3; url=index.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $project ? htmlspecialchars($project['project_name']) . ' - Gallery' : 'Project Not Found'; ?> | Imara Enterprise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #111827;
            color: #e5e7eb;
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
        }

        /* Masonry layout for larger screens */
        @media (min-width: 1024px) {
            .gallery-grid {
                columns: 3;
                column-gap: 16px;
            }

            .gallery-item {
                break-inside: avoid;
                margin-bottom: 16px;
            }
        }

        @media (max-width: 1023px) and (min-width: 640px) {
            .gallery-grid {
                columns: 2;
                column-gap: 16px;
            }

            .gallery-item {
                break-inside: avoid;
                margin-bottom: 16px;
            }
        }

        @media (max-width: 639px) {
            .gallery-grid {
                columns: 1;
            }

            .gallery-item {
                margin-bottom: 16px;
            }
        }

        .gallery-item img {
            width: 100%;
            border-radius: 12px;
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            cursor: pointer;
        }

        .gallery-item:hover img {
            transform: scale(1.05) translateY(-4px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.5);
        }

        /* Smooth animation */
        @keyframes smoothFadeUp {
            0% {
                opacity: 0;
                transform: translateY(20px) scale(0.95);
            }

            50% {
                opacity: 0.5;
                transform: translateY(10px) scale(1.02);
            }

            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .animate-smooth {
            animation: smoothFadeUp 1s ease-in-out forwards;
        }
    </style>
</head>

<body>
    <?php if (!$project): ?>
    <div class="min-h-screen flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-white mb-4">Project Not Found</h1>
            <p class="text-gray-400">Redirecting to main page in 3 seconds...</p>
        </div>
    </div>
    <?php else: ?>
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row items-start md:items-center gap-6 mb-8">
            <a href="index.php"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium border border-gray-700 bg-gray-800 hover:bg-gray-700 text-white h-10 px-4 py-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="w-4 h-4 mr-2">
                    <path d="m12 19-7-7 7-7"></path>
                    <path d="M19 12H5"></path>
                </svg>
                Back to Projects
            </a>

            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-2xl md:text-3xl font-bold text-white"><?php echo htmlspecialchars($project['project_name']); ?></h1>
                    <div
                        class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-purple-500/20 text-purple-300 border-purple-500/30">
                        <?php echo ucfirst(htmlspecialchars($project['project_type'])); ?>
                    </div>
                </div>
                <p class="text-gray-400 mb-4 max-w-3xl">
                    Completed project finished on <?php echo date("F j, Y", strtotime($project['last_updated'])); ?>.
                </p>
                <div class="flex flex-wrap gap-2 mb-4">
                    <div
                        class="inline-flex items-center rounded-full border border-gray-700 px-2.5 py-0.5 text-xs font-semibold text-gray-300">Project
                        Gallery</div>
                    <div
                        class="inline-flex items-center rounded-full border border-gray-700 px-2.5 py-0.5 text-xs font-semibold text-gray-300"><?php echo count($images); ?>
                        Images</div>
                </div>
                <div class="flex gap-3">
                    <button
                        class="inline-flex items-center justify-center gap-2 text-sm font-medium h-9 px-3 rounded-md border border-gray-700 bg-gray-800 hover:bg-gray-700 text-white"
                        onclick="alert('Code repository link coming soon!')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="mr-2">
                            <path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4"></path>
                            <path d="M9 18c-4.51 2-5-2-7-2"></path>
                        </svg>
                        View Code
                    </button>
                    <button
                        class="inline-flex items-center justify-center gap-2 text-sm font-medium h-9 px-3 rounded-md bg-cyan-600 text-white hover:bg-cyan-500"
                        onclick="alert('Live demo coming soon!')">
                        Live Demo
                    </button>
                </div>
            </div>
        </div>

        <!-- Error Message -->
        <?php if ($error): ?>
        <div class="bg-red-900/50 text-red-200 p-4 rounded-lg mb-8 text-center" role="alert">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <!-- Gallery Grid -->
        <?php if (!empty($images)): ?>
        <div class="gallery-grid">
            <?php foreach ($images as $index => $image): ?>
            <div class="gallery-item animate-smooth" style="animation-delay: <?php echo $index * 0.1; ?>s">
                <img src="../uploads/projects/<?php echo htmlspecialchars($image['file_path']); ?>"
                    alt="<?php echo htmlspecialchars($project['project_name'] . ' - Image ' . ($index + 1)); ?>"
                    loading="lazy">
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</body>

</html>
