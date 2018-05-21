<?php
require_once "pdo.php";
session_start();
if ( ! isset($_SESSION['id']) && $_SESSION['type']!==0) {
    die('ACCESS DENIED');
}	
if ( isset($_POST['cancel']) ) {
	header('Location: courses_info.php');
    return;
}

// Guardian: Make sure that user_id is present
if ( ! isset($_GET['c_id']) ) {
	$_SESSION['failure'] = 'Invalid Course ID';
	header('Location: courses_info.php');
	return;
}

$stmt = $pdo->prepare("SELECT * FROM courses where c_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['c_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
	$_SESSION['failure'] = 'Invalid course ID';
    header( 'Location: courses_info.php' ) ;
    return;
}

$cid = htmlentities($row['c_id']);
$cname = htmlentities($row['c_name']);
$crfid = htmlentities($row['c_rfid']);
$tid = htmlentities($row['t_id']);

if ( isset($_POST['cid']) && isset($_POST['cname']) && isset($_POST['crfid']) && isset($_POST['tid']) ){
	$stmt = $pdo->prepare("SELECT * FROM teacher where t_id = :xyz");
	$stmt->execute(array(':xyz' => $_POST['tid']));
	$teacher_exist = $stmt->fetch(PDO::FETCH_ASSOC);
	if(strlen($_POST['cid']) < 1 || strlen($_POST['cname']) < 1 || strlen($_POST['crfid']) < 1 || strlen($_POST['tid']) < 1 ){
		$_SESSION['failure']='All fields are required';
		header( 'Location: edit_courses.php?c_id='.$_REQUEST['c_id'] ) ;
		return;
	}
	else if ($teacher_exist==0){
		$_SESSION['failure']='Given assigned teacher id doesnot exist!';
		header( 'Location: edit_courses.php?c_id='.$_REQUEST['c_id'] ) ;
		return;
	}
	else {
	$sql = "UPDATE courses SET 
            c_name = :cname, c_rfid = :crfid, t_id= :tid
            WHERE c_id = :cid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
		':cid' => $_POST['cid'],
        ':cname' => $_POST['cname'],
        ':crfid' => $_POST['crfid'],
		':tid' => $_POST['tid']));
		$_SESSION['success'] = 'Record of course with ID '.$_POST["cid"].' is edited.';
        header( 'Location: courses_info.php' ) ;
	}
    return;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Editing course info</title>
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
<h4>Edit course info with ID <?= $cid ?></h4>
<form method="post">
<input type="hidden" name="cid" size="40" value="<?= $cid ?>"/>
<p>Course Name<input type="text" name="cname" size="40" value="<?= $cname ?>"/></p>
<p>Course RFID no.<input type="text" name="crfid" size="20" value="<?= $crfid ?>"/></p>
<p>Assigned teacher ID<input type="text" name="tid" size="40" value="<?= $tid ?>"/></p>
<input type="submit" value="Save">
<input type="submit" name="cancel" value="Cancel">
</form>
<p>
</div>
</body>
</html>