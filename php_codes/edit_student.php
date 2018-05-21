<?php
require_once "pdo.php";
session_start();
if ( ! isset($_SESSION['id']) || $_SESSION['type']!==0 ) {
    die('ACCESS DENIED');
}	
if ( isset($_POST['cancel']) ) {
	header('Location: students_info.php');
    return;
}

// Guardian: Make sure that user_id is present
if ( ! isset($_GET['s_id']) ) {
	$_SESSION['failure'] = 'Invalid Student ID';
	header('Location: students_info.php');
	return;
}

$stmt = $pdo->prepare("SELECT * FROM students where s_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['s_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
	$_SESSION['failure'] = 'Invalid Student ID';
    header( 'Location: students_info.php' ) ;
    return;
}

$sid = htmlentities($row['s_id']);
$name = htmlentities($row['s_name']);
$rfid = htmlentities($row['s_rfid']);

if ( isset($_POST['sid']) && isset($_POST['name']) && isset($_POST['rfid']) ){
	if( strlen($_POST['sid'])<1 || strlen($_POST['name']) < 1 || strlen($_POST['rfid']) < 1 ){
		$_SESSION['failure']='All fields are required';
		header( 'Location: edit_student.php?s_id='.$_REQUEST['s_id'] ) ;
		return;
	}
	else
	{
	$sql = "UPDATE students SET 
            s_name = :name, s_rfid = :rfid
            WHERE s_id = :sid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':sid' => $_POST['sid'],
        ':name' => $_POST['name'],
        ':rfid' => $_POST['rfid']));
		$_SESSION['success'] = 'Record of student with Roll no. '.$_POST["sid"].' is edited.';
        header( 'Location: students_info.php' ) ;
		return;
	}
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Editing student info</title>
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
<h4>Edit Student info with Roll no. <?= $sid ?></h4>
<form method="post">
<input type="hidden" name="sid" size="40" value="<?= $sid ?>"/>
<p>Student Name<input type="text" name="name" size="40" value="<?= $name ?>"/></p>
<p>Student RFID<input type="text" name="rfid" size="20" value="<?= $rfid ?>"/></p>
<input type="submit" value="Save">
<input type="submit" name="cancel" value="Cancel">
</form>
<p>
</div>
</body>
</html>