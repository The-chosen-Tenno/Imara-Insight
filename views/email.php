<?php
require_once __DIR__ . '/../models/ProjectImageModel.php';
require_once __DIR__ . '/../models/Logs.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../helpers/AppManager.php';
require_once __DIR__ . '/../models/BaseModel.php';

$project_logs = new Logs();
$logs_data = $project_logs->getCompleted();
$imgModel   = new ProjectImageModel();
$images = $imgModel->getAll();

// group images by project_id
$projectImages = [];
foreach ($images as $img) {
    $projectImages[$img['project_id']][] = $img;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imara Enterprise</title>
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
            background-color: #000000ff;
            color: #e5e7eb;
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
        }

        .scroll-indicator div {
            background-color: #0ff;
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
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-cyan-400 animate-pulse" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <circle cx="12" cy="12" r="4"></circle>
                        <path d="M12 2v2M12 22v-2M17 20.66l-1-1.73M11 10.27 7 3.34M20.66 17l-1.73-1M3.34 7l1.73 1M14 12h8M2 12h2M20.66 7l-1.73 1M3.34 17l1.73-1M17 3.34l-1 1.73M11 13.73l-4 6.93" />
                    </svg>
                    <span class="text-white font-medium">Smart Automation</span>
                </div>

                <div class="flex items-center gap-3 px-6 py-3 bg-purple-900/50 rounded-full backdrop-blur-sm hover:shadow-cyan-500/50 hover:scale-105 transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-cyan-400 animate-pulse" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="16 18 22 12 16 6"></polyline>
                        <polyline points="8 6 2 12 8 18"></polyline>
                    </svg>
                    <span class="text-white font-medium">Custom Development</span>
                </div>
            </div>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center" data-aos="fade-up" data-aos-delay="800">
                <button class="inline-flex items-center justify-center gap-2 font-medium text-white bg-gradient-to-r from-cyan-500 to-purple-600 hover:shadow-2xl hover:shadow-cyan-500/50 hover:scale-105 h-11 rounded-md text-lg px-8 transition-all duration-300">
                    Explore Our Work
                </button>

                <button class="inline-flex items-center justify-center gap-2 font-medium h-11 rounded-md text-lg px-8 border border-cyan-400 text-white hover:bg-cyan-500/20 hover:shadow-2xl hover:shadow-cyan-500/50 transition-all duration-300">
                    Get In Touch
                </button>
            </div>
        </div>

        <!-- Scroll Down Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <div class="w-6 h-10 border-2 border-cyan-400 rounded-full flex justify-center">
                <div class="w-1 h-3 rounded-full mt-2 animate-pulse bg-cyan-400"></div>
            </div>
        </div>
    </section>


    <!-- Projects Section -->
    <section id="projects" class="py-20 px-4 bg-gray-900">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-foreground mb-4" data-aos="fade-down">Our Projects</h2>
                <p class="text-muted-foreground text-lg max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="200">
                    Explore our diverse portfolio of automation solutions and software development projects
                </p>
            </div>

            <!-- Filter Buttons -->
            <div class="flex justify-center gap-4 mb-12" data-aos="fade-up" data-aos-delay="400">
                <button data-type="all"
                    class="filter-btn inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium bg-cyan-500 text-white hover:bg-cyan-500/90 h-10 px-4 py-2">
                    All Projects (<?= count($logs_data) ?>)
                </button>
                <button data-type="automation"
                    class="filter-btn inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                    Automation (<?= count(array_filter($logs_data, fn($p) => $p['project_type'] === 'automation')) ?>)
                </button>
                <button data-type="coding"
                    class="filter-btn inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                    Coding (<?= count(array_filter($logs_data, fn($p) => $p['project_type'] === 'coding')) ?>)
                </button>
            </div>


            <!-- Projects Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($logs_data as $project): ?>
                    <?php
                    // Get first image if available
                    $imagePath = "../assets/img/defaultProject.jpeg";
                    if (!empty($projectImages[$project['id']])) {
                        $lastImg = end($projectImages[$project['id']]);
                        $imagePath = "../uploads/projects/" . $lastImg['file_path'];
                    }
                    ?>
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm project-card group transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-cyan-500/50"
                        data-project-type="<?= $project['project_type'] ?>">
                        <div class="relative overflow-hidden rounded-t-lg mb-4">
                            <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($project['project_name']) ?>"
                                class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105">
                            <div class="absolute top-3 right-3">
                                <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-purple-500/20 text-purple-300">
                                    <?= ucfirst($project['project_type']) ?>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4 px-4 pb-4">
                            <h3 class="text-xl font-semibold text-foreground group-hover:text-primary transition-all duration-300">
                                <?= htmlspecialchars($project['project_name']) ?>
                            </h3>
                            <p class="text-muted-foreground text-sm leading-relaxed">
                                Completed project finished on <?= date("F j, Y", strtotime($project['last_updated'])) ?>.
                            </p>
                            <div class="flex gap-3 pt-2">
                                <a href="#" target="_blank"
                                    class="inline-flex items-center justify-center gap-2 text-sm font-medium h-9 px-3 rounded-md border border-input bg-background hover:bg-accent hover:text-accent-foreground shadow-md hover:shadow-2xl hover:shadow-cyan-500/50 transition-all duration-300">
                                    Code
                                </a>
                                <a href="#" target="_blank"
                                    class="inline-flex items-center justify-center gap-2 text-sm font-medium h-9 px-3 rounded-md bg-primary text-primary-foreground hover:bg-primary/90 shadow-md hover:shadow-2xl hover:shadow-cyan-500/50 transition-all duration-300">
                                    Live Demo
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            easing: 'ease-out-cubic',
            once: true,
            mirror: false
        });

        document.addEventListener("DOMContentLoaded", function() {
            const buttons = document.querySelectorAll(".filter-btn");
            const cards = document.querySelectorAll(".project-card");

            buttons.forEach(btn => {
                btn.addEventListener("click", () => {
                    const type = btn.getAttribute("data-type");

                    // highlight active button
                    buttons.forEach(b => b.classList.remove("bg-cyan-500", "text-white"));
                    buttons.forEach(b => b.classList.add("border", "border-input", "bg-background"));
                    btn.classList.add("bg-cyan-500", "text-white");
                    btn.classList.remove("border", "border-input", "bg-background");

                    // filter projects
                    cards.forEach(card => {
                        if (type === "all" || card.getAttribute("data-project-type") === type) {
                            card.style.display = "block";
                        } else {
                            card.style.display = "none";
                        }
                    });
                });
            });
        });
    </script>

</body>

</html>