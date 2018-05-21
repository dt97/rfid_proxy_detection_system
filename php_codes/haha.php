<?php
require_once "pdo.php";
session_start();
//$c_rfid="5800A9334785";
//$s_rfid="5800A7897A0C";
static $c_rfid = "";
$s_rfid = "";
static $c_id = "";
static $cur_day;
static $day_col;//for corresponding day-column in corresponding course table under current day example d1 of course_cs200_table for current day = 1
static $next_day;
static $table_name = "";
if($c_rfid!="")
{
	$stmt1 = $pdo->prepare("SELECT c_id, day FROM courses where c_rfid = :c_rfid");
	if($stmt1->execute(array('c_rfid' => $c_rfid)))//key should match actual sql parameter c_rfid 
	{
		$res1 = $stmt1->fetch(PDO::FETCH_ASSOC);
		if($res1)//if $res1 is not null i.e data fetched successfully
		{
			$c_id = $res1['c_id'];
			$table_name = "course_".$c_id."_table";
			$cur_day = $res1['day'];
			$day_col = "d".$cur_day;
			echo "$day_col<br>";
			$next_day = $cur_day+1;	
			$stmt2 = $pdo->prepare("UPDATE courses SET day = :next_day where c_rfid = :c_rfid");
			if($stmt2->execute(array('c_rfid' => $c_rfid, 'next_day' => $next_day)))//key should match actual sql parameter c_rfid 
			{
				echo "Day updated successfully in courses table entry with c_id = $c_id<br>";
			}	
			else
			{
				echo "Error in updating day in courses table entry with c_id = $c_id<br>";	
			}
		}
		else
		{
			echo "Failure to fetch data from database, or no such c_rfid found in courses table<br>";
		}
	}
	else
	{
		echo "Query failure. Couldn't find c_id and day corresponding to given c_rfid<br>";
	}
}
if($s_rfid!="")
{
	$stmt3 = $pdo->prepare("SELECT s_id, s_name FROM students where s_rfid = :s_rfid");
	if($stmt3->execute(array('s_rfid' => $s_rfid)))//key should match actual sql parameter c_rfid 
	{
		$res2 = $stmt3->fetch(PDO::FETCH_ASSOC);
		if($res2)//if $res2 is not null i.e data fetched successfully 
		{
			echo "$day_col<br>";
			$student_roll = $res2['s_id'];
			$student_name = $res2['s_name'];
			echo "$student_roll <br> $student_name <br> $table_name <br>";
			$stmt4 = $pdo->prepare("UPDATE $table_name SET $day_col ='1' where s_id = :student_roll");		
			if($stmt4->execute(array('student_roll' => $student_roll)))//key should match actual sql parameter s_id	
			{
				//on successful exution of query
				echo "Successfully updated attendance for student $student_name with roll no $student_roll and s_rfid $s_rfid<br>";
			}
			else
			{
				echo "Query failure. Couldn't update attendance in $table_name table<br>";
			}
		}
	}
}
			
			?>