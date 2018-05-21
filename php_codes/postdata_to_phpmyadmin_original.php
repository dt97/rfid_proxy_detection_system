<?php
require_once "pdo.php";
session_start();
static $c_rfid = "";//As we need to store value of c_rfid once proff has been detected for attendance everytime a new student passes through the system
$s_rfid = "";//Its dynamic for each and every student so no need to store
static $course_id = "";
static $cur_day;
static $day_col;//for corresponding day-column in corresponding course table under current day example d1 of course_cs200_table for current day = 1
static $next_day;
static $table_name = "";
foreach ($_REQUEST as $key => $value)
{
	if($key == "c_rfid")//when crfid is sent from node mcu to this php code
	{
		$c_rfid = $value;
	}
	if($key == "s_rfid")//when crfid is sent from node mcu to this php code 
	{
		$s_rfid = $value;
	}
}
if($c_rfid!="")
{
	$stmt1 = $pdo->prepare("SELECT c_id, day FROM courses where c_rfid = :c_rfid");
	$stmt1->execute(array('c_rfid' => $c_rfid));//key should match actual sql parameter c_rfid 
	$res1 = $stmt1->fetch(PDO::FETCH_ASSOC);
	$course_id = $res1['c_id'];
	$table_name = "course_".$course_id."_table";//to be contd
	$cur_day = $res1['day'];
	$day_col = "d".$cur_day;
	$next_day = $cur_day+1;
	$stmt2 = $pdo->prepare("UPDATE courses SET day = :next_day where c_rfid = :c_rfid");
	$stmt2->execute(array('c_rfid' => $c_rfid));//key should match actual sql parameter c_rfid 
	$stmt3 = $pdo->prepare("SELECT day FROM courses where c_rfid = :c_rfid");
	$stmt3->execute(array('c_rfid' => $c_rfid));//key should match actual sql parameter c_rfid
	$res2 = $stmt3->fetch(PDO::FETCH_ASSOC);
	$op1 = $res2['day'];
	if($op1==$next_day)
	{
		echo "Day updated successfully in courses table entry with c_id = $course_id<br>";
	}
	else
	{
		echo "Error in updating day in courses table entry with c_id = $course_id<br>";	
	}
}
if($s_rfid!="")
{
	$stmt4 = $pdo->prepare("SELECT s_id FROM students where s_rfid = :s_rfid");
	$stmt4->execute(array('s_rfid' => $s_rfid));//key should match actual sql parameter c_rfid 
	$res3 = $stmt1->fetch(PDO::FETCH_ASSOC);
	$student_roll = $res3['s_id'];
	$stmt5 = $pdo->prepare("UPDATE :table_name SET :day_col = 1 where s_id = :student_roll");
	$stmt5->execute(array('s_id' => $student_roll));//key should match actual sql parameter s_id 
	$stmt6 = $pdo->prepare("SELECT :day_col FROM :table_name where s_id = :student_roll");
	$stmt6->execute(array('s_id' => $student_roll));//key should match actual sql parameter s_id
	$res4 = $stmt3->fetch(PDO::FETCH_ASSOC);
	$op2 = $res4['day_col'];
	if($op2==1)
	{
		echo "Attendance updated successfully in $table_name for student with roll no. = $student_roll<br>";
	}
	else
	{
		echo "Error in updating attendance in $table_name for student with roll no. = $student_roll<br>";	
	}	
}
/*if ($age < 21) {
echo "<p> $name, You're not old enough to drink.</p>\n";
} else {
echo "<p> Hi $name. You're old enough to have a drink, ";
echo "but do so responsibly.</p>\n";
}*/
?>