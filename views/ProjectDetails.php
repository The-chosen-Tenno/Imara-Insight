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

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- <title><?= h($project_name) ?> — Project Details</title> -->
    <title>Imara - Insight</title>
    <meta name="description" content="<?= h(mb_strimwidth($desc, 0, 150, '…')) ?>">
    <meta property="og:title" content="<?= h($project_name) ?>">
    <meta property="og:description" content="<?= h(mb_strimwidth($desc, 0, 150, '…')) ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="<?= asset('assets/img/favicon/favicon.png') ?>" />
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <!-- AOS CSS -->
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">


    <style>
        :root {
            --primary: #5A5BFF;
            --primary-foreground: #FFFFFF;
            --primary-glow: #8E8EFF;
            --secondary: #A855F7;
            --accent: #C084FC;
            --success: #4ADE80;
            --warning: #FBBF24;
            --destructive: #EF4444;
            --muted: #6B7280;
            --foreground: #111827;
            --card-bg: #FFFFFF;
            --background: #F9FAFB;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            background: var(--background);
            color: var(--foreground);
            padding: 2rem 1rem;
        }

        .container-narrow {
            max-width: 1100px;
            margin: 0 auto;
        }

        /* Glass Card */
        .glass-card {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            border: 1px solid hsl(240 20% 90% / 0.3);
            box-shadow: 0 4px 16px hsl(240 25% 25% / 0.08);
            padding: 32px;
            margin-bottom: 2rem;
        }

        .header-bg {
            position: absolute;
            inset: 0;
            opacity: 0.06;
        }

        .header-bg .overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, hsl(240 100% 95%), hsl(260 100% 96%));
        }

        .header-content {
            position: relative;
            z-index: 1;
        }

        .title-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .title {
            font-size: clamp(1.6rem, 3.5vw, 2.5rem);
            font-weight: 800;
            margin: 0;
        }

        .text-gradient {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Badges */
        .badge {
            padding: 6px 12px;
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 600;
            letter-spacing: .02em;
        }

        .bg-primary {
            background-color: var(--primary) !important;
            color: var(--primary-foreground) !important;
        }

        .bg-secondary {
            background-color: var(--secondary) !important;
            color: #fff !important;
        }

        .bg-success {
            background-color: var(--success) !important;
            color: #0b2 !important;
            color: #fff !important;
        }

        .bg-warning {
            background-color: var(--warning) !important;
            color: #111 !important;
        }

        .bg-destructive {
            background-color: var(--destructive) !important;
            color: #fff !important;
        }

        .bg-muted {
            background-color: var(--muted) !important;
            color: #fff !important;
        }

        .description {
            font-size: 1rem;
            color: var(--muted);
            max-width: 65ch;
            margin: 6px 0 24px;
        }

        /* Metadata */
        .metadata-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        .metadata-card {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255, 255, 255, .6);
            border-radius: 16px;
            padding: 16px;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(17, 24, 39, 0.05);
        }

        .icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-primary-light {
            background-color: hsla(240, 100%, 65%, 0.12);
        }

        .bg-secondary-light {
            background-color: hsla(260, 80%, 70%, 0.12);
        }

        .bg-accent-light {
            background-color: hsla(280, 60%, 65%, 0.12);
        }

        .meta-label {
            font-size: .8rem;
            color: var(--muted);
            margin: 0;
        }

        .meta-value {
            font-weight: 600;
            margin: 0;
        }

        /* Section */
        .section-header {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
        }

        .section-title {
            font-size: clamp(1.25rem, 2.6vw, 1.75rem);
            font-weight: 800;
            margin: 0;
        }

        .section-subtitle {
            color: var(--muted);
            margin: 4px 0 0;
        }

        /* Gallery */
        .screenshots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.25rem;
        }

        .screenshot-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .08);
            transition: transform .25s ease, box-shadow .25s ease;
            border: 1px solid rgba(17, 24, 39, 0.05);
        }

        .screenshot-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .10);
        }

        .image-container {
            position: relative;
            aspect-ratio: 4 / 3;
            overflow: hidden;
        }

        .image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .5s ease;
        }

        .screenshot-card:hover img {
            transform: scale(1.05);
        }

        .card-content {
            padding: 1rem;
        }

        .card-title {
            font-weight: 700;
            margin-bottom: .35rem;
        }

        .card-desc {
            font-size: .92rem;
            color: var(--muted);
            margin: 0;
        }

        .alert-empty {
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            background: var(--card-bg);
            border: 1px dashed rgba(17, 24, 39, 0.15);
        }

        /* Breadcrumb (optional) */
        .breadcrumb {
            --bs-breadcrumb-divider: '›';
        }
    </style>
