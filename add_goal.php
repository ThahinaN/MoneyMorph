<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include('db_conn.php'); 

// 1. Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['type'] != 1) {
    header("Location: ../Home/signin.php");
    exit;
}

$userid = $_SESSION['user_id'];
$user_email = $_SESSION['email']; 

// 2. Handle New Goal Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['goalName'])) {
    $goalName = $_POST['goalName'];
    $targetAmount = $_POST['targetAmount'];
    $startDate = $_POST['startDate'];
    $targetDate = $_POST['targetDate'];
    $monthlyContribution = $_POST['monthlyContribution'];
    $priorityLevel = $_POST['priorityLevel'];
    $category = $_POST['category'];
    $notes = $_POST['notes'];
    
    if (strtotime($targetDate) <= strtotime($startDate)) {
        echo "<script>alert('Error: Target date must be after the start date.'); window.history.back();</script>";
    } else {
        $stmt = $connection->prepare("INSERT INTO goals (goal_name, target_amount, start_date, target_date, monthly_contribution, priority_level, category, notes, user_id, current_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
        $stmt->bind_param("sdssdssss", $goalName, $targetAmount, $startDate, $targetDate, $monthlyContribution, $priorityLevel, $category, $notes, $userid);

        if ($stmt->execute()) {
            echo "<script>alert('Goal added successfully!'); window.location.href='view_goal.php';</script>";
            exit;
        }
    }
}

