<?php
session_start();
include('db_conn.php');

// 1. Initialize default navbar variables
$user_name = 'Guest';
$profile_img = 'img/default-user.png'; // Default fallback image
$dashboard_link = 'signin.php';

// 2. Logic for Logged-In User Profile
if (isset($_SESSION['id'])) {
    $u_id = $_SESSION['id'];
    $user_type = $_SESSION['type']; // 0 = Admin, 1 = User, 2 = Advisor

    // Fetch user details from the 'reg' table
    $u_stmt = $connection->prepare("SELECT name, photo FROM reg WHERE id = ?");
    $u_stmt->bind_param("i", $u_id);
    $u_stmt->execute();
    $u_res = $u_stmt->get_result();
    
    if ($user = $u_res->fetch_assoc()) {
        $user_name = $user['name'];
        // Use user's photo if it exists in the database
        if (!empty($user['photo'])) {
            $profile_img = 'img/' . $user['photo'];
        }
    }

    // 3. Determine the correct dashboard link based on role
    if ($user_type == 0) {
        $dashboard_link = "../Admin_Dashboard/index.php";
    } elseif ($user_type == 1) {
        $dashboard_link = "../User_Dashboard/index.php";
    } elseif ($user_type == 2) {
        $dashboard_link = "../advisor_dashboard/index.php";
    }
}

/* 3. Advisor ID check - Ensure it handles both POST and GET if needed */
if (isset($_POST['id'])) {
    $advisor_id = $_POST['id'];
} elseif (isset($_GET['id'])) {
    $advisor_id = $_GET['id'];
} else {
    header("Location: fin_advisor.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>MoneyMorph - Appointment Form</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="img/favicon.ico" rel="icon">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Oswald:wght@600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
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
    <!-- Navbar Start  <i class="fa fa-cut me-3"></i> -->
    <nav class="navbar navbar-expand-lg bg-secondary navbar-dark sticky-top py-lg-0 px-lg-5 wow fadeIn" data-wow-delay="0.1s">
        <a href="index.php" class="navbar-brand ms-4 ms-lg-0">
            <h1 class="mb-0 text-primary text-uppercase">MoneyMorph</h1>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="index.php" class="nav-item nav-link ">Home</a>
                <a href="fin_advisor.php" class="nav-item nav-link active">FINANCIAL ADVISORS</a>
                <a href="resource.php" class="nav-item nav-link">RESOURCES</a>
                <a href="plan.php" class="nav-item nav-link">PLANS</a>
                <a href="service.php" class="nav-item nav-link">SERVICES</a>
                <a href="testimonial.php" class="nav-item nav-link">Testimonial</a>
                <a href="about.php" class="nav-item nav-link">About US</a>
                <a href="contact.php" class="nav-item nav-link">Contact US</a>
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
    <!-- Navbar End -->

  <!-- Appointment Form Start -->
  <div class="container-xxl py-5" style="background-color: #1c1c1e; color: #f5f5f7;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="text-uppercase" style="color: #ffa500; font-weight: bold;">Book an Appointment</h2>
                <p class="text-muted">Schedule a session with your chosen Financial Advisor</p>
            </div>
            <form action="app.php" method="POST">
    <input type="hidden" name="advisor_id" value="<?php echo $advisor_id; ?>">

    <input type="hidden" name="user_email" value="<?php echo $_SESSION['user_id']; ?>">
    
                <div class="col-md-6">
                    <label class="form-label" style="color: #f5f5f7;">Appointment Date</label>
                    <input type="date" name="date" class="form-control" required style="border: 2px solid #ffa500; border-radius: 10px; padding: 15px;">
                </div>

               <div class="col-md-6">
    <label class="form-label" style="color: #f5f5f7;">Preferred Time</label>
    <input type="time" 
           name="time" 
           class="form-control" 
            required style="border: 2px solid #ffa500; border-radius: 10px; padding: 15px;" placeholder="Why are you scheduling the appointment?">
                 <small class="text-muted">Select time (Format: HH:MM AM/PM)</small>
</div>

                <div class="col-md-12">
                    <label class="form-label" style="color: #f5f5f7;">Purpose of Appointment</label>
                    <input type="text" name="purpose" class="form-control" required style="border: 2px solid #ffa500; border-radius: 10px; padding: 15px;" placeholder="Why are you scheduling the appointment?">
                </div>

                <div class="col-md-12">
                    <label class="form-label" style="color: #f5f5f7;">Additional Information</label>
                    <textarea name="additional_info" class="form-control" rows="4" style="border: 2px solid #ffa500; border-radius: 10px; padding: 15px;" placeholder="Provide any details you'd like the advisor to know."></textarea>
                </div>

                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-warning" name="submit" style="font-weight: bold; color: #1c1c1e; padding: 15px 40px; border-radius: 50px; font-size: 16px;">
                        Book Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Appointment Form End -->
    <!-- Footer (same as previous) -->
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
                    <a class="btn btn-link" href="">Privacy Policy</a>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h4 class="text-uppercase mb-4">Newsletter</h4>
                    <p>Subscribe to our newsletter for the latest updates!</p>
                    <div class="position-relative mx-auto" style="max-width: 400px;">
                        <input class="form-control border-0 rounded-pill ps-4 pe-5" type="text" placeholder="Your email">
                        <button type="button" class="btn btn-primary rounded-pill position-absolute top-0 end-0 mt-1 me-2">Subscribe</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid bg-dark text-light py-4">
            <div class="container text-center">
                <p class="mb-0">&copy; <a href="#" class="text-light">Your Site Name</a>. All Rights Reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
