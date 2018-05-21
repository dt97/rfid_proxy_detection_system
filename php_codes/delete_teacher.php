<?php
require_once "pdo.php";
session_start();
if ( ! isset($_SESSION['id']) || $_SESSION['type']!==0) {
    die('ACCESS DENIED');
}	
if ( isset($_POST['cancel']) ) {
    header('Location: teacher_info.php');
    return;
}
if ( isset($_POST['delete']) && isset($_POST['t_id']) ) {
    $sql = "DELETE FROM teacher WHERE t_id= :dd";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':dd' => $_POST['t_id']));
    $_SESSION['success'] = 'Record of teacher with ID '.$_POST['t_id'].' is deleted';
    header( 'Location: teacher_info.php' ) ;
    return;
}

// Guardian: Make sure that t_id is present
if ( ! isset($_GET['t_id']) ) {
  $_SESSION['failure'] = 'Invalid Teacher ID';
  header('Location: teacher_info.php');
  return;
}
$stmt = $pdo->prepare("SELECT t_name, t_id FROM teacher where t_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['t_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
	$_SESSION['failure']= "No such teacher exist!";
    header( 'Location: teacher_info.php' ) ;
    return;
}

?>
<html>
<head>
<title>Deleting teacher entry</title>
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
<h4> Delete teacher entry</h4>
<p>Confirm: Deleting <?= htmlentities($row['t_name']) ?></p>
<form method="post">
<input type="hidden" name="t_id" value="<?= $row['t_id'] ?>"> 
<input type="submit" value="Delete" name="delete">
<input type="submit" value="Cancel" name="cancel">
</form>
</div>
</body>