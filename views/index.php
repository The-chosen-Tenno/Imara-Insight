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

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            z-index: 1000;
            overflow: auto;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal.show {
            display: block;
            opacity: 1;
        }

        .modal-content {
            position: relative;
            background-color: #111827;
            margin: 2% auto;
            padding: 20px;
            width: 90%;
            max-width: 1200px;
            border-radius: 12px;
            color: #e5e7eb;
            max-height: 90vh;
            overflow-y: auto;
        }

        .close-modal {
            position: absolute;
            top: 15px;
            right: 25px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
            z-index: 1001;
        }

        .close-modal:hover {
            color: #bbb;
        }

        .gallery-grid {
            columns: 3;
            column-gap: 16px;
        }

        .gallery-item {
            break-inside: avoid;
            margin-bottom: 16px;
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

        @media (max-width: 1023px) and (min-width: 640px) {
            .gallery-grid {
                columns: 2;
            }
        }

        @media (max-width: 639px) {
            .gallery-grid {
                columns: 1;
            }
        }

        @media (max-width: 768px) {
            .hero-content {
                text-align: center;
            }

            .project-grid {
                grid-template-columns: 1fr;
            }

            .modal-content {
                width: 95%;
                margin: 5% auto;
                padding: 10px;
            }
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="navbar fixed w-full z-50 py-4 px-6 md:px-12">
        <div class="container mx-auto flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold gradient-text">Imara<span class="text-indigo-700">Soft</span></a>
            <div class="hidden md:flex space-x-10">
                <a href="index.php" class="text-gray-700 hover:text-indigo-600 font-medium">Home</a>
                <a href="#projects" class="text-gray-700 hover:text-indigo-600 font-medium">Projects</a>
                <a href="#about" class="text-gray-700 hover:text-indigo-600 font-medium">About</a>
                <a href="#contact" class="text-gray-700 hover:text-indigo-600 font-medium">Contact</a>
            </div>
            <button class="md:hidden text-gray-700">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>
    </nav>

    <!-- Gallery Modal -->
    <div id="galleryModal" class="modal">
        <span class="close-modal">&times;</span>
        <div class="modal-content">
            <div id="modalContent"></div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero-section pt-32 pb-20 px-4 md:px-8">
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
                            <div class="relative overflow-hidden cursor-pointer group project-image-container" data-project-id="<?= $project['id'] ?>">
                                <img src="<?= $imagePath ?>" alt="<?= $altText ?>"
                                    class="project-image w-full h-auto transform transition-transform duration-500 group-hover:scale-110"
                                    loading="lazy">

                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center">
                                    <span class="text-white font-medium opacity-0 group-hover:opacity-100 transition-opacity">
                                        View Project
                                    </span>
                                </div>

                                <div class="absolute top-3 right-3">
                                    <span class="tag <?= $project['project_type'] === 'automation' ? 'tag-automation' : 'tag-coding' ?>">
                                        <?= ucfirst(htmlspecialchars($project['project_type'])) ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-6">
                                <h3 class="text-xl font-semibold text-gray-800 mb-2">
                                    <?= htmlspecialchars($project['project_name']) ?>
                                </h3>
                                <p class="text-gray-600 text-sm mb-4">
                                    Completed on <?= date("F j, Y", strtotime($project['last_updated'])) ?>
                                </p>
                                <div class="flex justify-between items-center">
                                    <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium" onclick="alert('Code repository link coming soon!')">
                                        View Code
                                    </button>
                                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded" onclick="alert('Live demo coming soon!')">
                                        Live Demo
                                    </button>
                                </div>
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

    <!-- Contact Section
    <section id="contact" class="py-20 px-4 md:px-8 bg-white">
        <div class="container mx-auto max-w-4xl">
            <h2 class="text-3xl font-bold text-gray-800 section-title text-center mx-auto mb-2" data-aos="fade-down">Get In Touch</h2>
            <p class="text-gray-600 text-center mb-12 max-w-2xl mx-auto" data-aos="fade-up">
                Have a project in mind or need technical consultation? Reach out to us and let's discuss how we can help.
            </p>
            
            <div class="bg-gray-50 rounded-2xl p-8 shadow-lg" data-aos="zoom-in">
                <form class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Your Name</label>
                        <input type="text" id="name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" id="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="md:col-span-2">
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                        <input type="text" id="subject" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="md:col-span-2">
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                        <textarea id="message" rows="5" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <button type="button" onclick="alert('Contact form submission would be processed here.')" class="btn-primary text-white font-medium py-3 px-6 rounded-lg w-full md:w-auto">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section> -->

    <!-- Footer -->
    <footer class="footer py-12 px-4 md:px-8">
        <div class="container mx-auto max-w-6xl">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold text-white mb-6">ImaraSoft</h3>
                    <p class="text-gray-400 mb-6">
                        Creating innovative solutions through code and automation.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-github"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-white mb-6">Quick Links</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Home</a></li>
                        <li><a href="#projects" class="text-gray-400 hover:text-white transition-colors">Projects</a></li>
                        <li><a href="#about" class="text-gray-400 hover:text-white transition-colors">About</a></li>
                        <li><a href="#contact" class="text-gray-400 hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-white mb-6">Services</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Web Development</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Automation</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Mobile Apps</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Cloud Solutions</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-white mb-6">Contact Info</h4>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-envelope text-indigo-400 mt-1 mr-3"></i>
                            <span class="text-gray-400">contact@imarsoft.net</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone-alt text-indigo-400 mt-1 mr-3"></i>
                            <span class="text-gray-400">+1 (123) 456-7890</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt text-indigo-400 mt-1 mr-3"></i>
                            <span class="text-gray-400">Nairobi, Kenya</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400 text-sm">
                <p>&copy; <?php echo date('Y'); ?> ImaraSoft. All rights reserved.</p>
            </div>
        </div>
    </footer>

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

            // Modal functionality
            const modal = document.getElementById("galleryModal");
            const modalContent = document.getElementById("modalContent");
            const closeModal = document.querySelector(".close-modal");

            // Open gallery when project image is clicked
            document.querySelectorAll('.project-image-container').forEach(container => {
                container.addEventListener('click', function() {
                    const projectId = this.getAttribute('data-project-id');
                    openGalleryModal(projectId);
                });
            });

            // Close modal when clicking on X
            closeModal.addEventListener('click', function() {
                modal.classList.remove('show');
            });

            // Close modal when clicking outside content
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.remove('show');
                }
            });

            // Function to open gallery modal
            function openGalleryModal(projectId) {
                // Show loading state
                modalContent.innerHTML = '<div class="text-center py-12"><p class="text-white">Loading gallery...</p></div>';
                modal.classList.add('show');

                // Fetch project data via AJAX
                fetch(`gallery.php?project_id=${projectId}`)
                    .then(response => response.text())
                    .then(data => {
                        // Extract just the gallery content from the response
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(data, 'text/html');

                        // Get the project header and gallery grid
                        const projectHeader = doc.querySelector('.container.mx-auto .flex.flex-col.md\\:flex-row');
                        const galleryGrid = doc.querySelector('.gallery-grid');

                        if (galleryGrid) {
                            // Create modal content
                            modalContent.innerHTML = '';

                            if (projectHeader) {
                                modalContent.appendChild(projectHeader.cloneNode(true));
                            }

                            if (galleryGrid) {
                                modalContent.appendChild(galleryGrid.cloneNode(true));
                            }

                            // Reinitialize animations for the modal content
                            document.querySelectorAll('.gallery-item').forEach((item, index) => {
                                item.style.animationDelay = `${index * 0.1}s`;
                            });
                        } else {
                            modalContent.innerHTML = '<div class="text-center py-12"><p class="text-white">No images found for this project.</p></div>';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading gallery:', error);
                        modalContent.innerHTML = '<div class="text-center py-12"><p class="text-white">Error loading gallery. Please try again.</p></div>';
                    });
            }
        });
    </script>

</body>

</html>