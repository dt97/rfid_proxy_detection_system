<?php
require_once "pdo.php";
session_start();

if ( ! isset($_SESSION['id']) || $_SESSION['type']!==0  ) {
    die('ACCESS DENIED');
}
if ( isset($_POST['cancel']) ) {
    header('Location: students_info.php');
    return;
}

if ( isset($_POST['S_id']) && isset($_POST['name']) && isset($_POST['rfid']) ){
	if(strlen($_POST['S_id']) < 1 || strlen($_POST['name']) < 1 ||strlen($_POST['rfid']) < 1 ){
		$_SESSION['failure']='All fields are required';
		header( 'Location: add_student.php' ) ;
		return;
	}
	else{
	$stmt = $pdo->prepare("SELECT * FROM students where s_id = :xyz");
	$stmt->execute(array(":xyz" => $_POST['S_id']));
	$already_exist = $stmt->fetch(PDO::FETCH_ASSOC);
	if ( $already_exist) {
		$_SESSION['failure']= "Student with this Roll no. already exists! Assign another roll no.!!";
		header( 'Location: add_student.php' ) ;
		return;
	}
	else{
		$stmt = $pdo->prepare('INSERT INTO students
			(s_id, s_name, s_rfid) VALUES ( :S_id, :name, :rfid)');
		$stmt->execute(array(
			':S_id' => $_POST['S_id'],
			':name' => $_POST['name'],
			':rfid' => $_POST['rfid']));
		$_SESSION['success']='New student added';
		header('Location: students_info.php');
		return;
	}
	}
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Adding new student info</title>
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

if ( isset($_SESSION['failure']) ) {
        echo('<p style="color: red;">'.htmlentities($_SESSION['failure'])."</p>\n");
        unset($_SESSION['failure']);
    }
?>
<h3>Add information about a new student</h3>
<form method="post">
<p>Student Roll no.:
<input type="text" name="S_id" size="40"/></p>
<p>Name:
<input type="text" name="name" size="40"/></p>
<p>RFID:
<input type="text" name="rfid" size="20"/></p>
<input type="submit" name='add' value="Add">
<input type="submit" name="cancel" value="Cancel">
</form>
<p>
</div>
</body>
</html>