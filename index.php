<?php
// index.php - Homepage
// Aman untuk XAMPP + Alias /MPTI_Project
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Topic Listing | MPTI Project</title>

    <!-- BASE URL -->
    <base href="/MPTI_Project/">

    <!-- GOOGLE FONTS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&family=Open+Sans&display=swap" rel="stylesheet">

    <!-- CSS FILES -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-icons.css" rel="stylesheet">
    <link href="css/templatemo-topic-listing.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>

<!-- ================= HEADER ================= -->
<header class="border-bottom mb-4">
    <div class="container py-3 d-flex flex-wrap align-items-center justify-content-between">

        <a href="index.php" class="text-decoration-none">
            <h4 class="m-0 fw-bold">MPTI Project</h4>
        </a>

        <ul class="nav">
            <li class="nav-item"><a href="#" class="nav-link px-2 link-secondary">Home</a></li>
            <li class="nav-item"><a href="#" class="nav-link px-2 link-dark">Features</a></li>
            <li class="nav-item"><a href="#" class="nav-link px-2 link-dark">Pricing</a></li>
            <li class="nav-item"><a href="#" class="nav-link px-2 link-dark">FAQs</a></li>
            <li class="nav-item"><a href="#" class="nav-link px-2 link-dark">About</a></li>
        </ul>

        <div class="text-end">
            <a href="auth/login_form.php" class="btn btn-outline-primary me-2">Login</a>
            <a href="auth/register_form.php" class="btn btn-primary">Sign Up</a>
        </div>

    </div>
</header>
<!-- ================= END HEADER ================= -->


<!-- ================= HERO ================= -->
<section class="hero-section d-flex align-items-center justify-content-center text-center" id="section_1">
    <div class="container">
        <h1 class="text-white">Discover. Learn. Enjoy</h1>
        <p class="text-white">platform for creatives around the world</p>

        <form method="get" class="custom-form mt-4">
            <div class="input-group input-group-lg">
                <span class="input-group-text bi-search"></span>
                <input type="search" name="keyword" class="form-control" placeholder="Design, Code, Marketing, Finance">
                <button class="btn btn-primary">Search</button>
            </div>
        </form>
    </div>
</section>
<!-- ================= END HERO ================= -->


<!-- ================= FEATURED ================= -->
<section class="featured-section py-5">
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-lg-4 mb-4">
                <div class="custom-block bg-white shadow-lg">
                    <a href="#">
                        <div class="d-flex">
                            <div>
                                <h5>Web Design</h5>
                                <p>Best free CSS templates</p>
                            </div>
                            <span class="badge bg-design rounded-pill ms-auto">14</span>
                        </div>
                        <img src="images/topics/undraw_Remote_design_team_re_urdx.png" class="img-fluid">
                    </a>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="custom-block custom-block-overlay">
                    <img src="images/businesswoman-using-tablet-analysis.jpg" class="img-fluid">
                    <div class="custom-block-overlay-text">
                        <h5 class="text-white">Finance</h5>
                        <p class="text-white">Topic Listing Template based on Bootstrap 5</p>
                        <a href="#" class="btn custom-btn">Learn More</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
<!-- ================= END FEATURED ================= -->


<!-- ================= FOOTER ================= -->
<footer class="site-footer section-padding">
    <div class="container text-center">
        <p class="mb-0">
            © <?php echo date('Y'); ?> MPTI Project  
            <br>
            Template by <a href="https://templatemo.com" target="_blank">TemplateMo</a>
        </p>
    </div>
</footer>
<!-- ================= END FOOTER ================= -->


<!-- ================= JAVASCRIPT ================= -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/jquery.sticky.js"></script>
<script src="js/click-scroll.js"></script>
<script src="js/custom.js"></script>

</body>
</html>
