<?php //to check whether connection to database here "attendance_system" is successfully established or not
$servername = "localhost";
$username = "root";
$password = "";//default username and password of phpmyadmin
try 
{
    $pdo = new PDO("mysql:host=$servername;dbname=attendance_system", $username, $password);
    // set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected to database successfully"; 
}
catch(PDOException $e)
{
    echo "Connection failed: " . $e->getMessage();
}
//require_once "pdo.php";
//session_start();
//echo "<h2>Course RFID Verification!</h2>";
static $c_rfid = "";//As we need to store value of c_rfid once proff has been detected for attendance everytime a new student passes through the system
static $table_name = "";
static $cur_day = "";
static $next_day = "";
static $day = "";
static $day_col = "";
foreach ($_REQUEST as $key => $value)
{
	if($key == "c_rfid")//when crfid is sent from node mcu to this php code
	{
		$c_rfid = $value;
	}
}
if($c_rfid!="")
{
	$stmt1 = $pdo->prepare("SELECT c_rfid, day FROM courses where c_rfid = :c_rfid");
	//key should match actual sql parameter c_rfid
	if($stmt1->execute(array('c_rfid' => $c_rfid)))
	{
		$res1 = $stmt1->fetch(PDO::FETCH_ASSOC);
		if($res1)//if c_rfid is valid course id $res1 is not null 
		{
			$c_rfid = $res1['c_rfid'];
			$stmt2 = $pdo->prepare("SELECT c_id, day FROM courses where c_rfid = :c_rfid");
			if($stmt2->execute(array('c_rfid' => $c_rfid)))//key should match actual sql parameter c_rfid 
			{
				$res2 = $stmt2->fetch(PDO::FETCH_ASSOC);
				if($res2)//if $res2 is not null i.e data fetched successfully
				{
					$c_id = $res2['c_id'];
					$table_name = "course_".$c_id."_table";
					$cur_day = $res2['day'];//as day is updated as next day in get_crfids_to_node_mcu.php
					$day_col = "d".$cur_day;
					$next_day = $cur_day+1;	
					$stmt3 = $pdo->prepare("UPDATE courses SET day = :next_day where c_rfid = :c_rfid");
					if($stmt3->execute(array('c_rfid' => $c_rfid, 'next_day' => $next_day)))//key should match actual sql parameter c_rfid 
					{
						//echo "Day updated successfully in courses table entry with c_id = $c_id<br>";
					}	
					else
					{
						//echo "Error in updating day in courses table entry with c_id = $c_id<br>";	
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
		else
		{
			$c_rfid = "null";		
		}
	}
	echo "$c_rfid<br>";
}
else
{
	echo "No course rfid(c_rfid) received<br>";
}
/*if ($age < 21) {
echo "<p> $name, You're not old enough to drink.</p>\n";
} else {
echo "<p> Hi $name. You're old enough to have a drink, ";
echo "but do so responsibly.</p>\n";
}*/
?>