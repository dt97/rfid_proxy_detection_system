<?php
require_once "pdo.php";
session_start();
if ( ! isset($_SESSION['id']) ) {
    die('ACCESS DENIED');
}
// If the user requested logout go back to index.php
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Main Page</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/detail.css">
</head>
<body>
<div class="container">
<h1>Attendance System</h1><hr>
<h2> Welcome 
<?php
if( isset($_SESSION['id']) && $_SESSION['type']===0){             //ADMIN
	$stmt = $pdo->prepare("SELECT a_name FROM admin WHERE a_id=:id");
	$stmt->execute(array(
						':id' => $_SESSION['id']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	echo(" Admin ".$row['a_name']."</h2>");
	echo("<p><a href='teacher_info.php'>Teachers</a></p>");
	echo("<p><a href='courses_info.php'>Courses</a></p>");
	echo("<p><a href='students_info.php'>Student</a></p>");
}
else if( isset($_SESSION['id']) && $_SESSION['type']===1){        //TEACHER
	$stmt = $pdo->prepare("SELECT t_name FROM teacher WHERE t_id=:id");
	$stmt->execute(array(
						':id' => $_SESSION['id']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	echo(" Professor ".$row['t_name']."</h2>");
	if ( isset($_SESSION['success']) ) {
        echo('<p style="color: blue;">'.htmlentities($_SESSION['success'])."</p>\n");
        unset($_SESSION['success']);
    }
	echo('<p><a href="courses_info.php?t_id='.$_SESSION['id'].'">Courses</a></p>');
	echo('<p><a href="edit_teacher.php?t_id='.$_SESSION['id'].'">Edit your profile</a></p>');
}
?>
<form method="POST" action="logout.php">
<input type="submit" value="Log Out">
</form>
</div>
</body>
</html>