<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImaraSoft - AutoChat-Discord Project</title>
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
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <nav id="navbar" class="navbar fixed top-0 left-0 w-full bg-white z-50 py-4 px-6 md:px-8">
        <div class="container mx-auto flex justify-between items-center">
            <a href="#" class="text-2xl font-bold gradient-text">ImaraSoft</a>
            
            <div class="hidden md:flex space-x-8">
                <a href="#" class="text-gray-700 hover:text-indigo-600 transition-colors">Home</a>
                <a href="#" class="text-gray-700 hover:text-indigo-600 transition-colors">Projects</a>
                <a href="#" class="text-gray-700 hover:text-indigo-600 transition-colors">Services</a>
                <a href="#" class="text-gray-700 hover:text-indigo-600 transition-colors">About</a>
                <a href="#" class="text-gray-700 hover:text-indigo-600 transition-colors">Contact</a>
            </div>
            
            <button id="menu-btn" class="md:hidden text-gray-700">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>
        
        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white py-4 px-6 absolute top-full left-0 w-full shadow-lg">
            <a href="#" class="block py-2 text-gray-700 hover:text-indigo-600">Home</a>
            <a href="#" class="block py-2 text-gray-700 hover:text-indigo-600">Projects</a>
            <a href="#" class="block py-2 text-gray-700 hover:text-indigo-600">Services</a>
            <a href="#" class="block py-2 text-gray-700 hover:text-indigo-600">About</a>
            <a href="#" class="block py-2 text-gray-700 hover:text-indigo-600">Contact</a>
        </div>
    </nav>

    <div class="pt-24 pb-12 px-4 md:px-8">
        <div class="container mx-auto max-w-6xl">
            <!-- Project Header -->
            <div class="p-6 md:p-8 mb-8 bg-white rounded-xl shadow-sm" data-aos="fade-up">
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <h2 class="text-3xl font-bold text-gray-800 section-title">
                        AutoChat-Discord
                    </h2>
                    <span class="inline-block px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800">
                        IN PROGRESS
                    </span>
                    <span class="tag tag-automation">Automation</span>
                </div>

                <p class="text-gray-600 mt-4 max-w-2xl" data-aos="fade-up">
                    AutoChat provides a robust occupational attack mitigation pipeline for Discord's secondary logistics. 
                    It exports momentum solution triggers and handles data internal perturbation with patented peregrinism technology.
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                    <div class="info-card bg-white p-5 border border-gray-100" data-aos="fade-up" data-aos-delay="100">
                        <div class="flex items-center">
                            <div class="bg-indigo-100 p-3 rounded-full mr-4">
                                <i class="fas fa-user text-indigo-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Assigned To</p>
                                <p class="font-medium">John Smith</p>
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
                                <p class="font-medium">2025-09-15</p>
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
                                <p class="font-medium">August 26, 2025 09:52</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Details -->
            <section class="p-6 md:p-8 bg-white rounded-xl shadow-sm mb-8" data-aos="fade-up">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800 section-title mb-6">
                    Project Details
                </h2>
                
                <div class="prose max-w-none mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">About AutoChat-Discord</h3>
                    <p class="text-gray-600 mb-4">
                        AutoChat solides robust occupational attack mitigation pipeline to assume Discord second logisache. 
                        Progymb export momentum solution triggers, at present, data internal perturbation peregrinism, patent. 
                        Programs let bep/Box 24/7, memureglibram peregrinism: pressu downsets up gap between internal, self-organ grammaticalism.
                    </p>
                    
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 mt-8">Key Features</h3>
                    <ul class="text-gray-600 list-disc pl-5 mb-4">
                        <li>Customize the message content as needed</li>
                        <li>Send messages to multiple discord channels simultaneously</li>
                        <li>Set custom delay selected between messages for controlled messaging</li>
                        <li>AutoChat runs non-align to ensure continuous messaging</li>
                    </ul>
                    
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 mt-8">Technologies Used</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-gray-50 rounded-lg overflow-hidden">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Success</th>
                                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Anti</th>
                                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Software CSS</th>
                                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Method CS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-gray-200">
                                    <td class="py-3 px-4">Prefixes</td>
                                    <td class="py-3 px-4">Feedback</td>
                                    <td class="py-3 px-4">SupervisionCS</td>
                                    <td class="py-3 px-4">VISA</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Project Screenshots -->
            <section class="p-6 md:p-8 bg-white rounded-xl shadow-sm" data-aos="fade-up">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800 section-title mb-6">
                    Project Screenshots
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Screenshot 1 -->
                    <div class="rounded-xl overflow-hidden group border border-gray-200 shadow-sm screenshot-card"
                        data-aos="fade-up" data-aos-delay="100">
                        <div class="relative overflow-hidden cursor-pointer">
                            <img src="https://images.unsplash.com/photo-1614680376573-df3480f0c6ff?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1074&q=80" 
                                 alt="Discord Interface" class="w-full h-48 object-cover transform transition-transform duration-500 group-hover:scale-110" />
                        </div>
                        <div class="p-5">
                            <h3 class="font-semibold text-lg mb-2 text-gray-800">Discord Integration</h3>
                            <p class="text-gray-600 text-sm">Showing how AutoChat integrates with Discord's interface</p>
                        </div>
                    </div>
                    
                    <!-- Screenshot 2 -->
                    <div class="rounded-xl overflow-hidden group border border-gray-200 shadow-sm screenshot-card"
                        data-aos="fade-up" data-aos-delay="200">
                        <div class="relative overflow-hidden cursor-pointer">
                            <img src="https://images.unsplash.com/photo-1589254065878-42c9da997cc6?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80" 
                                 alt="Message Settings" class="w-full h-48 object-cover transform transition-transform duration-500 group-hover:scale-110" />
                        </div>
                        <div class="p-5">
                            <h3 class="font-semibold text-lg mb-2 text-gray-800">Message Configuration</h3>
                            <p class="text-gray-600 text-sm">Customizable message settings and timing options</p>
                        </div>
                    </div>
                    
                    <!-- Screenshot 3 -->
                    <div class="rounded-xl overflow-hidden group border border-gray-200 shadow-sm screenshot-card"
                        data-aos="fade-up" data-aos-delay="300">
                        <div class="relative overflow-hidden cursor-pointer">
                            <img src="https://images.unsplash.com/photo-1620641788421-7a1c342ea42e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1074&q=80" 
                                 alt="Dashboard" class="w-full h-48 object-cover transform transition-transform duration-500 group-hover:scale-110" />
                        </div>
                        <div class="p-5">
                            <h3 class="font-semibold text-lg mb-2 text-gray-800">Dashboard View</h3>
                            <p class="text-gray-600 text-sm">Overview of all automated messaging activities</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12 px-4 md:px-8">
        <div class="container mx-auto max-w-6xl">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">ImaraSoft</h3>
                    <p class="text-gray-400">Creating innovative software solutions for modern businesses.</p>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Home</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Projects</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Services</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Contact Us</h3>
                    <p class="text-gray-400 mb-2"><i class="fas fa-envelope mr-2"></i> info@imarsoft.com</p>
                    <p class="text-gray-400 mb-2"><i class="fas fa-phone mr-2"></i> +1 (555) 123-4567</p>
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-github"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 ImaraSoft. All rights reserved.</p>
            </div>
        </div>
    </footer>

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

            // Mobile menu toggle
            document.getElementById('menu-btn').addEventListener('click', function() {
                document.getElementById('mobile-menu').classList.toggle('hidden');
            });

            // Shrink navbar on scroll
            window.addEventListener('scroll', function() {
                const navbar = document.getElementById('navbar');
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
        });
    </script>
</body>
</html>