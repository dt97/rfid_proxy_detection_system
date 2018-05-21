<?php
require_once "pdo.php";
session_start();
if ( ! isset($_SESSION['id']) || $_SESSION['type']!==0) {
    die('ACCESS DENIED');
}	
if ( isset($_POST['cancel']) ) {
    header('Location: students_info.php');
    return;
}
if ( isset($_POST['delete']) && isset($_POST['s_id']) ) {
	$sql2 ="SELECT c_id FROM courses";                 //check all courses table to match s_id and decrease number of students in that course if match
	$stmt2 = $pdo->prepare($sql2);
	$stmt2->execute();
	$row1 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
	foreach($row1 as $row1){
	$table="course_".$row1['c_id']."_table";
	$sql3 = "SELECT count(*) FROM $table WHERE s_id= :dd";
	$stmt3 = $pdo->prepare($sql3);
	$stmt3->execute(array(':dd' => $_POST['s_id']));
	$row2= $stmt3->fetch(PDO::FETCH_ASSOC);
	echo($row2['count(*)']);
	if( $row2['count(*)']==1){
		$sql4 = "UPDATE courses SET num=num-1 WHERE c_id=:cc";
		$stmt4 = $pdo->prepare($sql4);
		$stmt4->execute(array(':cc' => $row1['c_id']));
	}
	}
    $sql = "DELETE FROM students WHERE s_id= :dd";   //delete from student table
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':dd' => $_POST['s_id']));
    $_SESSION['success'] = 'Record of student with Roll no. '.$_POST['s_id'].' is deleted';  //success msg
    header( 'Location: students_info.php' ) ;
    return;
}

// Guardian: Make sure that s_id is present
if ( ! isset($_GET['s_id']) ) {
  $_SESSION['failure'] = 'Invalid Student ID';
  header('Location: students_info.php');
  return;
}
$stmt = $pdo->prepare("SELECT s_name, s_id FROM students where s_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['s_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
	$_SESSION['failure']= "No such student exist!";
    header( 'Location: students_info.php' ) ;
    return;
}

?>
<html>
<head>
<title>Deleting student entry</title>
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
	$admin = $stmt->fetch(PDO::FETCH_ASSOC);
	echo(" Admin ".$admin['a_name']."</h2>");
}

if ( isset($_SESSION['failure']) ) {
        echo('<p style="color: red;">'.htmlentities($_SESSION['failure'])."</p>\n");
        unset($_SESSION['failure']);
    }
?>
<h4> Delete student entry</h4>
<p>Confirm: Deleting <?= htmlentities($row['s_name']) ?></p>
<form method="post">
<input type="hidden" name="s_id" value="<?= $row['s_id'] ?>"> 
<input type="submit" value="Delete" name="delete">
<input type="submit" value="Cancel" name="cancel">
</form>
</div>
</body>