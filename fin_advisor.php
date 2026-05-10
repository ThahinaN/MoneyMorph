<?php
session_start();
include('db_conn.php');

$user_name = 'Guest';
$profile_img = 'img/default-user.png';
$dashboard_link = 'signin.php';

// CHANGE: Use 'user_id' to match your signin.php session key
if (isset($_SESSION['user_id'])) {
    $u_id = $_SESSION['user_id'];
    $user_type = $_SESSION['type']; 

    $u_stmt = $connection->prepare("SELECT name FROM reg WHERE id = ?");
    $u_stmt->bind_param("i", $u_id);
    $u_stmt->execute();
    $u_res = $u_stmt->get_result();
    
    if ($user = $u_res->fetch_assoc()) {
        $user_name = $user['name'];
        if (!empty($user['photo'])) {
            $profile_img = 'img/' . $user['photo'];
        }
    }

    if ($user_type == 0) $dashboard_link = "../Admin_Dashboard/index.php";
    elseif ($user_type == 1) $dashboard_link = "../User_Dashboard/index.php";
    elseif ($user_type == 2) $dashboard_link = "../advisor_dashboard/index.php";
}


// Fetch ONLY APPROVED advisors for the main content
$sql = "SELECT a.id, a.photo, a.name, a.qual, a.description, a.email 
        FROM adv_reg a 
        JOIN reg r ON a.email = r.email 
        WHERE r.status = 'approved'";
$result = $connection->query($sql);
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
        .card {
    border-radius: 10px;
}

.card-title {
    font-size: 1.1rem;
    font-weight: 600;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

    </style>
    <style>
        .card { border-radius: 10px; }
        .card-title { font-size: 1.1rem; font-weight: 600; }
        /* Profile Image Style */
        .user-profile-img {
            width: 35px;
            height: 35px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #BC8E51;
        }
    </style>
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
    
    <!-- Navbar End -->

    <!-- Advisors Section Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <p class="d-inline-block bg-secondary text-primary py-1 px-4">Our Advisors</p>
                <h1 class="text-uppercase">Meet Our Freelance Financial Advisors</h1>
            </div>
           <div class="row g-4">
    <!-- PHP code to display financial advisors -->
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">';
            echo '    <div class="card h-100 border-0 shadow-lg" style="background-color: #1c1c1e; color: #f5f5f7; border-radius: 10px;">';
            echo '        <div class="position-relative overflow-hidden" style="height: 350px; border-top-left-radius: 10px; border-top-right-radius: 10px;">';
            echo '            <img class="card-img-top img-fluid" src="../Advisor_Dashboard/uploads/advisors/' . htmlspecialchars($row["photo"]) . '" alt="' . htmlspecialchars($row["name"]) . '" style="height: 100%; width: 100%; object-fit: cover; filter: brightness(80%);">';
            echo '            <div class="position-absolute top-0 w-100 h-100" style="background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.8));"></div>';
            echo '        </div>';
            echo '        <div class="card-body text-center p-4">';
            echo '            <h5 class="card-title text-uppercase mb-2" style="color: #ffa500; font-weight: bold;">' . htmlspecialchars($row["name"]) . '</h5>';
            echo '            <p class="card-text mb-2" style="color: #d1d1d6;"><strong>Qualifications:</strong> ' . htmlspecialchars($row["qual"]) . '</p>';
            echo '            <p class="card-text mb-2" style="color: #d1d1d6;"><strong>Services:</strong> ' . htmlspecialchars($row["description"]) . '</p>';
            echo '            <p class="text-light mb-3" style="font-weight: 500;"><strong>Contact:</strong> ' . htmlspecialchars($row["email"]) . '</p>';

            // Contact Button
            echo '           <form action="detail_fin.php" method="POST">';
            echo '               <button type="submit" class="btn btn-warning w-100" style="color: #1c1c1e; font-weight: bold;" name="id" value="' . htmlspecialchars($row['id']) . '">Contact</button>';
            echo '           </form>';
            echo '        </div>';
            echo '    </div>';
            echo '</div>';
        }
    } else {
        echo '<p class="text-center">No financial advisors found.</p>';
    }

    // Close connection
  $connection->close();

    ?>
</div>


        </div>
    </div>
    <!-- Advisors Section End -->

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
                        <button type="button" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2">SignUp</button>
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
    </div>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- Add your JavaScript links here -->
    <script src="path/to/bootstrap.bundle.js"></script>
    <script src="path/to/your/custom/scripts.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script
</body>

</html>
