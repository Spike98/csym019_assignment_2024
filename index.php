<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "university";

//δημιουργία σύνδεσης με database
$conn = new mysqli($servername, $username, $password, $dbname);

// έλεγχος σύνδεσης
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//διαγραφή επιλεγμένου μαθήματος
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    if (!empty($_POST['course_ids'])) {
        $course_ids = $_POST['course_ids'];
        
        // Δημιουργία placeholders για την SQL εντολή
        $placeholders = implode(',', array_fill(0, count($course_ids), '?'));
        
        $sql = "DELETE FROM courses WHERE id IN ($placeholders)";
        $stmt = $conn->prepare($sql);
        
        // Ορισμός των τύπων παραμέτρων για bind_param
        $types = str_repeat('i', count($course_ids));
        $stmt->bind_param($types, ...$course_ids);      
                
        if ($stmt->execute() === TRUE) {
            echo "<p>Course deleted successfully</p>";
        } else {
            echo "<p>Error deleting course: " . $conn->error . "</p>";
        }
      $stmt->close();
    } else {
        echo "<p>No course selected</p>";
    }
}

//ανάκτηση δεδομένων από τον πίνακα courses
$sql = "SELECT id, title, overview, highlights, course_details, entry_requirements, fees_funding, faqs FROM courses";
$result = $conn->query($sql);

//επέλεξα να ενσωματώσω το html εντός του php αρχείου γιατί όταν
//προσπαθούσα να το κάνω με 2 ξεχωριστά δεν μου δούλευε σωστά
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course List</title>
    <link rel="stylesheet" href="pageStyle.css">
</head>
<body>
    <header>
        <h1>University Courses</h1>
    </header>
    <div class="sidebar">
        <a href="index.php" class="active">Homepage</a>
        <a href="newCourse.php">New Course</a>        
    </div>
    <main>
        <h1>Select Courses for Report</h1>
        <form action="index.php" method="post">
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
                    <?php if ($result->num_rows > 0): ?> <!-- έλεγχος για ήδη υπάρχουσες εγγραφές -->
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                            <!-- δημιουργία checkbox  για καθε μάθημα και συσχετισμός 
                             τιμής checkbox με το id επιλεγμένου μαθήματος -->
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
                            <!-- εμφάνιση μηνύματος αν δεν υπάρχουν εγγραφές -->
                        <tr>
                            <td colspan="8">No courses found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <input type="submit" value="Generate Report" class="generate-button">
            <input type="submit" name="delete" value="Delete Selected Course" class="delete-button">
        </form>
    </main>
    <footer>&copy; CSYM019 2024</footer>
</body>
</html>

<?php
//τέλος σύνδεσης με Database
$conn->close();
?>