</head>

<body>
    <div class="container-narrow">


        <!-- Header Card -->
        <div class="project-header glass-card" data-aos="fade-up">
            <div class="header-bg">
                <div class="overlay"></div>
            </div>

            <div class="header-content">
                <div class="title-row">
                    <h1 class="title text-gradient"><?= h($project_name) ?></h1>
                    <span class="badge <?= h($status_class) ?>"><?= h($status_text) ?></span>
                </div>

                <?php if ($desc): ?>
                    <p class="description"><?= h($desc) ?></p>
                <?php endif; ?>

                <div class="metadata-grid">
                    <div class="metadata-card"  data-aos="zoom-in" data-aos-delay="100">
                        <div class="icon bg-primary-light">
                            <!-- user icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <div>
                            <p class="meta-label">Assigned to</p>
                            <p class="meta-value"><?= h($assigned_name) ?></p>
                        </div>
                    </div>

                    <div class="metadata-card" data-aos="zoom-in" data-aos-delay="200">
                        <div class="icon bg-secondary-light">
                            <!-- calendar icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                        <div>
                            <p class="meta-label">Due Date</p>
                            <p class="meta-value"><?= h($due_date) ?></p>
                        </div>
                    </div>

                    <div class="metadata-card" data-aos="zoom-in" data-aos-delay="300">
                        <div class="icon bg-accent-light">
                            <!-- clock icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div>
                            <p class="meta-label">Last Updated</p>
                            <p class="meta-value"><?= h($last_updated) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Screenshots -->
        <section class="screenshots-section">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Project Screenshots</h2>
                    <p class="section-subtitle">Visual preview of the project's key features and interfaces</p>
                </div>
                <?php if (!empty($images)): ?>
                    <span class="badge bg-secondary"><?= count($images) ?> Image<?= count($images) > 1 ? 's' : '' ?></span>
                <?php endif; ?>
            </div>

            <?php if (!empty($images)): ?>
                <div class="screenshots-grid">
                    <?php foreach ($images as $img): ?>
                        <?php
                        $file     = (string)($img['file_path'] ?? '');
                        $imgSrc   = $file !== '' ? build_img_src($uploadBaseRel, $file) : '';
                        $title    = $img['title'] ?? 'Screenshot';
                        $imgDesc  = $img['description'] ?? '';
                        ?>
                        <div class="screenshot-card" data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>">
                            <div class="image-container">
                                <?php if ($imgSrc): ?>
                                    <img src="<?= h($imgSrc) ?>" alt="<?= h($title) ?>" loading="lazy" referrerpolicy="no-referrer" />
                                <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-center h-100 w-100 text-muted">No image</div>
                                <?php endif; ?>
                            </div>
                            <div class="card-content">
                                <h3 class="card-title"><?= h($title) ?></h3>
                                <?php if (!empty($imgDesc)): ?>
                                    <p class="card-desc"><?= h($imgDesc) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning alert-empty">
                    No screenshots available for this project.
                </div>
            <?php endif; ?>
        </section>
    </div>
    <!-- AOS JS -->
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 800,   // animation speed (ms)
    easing: 'ease-in-out',
    once: true,      // run animation only once
  });
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>