<?php
require_once __DIR__ . '/../models/ProjectImageModel.php';
require_once __DIR__ . '/../models/Logs.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../helpers/AppManager.php';
require_once __DIR__ . '/../models/BaseModel.php';

// Initialize variables with default values
$logs_data = [];
$images = [];
$projectImages = [];
$error = '';

try {
    $project_logs = new Logs();
    $logs_data = $project_logs->getCompleted() ?: [];
    
    $imgModel = new ProjectImageModel();
    $images = $imgModel->getAll() ?: [];

    // Group images by project_id
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

    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <style>
        .bg-gradient-hero {
            background: linear-gradient(135deg, rgba(15, 19, 19, 0.3), rgba(163, 0, 255, 0.3));
        }

        .button-glow:hover {
            box-shadow: 0 0 10px #0ff, 0 0 20px #a300ff;
        }

        .badge-glow {
            background-color: rgba(163, 0, 255, 0.2);
            color: #0ff;
        }

        body {
            background-color: #000000;
            color: #e5e7eb;
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
        }

        .scroll-indicator div {
            background-color: #0ff;
        }
        
        /* Focus styles for accessibility */
        button:focus-visible,
        a:focus-visible {
            outline: 2px solid #0ff;
            outline-offset: 2px;
        }
        
        /* Reduced motion for accessibility */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>

<body>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-br from-black via-gray-900 to-purple-950 opacity-95"></div>
        </div>

        <div class="relative z-10 text-center px-4 max-w-5xl mx-auto">
            <h1 class="text-4xl md:text-6xl font-bold mb-6 text-white group" data-aos="fade-down" data-aos-delay="200">
                Innovation Through<br>Code & Automation
            </h1>

            <p class="text-xl md:text-2xl text-gray-300 mb-8 max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="400">
                We create cutting-edge solutions that bridge the gap between intelligent automation and powerful software development
            </p>

            <!-- Feature Buttons -->
            <div class="flex flex-col sm:flex-row gap-6 justify-center items-center mb-12" data-aos="zoom-in" data-aos-delay="600">
                <div class="flex items-center gap-3 px-6 py-3 bg-purple-900/50 rounded-full backdrop-blur-sm hover:shadow-cyan-500/50 hover:scale-105 transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-cyan-400 animate-pulse" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"></circle>
                        <circle cx="12" cy="12" r="4"></circle>
                        <path d="M12 2v2M12 22v-2M17 20.66l-1-1.73M11 10.27 7 3.34M20.66 17l-1.73-1M3.34 7l1.73 1M14 12h8M2 12h2M20.66 7l-1.73 1M3.34 17l1.73-1M17 3.34l-1 1.73M11 13.73l-4 6.93" />
                    </svg>
                    <span class="text-white font-medium">Smart Automation</span>
                </div>

                <div class="flex items-center gap-3 px-6 py-3 bg-purple-900/50 rounded-full backdrop-blur-sm hover:shadow-cyan-500/50 hover:scale-105 transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-cyan-400 animate-pulse" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <polyline points="16 18 22 12 16 6"></polyline>
                        <polyline points="8 6 2 12 8 18"></polyline>
                    </svg>
                    <span class="text-white font-medium">Custom Development</span>
                </div>
            </div>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center" data-aos="fade-up" data-aos-delay="800">
                <a href="#projects" class="inline-flex items-center justify-center gap-2 font-medium text-white bg-gradient-to-r from-cyan-500 to-purple-600 hover:shadow-2xl hover:shadow-cyan-500/50 hover:scale-105 h-11 rounded-md text-lg px-8 transition-all duration-300">
                    Explore Our Work
                </a>

                <button class="inline-flex items-center justify-center gap-2 font-medium h-11 rounded-md text-lg px-8 border border-cyan-400 text-white hover:bg-cyan-500/20 hover:shadow-2xl hover:shadow-cyan-500/50 transition-all duration-300" onclick="alert('Contact feature coming soon!')">
                    Get In Touch
                </button>
            </div>
        </div>

        <!-- Scroll Down Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce" aria-hidden="true">
            <div class="w-6 h-10 border-2 border-cyan-400 rounded-full flex justify-center">
                <div class="w-1 h-3 rounded-full mt-2 animate-pulse bg-cyan-400"></div>
            </div>
        </div>
    </section>


    <!-- Projects Section -->
    <section id="projects" class="py-20 px-4 bg-gray-900">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4" data-aos="fade-down">Our Projects</h2>
                <p class="text-gray-400 text-lg max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="200">
                    Explore our diverse portfolio of automation solutions and software development projects
                </p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-900/50 text-red-200 p-4 rounded-lg mb-8 text-center" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Filter Buttons -->
            <div class="flex flex-wrap justify-center gap-4 mb-12" data-aos="fade-up" data-aos-delay="400">
                <button data-type="all"
                    class="filter-btn inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium bg-cyan-500 text-white hover:bg-cyan-500/90 h-10 px-4 py-2"
                    aria-pressed="true">
                    All Projects (<?= count($logs_data) ?>)
                </button>
                <button data-type="automation"
                    class="filter-btn inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium border border-gray-700 bg-gray-800 hover:bg-gray-700 text-white h-10 px-4 py-2"
                    aria-pressed="false">
                    Automation (<?= count(array_filter($logs_data, fn($p) => $p['project_type'] === 'automation')) ?>)
                </button>
                <button data-type="coding"
                    class="filter-btn inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium border border-gray-700 bg-gray-800 hover:bg-gray-700 text-white h-10 px-4 py-2"
                    aria-pressed="false">
                    Coding (<?= count(array_filter($logs_data, fn($p) => $p['project_type'] === 'coding')) ?>)
                </button>
            </div>

            <!-- Projects Grid -->
            <?php if (empty($logs_data)): ?>
                <div class="text-center py-12">
                    <p class="text-gray-400 text-lg">No projects available at the moment. Please check back later.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($logs_data as $project): ?>
                        <?php
                        // Get first image if available
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
                        <div class="rounded-lg border border-gray-700 bg-gray-800 text-white shadow-sm project-card group transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl hover:shadow-cyan-500/20"
                            data-project-type="<?= htmlspecialchars($project['project_type']) ?>">
                            <div class="relative overflow-hidden rounded-t-lg mb-4 cursor-pointer project-image-container" 
                                 data-project-id="<?= $project['id'] ?>">
                                <img src="<?= $imagePath ?>" alt="<?= $altText ?>"
                                    class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105"
                                    loading="lazy">
                                <?php if ($imageCount > 0): ?>
                                <div class="absolute top-3 left-3 bg-black/70 text-white text-xs px-2 py-1 rounded-full">
                                    <?= $imageCount ?> image<?= $imageCount > 1 ? 's' : '' ?>
                                </div>
                                <?php endif; ?>
                                <div class="absolute top-3 right-3">
                                    <div class="inline-flex items-center rounded-full border border-purple-700 px-2.5 py-0.5 text-xs font-semibold bg-purple-500/20 text-purple-300">
                                        <?= ucfirst(htmlspecialchars($project['project_type'])) ?>
                                    </div>
                                </div>
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                    <span class="text-white font-medium">View Gallery</span>
                                </div>
                            </div>
                            <div class="space-y-4 px-4 pb-4">
                                <h3 class="text-xl font-semibold text-white group-hover:text-cyan-400 transition-all duration-300">
                                    <?= htmlspecialchars($project['project_name']) ?>
                                </h3>
                                <p class="text-gray-400 text-sm leading-relaxed">
                                    Completed project finished on <?= date("F j, Y", strtotime($project['last_updated'])) ?>.
                                </p>
                                <div class="flex gap-3 pt-2">
                                    <button class="inline-flex items-center justify-center gap-2 text-sm font-medium h-9 px-3 rounded-md border border-gray-700 bg-gray-800 hover:bg-gray-700 text-white shadow-md hover:shadow-cyan-500/20 transition-all duration-300"
                                        onclick="alert('Code repository link coming soon!')">
                                        Code
                                    </button>
                                    <button class="inline-flex items-center justify-center gap-2 text-sm font-medium h-9 px-3 rounded-md bg-cyan-600 text-white hover:bg-cyan-500 shadow-md hover:shadow-cyan-500/20 transition-all duration-300"
                                        onclick="alert('Live demo coming soon!')">
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

            // Filter functionality
            const buttons = document.querySelectorAll(".filter-btn");
            const cards = document.querySelectorAll(".project-card");

            buttons.forEach(btn => {
                btn.addEventListener("click", () => {
                    const type = btn.getAttribute("data-type");

                    // Update active button state
                    buttons.forEach(b => {
                        const isPressed = b === btn;
                        b.setAttribute("aria-pressed", isPressed);
                        if (isPressed) {
                            b.classList.add("bg-cyan-500", "text-white");
                            b.classList.remove("border", "border-gray-700", "bg-gray-800");
                        } else {
                            b.classList.remove("bg-cyan-500", "text-white");
                            b.classList.add("border", "border-gray-700", "bg-gray-800");
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

            // Open gallery when project image is clicked
            document.querySelectorAll('.project-image-container').forEach(container => {
                container.addEventListener('click', function() {
                    const projectId = this.getAttribute('data-project-id');
                    window.open('gallery.php?project_id=' + projectId, '_blank');
                });
            });
        });
    </script>

</body>

</html>