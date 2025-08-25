<?php
require_once __DIR__ . '/../models/ProjectImageModel.php';
require_once __DIR__ . '/../models/Logs.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../helpers/AppManager.php';
require_once __DIR__ . '/../models/BaseModel.php';

$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;

$project = null;
$images = [];
$error = '';

try {
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

            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-2xl md:text-3xl font-bold text-white"><?php echo htmlspecialchars($project['project_name']); ?></h1>
                    <!-- <div
                        class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-purple-500/20 text-purple-300 border-purple-500/30">
                        <?php echo ucfirst(htmlspecialchars($project['project_type'])); ?>
                    </div> -->
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
            </div>
        </div>

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
