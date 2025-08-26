<?php
require_once __DIR__ . '/../models/ProjectImageModel.php';
require_once __DIR__ . '/../models/Logs.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../helpers/AppManager.php';
require_once __DIR__ . '/../models/BaseModel.php';

$logs_data = [];
$images = [];
$projectImages = [];
$error = '';

try {
    $project_logs = new Logs();
    $logs_data = $project_logs->getCompleted() ?: [];

    $imgModel = new ProjectImageModel();
    $images = $imgModel->getAll() ?: [];

    foreach ($images as $img) {
        if (isset($img['project_id'])) {
            $projectImages[$img['project_id']][] = $img;
        }
    }
} catch (Exception $e) {
    $error = 'Unable to load project data. Please try again later.';
    error_log("Error loading project data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Imara Enterprise - Innovation through code and automation. We create cutting-edge solutions that bridge the gap between intelligent automation and powerful software development.">
    <title>Imara Enterprise - Software Development & Automation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #6366f1;
            --accent-color: #10b981;
            --dark-color: #1f2937;
            --light-color: #f9fafb;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #374151;
            line-height: 1.6;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }

        .hero-section {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,0 L100,100 Z" fill="rgba(79,70,229,0.05)"/></svg>');
            background-size: cover;
            opacity: 0.7;
        }

        .gradient-text {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
        }

        .project-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px rgba(0, 0, 0, 0.1);
        }

        .project-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
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

        .filter-btn {
            transition: all 0.3s ease;
        }

        .filter-btn.active {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .footer {
            background-color: var(--dark-color);
            color: var(--light-color);
        }

        .social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background: var(--primary-color);
            transform: translateY(-3px);
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

        @media (max-width: 768px) {
            .hero-content {
                text-align: center;
            }

            .project-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="fixed w-full bg-white/80 backdrop-blur-md shadow-md z-50 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">

                <a href="#" class="flex items-center space-x-2 text-2xl font-bold text-gray-800 hover:text-blue-600 transition">
                <img src="../assets/img/favicon/favicon.png" alt="ImaraSoft Logo" class="h-8 w-8 object-contain">
                <span>ImaraSoft</span>
            </a>
                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-8 text-gray-700 font-medium">
                    <a href="#hero" class="hover:text-blue-600 transition relative after:content-[''] after:absolute after:w-0 after:h-[2px] after:bg-blue-600 after:left-0 after:-bottom-1 after:transition-all after:duration-300 hover:after:w-full">
                        Home
                    </a>
                    <a href="#projects" class="hover:text-blue-600 transition relative after:content-[''] after:absolute after:w-0 after:h-[2px] after:bg-blue-600 after:left-0 after:-bottom-1 after:transition-all after:duration-300 hover:after:w-full">
                        Projects
                    </a>
                    <a href="#about" class="hover:text-blue-600 transition relative after:content-[''] after:absolute after:w-0 after:h-[2px] after:bg-blue-600 after:left-0 after:-bottom-1 after:transition-all after:duration-300 hover:after:w-full">
                        About
                    </a>
                    <!-- <a href="#contact" class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-500 transition transform hover:scale-105">
                        Contact
                    </a> -->
                </div>

                <!-- Mobile Menu Button -->
                <button id="menu-btn" class="md:hidden text-gray-800 focus:outline-none">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                </button>

            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white/95 backdrop-blur-md shadow-lg flex flex-col space-y-4 px-6 py-4">
            <a href="#hero" class="hover:text-blue-600 transition">Home</a>
            <a href="#projects" class="hover:text-blue-600 transition">Projects</a>
            <a href="#about" class="hover:text-blue-600 transition">About</a>
            <!-- <a href="#contact" class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-500 transition transform hover:scale-105 text-center">
                Contact
            </a> -->
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="hero" class="hero-section pt-32 pb-20 px-4 md:px-8">
        <div class="container mx-auto max-w-6xl">
            <div class="flex flex-col md:flex-row items-center">
                <div class="hero-content md:w-1/2 mb-12 md:mb-0" data-aos="fade-right">
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-6">
                        Transforming Ideas Into <span class="gradient-text">Digital Solutions</span>
                    </h1>
                    <p class="text-lg text-gray-600 mb-8">
                        We specialize in creating innovative software and automation solutions that help businesses thrive in the digital age.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="#projects" class="btn-primary text-white font-medium py-3 px-6 rounded-lg">
                            View Our Work
                        </a>
                        <a href="#contact" class="border border-indigo-600 text-indigo-600 font-medium py-3 px-6 rounded-lg hover:bg-indigo-50 transition-colors">
                            Get In Touch
                        </a>
                    </div>
                </div>

                <div class="md:w-1/2" data-aos="fade-left">
                    <div class="bg-white p-6 rounded-2xl shadow-xl">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-indigo-100 p-4 rounded-lg">
                                <i class="fas fa-robot text-indigo-600 text-3xl mb-3"></i>
                                <h3 class="font-semibold text-gray-800">Automation</h3>
                                <p class="text-sm text-gray-600">Streamline processes</p>
                            </div>
                            <div class="bg-green-100 p-4 rounded-lg">
                                <i class="fas fa-code text-green-600 text-3xl mb-3"></i>
                                <h3 class="font-semibold text-gray-800">Development</h3>
                                <p class="text-sm text-gray-600">Custom solutions</p>
                            </div>
                            <div class="bg-blue-100 p-4 rounded-lg">
                                <i class="fas fa-mobile-alt text-blue-600 text-3xl mb-3"></i>
                                <h3 class="font-semibold text-gray-800">Mobile Apps</h3>
                                <p class="text-sm text-gray-600">iOS & Android</p>
                            </div>
                            <div class="bg-purple-100 p-4 rounded-lg">
                                <i class="fas fa-cloud text-purple-600 text-3xl mb-3"></i>
                                <h3 class="font-semibold text-gray-800">Cloud</h3>
                                <p class="text-sm text-gray-600">Scalable infrastructure</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Projects Section -->
    <section id="projects" class="py-20 px-4 md:px-8 bg-white">
        <div class="container mx-auto max-w-6xl">
            <h2 class="text-3xl font-bold text-gray-800 section-title" data-aos="fade-down">Our Projects</h2>
            <p class="text-gray-600 mb-12 max-w-2xl" data-aos="fade-up">
                Explore our portfolio of innovative solutions that demonstrate our expertise in software development and automation.
            </p>

            <?php if ($error): ?>
                <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-8 text-center" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Filter Buttons -->
            <div class="flex flex-wrap gap-3 mb-12" data-aos="fade-up">
                <button data-type="all" class="filter-btn active px-5 py-2 rounded-full text-sm font-medium">
                    All Projects (<?= count($logs_data) ?>)
                </button>
                <button data-type="automation" class="filter-btn px-5 py-2 rounded-full text-sm font-medium border border-gray-300">
                    Automation (<?= count(array_filter($logs_data, fn($p) => $p['project_type'] === 'automation')) ?>)
                </button>
                <button data-type="coding" class="filter-btn px-5 py-2 rounded-full text-sm font-medium border border-gray-300">
                    Development (<?= count(array_filter($logs_data, fn($p) => $p['project_type'] === 'coding')) ?>)
                </button>
            </div>

            <?php if (empty($logs_data)): ?>
                <div class="text-center py-12">
                    <p class="text-gray-500 text-lg">No projects available at the moment. Please check back later.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($logs_data as $project): ?>
                        <?php
                        $imagePath = "../assets/img/defaultProject.jpeg";
                        $altText = "Default project image";
                        $imageCount = 0;

                        if (!empty($projectImages[$project['id']])) {
                            $lastImg = end($projectImages[$project['id']]);
                            $imagePath = "../uploads/projects/" . htmlspecialchars($lastImg['file_path']);
                            $altText = htmlspecialchars($project['project_name']) . " project image";
                            $imageCount = count($projectImages[$project['id']]);
                        }
                        ?>
                        <div class="project-card bg-white" data-project-type="<?= htmlspecialchars($project['project_type']) ?>">
                            <a href="ProjectDetails.php?id=<?= $project['id'] ?>" target="_blank" class="block">
                                <div class="relative overflow-hidden cursor-pointer group project-image-container" data-project-id="<?= $project['id'] ?>">
                                    <img src="<?= $imagePath ?>" alt="<?= $altText ?>"
                                        class="project-image w-full h-auto transform transition-transform duration-500 group-hover:scale-110"
                                        loading="lazy">

                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center">
                                        <span class="text-white font-medium opacity-0 group-hover:opacity-100 transition-opacity">
                                            View Project Details
                                        </span>
                                    </div>

                                    <div class="absolute top-3 right-3">
                                        <span class="tag <?= $project['project_type'] === 'automation' ? 'tag-automation' : 'tag-coding' ?>">
                                            <?= ucfirst(htmlspecialchars($project['project_type'])) ?>
                                        </span>
                                    </div>
                                </div>
                            </a>

                            <!-- Content -->
                            <div class="p-6">
                                <h3 class="text-xl font-semibold text-gray-800 mb-2">
                                    <?= htmlspecialchars($project['project_name']) ?>
                                </h3>
                                <p class="text-gray-600 text-sm mb-4">
                                    Completed on <?= date("F j, Y", strtotime($project['last_updated'])) ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 px-4 md:px-8 bg-gray-100">
        <div class="container mx-auto max-w-6xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right">
                    <h2 class="text-3xl font-bold text-gray-800 section-title mb-6">About ImaraSoft</h2>
                    <p class="text-gray-600 mb-6">
                        We are a dedicated team of developers and automation specialists passionate about creating solutions that make a difference. With years of experience in the industry, we've helped numerous businesses transform their operations through technology.
                    </p>
                    <p class="text-gray-600 mb-8">
                        Our approach combines technical expertise with a deep understanding of business needs, ensuring that every solution we deliver is both technically sound and strategically valuable.
                    </p>
                    <div class="flex gap-4">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-indigo-600 mb-2">50+</div>
                            <div class="text-gray-600 text-sm">Projects Completed</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-indigo-600 mb-2">5+</div>
                            <div class="text-gray-600 text-sm">Years Experience</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-indigo-600 mb-2">100%</div>
                            <div class="text-gray-600 text-sm">Client Satisfaction</div>
                        </div>
                    </div>
                </div>
                <div data-aos="fade-left">
                    <div class="bg-white p-2 rounded-2xl shadow-lg">
                        <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=400&q=80" alt="Team working" class="rounded-lg w-full h-auto">
                    </div>
                </div>
            </div>
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

            // Filter functionality
            const buttons = document.querySelectorAll(".filter-btn");
            const cards = document.querySelectorAll(".project-card");

            buttons.forEach(btn => {
                btn.addEventListener("click", () => {
                    const type = btn.getAttribute("data-type");

                    // Update active button state
                    buttons.forEach(b => {
                        if (b === btn) {
                            b.classList.add("active", "text-white");
                            b.classList.remove("border", "border-gray-300");
                        } else {
                            b.classList.remove("active", "text-white");
                            b.classList.add("border", "border-gray-300");
                        }
                    });

                    // Filter projects
                    cards.forEach(card => {
                        if (type === "all" || card.getAttribute("data-project-type") === type) {
                            card.style.display = "block";
                            setTimeout(() => {
                                card.style.opacity = "1";
                                card.style.transform = "translateY(0)";
                            }, 10);
                        } else {
                            card.style.opacity = "0";
                            card.style.transform = "translateY(20px)";
                            setTimeout(() => {
                                card.style.display = "none";
                            }, 300);
                        }
                    });
                });
            });

            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Mobile menu toggle
            document.getElementById('menu-btn').addEventListener('click', function() {
                document.getElementById('mobile-menu').classList.toggle('hidden');
            });

            // Shrink navbar on scroll
            window.addEventListener('scroll', function() {
                const navbar = document.getElementById('navbar');
                if (window.scrollY > 50) {
                    navbar.classList.add('shadow-lg', 'py-1');
                } else {
                    navbar.classList.remove('shadow-lg', 'py-1');
                }
            });
        });
    </script>

</body>

</html>