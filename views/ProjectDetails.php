<?php
require_once __DIR__ . '/../helpers/AppManager.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/Logs.php';
require_once __DIR__ . '/../models/Users.php';
require_once __DIR__ . '/../models/ProjectImageModel.php';

function h(?string $v): string
{
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
function safe_date(?string $v, string $format = 'Y-m-d'): string
{
    if (!$v) return 'N/A';
    $t = strtotime($v);
    return $t ? date($format, $t) : 'N/A';
}
function status_badge(string $status): array
{
    $map = [
        'finished'    => ['COMPLETED', 'bg-green-100 text-green-800'],
        'in_progress' => ['IN PROGRESS', 'bg-blue-100 text-blue-800'],
        'idle'        => ['IDLE', 'bg-gray-100 text-gray-800'],
        'cancelled'   => ['CANCELLED', 'bg-red-100 text-red-800'],
    ];
    return $map[$status] ?? ['UNKNOWN', 'bg-gray-100 text-gray-800'];
}

// === Input Validation ===
$project_id = isset($_GET['id']) ? trim($_GET['id']) : '';
if ($project_id === '' || !ctype_digit($project_id)) {
    http_response_code(400);
    echo '<!doctype html><html><head><meta charset="utf-8"><title>Invalid project</title></head><body><h3>Invalid project ID.</h3></body></html>';
    exit;
}

try {
    $logsModel  = new Logs();
    $usersModel = new User();
    $imgModel   = new ProjectImageModel();

    $project    = $logsModel->getById((int)$project_id);

    if (!$project) {
        http_response_code(404);
        echo '<!doctype html><html><head><meta charset="utf-8"><title>Project not found</title></head><body><h3>Project not found.</h3></body></html>';
        exit;
    }

    $assigned   = isset($project['user_id']) ? $usersModel->getById((int)$project['user_id']) : null;
    $images     = $imgModel->getImagebyProjectId((int)$project_id) ?? [];
} catch (Throwable $e) {
    http_response_code(500);
    echo '<!doctype html><html><head><meta charset="utf-8"><title>Error</title></head><body><h3>Something went wrong loading this project.</h3></body></html>';
    exit;
}

[$status_text, $status_class] = status_badge((string)($project['status'] ?? ''));
$project_name  = $project['project_name'] ?? 'Project';
$desc          = $project['project_description'] ?? '';
$due_date      = safe_date($project['due_date'] ?? null, 'Y-m-d');
$last_updated  = safe_date($project['last_updated'] ?? null, 'F j, Y H:i');
$assigned_name = $assigned['full_name'] ?? 'N/A';
$project_type  = $project['project_type'] ?? '';

$uploadBaseRel = '../uploads/projects/';
function build_img_src(string $base, string $file): string
{
    $file = ltrim($file, '/\\');
    return $base . $file;
}

// Determine tag class based on project type
$tag_class = '';
if (stripos($project_type, 'automation') !== false) {
    $tag_class = 'tag-automation';
} elseif (stripos($project_type, 'coding') !== false || stripos($project_type, 'development') !== false) {
    $tag_class = 'tag-coding';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ImaraSoft - <?= h($project_name) ?></title>
    <meta name="description" content="<?= h(mb_strimwidth($desc, 0, 150, '…')) ?>">
    <meta property="og:title" content="<?= h($project_name) ?>">
    <meta property="og:description" content="<?= h(mb_strimwidth($desc, 0, 150, '…')) ?>">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #6366f1;
            --accent-color: #10b981;
            --dark-color: #1f2937;
            --light-color: #ffffff; /* Changed to white */
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #374151;
            line-height: 1.6;
            background-color: #ffffff; /* Full white background */
        }

        .gradient-text {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .project-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            background-color: #ffffff; /* White background for cards */
        }

        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px rgba(0, 0, 0, 0.1);
        }

        .tag {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .tag-automation {
            background-color: rgba(16, 185, 129, 0.1);
            color: #047857;
        }

        .tag-coding {
            background-color: rgba(79, 70, 229, 0.1);
            color: var(--primary-color);
        }

        .section-title {
            position: relative;
            display: inline-block;
            margin-bottom: 2rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 3px;
        }

        .info-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            background-color: #ffffff; /* White background */
        }

        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .screenshot-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            background-color: #ffffff; /* White background */
        }

        .screenshot-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .screenshot-card img {
            transition: transform 0.5s ease;
        }

        .screenshot-card:hover img {
            transform: scale(1.05);
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

        /* Navigation styles */
        .navbar {
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="bg-white min-h-screen"> <!-- Changed to bg-white -->
    <div class="pt-5 pb-5 px-4 md:px-8">
        <div class="container mx-auto max-w-6xl">
            <!-- Project Header -->
            <div class="p-6 md:p-8 mb-8 bg-white rounded-xl shadow-sm" data-aos="fade-up">
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <h2 class="text-3xl font-bold text-gray-800 section-title">
                        <?= h($project_name) ?>
                    </h2>
                    <span class=" inline-block px-3 py-1 mb-5 text-sm font-medium rounded-full <?= $status_class ?>">
                        <?= h($status_text) ?>
                    </span>
                    <?php if ($tag_class): ?>
                        <span class="tag <?= $tag_class ?> mb-5"><?= h($project_type) ?></span>
                    <?php endif; ?>
                </div>

                <p class="text-gray-600 mt-4 max-w-2xl" data-aos="fade-up">
                    <?= h($desc) ?>
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                    <div class="info-card bg-white p-5 border border-gray-100" data-aos="fade-up" data-aos-delay="100">
                        <div class="flex items-center">
                            <div class="bg-indigo-100 p-3 rounded-full mr-4">
                                <i class="fas fa-user text-indigo-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Assigned To</p>
                                <p class="font-medium"><?= h($assigned_name) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="info-card bg-white p-5 border border-gray-100" data-aos="fade-up" data-aos-delay="200">
                        <div class="flex items-center">
                            <div class="bg-green-100 p-3 rounded-full mr-4">
                                <i class="fas fa-calendar text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Due Date</p>
                                <p class="font-medium"><?= h($due_date) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="info-card bg-white p-5 border border-gray-100" data-aos="fade-up" data-aos-delay="300">
                        <div class="flex items-center">
                            <div class="bg-purple-100 p-3 rounded-full mr-4">
                                <i class="fas fa-sync-alt text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Last Updated</p>
                                <p class="font-medium"><?= h($last_updated) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Screenshots -->
            <section class="p-6 md:p-8 bg-white rounded-xl shadow-sm" data-aos="fade-up">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800 section-title mb-6">
                    Project Screenshots
                </h2>

                <?php if (!empty($images)): ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        <?php foreach ($images as $i => $img): ?>
                            <?php
                            $file     = (string)($img['file_path'] ?? '');
                            $imgSrc   = $file !== '' ? build_img_src($uploadBaseRel, $file) : '';
                            $title    = $img['title'] ?? 'Screenshot';
                            $imgDesc  = $img['description'] ?? '';
                            ?>
                            <!-- Screenshot Card -->
                            <div class="rounded-xl overflow-hidden group border border-gray-200 shadow-sm screenshot-card"
                                data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>">
                                <div class="relative overflow-hidden cursor-pointer">
                                    <?php if ($imgSrc): ?>
                                        <img src="<?= h($imgSrc) ?>" alt="<?= h($title) ?>"
                                            class="w-full h-48 object-cover transform transition-transform duration-500 group-hover:scale-110"
                                            loading="lazy" referrerpolicy="no-referrer" />
                                    <?php else: ?>
                                        <div class="h-48 w-full flex items-center justify-center bg-gray-100 text-gray-400">
                                            <i class="fas fa-image text-4xl"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="p-5">
                                    <h3 class="font-semibold text-lg mb-2 text-gray-800"><?= h($title) ?></h3>
                                    <?php if (!empty($imgDesc)): ?>
                                        <p class="text-gray-600 text-sm"><?= h($imgDesc) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-8 text-center">
                        <i class="fas fa-image text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500">No screenshots available for this project.</p>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>


    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize AOS
            AOS.init({
                duration: 1000,
                easing: 'ease-out-cubic',
                once: true,
                mirror: false
            });
        });
    </script>
</body>

</html>