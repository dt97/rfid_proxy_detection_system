<?php
require_once "pdo.php";
session_start();
if ( ! isset($_SESSION['id'])) {
    die('ACCESS DENIED');
}
  
$columns = array(
    0 => 'd1', 
    1 => 'd2',
    2 => 'd3',
	3 => 'd4', 
    4 => 'd5',
    5 => 'd6',
	6 => 'd7', 
    7 => 'd8',
    8 => 'd9',
	9 => 'd10', 
    10 => 'd11', 
    11 => 'd12',
    12 => 'd13',
	13 => 'd14', 
    14 => 'd15',
    15 => 'd16',
	16 => 'd17', 
    17 => 'd18',
    18 => 'd19',
	19 => 'd20'
	
  );
  $error = false;
  $colVal = '';
  $colIndex = $rowId = 0;
  
  $msg = array('status' => !$error, 'msg' => 'Failed! updation in mysql');
 
  if(isset($_POST)){
    if(isset($_POST['val']) && $_POST['val']!=NULL && $_POST['val']<=1 && !$error) {
      $colVal = $_POST['val'];
      $error = false;
      
    } else {
      $error = true;
    }
    if(isset($_POST['index']) && $_POST['index'] >= 0 && !$error) {
      $colIndex = $_POST['index'];
      $error = false;
    } else {
      $error = true;
    }
    if(isset($_POST['id']) && $_POST['id'] > 0 && !$error) {
      $rowId = $_POST['id'];
      $error = false;
    } else {
      $error = true;
    }
  
    if(!$error) {
		$table="Course_".$_SESSION['class']."_table";
		//echo($colIndex);
		//echo($columns);
		//echo($columns[$colIndex]);
        $sql = "UPDATE $table SET $columns[$colIndex] = :col WHERE s_id=:sid ";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array(
		':col' => $colVal,
        ':sid' => $rowId));
        $msg = array('error' => $error, 'msg' => 'Success! Attendance updated :)');
	} else {
		$msg = array('error' => $error, 'msg' => 'Failed! Attendance not updated: NULL value or invalid character');
    }
  }
  
  // send data as json format
  echo json_encode($msg);
  
?>