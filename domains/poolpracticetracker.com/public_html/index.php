<?php
// Set the page title before including the header
$page_title = 'Home';
// Include the standard header for the site
include 'header.php';
?>

<!-- Main Content for the Homepage -->
<main class="main-container">

    <!-- Large Logo/Title Section -->
    <section class="hero-section">
        <!-- Placeholder for large logo image -->
        <div class="large-logo-placeholder">
            <span class="large-logo-icon" role="img" aria-label="Billiards Eight Ball">🎱</span>
        </div>
        <h1 class="hero-title">
            PoolPractice<span class="hero-primary-text">Tracker</span>
        </h1>
        <p class="hero-description">
            Your definitive platform for disciplined practice and measurable improvement in billiards.
        </p>
    </section>

    <!-- Description and Embedded Video Section -->
    <section class="main-content">
        <div class="grid-layout">
            
            <!-- Left Column: Video Description -->
            <div class="description-card">
                <h2 class="card-title">Why Track Your Practice?</h2>
                
                <p class="card-text">
                    Welcome to PoolPracticeTracker.com! This platform is dedicated to helping serious and casual pool players elevate their game. By providing a curated collection of <strong class="text-highlight">Skill Drills</strong> and structured practice routines, we offer a straightforward way for you to track your improvement and progression in the game of pool.
                </p>
                
                <p class="card-text">
                    Simply <a href="login.php" class="text-link">log in</a> and record your scores after practicing to visualize your growth over time. Keeping detailed records will highlight your <strong class="text-highlight">strengths and weaknesses</strong>, allowing you to tailor your practice sessions and plan your route to becoming the best player you can be.
                </p>
                
                <a href="drills.php" class="primary-button">
                    Explore Drills Now &rarr;
                </a>
            </div>

            <!-- Right Column: Embedded YouTube Video -->
            <div class="video-card">
                <h2 class="video-card-title">Welcome Video</h2>
                <!-- Embedded YouTube iFrame for the provided URL -->
                <div class="video-responsive-container">
                    <iframe 
                        src="https://www.youtube.com/embed/O-zxKYgW86E?rel=0" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen
                        title="Welcome to Cellar Cue Sports"
                    ></iframe>
                </div>
                <p class="video-caption">
                    This introductory video explains the vision behind PoolPracticeTracker.com and how it can help you.
                </p>
            </div>
        </div>

    <!-- Placeholder for future sections like testimonials or latest drills -->
    <section class="future-content">
        <p>More content, like latest practice results and community features, will go here!</p>
    </section>

</main>

<?php
// Include the standard footer for the site
include 'footer.php';
?>
