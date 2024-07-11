<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "university";

$conn = new mysqli($servername, $username, $password, $dbname);

// έλεγχος σύνδεσης
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// έλεγχος αν ο χρήστης έχει επιλέξει τουλάχιστον ένα μάθημα
$course_ids = $_POST['course_ids'];
if (empty($course_ids)) {
    die("No courses selected.");
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

//για καθε module τοποθετουμε το module στον modules_by_course 
//με key το course_id του , δηλαδη ο modules_by_course εμπεριέχει
//για καθε course_id το module_name, το module_id (1,2,3 κλπ) και τα
//credits που αντιστοιχούν στο συγκεκριμένο module
$modules_by_course = [];
foreach ($modules as $module) {
    $modules_by_course[$module['course_id']][] = $module;
}

$conn->close();
?>


<!--Προσπάθησα επανηλημένως να καλώ τα script απο 2 αρχεία js για τη δημιουργία 
    των γραφημάτων αλλά δεν μου εμφάνιζε τα γραφήματα στην σελίδα, για αυτό τα ενσωμάτωσα εδώ-->

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
        function createPieChart(courseId, moduleNames, moduleCredits) {
            console.log("Creating pie chart for course ID:", courseId);
            var ctx = document.getElementById('pieChart' + courseId).getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: moduleNames,
                    datasets: [{
                        data: moduleCredits,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
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
                            text: 'Modules Distribution for Course ' + courseId
                        }
                    }
                }
            });
        }

        function createBarChart(courseLabels, datasets) {
            console.log("Creating bar chart with labels:", courseLabels);
            console.log("Datasets:", datasets);
            var ctx = document.getElementById('barChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: courseLabels,
                    datasets: datasets
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
            console.log("Chart.js loaded:", typeof Chart !== 'undefined');

            <?php foreach ($courses as $course): ?>
                var moduleNames = [];
                var moduleCredits = [];
                <?php foreach ($modules_by_course[$course['id']] as $module): ?>
                    moduleNames.push("<?php echo $module['module_name']; ?>");
                    moduleCredits.push(<?php echo $module['credits']; ?>);
                <?php endforeach; ?>

                console.log("Calling createPieChart for course ID:", <?php echo $course['id']; ?>);
                createPieChart(<?php echo $course['id']; ?>, moduleNames, moduleCredits);
            <?php endforeach; ?>

            <?php if (count($courses) > 1): ?>
                var barLabels = [];
                var barDatasets = [];

                <?php foreach ($courses as $course): ?>
                    var courseModules = <?php echo json_encode($modules_by_course[$course['id']]); ?>;
                    var courseLabel = '<?php echo $course['title']; ?>';
                    var courseData = [];
                    var courseModuleNames = [];

                    for (var i = 0; i < courseModules.length; i++) {
                        courseModuleNames.push(courseModules[i].module_name);
                        courseData.push(courseModules[i].credits);
                    }

                    if (barLabels.length === 0) {
                        barLabels = courseModuleNames;
                    }

                    barDatasets.push({
                        label: courseLabel,
                        data: courseData,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    });
                <?php endforeach; ?>

                console.log("Bar Labels:", barLabels);
                console.log("Bar Datasets:", barDatasets);

                createBarChart(barLabels, barDatasets);
            <?php endif; ?>
        };
    </script>
</head>
<body>
    <header>
        <h1>Course Report</h1>
    </header>
    <main>
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
    </main>
    <footer>&copy; CSYM019 2024</footer>
</body>
</html>
