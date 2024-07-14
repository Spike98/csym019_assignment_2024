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

$conn = new mysqli($servername, $username, $password, $dbname);

// Έλεγχος σύνδεσης
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//αποθήκευση των δεδομένων της φόρμας κατα την υποβολη της στις αντιστοιχες μεταβλητές
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $overview = $_POST['overview'];
    $highlights = $_POST['highlights'];
    $entry_requirements = $_POST['entry_requirements'];
    $fees_funding = $_POST['fees_funding'];
    $faqs = $_POST['faqs'];

    //εκτέλεση sql εντολης για εισαγωγη σε πινακα courses
    //χρηση prepare και bind_param ως best practice για αποφυγή injection
    //https://www.w3schools.com/php/php_mysql_prepared_statements.asp
    $sql = "INSERT INTO courses (title, overview, highlights, entry_requirements, fees_funding, faqs) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $title, $overview, $highlights, $entry_requirements, $fees_funding, $faqs);

    if ($stmt->execute() === TRUE) {
        $course_id = $stmt->insert_id; //id νεου μαθήματος που εισάγεται
        $modules = $_POST['module_name'];
        $credits = $_POST['module_credits'];
        
        //ενημέρωση πίνακα modules
        //εκτέλεση sql εντολης για εισαγωγη σε πινακα modules
        //χρηση prepare και bind_param ως best practice για αποφυγή injection
        //https://www.w3schools.com/php/php_mysql_prepared_statements.asp
        $module_sql = "INSERT INTO modules (course_id, module_name, credits) VALUES (?, ?, ?)";
        $module_stmt = $conn->prepare($module_sql);
       
        foreach ($modules as $index => $module_name) {
            $module_credits = $credits[$index];
            $module_stmt->bind_param("isi", $course_id, $module_name, $module_credits);
            $module_stmt->execute();
        }
        
        echo "<p>New course added successfully</p>"; //επιβεβαίωση καταχώρησης
    } else {
        echo "<p>Error: " . $sql . "<br>" . $conn->error . "</p>";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Course</title>
    <link rel="stylesheet" href="pageStyle.css">
    <script>
        function toggleSidebar() {
            var sidebar = document.querySelector('.sidebar');
            var main = document.querySelector('.main');
            if (sidebar.style.width === '0px' || sidebar.style.width ==='') {
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
            <h1>Add New Course</h1>
        </header>
    </div>
    <div class="sidebar">
        <a href="index.php">Homepage</a>
        <a href="newCourse.php" class="active">New Course</a>        
    </div>
    <div class="main">
        <h1>Enter course Details</h1>
        <form action="newCourse.php" method="post">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required><br>

            <label for="overview">Overview:</label>
            <textarea id="overview" name="overview" required></textarea><br>

            <label for="highlights">Highlights:</label>
            <textarea id="highlights" name="highlights" required></textarea><br>

            <label for="course_details">Course Details:</label>
            <textarea id="course_details" name="course_details" required></textarea><br>

            <label for="entry_requirements">Entry Requirements:</label>
            <textarea id="entry_requirements" name="entry_requirements" required></textarea><br>

            <label for="fees_funding">Fees and Funding:</label>
            <textarea id="fees_funding" name="fees_funding" required></textarea><br>

            <label for="faqs">FAQs:</label>
            <textarea id="faqs" name="faqs" required></textarea><br>

            <label>Modules:</label>
            <div id="modules">
                <!-- δυναμικά πεδία για modules -->
            </div>
            <button  type="button" onclick="addModule()">Add Module</button>
            <button  type="button" onclick="removeModule()">Remove Module</button><br>

            <input class="addCourseBtn" type="submit" value="Add Course" class="add-button">
        </form>
    </div>
    <footer>&copy; CSYM019 2024</footer>
    <script src="newCourse.js"></script>
</body>
</html>