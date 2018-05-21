<?php
require_once "pdo.php";
session_start();
if ( ! isset($_SESSION['id']) || $_SESSION['type']!==0 ) {
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
<title>Students' info</title>
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
?>
<h4> Students record</h4>
<?php
if ( isset($_SESSION['failure']) ) {
        echo('<p style="color: red;">'.htmlentities($_SESSION['failure'])."</p>\n");
        unset($_SESSION['failure']);
    }
	
if ( isset($_SESSION['success']) ) {
        echo('<p style="color: blue;">'.htmlentities($_SESSION['success'])."</p>\n");
        unset($_SESSION['success']);
}

if( isset($_SESSION['id'])) {
	$stmt = $pdo->query("SELECT * FROM students");
	if( $output = $stmt->fetchAll(PDO::FETCH_ASSOC)){
		echo('<center><table border="1">'."\n");
		echo("<thead><tr>
				<th>&nbsp;Student Roll no.&nbsp;</th>
				<th>&nbsp;Student's Name&nbsp;</th>
				<th>&nbsp;Student's RFID&nbsp;</th>
				<th>&nbsp;Actions &nbsp;</th>
				</tr></thead>");
		foreach ( $output as $output ) {
			echo ("<tr>");
			echo ("<td><center>".htmlentities($output['s_id'])."</td>");
			echo ("<td><center>".htmlentities($output['s_name'])."</td>");
			echo ("<td><center>".htmlentities($output['s_rfid'])."</td>");
			echo ('<td><center><a href="edit_student.php?s_id='.$output['s_id'].'">&nbsp;Edit&nbsp;</a> / <a href="delete_student.php?s_id='.$output['s_id'].'">&nbsp;Delete&nbsp;</a></td>');
		}
		echo("</tr></table></center>");
	}
	else {
		echo ("No student available!");
	}
}
?>
<form method="POST" >
<p><a href='add_student.php'>Add a new student</a></p>
<input type="submit" value="Log Out" name="log_out">
<input type="submit" value="Cancel" name="cancel">
</form>
</div>
</body>
</html>