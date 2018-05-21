<?php
require_once "pdo.php";
session_start();

$salt = 'XyZzy12*_';

if ( ! isset($_SESSION['id']) || $_SESSION['type']!==0  ) {
    die('ACCESS DENIED');
}
if ( isset($_POST['cancel']) ) {
    header('Location: teacher_info.php');
    return;
}

if ( isset($_POST['T_id']) && isset($_POST['name']) && isset($_POST['pw']) ){
	if(strlen($_POST['T_id']) < 1 || strlen($_POST['name']) < 1 ||strlen($_POST['pw']) < 1 ){
		$_SESSION['failure']='All fields are required';
		header( 'Location: add_teachers.php' ) ;
		return;
	}
	else{
	$stmt = $pdo->prepare("SELECT * FROM teacher where t_id = :xyz");
	$stmt->execute(array(":xyz" => $_POST['T_id']));
	$already_exist = $stmt->fetch(PDO::FETCH_ASSOC);
	if ( $already_exist) {
		$_SESSION['failure']= "Teacher with this ID already exists! Chose another!!";
		header( 'Location: add_teachers.php' ) ;
		return;
	}
	else{
		$hashed_pw = hash('md5', $salt.$_POST['pw']);	//store pw in hashed format 
		$_SESSION['check']=$hashed_pw;
		$stmt = $pdo->prepare('INSERT INTO teacher
			(t_id, t_name, t_pw) VALUES ( :T_id, :name, :pw)');
		$stmt->execute(array(
			':T_id' => $_POST['T_id'],
			':name' => $_POST['name'],
			':pw' => $hashed_pw));
		$_SESSION['success']='New teacher added';
		header('Location: teacher_info.php');
		return;
	}
	}
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Adding new teacher info</title>
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
<h3>Add information about a new teacher</h3>
<form method="post">
<p>Teacher ID:
<input type="text" name="T_id" size="40"/></p>
<p>Name:
<input type="text" name="name" size="40"/></p>
<p>Password:
<input type="password" name="pw" size="15"/></p>
<input type="submit" name='add' value="Add">
<input type="submit" name="cancel" value="Cancel">
</form>
<p>
</div>
</body>
</html>