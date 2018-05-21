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
if ( isset($_POST['unenroll']) && isset($_POST['s_id']) ) {
	$table="Course_".$_SESSION['class']."_table";
    $sql = "DELETE FROM $table WHERE s_id= :dd";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':dd' => $_POST['s_id']));
	$sql2="UPDATE courses SET num=num-1 WHERE c_id=:cid";
	$stmt2=$pdo->prepare($sql2);
	$stmt2->execute(array(
			':cid' => $_SESSION['class']));
    $_SESSION['success'] = 'Student with Roll no. '.$_POST['s_id'].' is unenrolled from this course';
    header( 'Location: roll_call.php?c_id='.$_SESSION['class']) ;
    return;
}

// Guardian: Make sure that s_id is present
if ( ! isset($_GET['s_id']) ) {
  $_SESSION['failure'] = 'Invalid Student ID';
  header('Location: roll_call.php?c_id='.$_SESSION['class']);
  return;
}
//$table="Course_".$_SESSION['class']."_table";
$stmt = $pdo->prepare("SELECT  s_id,s_name FROM students where s_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['s_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
	$_SESSION['failure']= "No such student exist!";
    header( 'Location: roll_call.php?c_id='.$_SESSION['class'] ) ;
    return;
}

?>
<html>
<head>
<title>Unenrolling student</title>
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
else if( isset($_SESSION['id']) && $_SESSION['type']===1){        //TEACHER
	$stmt = $pdo->prepare("SELECT t_name FROM teacher WHERE t_id=:id");
	$stmt->execute(array(
						':id' => $_SESSION['id']));
	$row1 = $stmt->fetch(PDO::FETCH_ASSOC);
	echo(" Professor ".$row1['t_name']."</h2>");
}

if ( isset($_SESSION['failure']) ) {
        echo('<p style="color: red;">'.htmlentities($_SESSION['failure'])."</p>\n");
        unset($_SESSION['failure']);
    }
?>
<h4> Unenroll student from course <?=$_SESSION['class']?></h4>
<p>Confirm: Unenrolling <?= htmlentities($row['s_name']) ?></p>
<form method="post">
<input type="hidden" name="s_id" value="<?= $row['s_id'] ?>"> 
<input type="submit" value="Unenroll" name="unenroll">
<input type="submit" value="Cancel" name="cancel">
</form>
</div>
</body>