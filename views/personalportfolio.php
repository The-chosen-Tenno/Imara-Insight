<?php
require_once __DIR__ . '/../helpers/AppManager.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/Logs.php';
require_once __DIR__ . '/../models/Users.php';
require_once __DIR__ . '/../models/ProjectImageModel.php';

$usersModel = new User();
$userData = $usersModel->getUserById($_GET['id'] ?? null);
$logsModel = new Logs();
$logs_data = $logsModel->getByUserId($_GET['id'] ?? null);
$projectImageModel = new ProjectImageModel();
$projectImages = [];
if (!empty($logs_data)) {
    foreach ($logs_data as $project) {
        $projectImages[$project['id']] = $projectImageModel->getImagebyProjectId($project['id']);
    }
}

function h(?string $v): string
{
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
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

function get_tag_class(?string $project_type): string
{
    if (stripos($project_type ?? '', 'automation') !== false) {
        return 'tag-automation';
    } elseif (stripos($project_type ?? '', 'coding') !== false || stripos($project_type ?? '', 'development') !== false) {
        return 'tag-coding';
    }
    return '';
}

$full_name = $userData['full_name'];
$user_name = $userData['user_name'];
$email = $userData['email'];
$photo = $userData['photo'];
$created_at = $userData['created_at'];

$start_date = new DateTime($created_at);
$current_date = new DateTime();
$experience = $start_date->diff($current_date)->y;

$project_count = count($logs_data ?? []);
$completed_projects = array_filter($logs_data ?? [], function ($project) {
    return ($project['status'] ?? '') === 'finished';
});
$completed_count = count($completed_projects);

$technologies = [];
foreach ($logs_data as $project) {
    $type = $project['project_type'] ?? '';
    if (!empty($type) && !in_array($type, $technologies)) {
        $technologies[] = $type;
    }
}
$tech_count = count($technologies);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($full_name) ?> - Portfolio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #6366f1;
            --accent-color: #10b981;
            --dark-color: #1f2937;
            --light-color: #ffffff;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #374151;
            line-height: 1.6;
            background-color: #ffffff;
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
            background-color: #ffffff;
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
            background-color: #ffffff;
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
            background-color: #ffffff;
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

        .navbar {
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .profile-image {
            border: 4px solid #f3f4f6;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .skill-bar {
            transition: width 1.5s ease-in-out;
        }

        .upload-base {
            position: relative;
            overflow: hidden;
        }

        .upload-base img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>

<body class="bg-white min-h-screen">
    <!-- Navigation
    <nav class="navbar bg-white fixed w-full z-10 shadow-sm">
        <div class="container mx-auto px-4 md:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="text-xl font-bold gradient-text"><?= h($full_name) ?></div>
                <div class="hidden md:flex space-x-8">
                    <a href="#about" class="text-gray-600 hover:text-indigo-600 transition-colors">About</a>
                    <a href="#skills" class="text-gray-600 hover:text-indigo-600 transition-colors">Skills</a>
                    <a href="#projects" class="text-gray-600 hover:text-indigo-600 transition-colors">Projects</a>
                    <a href="#contact" class="text-gray-600 hover:text-indigo-600 transition-colors">Contact</a>
                </div>
                <div class="md:hidden">
                    <button class="text-gray-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav> -->

    <!-- Hero Section -->
    <section class="pt-32 pb-20 px-4 md:px-8 bg-gradient-to-b from-indigo-50 to-white">
        <div class="container mx-auto max-w-6xl">
            <div class="flex flex-col md:flex-row items-center gap-10">
                <div class="md:w-1/2" data-aos="fade-right">
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                        Hi, I'm <span class="gradient-text"><?= h($full_name) ?></span>
                    </h1>
                    <p class="text-xl text-gray-600 mb-6">
                        A passionate developer with expertise in creating efficient and scalable applications.
                    </p>
                    <div class="flex gap-4">
                        <a href="#projects" class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                            View Projects
                        </a>
                    </div>
                </div>
                <div class="md:w-1/2 flex justify-center" data-aos="fade-left">
                    <div class="upload-base rounded-full w-64 h-64 profile-image">
                        <img src="../<?= h($photo) ?>" alt="<?= h($full_name) ?>">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 px-4 md:px-8 bg-white">
        <div class="container mx-auto max-w-6xl">
            <h2 class="text-3xl font-bold text-gray-800 section-title mb-12" data-aos="fade-up">About Me</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div data-aos="fade-right">
                    <p class="text-gray-600 mb-6">
                        I'm a developer with over <?= $experience ?> years of experience in building web applications.
                        I specialize in creating efficient, scalable, and user-friendly applications that solve real-world problems.
                    </p>
                    <p class="text-gray-600 mb-6">
                        My passion lies in turning ideas into reality using elegant solutions. I enjoy working on diverse projects
                        and continuously learning new technologies to enhance my skills.
                    </p>
                    <p class="text-gray-600">
                        When I'm not coding, you can find me exploring new technologies, contributing to open source projects,
                        or sharing knowledge with the developer community.
                    </p>
                </div>

                <div data-aos="fade-left">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="info-card bg-white p-5 border border-gray-100 text-center">
                            <div class="text-3xl font-bold text-indigo-600 mb-2"><?= $experience ?>+</div>
                            <p class="text-gray-600">Years Experience</p>
                        </div>
                        <div class="info-card bg-white p-5 border border-gray-100 text-center">
                            <div class="text-3xl font-bold text-indigo-600 mb-2"><?= $project_count ?></div>
                            <p class="text-gray-600">Projects Completed</p>
                        </div>
                        <div class="info-card bg-white p-5 border border-gray-100 text-center">
                            <div class="text-3xl font-bold text-indigo-600 mb-2"><?= $completed_count ?></div>
                            <p class="text-gray-600">Successful Projects</p>
                        </div>
                        <div class="info-card bg-white p-5 border border-gray-100 text-center">
                            <div class="text-3xl font-bold text-indigo-600 mb-2"><?= $tech_count ?></div>
                            <p class="text-gray-600">Technologies</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Projects Section -->
    <section id="projects" class="py-20 px-4 md:px-8 bg-white">
        <div class="container mx-auto max-w-6xl">
            <h2 class="text-3xl font-bold text-gray-800 section-title mb-12" data-aos="fade-up">My Projects</h2>

            <?php if (!empty($logs_data)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($logs_data as $i => $project):
                        $project_id = $project['id'];
                        $project_name = $project['project_name'] ?? 'Untitled Project';
                        $project_desc = $project['project_description'] ?? 'No description available.';
                        $project_type = $project['project_type'] ?? '';
                        $status = $project['status'] ?? '';
                        $images = $projectImages[$project_id] ?? [];

                        [$status_text, $status_class] = status_badge($status);
                        $tag_class = get_tag_class($project_type);
                    ?>
                        <div class="project-card border border-gray-100 overflow-hidden group"
                            data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>">
                            <div class="h-48 overflow-hidden p-2">
                                <?php if (!empty($images) && !empty($images[0]['file_path'])): ?>
                                    <img src="../uploads/projects/<?= h($images[0]['file_path']) ?>"
                                        alt="<?= h($project_name) ?>"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                <?php else: ?>
                                    <div class="h-full w-full flex items-center justify-center bg-gray-100 text-gray-400">
                                        <i class="fas fa-image text-4xl"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="p-5">
                                <div class="flex items-center gap-3 mb-3">
                                    <h3 class="font-bold text-lg text-gray-800"><?= h($project_name) ?></h3>
                                    <span class="px-3 py-1 text-sm font-medium rounded-full <?= $status_class ?>"><?= h($status_text) ?></span>
                                </div>
                                <p class="text-gray-600 mb-4"><?= h(mb_strimwidth($project_desc, 0, 100, '...')) ?></p>
                                <div class="flex justify-between items-center">
                                    <?php if (!empty($project_type)): ?>
                                        <span class="tag <?= $tag_class ?>"><?= h($project_type) ?></span>
                                    <?php else: ?>
                                        <span></span>
                                    <?php endif; ?>
                                    <a href="ProjectDetails.php?id=<?= $project_id ?>"
                                        class="text-indigo-600 hover:text-indigo-800 font-medium">View Details</a>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-8 text-center">
                    <i class="fas fa-folder-open text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">No projects available yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize AOS
            AOS.init({
                duration: 2000,
                easing: 'ease-out-cubic',
                once: true,
                mirror: false
            });

            const skillBars = document.querySelectscrollorAll('.skill-bar');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const width = entry.target.style.width;
                        entry.target.style.width = '0';
                        setTimeout(() => {
                            entry.target.style.width = width;
                        }, 100);
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.5
            });

            skillBars.forEach(bar => {
                observer.observe(bar);
            });
        });
    </script>
</body>

</html>