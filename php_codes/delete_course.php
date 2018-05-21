<?php
require_once "pdo.php";
session_start();
if ( ! isset($_SESSION['id']) || $_SESSION['type']!==0) {
    die('ACCESS DENIED');
}	
if ( isset($_POST['cancel']) ) {
    header('Location: courses_info.php');
    return;
}
if ( isset($_POST['delete']) && isset($_POST['c_id']) ) {
	$table="Course_".$_GET['c_id']."_table";
	$sql_delete="DROP table $table";
	$creat=$pdo->exec($sql_delete);
    $sql = "DELETE FROM courses WHERE c_id= :dd";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':dd' => $_POST['c_id']));
    $_SESSION['success'] = 'Record of course with ID '.$_POST['c_id'].' is deleted';
    header( 'Location: courses_info.php' ) ;
    return;
}

// Guardian: Make sure that t_id is present
if ( ! isset($_GET['c_id']) ) {
  $_SESSION['failure'] = 'Invalid Course ID';
  header('Location: courses_info.php');
  return;
}
$stmt = $pdo->prepare("SELECT c_name, c_id FROM courses where c_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['c_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
	$_SESSION['failure']= "No such course exist!";
    header( 'Location: courses_info.php' ) ;
    return;
}

?>
<html>
<head>
<title>Deleting course entry</title>
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
<h4> Delete course entry</h4>
<p>Confirm: Deleting <?= htmlentities($row['c_name']) ?></p>
<form method="post">
<input type="hidden" name="c_id" value="<?= $row['c_id'] ?>"> 
<input type="submit" value="Delete" name="delete">
<input type="submit" value="Cancel" name="cancel">
</form>
</div>
</body>