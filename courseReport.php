<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: loginPage.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "university";

// Έλεγχος αν ο χρήστης έχει επιλέξει τουλάχιστον ένα μάθημα
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['generate_report'])) {
    $course_ids = $_POST['course_ids'];
    if (empty($course_ids)) {
        die("No courses selected.");
    }

    $conn = new mysqli($servername, $username, $password, $dbname);

    // έλεγχος σύνδεσης
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // ανάκτηση δεδομένων από τον πίνακα courses
    $placeholders = implode(',', array_fill(0, count($course_ids), '?'));
    $sql_courses = "SELECT * FROM courses WHERE id IN ($placeholders)";
    $stmt_courses = $conn->prepare($sql_courses);
    $types = str_repeat('i', count($course_ids));
    $stmt_courses->bind_param($types, ...$course_ids);
    $stmt_courses->execute();
    $result_courses = $stmt_courses->get_result();
    $courses = $result_courses->fetch_all(MYSQLI_ASSOC);
    $stmt_courses->close();

    // ανάκτηση δεδομένων από τον πίνακα modules
    $sql_modules = "SELECT * FROM modules WHERE course_id IN ($placeholders)";
    $stmt_modules = $conn->prepare($sql_modules);
    $stmt_modules->bind_param($types, ...$course_ids);
    $stmt_modules->execute();
    $result_modules = $stmt_modules->get_result();
    $modules = $result_modules->fetch_all(MYSQLI_ASSOC);
    $stmt_modules->close();

    // για καθε module τοποθετουμε το module στον modules_by_course
    // με key το course_id του, δηλαδη ο modules_by_course εμπεριέχει
    // για καθε course_id το module_name, το module_id (1,2,3 κλπ) και τα
    // credits που αντιστοιχούν στο συγκεκριμένο module
    $modules_by_course = [];
    foreach ($modules as $module) {
        $modules_by_course[$module['course_id']][] = $module;
    }

    $conn->close();
} else {
    die("No courses selected or invalid request.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Report</title>
    <link rel="stylesheet" href="pageStyle.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .barChartContainer {
            width: 100%;
        }
    </style>
    <script>
        function toggleSidebar() {
            var sidebar = document.querySelector('.sidebar');
            var main = document.querySelector('.main');
            if (sidebar.style.width === '0px' || sidebar.style.width === '') {
                sidebar.style.width = '200px';
                main.style.marginLeft = '220px';
            } else {
                sidebar.style.width = '0px';
                main.style.marginLeft = '0px';
            }
        }
        
        function randomColor() {                //δημιουργία τυχαίων χρωμάτων 
            var letters = '0123456789ABCDEF';   //για τα τμηματα των διαγραμμάτων
            var color = '#';
            for (var i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        function createPieChart(courseId, moduleNames, moduleCredits) {
            var ctx = document.getElementById('pieChart' + courseId).getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: moduleNames,
                    datasets: [{
                        data: moduleCredits,
                        backgroundColor: moduleNames.map(() => randomColor()),
                        borderColor: moduleNames.map(() => randomColor()),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Modules Distribution'
                        }
                    }
                }
            });
        }

        function createBarChart(barLabels, barDatasets) {
            var ctx = document.getElementById('barChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: barLabels,
                    datasets: barDatasets
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Comparison of Courses'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        window.onload = function() {
            <?php foreach ($courses as $course): ?>
                var moduleNames = [];
                var moduleCredits = [];
                <?php foreach ($modules_by_course[$course['id']] as $module): ?>
                    moduleNames.push("<?php echo $module['module_name']; ?>");
                    moduleCredits.push(<?php echo $module['credits']; ?>);
                <?php endforeach; ?>

                createPieChart(<?php echo $course['id']; ?>, moduleNames, moduleCredits);
            <?php endforeach; ?>

            <?php if (count($courses) > 1): ?>
                var barLabels = [];
                var barDatasets = [];
                var courseColors = {};
                var moduleIndex = 0;

                <?php foreach ($courses as $course): ?>
                    var courseModules = <?php echo json_encode($modules_by_course[$course['id']]); ?>;
                    var courseLabel = '<?php echo $course['title']; ?>';
                    
                    var courseData = [];
                    if (!courseColors[courseLabel]) {
                        courseColors[courseLabel] = randomColor();
                    }
                    var courseColor = courseColors[courseLabel];

                    for (var i = 0; i < courseModules.length; i++) {
                        barLabels.push(courseModules[i].module_name + ' (' + courseLabel + ')');
                        courseData.push(courseModules[i].credits);
                    }

                    barDatasets.push({
                        label: courseLabel,
                        data: courseData,
                        backgroundColor: courseColor,
                        borderColor: courseColor.replace('0.2', '1'),
                        borderWidth: 1
                    });
                    
                    //κενο μεταξυ courses
                    barLabels.push("");
                    moduleIndex++;
                <?php endforeach; ?>

                createBarChart(barLabels, barDatasets);
            <?php endif; ?>
        };
    </script>
</head>
<body>
    <div class="header-wrapper">
        <div class="toggle-btn" onclick="toggleSidebar()">☰</div>
        <header>
            <h1>Course Report</h1>
        </header>
    </div>
    <div class="sidebar">
        <a href="index.php">Homepage</a>
        <a href="newCourse.php" class="active">New Course</a>        
    </div>
    <div class="main">
        <?php foreach ($courses as $course): ?>
            <h2><?php echo $course['title']; ?></h2>
            <table border="1">
                <tr><th>Overview</th><td><?php echo $course['overview']; ?></td></tr>
                <tr><th>Highlights</th><td><?php echo $course['highlights']; ?></td></tr>
                <tr><th>Course Details</th><td><?php echo $course['course_details']; ?></td></tr>
                <tr><th>Entry Requirements</th><td><?php echo $course['entry_requirements']; ?></td></tr>
                <tr><th>Fees and Funding</th><td><?php echo $course['fees_funding']; ?></td></tr>
                <tr><th>FAQs</th><td><?php echo $course['faqs']; ?></td></tr>
            </table>

            <canvas id="pieChart<?php echo $course['id']; ?>" width="400" height="400"></canvas>
        <?php endforeach; ?>

        <?php if (count($courses) > 1): ?>
            <div class="barChartContainer">
                <canvas id="barChart"></canvas>
            </div>
        <?php endif; ?>
    </div>
    <footer>&copy; CSYM019 2024</footer>
</body>
</html>
