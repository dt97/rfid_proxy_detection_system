<?php
require_once "pdo.php";
session_start();
if ( ! isset($_SESSION['id']) ) {
    die('ACCESS DENIED');
}
// If the user requested logout go back to index.php
if ( isset($_POST['cancel']) ) {
    header('Location: view.php');
    return;
}

if (isset($_POST['log_out'])) {
	header('Location: logout.php');
	return;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Courses's info</title>
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
}
else if( isset($_SESSION['id']) && $_SESSION['type']===1){        //TEACHER
	$stmt = $pdo->prepare("SELECT t_name FROM teacher WHERE t_id=:id");
	$stmt->execute(array(
						':id' => $_SESSION['id']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	echo(" Professor ".$row['t_name']."</h2>");
}
?>
<h4> Courses record</h4>
<?php
if ( isset($_SESSION['failure']) ) {
        echo('<p style="color: red;">'.htmlentities($_SESSION['failure'])."</p>\n");
        unset($_SESSION['failure']);
    }
	
if ( isset($_SESSION['success']) ) {
        echo('<p style="color: blue;">'.htmlentities($_SESSION['success'])."</p>\n");
        unset($_SESSION['success']);
}
	
/*if ( isset($_SESSION['check']) ) {
        echo('<p style="color: red;">'.htmlentities($_SESSION['check'])."</p>\n");
        unset($_SESSION['check']);
}*/

if( isset($_SESSION['id'])) {
	if ($_SESSION['type']===0){
	$stmt = $pdo->query("SELECT c_id, c_name, c_rfid, courses.t_id, t_name, num FROM courses JOIN teacher ON courses.t_id=teacher.t_id");
	if( $output = $stmt->fetchAll(PDO::FETCH_ASSOC)){
		echo('<center><table border="1">'."\n");
		echo("<thead><tr>
				<th>&nbsp;Course ID&nbsp;</th>
				<th>&nbsp;Course Name&nbsp;</th>
				<th>&nbsp;Course RFID Card no.&nbsp;</th>
				<th>&nbsp;Assigned Teacher ID&nbsp;</th>
				<th>&nbsp;Teacher name&nbsp;</th>
				<th>&nbsp;Number of Students&nbsp;</th>
				<th>&nbsp;Actions&nbsp;</th>
				</tr></thead>");
		foreach ( $output as $output ) {
			echo ("<tr>");
			echo ("<td><center>".htmlentities($output['c_id'])."</td>");
			echo ('<td><center><a href="roll_call.php?c_id='.$output['c_id'].'">'.htmlentities($output['c_name'])."</a></td>");
			echo ("<td><center>".htmlentities($output['c_rfid'])."</td>");
			echo ("<td><center>".htmlentities($output['t_id'])."</td>");
			echo ("<td><center>".htmlentities($output['t_name'])."</td>");
			echo ("<td><center>".htmlentities($output['num'])."</td>");
			echo ('<td><a href="edit_courses.php?c_id='.$output['c_id'].'">&nbsp;Edit&nbsp;</a> / <a href="delete_course.php?c_id='.$output['c_id'].'">&nbsp;Delete&nbsp;</a></td>');
		}
		echo("</tr></table></center>");
	}
	else {
		echo ("No courses available!");
		}
	}
	else if ($_SESSION['type']===1) {
		$stmt = $pdo->prepare("SELECT * FROM courses WHERE t_id=:tid");
		$stmt->execute(array(
						':tid' => $_SESSION['id']));
		if( $output = $stmt->fetchAll(PDO::FETCH_ASSOC)){
			echo('<table border="1">'."\n");
			echo("<thead><tr>
				<th>Course ID</th>
				<th>Course Name</th>
				<th>Course RFID Card no.</th>
				<th>Number of Students</th>
				</tr></thead>");
			foreach ( $output as $output ) {
				echo ("<tr>");
				echo ('<td>'.htmlentities($output['c_id'])."</td>");
				echo ('<td><a href="roll_call.php?c_id='.$output['c_id'].'">'.htmlentities($output['c_name'])."</td>");
				echo ("<td>".htmlentities($output['c_rfid'])."</td>");
				echo ("<td>".htmlentities($output['num'])."</td>");
				
			}
		echo("</tr></table>");
		}
		else {
		echo ("No courses available!");
		}
	}
}
?>
<form method="POST" >
<?php
if($_SESSION['type']===0)
echo('<p><a href="add_course.php">Add a new course</a></p>');
?>
<input type="submit" value="Log Out" name="log_out">
<input type="submit" value="Cancel" name="cancel">
</form>
</div>
</body>
</html>