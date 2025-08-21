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
        'finished'    => ['COMPLETED', 'bg-success'],
        'in_progress' => ['IN PROGRESS', 'bg-primary'],
        'idle'        => ['IDLE', 'bg-muted'],
        'cancelled'   => ['CANCELLED', 'bg-destructive'],
    ];
    return $map[$status] ?? ['UNKNOWN', 'bg-muted'];
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
$desc          = $project['description'] ?? '';
$due_date      = safe_date($project['due_date'] ?? null, 'Y-m-d');
$last_updated  = safe_date($project['last_updated'] ?? null, 'F j, Y H:i');
$assigned_name = $assigned['full_name'] ?? 'N/A';

$uploadBaseRel = '../uploads/projects/';
function build_img_src(string $base, string $file): string
{
    $file = ltrim($file, '/\\');
    return $base . $file;
}
?>
<!DOCTYPE html>
<html lang="en">

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Imara - Insight</title>
    <meta name="description" content="<?= h(mb_strimwidth($desc, 0, 150, '…')) ?>">
    <meta property="og:title" content="<?= h($project_name) ?>">
    <meta property="og:description" content="<?= h(mb_strimwidth($desc, 0, 150, '…')) ?>">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            primary: '#121212',
                            secondary: '#1e1e1e',
                            tertiary: '#252525',
                            accent: '#7c4dff',
                            'accent-secondary': '#6c40d9',
                            'accent-tertiary': '#5a34b3',
                            border: '#333333',
                        },
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.5s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                    },
                },
            },
        }
    </script>
    <link rel="icon" type="image/x-icon" href="<?= asset('assets/img/favicon/favicon.png') ?>" />
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <style type="text/css">
        .glass-card {
            background: rgba(30, 30, 30, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .text-gradient {
            background: linear-gradient(90deg, #7c4dff, #40c4ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .screenshot-card:hover img {
            transform: scale(1.05);
        }
        
        /* Custom scrollbar for dark theme */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #121212;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #252525;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #7c4dff;
        }
    </style>
</head>

<body class="bg-dark-primary text-gray-200 min-h-screen font-sans">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Header Card -->
            <div class="glass-card rounded-2xl overflow-hidden mb-8 shadow-xl" data-aos="flip-up" data-aos-easing="ease-out-cubic">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-900/10 to-blue-900/10 opacity-50"></div>
                <div class="relative z-10 p-6 md:p-8">
                    <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
                        <h1 class="text-3xl md:text-4xl font-bold text-gradient"><?= h($project_name) ?></h1>
                        <span class="px-4 py-2 rounded-full text-xs font-semibold uppercase tracking-wider <?= h($status_class) ?>"><?= h($status_text) ?></span>
                    </div>

                    <?php if ($desc): ?>
                        <p class="text-gray-400 text-lg mb-8 max-w-3xl"><?= h($desc) ?></p>
                    <?php endif; ?>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="bg-dark-tertiary rounded-xl p-5 border border-dark-border flex items-center transition-all duration-300 hover:border-purple-500 hover:shadow-lg" data-aos="zoom-in" data-aos-delay="100">
                            <div class="bg-purple-500/15 w-12 h-12 rounded-lg flex items-center justify-center mr-4 text-purple-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-400 text-sm">Assigned to</p>
                                <p class="font-semibold"><?= h($assigned_name) ?></p>
                            </div>
                        </div>

                        <div class="bg-dark-tertiary rounded-xl p-5 border border-dark-border flex items-center transition-all duration-300 hover:border-blue-500 hover:shadow-lg" data-aos="zoom-in" data-aos-delay="200">
                            <div class="bg-blue-500/15 w-12 h-12 rounded-lg flex items-center justify-center mr-4 text-blue-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-400 text-sm">Due Date</p>
                                <p class="font-semibold"><?= h($due_date) ?></p>
                            </div>
                        </div>

                        <div class="bg-dark-tertiary rounded-xl p-5 border border-dark-border flex items-center transition-all duration-300 hover:border-green-500 hover:shadow-lg" data-aos="zoom-in" data-aos-delay="300">
                            <div class="bg-green-500/15 w-12 h-12 rounded-lg flex items-center justify-center mr-4 text-green-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-400 text-sm">Last Updated</p>
                                <p class="font-semibold"><?= h($last_updated) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Screenshots -->
            <section class="mb-12">
                <div class="flex flex-wrap justify-between items-center gap-4 mb-8">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold">Project Screenshots</h2>
                        <p class="text-gray-400 mt-1">Visual preview of the project's key features and interfaces</p>
                    </div>
                    <?php if (!empty($images)): ?>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-dark-accent-secondary"><?= count($images) ?> Image<?= count($images) > 1 ? 's' : '' ?></span>
                    <?php endif; ?>
                </div>

                <?php if (!empty($images)): ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($images as $img): ?>
                            <?php
                            $file     = (string)($img['file_path'] ?? '');
                            $imgSrc   = $file !== '' ? build_img_src($uploadBaseRel, $file) : '';
                            $title    = $img['title'] ?? 'Screenshot';
                            $imgDesc  = $img['description'] ?? '';
                            ?>
                            <div class="bg-dark-secondary rounded-xl overflow-hidden border border-dark-border transition-all duration-300 hover:border-purple-500 hover:shadow-xl" data-aos="flip-up" data-aos-delay="<?= $i * 100 ?>">
                                <div class="h-48 overflow-hidden bg-dark-tertiary">
                                    <?php if ($imgSrc): ?>
                                        <img src="<?= h($imgSrc) ?>" alt="<?= h($title) ?>" class="w-full h-full object-cover transition-transform duration-500" loading="lazy" referrerpolicy="no-referrer" />
                                    <?php else: ?>
                                        <div class="h-full w-full flex items-center justify-center text-gray-500">
                                            No image
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="p-5">
                                    <h3 class="font-semibold text-lg mb-2"><?= h($title) ?></h3>
                                    <?php if (!empty($imgDesc)): ?>
                                        <p class="text-gray-400"><?= h($imgDesc) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-dark-secondary border border-dark-border rounded-xl p-8 text-center">
                        <p class="text-gray-400">No screenshots available for this project.</p>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>
    <!-- AOS JS -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            easing: 'ease-in-out',
            once: true, // run animation only once
        });
    </script>
</body>

</html>