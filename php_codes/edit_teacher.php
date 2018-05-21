<?php
require_once "pdo.php";
session_start();
if ( ! isset($_SESSION['id'])) {
    die('ACCESS DENIED');
}	
if ( isset($_POST['cancel']) ) {
	if($_SESSION['type']===1)
		header('Location: view.php');
	else
		header('Location: teacher_info.php');
    return;
}

// Guardian: Make sure that user_id is present
if ( ! isset($_GET['t_id']) ) {
	$_SESSION['failure'] = 'Invalid Teacher ID';
	header('Location: teacher_info.php');
	return;
}

$stmt = $pdo->prepare("SELECT * FROM teacher where t_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['t_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
	$_SESSION['failure'] = 'Invalid Teacher ID';
    header( 'Location: teacher_info.php' ) ;
    return;
}

$tid = htmlentities($row['t_id']);
$name = htmlentities($row['t_name']);
$pw = htmlentities($row['t_pw']);

if ( isset($_POST['tid']) && isset($_POST['name']) && isset($_POST['pw']) ){
	if( strlen($_POST['tid'])<1 || strlen($_POST['name']) < 1 || strlen($_POST['pw']) < 1 ){
		$_SESSION['failure']='All fields are required';
		header( 'Location: edit_teacher.php?t_id='.$_REQUEST['t_id'] ) ;
		return;
	}
	else
	{
	$salt = 'XyZzy12*_';
	$hashed_pw = hash('md5', $salt.$_POST['pw']);	//store pw in hashed format 
	$_SESSION['check']=$hashed_pw;
	$sql = "UPDATE teacher SET 
            t_name = :name, t_pw = :pw
            WHERE t_id = :tid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':tid' => $_POST['tid'],
        ':name' => $_POST['name'],
        ':pw' => $hashed_pw));
    //$_SESSION['success'] = 'Record of teacher with ID '.$_POST["tid"].' is edited.';
	if($_SESSION['type']===1) {
		$_SESSION['success'] = 'Your profile have been updated.';
		header('Location: view.php');
	}
	else {
		$_SESSION['success'] = 'Record of teacher with ID '.$_POST["tid"].' is edited.';
        header( 'Location: teacher_info.php' ) ;
	}
    return;
	}
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Editing teacher info</title>
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
<h4>Edit teacher info with ID <?= $tid ?></h4>
<form method="post">
<input type="hidden" name="tid" size="40" value="<?= $tid ?>"/>
<p>Name<input type="text" name="name" size="40" value="<?= $name ?>"/></p>
<p>Password<input type="password" name="pw" size="15" value=""/></p>
<input type="submit" value="Save">
<input type="submit" name="cancel" value="Cancel">
</form>
<p>
</div>
</body>
</html>