<?php
session_start();
include('db_conn.php'); 

if (!isset($_SESSION['user_id']) || $_SESSION['type'] != 1) {
    header("Location: ../Home/signin.php");
    exit;
}


 if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['monthlyIncome'])) {
    // Verify session
    if (!isset($_SESSION['user_id'])) {
        exit("Error: Session expired. Please login again.");
    }

    $user_id = $_SESSION['user_id'];
    
    // Use $connection (as defined in your db_conn.php)
    $income = mysqli_real_escape_string($connection, $_POST['monthlyIncome']);
    $expences = mysqli_real_escape_string($connection, $_POST['monthlyexpences']);
    $savings = mysqli_real_escape_string($connection, $_POST['currentSavings']);
    $goal = mysqli_real_escape_string($connection, $_POST['financialGoal']);
    $target = mysqli_real_escape_string($connection, $_POST['targetAmount']);
    $target_date = mysqli_real_escape_string($connection, $_POST['targetDate']);

    $sql = "INSERT INTO expence (user_id, monthly_income, monthly_expences, current_savings, financial_goal, target_amount, target_date) 
            VALUES ('$user_id', '$income', '$expences', '$savings', '$goal', '$target', '$target_date')";

    if (mysqli_query($connection, $sql)) {
        echo "success"; 
        exit;
    } else {
        echo "Database Error: " . mysqli_error($connection);
        exit;
    }
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
         <!-- Sidebar Start -->
          <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-secondary navbar-dark">
                <a href="index.php" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary">MoneyMorph</h3>
                </a>
                 <div class="navbar-nav w-100">
                    <a href="../Home/index.php" class="nav-item nav-link"> <i class="fa fa-home me-2"></i>Home</a>
                    <a href="index.php" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>DASHBOARD</a>
                       <a href="view_appoinment.php" class="nav-item nav-link"><i class="fa fa-calendar-check me-2"></i>APPOINTMENTS</a>
                    <a href="fin_plan.php" class="nav-item nav-link"><i class="fa fa-file-invoice-dollar me-2"></i>FIN PLANS</a>
                    
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-bullseye me-2"></i>GOALS</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="view_goal.php" class="dropdown-item">MANAGE GOALS</a>
                            <a href="add_goal.php" class="dropdown-item">ADD GOALS</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-credit-card me-2"></i>ACCOUNTS</a>
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
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <!-- Financial Plan Form -->
        <div class="col-sm-12 col-xl-6">
            <div class="bg-secondary rounded h-100 p-4">
                <h6 class="mb-4">CREATE FINANCIAL PLAN</h6>
                <form id="financialPlanForm" method="POST">
                    <!-- Monthly Income -->
                    <div class="mb-3">
                        <label for="monthlyIncome" class="form-label">MONTHLY INCOME</label>
                        <input type="number" class="form-control" id="monthlyIncome" name="monthlyIncome" placeholder="Enter your monthly income" required>
                    </div>

                    <!-- Monthly expences -->
                    <div class="mb-3">
                        <label for="monthlyexpences" class="form-label">MONTHLY expences</label>
                        <input type="number" class="form-control" id="monthlyexpences" name="monthlyexpences" placeholder="Enter your monthly expences" required>
                    </div>

                    <!-- Current Savings -->
                    <div class="mb-3">
                        <label for="currentSavings" class="form-label">CURRENT SAVINGS</label>
                        <input type="number" class="form-control" id="currentSavings" name="currentSavings" placeholder="Enter your current savings" required>
                    </div>

                    <!-- Financial Goals -->
                    <div class="mb-3">
                        <label for="financialGoal" class="form-label">FINANCIAL GOAL</label>
                        <input type="text" class="form-control" id="financialGoal" name="financialGoal" placeholder="e.g., Buy a House, Retirement" required>
                    </div>

                    <!-- Target Amount -->
                    <div class="mb-3">
                        <label for="targetAmount" class="form-label">TARGET AMOUNT</label>
                        <input type="number" class="form-control" id="targetAmount" name="targetAmount" placeholder="Enter the target amount" required>
                    </div>

                    <!-- Target Date -->
                    <div class="mb-3">
                        <label for="targetDate" class="form-label">TARGET DATE</label>
                        <input type="date" class="form-control" id="targetDate" name="targetDate" required>
                    </div>

                    <!-- Submit Button -->
                    <div class="mb-3">
                        <input type="submit" class="form-control btn btn-primary" value="Get Suggestions">
                    </div>
                </form>
            </div>
        </div>

        <!-- Financial Plan Suggestions Display -->
        <div class="col-sm-12 col-xl-6">
            <div class="bg-secondary rounded h-100 p-4">
                <h6 class="mb-4">YOUR FINANCIAL PLAN SUGGESTIONS</h6>
                <hr>
                <!-- Suggestions will be displayed here -->
                <div id="planSuggestions">
                    <p class="text-muted">Please fill out the form to see your personalized financial plan suggestions.</p>
                </div>
                 <hr>

        <h6 class="mt-4">Income vs expences</h6>
        <canvas id="incomeExpenseChart" height="200"></canvas>

        <h6 class="mt-4">Savings Growth Over Time</h6>
        <canvas id="savingsGrowthChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>
<!-- Form End -->
 <!-- Form End -->


           


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
 $(document).ready(function () {
    let incomeExpenseChart = null;
    let savingsGrowthChart = null;

    $('#financialPlanForm').on('submit', function (event) {
        // This stops the page from redirecting to the "success" screen
        event.preventDefault();

        // --- FIX 1: Define formData ---
        const formData = $(this).serialize(); 

        // 1. DATA ACQUISITION
        const income = parseFloat($('#monthlyIncome').val()) || 0;
        const expences = parseFloat($('#monthlyexpences').val()) || 0;
        const currentSavings = parseFloat($('#currentSavings').val()) || 0;
        const targetAmount = parseFloat($('#targetAmount').val()) || 0;
        const targetDateVal = $('#targetDate').val();
        
        if(!targetDateVal) return; // Don't run if date is empty
        
        const targetDate = new Date(targetDateVal);
        const today = new Date();

        // --- 2. DATABASE STORAGE (AJAX) ---
        $.ajax({
            type: 'POST',
            url: 'fin_plan.php', 
            data: formData, 
            success: function(response) {
                console.log("Database Response:", response);
                if(response.trim() === "success") {
                    // We stay on the page now!
                    console.log("Data saved successfully in background.");
                } else {
                    console.error("PHP Error:", response);
                }
            },
            error: function(xhr, status, error) {
                alert("Connection Error: " + error);
            }
        });

        // 3. CALCULATIONS (Logic strictly preserved)
        const monthlySurplus = income - expences;
        const monthsAvailable = Math.max(1, 
            (targetDate.getFullYear() - today.getFullYear()) * 12 + 
            (targetDate.getMonth() - today.getMonth())
        );
        const yearsAvailable = monthsAvailable / 12;

        const inflationRate = 0.05;
        const adjustedTarget = targetAmount * Math.pow((1 + inflationRate), yearsAvailable);
        const totalNeeded = adjustedTarget - currentSavings;
        const requiredMonthlySaving = totalNeeded > 0 ? totalNeeded / monthsAvailable : 0;
        const expenseRatio = (expences / income) * 100;
        const emergencyFundTarget = expences * 6;

        // 4. ANALYSIS ENGINE (Logic strictly preserved)
        let analysisHTML = `<h5 class="text-primary">Financial Analysis & Suggestions</h5><ul>`;

        if (expenseRatio > 70) {
            analysisHTML += `<li class="text-danger"><b>High Expense Ratio:</b> Your expences (${expenseRatio.toFixed(0)}%) exceed healthy limits. Reduce spending.</li>`;
        } else {
            analysisHTML += `<li class="text-success"><b>Healthy Spending:</b> Your expense ratio is within a safe range.</li>`;
        }

        if (currentSavings < emergencyFundTarget) {
            analysisHTML += `<li class="text-warning"><b>Emergency Fund:</b> Aim for ₹${emergencyFundTarget.toLocaleString()} (6 months of expences). You are ₹${(emergencyFundTarget - currentSavings).toLocaleString()} short.</li>`;
        }

        if (monthlySurplus <= 0) {
            analysisHTML += `<li class="text-danger"><b>Goal Impossible:</b> You have no monthly surplus. You must reduce expences or increase income to save.</li>`;
        } 
        else if (monthlySurplus < requiredMonthlySaving) {
            const actualMonthsNeeded = Math.ceil(totalNeeded / monthlySurplus);
            const realisticDate = new Date();
            realisticDate.setMonth(realisticDate.getMonth() + actualMonthsNeeded);

            const options = { month: 'long', year: 'numeric' };
            const formattedDate = realisticDate.toLocaleDateString('en-US', options);

            analysisHTML += `<li class="text-danger"><b>Goal Gap:</b> To hit your target by the chosen date, you need to save ₹${requiredMonthlySaving.toFixed(0)}/mo. You are short by ₹${(requiredMonthlySaving - monthlySurplus).toFixed(0)}.</li>`;
            
            analysisHTML += `
                <div class="alert alert-info mt-3" style="background-color: rgba(23, 162, 184, 0.1); border-left: 5px solid #17a2b8; color: #fff;">
                    <i class="bi bi-lightbulb-fill me-2"></i>
                    <strong>MoneyMorph Suggestion:</strong> At your current saving pace of ₹${monthlySurplus.toLocaleString()}/mo, 
                    you will reach your goal in <strong>${actualMonthsNeeded} months</strong>. 
                    Consider changing your target date to <strong>${formattedDate}</strong>.
                </div>`;
        } 
        else {
            analysisHTML += `<li class="text-success"><b>On Track:</b> Your goal is achievable! You have a surplus of ₹${(monthlySurplus - requiredMonthlySaving).toFixed(0)} beyond what is required.</li>`;
        }

        analysisHTML += `</ul>`;
        $('#planSuggestions').html(analysisHTML);

        // 5. UPDATE CHARTS
        updateVisuals(income, expences, monthlySurplus, currentSavings, monthsAvailable, adjustedTarget);
    });

    function updateVisuals(income, expences, surplus, savings, months, target) {
        if (incomeExpenseChart) incomeExpenseChart.destroy();
        const ctx1 = document.getElementById('incomeExpenseChart').getContext('2d');
        incomeExpenseChart = new Chart(ctx1, {
            type: 'doughnut',
            data: {
                labels: ['expences', 'Savings Potential'],
                datasets: [{
                    data: [expences, Math.max(0, surplus)],
                    backgroundColor: ['#FF6384', '#4BC192'],
                    borderWidth: 0
                }]
            },
            options: { plugins: { legend: { position: 'bottom', labels: { color: '#fff' } } } }
        });

        if (savingsGrowthChart) savingsGrowthChart.destroy();
        const ctx2 = document.getElementById('savingsGrowthChart').getContext('2d');
        let labels = [];
        let compoundData = [];
        let currentTotal = savings;
        const monthlyReturn = 0.07 / 12; 

        for (let i = 0; i <= Math.min(months, 60); i++) {
            labels.push("Month " + i);
            compoundData.push(currentTotal.toFixed(0));
            currentTotal = (currentTotal + Math.max(0, surplus)) * (1 + monthlyReturn);
        }

        savingsGrowthChart = new Chart(ctx2, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Projected Wealth (7% Return)',
                    data: compoundData,
                    borderColor: '#9966FF',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                scales: {
                    y: { ticks: { color: '#fff' }, grid: { color: 'rgba(255,255,255,0.1)' } },
                    x: { ticks: { color: '#fff' }, grid: { display: false } }
                }
            }
        });
    }
});
</script>

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
    



</body>

</html>