// 3. Fetch Latest Goal for Sidebar Projection
$result = $connection->query("SELECT * FROM goals WHERE user_id = '$userid' ORDER BY id DESC LIMIT 1");
$latestGoal = $result->fetch_assoc();
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
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@500;700&display=swap" rel="stylesheet"> 
    
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid position-relative d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


       <!-- Sidebar Start -->
       <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-secondary navbar-dark">
                <a href="../Home/index.php" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"></i>MoneyMoroh</h3>
                </a>

                <div class="d-flex align-items-center ms-4 mb-4">
                    <!-- USER IMAGE DISPLAY SECTION-->
                    <div class="position-relative">
                      <!---->  <img class="rounded-circle" src="img/usr.png" alt="" style="width: 40px; height: 40px;">
                        <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
                    </div>
                    <div class="ms-3">
                        <!-- USER NAME DISPLAY SECTION-->
                        <h6 class="mb-0">Dashboard</h6>
                        <!--<span>Admin</span>-->  
                    </div>
                </div>
                <div class="navbar-nav w-100">
                 <!--   <a href="../Home/index.html" class="nav-item nav-link active"><i class="fa fa-tachometer-alt me-2"></i>HOME</a>-->
                    <a href="index.php" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>DASHBOARD</a>
                    <a href="view_appoinment.php" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>Apoinment</a>
                    <a href="add_alert.php" class="nav-item nav-link"><i class="fa fa-table me-2"></i>Add Alert</a>
                    <a href="fin_plan.php" class="nav-item nav-link"><i class="fa fa-th me-2"></i>FIN PLANS</a>
                   
                    <a href="../Home/resource.php" class="nav-item nav-link"><i class="fa fa-table me-2"></i>RESOURSES</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="far fa-file-alt me-2"></i>GOALS</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="view_goal.php" class="dropdown-item">MANAGE GOALS</a>
                            <a href="add_goal.php" class="dropdown-item">ADD GOALS</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="far fa-file-alt me-2"></i>ACCOUNTS</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="add_acc.php" class="dropdown-item">ADD ACCOUNT</a>
                            <a href="view_acc.php" class="dropdown-item">MANAGE ACCOUNTS</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="far fa-file-alt me-2"></i>TRANSACATIONS</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="view_trns.php" class="dropdown-item">MANAGE TRANSACTIONS</a>
                            <a href="add_trns.php" class="dropdown-item">ADD TRANSACATIONS</a>
                        </div>
                    </div>
                   
                   
                
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->


        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <nav class="navbar navbar-expand bg-secondary navbar-dark sticky-top px-4 py-0">
                <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><i class="fa fa-user-edit"></i></h2>
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <div class="navbar-nav align-items-center ms-auto">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img class="rounded-circle me-lg-2" src="img/usr.png" alt="" style="width: 40px; height: 40px;">
                            <!--USER NAME DISPLAY SECTION-->
                            <span class="d-none d-lg-inline-flex">USER</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
                            <a href="alert.php" class="dropdown-item">Alerts</a>
                            <a href="../Home/logout.php" class="dropdown-item">Log Out</a>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Navbar End -->


 <!-- Form Start -->

      <form method="POST" action="">
    <div class="container-fluid pt-4 px-4">
        <div class="row g-4">
            <div class="col-sm-12 col-xl-6">
                <div class="bg-secondary rounded h-100 p-4">
                    
                  
                        <h6 class="mb-4">ADD GOAL</h6>
                        <input type="hidden" name="user_id" value="<?php echo $userid ?>" required>
                        
                        <div class="mb-3">
                            <label for="goalName" class="form-label">GOAL NAME</label>
                            <input type="text" class="form-control" id="goalName" name="goalName" required>
                        </div>

                        <div class="mb-3">
                            <label for="targetAmount" class="form-label">TARGET AMOUNT</label>
                            <input type="number" class="form-control" id="targetAmount" name="targetAmount" required>
                        </div>

                        <div class="mb-3">
                            <label for="startDate" class="form-label">START DATE</label>
                            <input type="date" class="form-control" id="startDate" name="startDate" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="targetDate" class="form-label">TARGET DATE</label>
                            <input type="date" class="form-control" id="targetDate" name="targetDate" required>
                        </div>

                        <div class="mb-3">
                            <label for="monthlyContribution" class="form-label">MONTHLY CONTRIBUTION</label>
                            <input type="number" class="form-control" id="monthlyContribution" name="monthlyContribution" required>
                        </div>

                        <select class="form-select mb-3" name="priorityLevel" aria-label="Priority Level" required>
                            <option value="" selected disabled>PRIORITY LEVEL</option>
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>

                        <select class="form-select mb-3" name="category" aria-label="Category" required>
                            <option value="" selected disabled>CATEGORY</option>
                            <option value="Savings">Savings</option>
                            <option value="Investment">Investment</option>
                            <option value="Debt Repayment">Debt Repayment</option>
                        </select>

                        <div class="mb-3">
                            <label for="notes" class="form-label">NOTES</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <input type="submit" class="form-control btn btn-primary" value="Submit">
                        </div>
                
                </div>
            </div>

            <div class="col-sm-12 col-xl-6">
                <div class="bg-secondary rounded h-100 p-4">
                    <h6 class="mb-4">GOALS</h6>
                    <hr>
                    <?php 
                    if (isset($latestGoal)) { 
                        $target = $latestGoal['target_amount'];
                        $monthly = $latestGoal['monthly_contribution'];
                        
                        $d1 = new DateTime($latestGoal['start_date']);
                        $d2 = new DateTime($latestGoal['target_date']);
                        $interval = $d1->diff($d2);
                        $monthsAvailable = ($interval->y * 12) + $interval->m;
                        if($monthsAvailable <= 0) $monthsAvailable = 1;

                        $totalPossible = $monthly * $monthsAvailable;
                        $shortfall = $target - $totalPossible;
                        $statusColor = ($shortfall <= 0) ? "text-success" : "text-danger";
                        $statusText = ($shortfall <= 0) ? "ON TRACK" : "UNDERFUNDED";
                    ?>
                    <div class="bg-dark rounded p-3 border-start border-4 <?php echo ($shortfall <= 0) ? 'border-success' : 'border-danger'; ?>">
                        <h5 class="<?php echo $statusColor; ?>"><?php echo $statusText; ?></h5>
                        <p class="mb-1 text-white"><b>Goal:</b> <?php echo htmlspecialchars($latestGoal['goal_name']); ?></p>
                        <p class="mb-1"><b>Required Total:</b> ₹<?php echo number_format($target); ?></p>
                        <p class="mb-1"><b>Projection:</b> ₹<?php echo number_format($totalPossible); ?> in <?php echo $monthsAvailable; ?> months</p>
                        
                        <?php if($shortfall > 0): ?>
                            <small class="text-warning">⚠️ You need ₹<?php echo number_format($shortfall / $monthsAvailable); ?> more monthly to hit this goal.</small>
                        <?php endif; ?>
                    </div>
                    <?php } else { ?>
                        <p>No goals to display.</p>
                    <?php } ?>
                    <br>
                    <hr>
                    <h6 class="mb-3">GROWTH PROJECTION</h6>
                    <div class="chart-container" style="position: relative; height:250px; width:100%; max-width: 100%;">
                        <canvas id="goalProjectionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


                   
                  
                </div>
            </div>
            
            <!-- Form End -->


         

        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/chart/chart.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
   
   <script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('goalProjectionChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    let projectionChart;

    function updateChart() {
        const target = parseFloat(document.getElementById('targetAmount').value) || 0;
        const monthly = parseFloat(document.getElementById('monthlyContribution').value) || 0;
        const startVal = document.getElementById('startDate').value;
        const endVal = document.getElementById('targetDate').value;

        if (!startVal || !endVal) return;

        const start = new Date(startVal);
        const end = new Date(endVal);
        let totalMonths = (end.getFullYear() - start.getFullYear()) * 12 + (end.getMonth() - start.getMonth());
        if (totalMonths <= 0) totalMonths = 1;

        let labels = [];
        let chartData = [];
        let targetLineData = []; // Data for the flat line
        let currentSavings = 0;

        for (let i = 0; i <= totalMonths; i++) {
            currentSavings += monthly;
            currentSavings *= 1.005; 

            if (totalMonths <= 12 || i % Math.ceil(totalMonths / 10) === 0 || i === totalMonths) {
                labels.push(i + "m");
                chartData.push(Math.round(currentSavings));
                targetLineData.push(target); // Keep target constant for every point
            }
        }

        if (projectionChart) projectionChart.destroy();

        projectionChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Your Growth',
                        data: chartData,
                        borderColor: currentSavings >= target ? '#00f2fe' : '#eb1616',
                        backgroundColor: 'rgba(0, 242, 254, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0
                    },
                    {
                        label: 'Goal Target',
                        data: targetLineData,
                        borderColor: '#ffffff', // White dashed line
                        borderWidth: 2,
                        borderDash: [5, 5], // Makes the line dashed
                        fill: false,
                        pointRadius: 0,
                        order: 1 // Keep it behind the growth line
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, labels: { color: '#fff', boxWidth: 10 } }
                },
                scales: {
                    x: { ticks: { maxTicksLimit: 6, color: '#888' }, grid: { display: false } },
                    y: { 
                        beginAtZero: true, 
                        grid: { color: 'rgba(255,255,255,0.1)' },
                        ticks: { color: '#888', callback: (value) => '₹' + value.toLocaleString() } 
                    }
                }
            }
        });
    }

    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('input', updateChart);
    });

    <?php if (isset($latestGoal)): ?>
        document.getElementById('targetAmount').value = "<?php echo $latestGoal['target_amount']; ?>";
        document.getElementById('monthlyContribution').value = "<?php echo $latestGoal['monthly_contribution']; ?>";
        document.getElementById('targetDate').value = "<?php echo $latestGoal['target_date']; ?>";
        document.getElementById('startDate').value = "<?php echo $latestGoal['start_date']; ?>";
        updateChart();
    <?php endif; ?>
});

</script>
</scrip>
</body>

</html>