<?php
require_once "pdo.php";
session_start();
if ( ! isset($_SESSION['id'])) {
    die('ACCESS DENIED');
}
$_SESSION['class']=$_GET['c_id'];
// If the user requested logout go back to courses_info.php
if ( isset($_POST['cancel']) ) {
	unset($_SESSION['class']);
    header('Location: courses_info.php');
    return;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Attendance Sheet</title>
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>
<link rel="stylesheet" href="css/detail.css"/>
<script type="text/javascript" src="jquery-1.11.1.min.js"></script>
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
<h4> Daily Attendance record for <?=$_SESSION['class']?></h4>
<div id="msg" class="alert">
</div>
<?php
if ( isset($_SESSION['failure']) ) {
        echo('<p style="color: red;">'.htmlentities($_SESSION['failure'])."</p>\n");
        unset($_SESSION['failure']);
    }
	
if ( isset($_SESSION['success']) ) {
        echo('<p style="color: blue;">'.htmlentities($_SESSION['success'])."</p>\n");
        unset($_SESSION['success']);
}

if( isset($_SESSION['id'])):
	$_SESSION['class']=$_GET['c_id'];
	$table="Course_".$_GET['c_id']."_table";
	$stmt = $pdo->query("SELECT * FROM $table");
	$count=$stmt->rowCount();
	$arr= Array();
	if( $output = $stmt->fetchAll(PDO::FETCH_ASSOC)):
?>
		<table border="1">
		<thead><tr>
				<th><center>Student Roll no.</center></th>
				<th>&nbsp; Day 1 &nbsp;</th>
				<th>&nbsp; Day 2 &nbsp;</th>
				<th>&nbsp; Day 3 &nbsp;</th>
				<th>&nbsp; Day 4 &nbsp;</th>
				<th>&nbsp; Day 5 &nbsp;</th>
				<th>&nbsp; Day 6 &nbsp;</th>
				<th>&nbsp; Day 7 &nbsp;</th>
				<th>&nbsp; Day 8 &nbsp;</th>
				<th>&nbsp; Day 9 &nbsp;</th>
				<th>&nbsp; Day 10 &nbsp;</th>
				<th>&nbsp; Day 11 &nbsp;</th>
				<th>&nbsp; Day 12 &nbsp;</th>
				<th>&nbsp; Day 13 &nbsp;</th>
				<th>&nbsp; Day 14 &nbsp;</th>
				<th>&nbsp; Day 15 &nbsp;</th>
				<th>&nbsp; Day 16 &nbsp;</th>
				<th>&nbsp; Day 17 &nbsp;</th>
				<th>&nbsp; Day 18 &nbsp;</th>
				<th>&nbsp; Day 19 &nbsp;</th>
				<th>&nbsp; Day 20 &nbsp;</th>
				<th>&nbsp; Actions &nbsp;</th>
				</tr></thead>
		
		<?php foreach ( $output as $output ):?>
		<tr data-row-id="<?= $output['s_id']?>">
		<td><?php echo(htmlentities($output['s_id']));?></td>
		<td class="editable-col" contenteditable="true" col-index='0' oldVal ="<?php echo(htmlentities($output['d1']));?>"><?php echo(htmlentities($output['d1']));?></td>
		<td class="editable-col" contenteditable="true" col-index='1' oldVal ="<?php echo(htmlentities($output['d2']));?>"><?php echo(htmlentities($output['d2']));?></td>
		<td class="editable-col" contenteditable="true" col-index='2' oldVal ="<?php echo(htmlentities($output['d3']));?>"><?php echo(htmlentities($output['d3']));?></td>
		<td class="editable-col" contenteditable="true" col-index='3' oldVal ="<?php echo(htmlentities($output['d4']));?>"><?php echo(htmlentities($output['d4']));?></td>
		<td class="editable-col" contenteditable="true" col-index='4' oldVal ="<?php echo(htmlentities($output['d5']));?>"><?php echo(htmlentities($output['d5']));?></td>
		<td class="editable-col" contenteditable="true" col-index='5' oldVal ="<?php echo(htmlentities($output['d6']));?>"><?php echo(htmlentities($output['d6']));?></td>
		<td class="editable-col" contenteditable="true" col-index='6' oldVal ="<?php echo(htmlentities($output['d7']));?>"><?php echo(htmlentities($output['d7']));?></td>
		<td class="editable-col" contenteditable="true" col-index='7' oldVal ="<?php echo(htmlentities($output['d8']));?>"><?php echo(htmlentities($output['d8']));?></td>
		<td class="editable-col" contenteditable="true" col-index='8' oldVal ="<?php echo(htmlentities($output['d9']));?>"><?php echo(htmlentities($output['d9']));?></td>
		<td class="editable-col" contenteditable="true" col-index='9' oldVal ="<?php echo(htmlentities($output['d10']));?>"><?php echo(htmlentities($output['d10']));?></td>
		<td class="editable-col" contenteditable="true" col-index='10' oldVal ="<?php echo(htmlentities($output['d11']));?>"><?php echo(htmlentities($output['d11']));?></td>
		<td class="editable-col" contenteditable="true" col-index='11' oldVal ="<?php echo(htmlentities($output['d12']));?>"><?php echo(htmlentities($output['d12']));?></td>
		<td class="editable-col" contenteditable="true" col-index='12' oldVal ="<?php echo(htmlentities($output['d13']));?>"><?php echo(htmlentities($output['d13']));?></td>
		<td class="editable-col" contenteditable="true" col-index='13' oldVal ="<?php echo(htmlentities($output['d14']));?>"><?php echo(htmlentities($output['d14']));?></td>
		<td class="editable-col" contenteditable="true" col-index='14' oldVal ="<?php echo(htmlentities($output['d15']));?>"><?php echo(htmlentities($output['d15']));?></td>
		<td class="editable-col" contenteditable="true" col-index='15' oldVal ="<?php echo(htmlentities($output['d16']));?>"><?php echo(htmlentities($output['d16']));?></td>
		<td class="editable-col" contenteditable="true" col-index='16' oldVal ="<?php echo(htmlentities($output['d17']));?>"><?php echo(htmlentities($output['d17']));?></td>
		<td class="editable-col" contenteditable="true" col-index='17' oldVal ="<?php echo(htmlentities($output['d18']));?>"><?php echo(htmlentities($output['d18']));?></td>
		<td class="editable-col" contenteditable="true" col-index='18' oldVal ="<?php echo(htmlentities($output['d19']));?>"><?php echo(htmlentities($output['d19']));?></td>
		<td class="editable-col" contenteditable="true" col-index='19' oldVal ="<?php echo(htmlentities($output['d20']));?>"><?php echo(htmlentities($output['d20']));?></td>
		<td><a href="unenroll_student.php?s_id=<?=$output['s_id']?>">Un-enroll</a></td>
		<?php endforeach; ?>
		</tr></table>
	
	<?php else :
		echo ("No student enrolled!");
		endif;
	echo('<br><p><a href="enroll_student.php?c_id='.$_SESSION['class'].'">Enroll a new student in this class</a></p>');
	endif;
?>
<form action="roll_call.php?c_id=<?=$_GET['c_id']?>" method="POST" >

<br>
<input type="submit" value="Show records available" name="save">
<input type="submit" value="Go back to Courses list" name="cancel">
</form>
</div>
</body>
</html>

<script type="text/javascript">
$(document).ready(function(){
		$('td.editable-col').on('focusout', function() {
		data = {};
		data['val'] = $(this).text();
		data['id'] = $(this).parent('tr').attr('data-row-id');
		data['index'] = $(this).attr('col-index');
	    if($(this).attr('oldVal') === data['val'])
		return false;

		$.ajax({   
				  
					type: "POST",  
					url: "server.php",  
					cache:false,  
					data: data,
					dataType: "json",				
					success: function(response)  
					{   
						//$("#loading").hide();
						if(!response.error) {
							$("#msg").removeClass('alert-danger');
							$("#msg").addClass('alert-success').html(response.msg);
							$("#msg").fadeIn('slow');
							$("#msg").fadeOut('slow');
						} else {
							$("#msg").removeClass('alert-success');
							$("#msg").addClass('alert-danger').html(response.msg);
							$("#msg").fadeIn('slow');
							$("#msg").fadeOut('slow');
						}
						
						/*setTimeout(function() {
							$('#msg').fadeOut('fast');
							}, 2000); // <-- time in milliseconds*/
					}   
				});
	});
});
</script>