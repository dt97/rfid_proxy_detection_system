<?php
require_once "pdo.php";
session_start();
if ( ! isset($_SESSION['id'])) {
    die('ACCESS DENIED');
}

if ( isset($_POST['cancel']) ) {
    header('Location: roll_call.php?c_id='.$_SESSION['class']);
    return;
}

if (isset($_POST['student'])){
	$table="Course_".$_SESSION['class']."_table";
	$stmt = $pdo->prepare("SELECT * FROM $table where s_id = :xyz");
	$stmt->execute(array(":xyz" => $_POST['student']));
	$already_exist = $stmt->fetch(PDO::FETCH_ASSOC);
	if ( $already_exist) {
		$_SESSION['failure']="Student already enrolled in this course!";
		header('Location: enroll_student.php?c_id='.$_SESSION['class']);
		return;
	}
	else{
	$sql="INSERT INTO $table
			(s_id) VALUES ( :S_id)";
	$stmt=$pdo->prepare($sql);
	$stmt->execute(array(
			':S_id' => $_POST['student']));
	$sql2="UPDATE courses SET num=num+1 WHERE c_id=:cid";
	$stmt2=$pdo->prepare($sql2);
	$stmt2->execute(array(
			':cid' => $_SESSION['class']));
	$_SESSION['success']='Student enrolled';
	header('Location: roll_call.php?c_id='.$_SESSION['class']);
	return;
	}
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Enrolling Student </title>
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
	echo(" Admin ".$row['a_name']);
}
else if( isset($_SESSION['id']) && $_SESSION['type']===1){        //TEACHER
	$stmt = $pdo->prepare("SELECT t_name FROM teacher WHERE t_id=:id");
	$stmt->execute(array(
						':id' => $_SESSION['id']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	echo(" Professor ".$row['t_name']);
}
?>
</h2>
<?php
if ( isset($_SESSION['failure']) ) {
        echo('<p style="color: red;">'.htmlentities($_SESSION['failure'])."</p>\n");
        unset($_SESSION['failure']);
    }
?>
<h4>Choose a student from the list below</h4><br>
<form method="POST" >
<select name="student">
<?php
	$stmt = $pdo->query("SELECT * FROM students");
	if($row = $stmt->fetchAll(PDO::FETCH_ASSOC)){
	foreach($row as $row){
	echo('<option value="'.htmlentities($row['s_id']).'">'.htmlentities($row['s_name']).'</option>');
	}
	}
  ?>
</select><br><br>
<input type="submit" value="Enroll selected student" name="enroll">
<input type="submit" value="Cancel" name="cancel">
</form>
</div>
</body>
</html>