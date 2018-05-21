<?php
require_once "pdo.php";
session_start();

if ( ! isset($_SESSION['id']) || $_SESSION['type']!==0  ) {
    die('ACCESS DENIED');
}
if ( isset($_POST['cancel']) ) {
    header('Location: courses_info.php');
    return;
}

if ( isset($_POST['C_id']) && isset($_POST['cname']) && isset($_POST['crfid']) && isset($_POST['tid']) ){
	$stmt = $pdo->prepare("SELECT * FROM teacher where t_id = :xyz");
	$stmt->execute(array(':xyz' => $_POST['tid']));
	$teacher_exist = $stmt->fetch(PDO::FETCH_ASSOC);
	if(strlen($_POST['C_id']) < 1 || strlen($_POST['cname']) < 1 || strlen($_POST['crfid']) < 1 || strlen($_POST['tid']) < 1 ){
		$_SESSION['failure']='All fields are required';
		header( 'Location: add_course.php' ) ;
		return;
	}
	else if ($teacher_exist==0){
		$_SESSION['failure']='Given assigned teacher id doesnot exist!';
		header( 'Location: add_course.php' ) ;
		return;
	}
	else{
	$stmt = $pdo->prepare("SELECT * FROM courses where c_id = :xyz");
	$stmt->execute(array(':xyz' => $_POST['C_id']));
	$already_exist = $stmt->fetch(PDO::FETCH_ASSOC);
	if ( $already_exist) {
		$_SESSION['failure']= "Course with this ID already exists! Chose another!!";
		header( 'Location: add_course.php' ) ;
		return;
	}
	else{
		$stmt = $pdo->prepare('INSERT INTO courses
			(c_id, c_name, c_rfid, t_id) VALUES ( :cid, :cname, :crfid, :tid)');
		$stmt->execute(array(
			':cid' => $_POST['C_id'],
			':cname' => $_POST['cname'],
			':crfid' => $_POST['crfid'],
			':tid' => $_POST['tid']));
		//new table creation for each course	
		$table="Course_".$_POST['C_id']."_table";
		$sql= "CREATE table $table (
					s_id VARCHAR(255) NOT NULL,
					d1 enum('0','1') DEFAULT '0',
					d2 enum('0','1') DEFAULT '0',
					d3 enum('0','1') DEFAULT '0',
					d4 enum('0','1') DEFAULT '0',
					d5 enum('0','1') DEFAULT '0',
					d6 enum('0','1') DEFAULT '0',
					d7 enum('0','1') DEFAULT '0',
					d8 enum('0','1') DEFAULT '0',
					d9 enum('0','1') DEFAULT '0',
					d10 enum('0','1') DEFAULT '0',
					d11 enum('0','1') DEFAULT '0',
					d12 enum('0','1') DEFAULT '0',
					d13 enum('0','1') DEFAULT '0',
					d14 enum('0','1') DEFAULT '0',
					d15 enum('0','1') DEFAULT '0',
					d16 enum('0','1') DEFAULT '0',
					d17 enum('0','1') DEFAULT '0',
					d18 enum('0','1') DEFAULT '0',
					d19 enum('0','1') DEFAULT '0',
					d20 enum('0','1') DEFAULT '0',
					mark enum('0','1') DEFAULT '0',
					CONSTRAINT FOREIGN KEY (s_id) REFERENCES students (s_id)
					ON DELETE CASCADE ON UPDATE CASCADE,
					PRIMARY KEY (s_id)
					) ENGINE=InnoDB CHARACTER SET=utf8;";
		$creat=$pdo->exec($sql);
					
		//
		$_SESSION['success']='New course added';
		header('Location: courses_info.php');
		return;
	}
	}
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Adding new course</title>
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
<h3>Add information about a new course</h3>
<form method="post">
<p>Course ID:
<input type="text" name="C_id" size="40"/></p>
<p>Course Name:
<input type="text" name="cname" size="40"/></p>
<p>Course RFID no.:
<input type="text" name="crfid" size="40"/></p>
<p>Assigned Teacher ID:
<input type="text" name="tid" size="40"/></p>
<input type="submit" name='add' value="Add">
<input type="submit" name="cancel" value="Cancel">
</form>
<p>
</div>
</body>
</html>