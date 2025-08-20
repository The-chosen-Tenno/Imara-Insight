<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hero Section</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <style>
        /* Background gradient for hero (cyan-purple style) */
        .bg-gradient-hero {
            background: linear-gradient(135deg, rgba(0, 255, 255, 0.3), rgba(163, 0, 255, 0.3));
        }

        /* Neon Glow Effects only for buttons and badges */
        .button-glow:hover {
            box-shadow: 0 0 10px #0ff, 0 0 20px #a300ff;
        }

        .badge-glow {
            background-color: rgba(163, 0, 255, 0.2);
            color: #0ff;
        }

        body {
            background-color: #0f0f1a;
            /* deep dark background */
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
        <div class="absolute inset-0 bg-gradient-to-br from-cyan-900 via-purple-900 to-purple-800 opacity-90"></div>
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
                    <path d="M12 2v2M12 22v-2M17 20.66l-1-1.73M11 10.27 7 3.34M20.66 17l-1.73-1M3.34 7l1.73 1M14 12h8M2 12h2M20.66 7l-1.73 1M3.34 17l1.73-1M17 3.34l-1 1.73M11 13.73l-4 6.93"/>
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
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14"></path>
                    <path d="m19 12-7 7-7-7"></path>
                </svg>
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
    <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-cyan-500 text-white hover:bg-cyan-500/90 h-10 px-4 py-2">
      All Projects (6)
    </button>
    <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
      Automation (3)
    </button>
    <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
      Coding (3)
    </button>
  </div>

  <!-- Projects Grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <!-- Project Card -->
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm project-card group animate-fade-in transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-cyan-500/50" style="animation-delay: 0s;">
      <div class="relative overflow-hidden rounded-t-lg mb-4">
        <img src="/assets/automation-project-BSCkLevg.jpg" alt="Smart Home Controller"
             class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105">
        <div class="absolute top-3 right-3">
          <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors hover:bg-secondary/80 category-badge bg-purple-500/20 text-purple-300">
            Automation
          </div>
        </div>
      </div>
      <div class="space-y-4 px-4 pb-4">
          <h3 class="text-xl font-semibold text-foreground group-hover:text-primary group-hover:text-glow transition-all duration-300">
      Smart Home Controller
    </h3>
        <p class="text-muted-foreground text-sm leading-relaxed">
          Advanced IoT automation system for smart homes with voice control, energy monitoring, and intelligent scheduling capabilities.
        </p>
        <div class="flex flex-wrap gap-2">
          <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold text-foreground">Python</div>
          <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold text-foreground">Raspberry Pi</div>
          <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold text-foreground">MQTT</div>
          <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold text-foreground">React</div>
          <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold text-foreground">Node.js</div>
        </div>
        <div class="flex gap-3 pt-2">
          <a href="https://github.com" target="_blank"
             class="inline-flex items-center justify-center gap-2 text-sm font-medium h-9 px-3 rounded-md border border-input bg-background hover:bg-accent hover:text-accent-foreground shadow-md hover:shadow-2xl hover:shadow-cyan-500/50 transition-all duration-300">
            Code
          </a>
          <a href="https://demo.com" target="_blank"
             class="inline-flex items-center justify-center gap-2 text-sm font-medium h-9 px-3 rounded-md bg-primary text-primary-foreground hover:bg-primary/90 shadow-md hover:shadow-2xl hover:shadow-cyan-500/50 transition-all duration-300">
            Live Demo
          </a>
        </div>
      </div>
    </div>

    <!-- Duplicate project cards with delay animation -->
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm project-card group animate-fade-in transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-cyan-500/50" style="animation-delay: 0.1s;">
      <!-- Project content similar to above with different image & details -->
    </div>
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm project-card group animate-fade-in transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-cyan-500/50" style="animation-delay: 0.2s;">
      <!-- Project content similar to above with different image & details -->
    </div>
  </div>
</div>
>
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
    </script>

</body>

</html>