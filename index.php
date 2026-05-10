<?php 
session_start();
include('db_conn.php');

$user_name = 'Guest';
$dashboard_link = 'signin.php'; // Default for guests
$is_logged_in = false;

if (isset($_SESSION['user_id'])) {
    $is_logged_in = true;
    $u_id = $_SESSION['user_id'];
    $u_type = $_SESSION['type']; // 0=Admin, 1=User, 2=Advisor

    // Fetch name from database
    $stmt = $connection->prepare("SELECT name FROM reg WHERE id = ?");
    $stmt->bind_param("i", $u_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($user = $res->fetch_assoc()) {
        $user_name = $user['name'];
    }

    // Set dynamic link based on the logged-in role
    if ($u_type == 0) $dashboard_link = "../Admin_Dashboard/index.php";
    elseif ($u_type == 1) $dashboard_link = "../User_Dashboard/index.php";
    elseif ($u_type == 2) $dashboard_link = "../Advisor_Dashboard/index.php";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>MoneyMorph</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Oswald:wght@600&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    <style>
        /* Small styling for the profile circular image */
        .user-profile-img {
            width: 35px;
            height: 35px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #BC8E51; /* Matches your primary gold color */
        }
    </style>
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

<nav class="navbar navbar-expand-lg bg-secondary navbar-dark sticky-top py-lg-0 px-lg-5 wow fadeIn" data-wow-delay="0.1s">
    <a href="index.php" class="navbar-brand ms-4 ms-lg-0">
        <h1 class="mb-0 text-primary text-uppercase">MoneyMorph</h1>
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0">
            <a href="index.php" class="nav-item nav-link active">Home</a>
            <a href="fin_advisor.php" class="nav-item nav-link">FINANCIAL ADVISORS</a>
            <a href="resource.php" class="nav-item nav-link">RESOURCES</a>
            <a href="plan.php" class="nav-item nav-link">PLANS</a>
            <a href="service.php" class="nav-item nav-link">SERVICES</a>
            <a href="Testimonial.php" class="nav-item nav-link">TESTIMONIALS</a>
            <a href="contact.php" class="nav-item nav-link">CONTACT US</a>
            <a href="about.php" class="nav-item nav-link">About US</a>
        </div>

        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="nav-item dropdown d-flex align-items-center ms-lg-4">
                <style>
                    .user-profile-img { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; border: 2px solid #D4AF37; }
                </style>
                <img src="<?php echo $profile_img; ?>" alt="Profile" class="user-profile-img me-2">
                <a href="#" class="nav-link dropdown-toggle text-white p-0" data-bs-toggle="dropdown">
                    Hi, <?php echo htmlspecialchars($user_name); ?>
                </a>
                <div class="dropdown-menu m-0 bg-secondary border-0">
                    <a href="<?php echo $dashboard_link; ?>" class="dropdown-item text-white">My Dashboard</a>
                    <a href="logout.php" class="dropdown-item text-danger">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <a href="signin.php" class="btn btn-primary rounded-0 py-2 px-lg-4 d-none d-lg-block ms-lg-4">
                SIGN IN <i class="fa fa-arrow-right ms-3"></i>
            </a>
        <?php endif; ?>
    </div>
</nav>

    <!-- Carousel Start  <i class="fa fa-phone-alt text-primary me-3"></i>   <i class="fa fa-map-marker-alt text-primary me-3"> <i class="fa fa-phone-alt text-primary me-3"></i <i class="fa fa-map-marker-alt text-primary me-3">  -->
    <div class="container-fluid p-0 mb-5 wow fadeIn" data-wow-delay="0.1s">
        <div id="header-carousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="w-100" height="850px" src="img/im.png" alt="Image">
                    <div class="carousel-caption d-flex align-items-center justify-content-center text-start">
                        <div class="mx-sm-5 px-5" style="max-width: 900px;">
                            <h1 class="display-2 text-white text-uppercase mb-4 animated slideInDown">Master Your Money</h1>
                            <h4 class="text-white text-uppercase mb-4 animated slideInDown"></i>Plan Your Future</h4>
                            <h4 class="text-white text-uppercase mb-4 animated slideInDown">Expert Guidance, Anytime</h4>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <img class="w-100" height="850px" src="img/im2.jpg" alt="Image">
                    <div class="carousel-caption d-flex align-items-center justify-content-center text-start">
                        <div class="mx-sm-5 px-5" style="max-width: 900px;">
                            <h1 class="display-2 text-white text-uppercase mb-4 animated slideInDown">Smart Savings Made Easy</h1>
                            <h4 class="text-white text-uppercase mb-4 animated slideInDown">Achieve Financial Freedom</h4>
                            <h4 class="text-white text-uppercase mb-4 animated slideInDown">Expert Guidance, Anytime</h4>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#header-carousel"
                data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#header-carousel"
                data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
    <!-- Carousel End -->

 <!-- Service Start -->
 <div class="container-xxl py-5">
    <div class="container">
        <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
            <p class="d-inline-block bg-secondary text-primary py-1 px-4">Services</p>
            <h1 class="text-uppercase">What We Provide</h1>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="service-item position-relative overflow-hidden bg-secondary d-flex h-100 p-5 ps-0">
                    <!-- <div class="bg-dark d-flex flex-shrink-0 align-items-center justify-content-center" style="width: 60px; height: 60px;">
                       <img class="img-fluid" src="img/haircut.png" alt="">
                    </div>-->
                    <div class="ps-4">
                        <h3 class="text-uppercase mb-3">Custom Financial Goal Setting</h3>
                        <p>Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam.</p>
                        <span class="text-uppercase text-primary">ABOTUS</span>
                    </div>
                   <!-- <a class="btn btn-square" href=""><i class="fa fa-plus text-primary"></i></a>-->
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="service-item position-relative overflow-hidden bg-secondary d-flex h-100 p-5 ps-0">
                    <!-- <div class="bg-dark d-flex flex-shrink-0 align-items-center justify-content-center" style="width: 60px; height: 60px;">
                       <img class="img-fluid" src="img/haircut.png" alt="">
                    </div>-->
                    <div class="ps-4">
                        <h3 class="text-uppercase mb-3">Interactive Budgeting Tools</h3>
                        <p>Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam.</p>
                        <span class="text-uppercase text-primary">ABOTUS</span>
                    </div>
                   <!-- <a class="btn btn-square" href=""><i class="fa fa-plus text-primary"></i></a>-->
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="service-item position-relative overflow-hidden bg-secondary d-flex h-100 p-5 ps-0">
                    <!-- <div class="bg-dark d-flex flex-shrink-0 align-items-center justify-content-center" style="width: 60px; height: 60px;">
                       <img class="img-fluid" src="img/haircut.png" alt="">
                    </div>-->
                    <div class="ps-4">
                        <h3 class="text-uppercase mb-3">Real-Time Expense Tracking</h3>
                        <p>Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam.</p>
                        <span class="text-uppercase text-primary">ABOTUS</span>
                    </div>
                   <!-- <a class="btn btn-square" href=""><i class="fa fa-plus text-primary"></i></a>-->
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="service-item position-relative overflow-hidden bg-secondary d-flex h-100 p-5 ps-0">
                    <!-- <div class="bg-dark d-flex flex-shrink-0 align-items-center justify-content-center" style="width: 60px; height: 60px;">
                       <img class="img-fluid" src="img/haircut.png" alt="">
                    </div>-->
                    <div class="ps-4">
                        <h3 class="text-uppercase mb-3">Personalized Financial Alerts</h3>
                        <p>Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam.</p>
                        <span class="text-uppercase text-primary">ABOTUS</span>
                    </div>
                   <!-- <a class="btn btn-square" href=""><i class="fa fa-plus text-primary"></i></a>-->
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="service-item position-relative overflow-hidden bg-secondary d-flex h-100 p-5 ps-0">
                    <!-- <div class="bg-dark d-flex flex-shrink-0 align-items-center justify-content-center" style="width: 60px; height: 60px;">
                       <img class="img-fluid" src="img/haircut.png" alt="">
                    </div>-->
                    <div class="ps-4">
                        <h3 class="text-uppercase mb-3">Expert Financial Advisor Network</h3>
                        <p>Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam.</p>
                        <span class="text-uppercase text-primary">ABOTUS</span>
                    </div>
                   <!-- <a class="btn btn-square" href=""><i class="fa fa-plus text-primary"></i></a>-->
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="service-item position-relative overflow-hidden bg-secondary d-flex h-100 p-5 ps-0">
                    <!-- <div class="bg-dark d-flex flex-shrink-0 align-items-center justify-content-center" style="width: 60px; height: 60px;">
                       <img class="img-fluid" src="img/haircut.png" alt="">
                    </div>-->
                    <div class="ps-4">
                        <h3 class="text-uppercase mb-3">Comprehensive Financial Education</h3>
                        <p>Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam.</p>
                        <span class="text-uppercase text-primary">ABOTUS</span>
                    </div>
                   <!-- <a class="btn btn-square" href=""><i class="fa fa-plus text-primary"></i></a>-->
                </div>
            </div>    
        </div>
    </div>
</div>
<!-- Service End -->
  


    <!-- About Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                    <div class="d-flex flex-column">
                        <img class="img-fluid w-75 align-self-end" src="img/MoneyMorph4.png" alt="">
                        <div class="w-50 bg-secondary p-5" style="margin-top: -25%;">
                            <h1 class="text-uppercase text-primary mb-3">25 Years</h1>
                            <h2 class="text-uppercase mb-0">Experience</h2>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                    <p class="d-inline-block bg-secondary text-primary py-1 px-4">About Us</p>
                    <h1 class="text-uppercase mb-4"> Learn More About Us!</h1>
                    <p>our mission is to empower individuals to take control of their financial future. We understand that managing personal finances can be overwhelming, so we’ve built a platform that simplifies the process. From budgeting tools to personalized financial guidance, we provide all the resources you need to make informed decisions. </p>
                    <p class="mb-4">With our integrated Freelance Financial Advisor Network and a wealth of educational content, FIN PLAN is more than just an app—it’s a complete financial solution designed with you in mind.</p>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h3 class="text-uppercase mb-3">Since 1990</h3>
                            <p class="mb-0"> FIN PLAN is more than just an app—it’s a complete financial solution designed with you in mind.</p>
                        </div>
                        <div class="col-md-6">
                            <h3 class="text-uppercase mb-3">1000+ clients</h3>
                            <p class="mb-0"> FIN PLAN is more than just an app—it’s a complete financial solution designed with you in mind.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->
    <!-- Testimonial Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <p class="d-inline-block bg-secondary text-primary py-1 px-4">Testimonial</p>
                <h1 class="text-uppercase">What Our Clients Say!</h1>
            </div>
            <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
                <div class="testimonial-item text-center" data-dot="<img class='img-fluid' src='img/testimonial-1.jpg' alt=''>">
                    <h4 class="text-uppercase">JOHN</h4>
                    <p class="text-primary">BARBER</p>
                    <span class="fs-5">FIN PLAN helped me save more and manage my budget effortlessly!.</span>
                </div>
                <div class="testimonial-item text-center" data-dot="<img class='img-fluid' src='img/testimonial-2.jpg' alt=''>">
                    <h4 class="text-uppercase">SAM</h4>
                    <p class="text-primary">DRIVER</p>
                    <span class="fs-5">The expert advice I received was a game changer for my financial planning!.</span>
                </div>
                <div class="testimonial-item text-center" data-dot="<img class='img-fluid' src='img/testimonial-3.jpg' alt=''>">
                    <h4 class="text-uppercase">ALEX</h4>
                    <p class="text-primary">SOFTWARE ENGINEER</p>
                    <span class="fs-5">Thanks to FIN PLAN, I'm finally confident in managing my finances..</span>
                </div>
            </div>      
        </div>
    </div>
    <!-- Testimonial End -->


    <!-- Footer Start -->
    <div class="container-fluid bg-secondary text-light footer mt-5 pt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-4 col-md-6">
                    <h4 class="text-uppercase mb-4">Get In Touch</h4>
                    <div class="d-flex align-items-center mb-2">
                        <div class="btn-square bg-dark flex-shrink-0 me-3">
                            <span class="fa fa-map-marker-alt text-primary"></span>
                        </div>
                        <span>123 Street, New York, USA</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="btn-square bg-dark flex-shrink-0 me-3">
                            <span class="fa fa-phone-alt text-primary"></span>
                        </div>
                        <span>+012 345 67890</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="btn-square bg-dark flex-shrink-0 me-3">
                            <span class="fa fa-envelope-open text-primary"></span>
                        </div>
                        <span>info@example.com</span>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h4 class="text-uppercase mb-4">Quick Links</h4>
                    <a class="btn btn-link" href="">About Us</a>
                    <a class="btn btn-link" href="">Contact Us</a>
                    <a class="btn btn-link" href="">Our Services</a>
                    <a class="btn btn-link" href="">Terms & Condition</a>
                    <a class="btn btn-link" href="">Support</a>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h4 class="text-uppercase mb-4">Newsletter</h4>
                    <div class="position-relative mb-4">
                        <input class="form-control border-0 w-100 py-3 ps-4 pe-5" type="text" placeholder="Your email">
                        <button type="button" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2">nnUp</button>
                    </div>
                    <div class="d-flex pt-1 m-n1">
                        <a class="btn btn-lg-square btn-dark text-primary m-1" href=""><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-lg-square btn-dark text-primary m-1" href=""><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-lg-square btn-dark text-primary m-1" href=""><i class="fab fa-youtube"></i></a>
                        <a class="btn btn-lg-square btn-dark text-primary m-1" href=""><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        &copy; <a class="border-bottom" href="#">MoneyMorph</a>, All Right Reserved.
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
                        Designed By <a class="border-bottom" href="">MURALI KRISHNA</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->









    <!-- Back to Top -->
    <a href="#" class="btn btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>

