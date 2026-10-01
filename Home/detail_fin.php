<?php       
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
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

if (isset($_POST['id'])) {
    $vID = $_POST['id'];
    
    // 1. Fetch data from database
    $stmt = $connection->prepare("SELECT * FROM adv_reg WHERE id = ?");
    $stmt->bind_param("i", $vID);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    // 2. CHECK: If advisor exists, assign the variables you use below
    if ($data) {
        $name = $data['name'];
        $email = $data['email'];
        $phone = $data['phone'];
        $qual = $data['qual'];
        $description = $data['description'];
        $image = $data['photo'];
    } else {
        // Redirect if ID doesn't exist in database
        header("Location: fin_advisor.php");
        exit;
    }
} else {
    header("Location: fin_advisor.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>MoneyMorph - Financial Advisor Details</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
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
    
    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-secondary navbar-dark sticky-top py-lg-0 px-lg-5 wow fadeIn">
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
 
    <!-- Advisor Profile Start -->
    <div class="container-xxl py-5" style="background-color: #1c1c1e; color: #f5f5f7;">
        <div class="container">
            <div class="row d-flex align-items-center">
                <!-- Advisor Image on Left Side -->
                <div class="col-lg-5 text-center p-4">
                    <div style="width: 100%; height:350px; max-width: 300px; border-radius: 8px; overflow: hidden; margin: auto;">
                        <img src="../Advisor_Dashboard/uploads/advisors/<?php echo $image; ?>" alt="Advisor Picture" class="img-fluid" style="width: 100%; height: auto; object-fit: cover; border: 3px solid #ffa500;">
                    </div>
                </div>

                <!-- Advisor Details on Right Side -->
                <div class="col-lg-7 p-4">
                    <h2 class="text-uppercase" style="color: #ffa500; font-weight: bold;"><?php echo $name; ?></h2>
                    <p class="text-muted mb-3" style="font-style: italic;">Certified Financial Planner</p>

                    <!-- Contact Details -->
                    <h4 class="text-light text-uppercase mb-3">Contact Details</h4>
                    <p class="text-muted" style="background-color: #2c2c2e; padding: 10px; border-radius: 6px;">
                        <strong>Email:</strong> <?php echo $email; ?><br>
                        <strong>Phone:</strong> <?php echo $phone; ?>
                    </p>

                    <!-- Qualifications Section -->
                    <h4 class="text-light text-uppercase mb-3 mt-4">Qualifications</h4>
                    <ul style="list-style-type: none; padding: 0;">
                        <li class="text-muted mb-2" style="background-color: #2c2c2e; padding: 10px; border-radius: 6px;">
                            <?php echo $qual; ?>
                        </li>
                    </ul>

                    <!-- Services and Description -->
                    <h4 class="text-light text-uppercase mb-3 mt-4">Services Offered</h4>
                    <p class="text-muted mb-3" style="background-color: #2c2c2e; padding: 10px; border-radius: 6px;">
                        <?php echo $description; ?>
                    </p>
                    
                    <!-- Appointment Button -->
                   <div class="text-center mt-4">
    
<form action="get_appoinment.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
    <button type="submit" class="btn btn-primary">Book Appointment</button>
</form>
</div>

<script>
function handleAppointment() {
    // 1. Show your specific approval message
    alert("Signin required.");
    
    // 2. Submit the form to get_appoinment.php
    document.getElementById('appointmentForm').submit();
}
</script>
                </div>
            </div>
        </div>
    </div>
    <!-- Advisor Profile End -->
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
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-0 back-to-top"><i class="fa fa-chevron-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="lib/jquery/jquery.min.js"></script>
    <script src="lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/jquery.waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>
</html>
