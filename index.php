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

// Δημιουργία σύνδεσης με database
$conn = new mysqli($servername, $username, $password, $dbname);

// Έλεγχος σύνδεσης
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    if (!empty($_POST['course_ids'])) {
        $course_ids = $_POST['course_ids'];

       // placeholders για την SQL εντολή διαγραφής courses/modules, η οποία θα χρησιμοποιηθεί πολλές φορές για διάφορα δεδομένα. 
       // Δημιουργούμε έναν πίνακα με αριθμό στοιχείων ίσο με τα course_ids που ειναι αποθηκευμένα στη database και κάθε στοιχείο έχει το σύμβολο “?”. 
       //μετατρέπουμε τον πίνακα σε συμβολοσειρά με χρήση της implode και προκύπτει μια συμβολοσειρά με μορφή: “?,?,?” 
        $placeholders = implode(',', array_fill(0, count($course_ids), '?'));

        // Διαγραφή σχετικών εγγραφών από τον πίνακα modules
        $sql_delete_modules = "DELETE FROM modules WHERE course_id IN ($placeholders)";
        $stmt_delete_modules = $conn->prepare($sql_delete_modules);
        $types = str_repeat('i', count($course_ids));
        //δέσμευση πραγματικών τιμών course_id σε αντίστοιχα placeholders
        $stmt_delete_modules->bind_param($types, ...$course_ids); //”...” διασπά το course id σε statements 
        $stmt_delete_modules->execute();    //εκτέλεση
        $stmt_delete_modules->close();      //κλείσιμο και αποδέσμευση

        // διαγραφή εγγραφών από τον πίνακα courses με την 
        // ίδια λογική όπως στον πίνακα modules, απλά έπρεπε πρώτα 
        // να διαγράφονται τα modules αφού εμπεριέχουν το course_id που τα συνδέει 
        // με τον πίνακα courses 
        $sql_delete_courses = "DELETE FROM courses WHERE id IN ($placeholders)";
        $stmt_delete_courses = $conn->prepare($sql_delete_courses);
        $stmt_delete_courses->bind_param($types, ...$course_ids);
        if ($stmt_delete_courses->execute() === TRUE) {
            echo "<p>Course deleted successfully</p>";
        } else {
            echo "<p>Error deleting course: " . $conn->error . "</p>";
        }
        $stmt_delete_courses->close();
    } else {
        echo "<p>No course selected</p>";
    }
}

// Ανάκτηση δεδομένων από τον πίνακα courses
$sql = "SELECT id, title, overview, highlights, course_details, entry_requirements, fees_funding, faqs FROM courses";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course List</title>
    <link rel="stylesheet" href="pageStyle.css">
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
    </script>
</head>
<body>
    <div class="header-wrapper">
        <div class="toggle-btn" onclick="toggleSidebar()">☰</div>
        <header>
            <h1>University Courses</h1>
        </header>
    </div>
    <div class="sidebar">
        <a href="index.php" class="active">Homepage</a>
        <a href="newCourse.php">New Course</a>
    </div>
    <div class="main">
        <h1>Select Courses for Report</h1>
        <form action="" method="post">
            <table border="1">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Title</th>
                        <th>Overview</th>
                        <th>Highlights</th>
                        <th>Course Details</th>
                        <th>Entry Requirements</th>
                        <th>Fees and Funding</th>
                        <th>FAQs</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><input type="checkbox" name="course_ids[]" value="<?php echo $row['id']; ?>"></td>
                                <td><?php echo $row["title"]; ?></td>
                                <td><?php echo $row["overview"]; ?></td>
                                <td><?php echo $row["highlights"]; ?></td>
                                <td><?php echo $row["course_details"]; ?></td>
                                <td><?php echo $row["entry_requirements"]; ?></td>
                                <td><?php echo $row["fees_funding"]; ?></td>
                                <td><?php echo $row["faqs"]; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">No courses found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <input type="submit" name="generate_report" value="Generate Report" class="generate-button" formaction="courseReport.php">
            <input type="submit" name="delete" value="Delete Selected Course" class="delete-button">
        </form>
    </div>
    <footer>&copy; CSYM019 2024</footer>
</body>
</html>

<?php
// Τέλος σύνδεσης με Database
$conn->close();
?>
