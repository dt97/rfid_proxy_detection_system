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
//-------------------------------------Now php code for updating attendance starts---------------------------------------------------//
static $c_rfid = "";//As we need to store value of c_rfid once proff has been detected for attendance everytime a new student passes through the system
$s_rfid = "";//Its dynamic for each and every student so no need to store by default set to null string
$student_roll = "";//Its dynamic for each and every student so no need to store by default set to null string
$student_name = "";//Its dynamic for each and every student so no need to store by default set to null string
$mark = "";
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
	if($stmt1->execute(array('c_rfid' => $c_rfid)))//key should match actual sql parameter c_rfid 
	{
		$res1 = $stmt1->fetch(PDO::FETCH_ASSOC);
		if($res1)//if $res1 is not null i.e data fetched successfully
		{
			$c_id = $res1['c_id'];
			$table_name = "course_".$c_id."_table";
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
	$stmt2 = $pdo->prepare("SELECT s_id, s_name FROM students where s_rfid = :s_rfid");
	if($stmt2->execute(array('s_rfid' => $s_rfid)))//key should match actual sql parameter c_rfid 
	{
		$res2 = $stmt2->fetch(PDO::FETCH_ASSOC);
		if($res2)//if $res2 is not null i.e data fetched successfully 
		{
			$student_roll = $res2['s_id'];
			$student_name = $res2['s_name'];
			$stmt3 = $pdo->prepare("SELECT mark FROM $table_name where s_id = :student_roll");		
			if($stmt3->execute(array('student_roll' => $student_roll)))//key should match actual sql parameter s_id
			{
				//on successful exution of query
				$res3 = $stmt3->fetch(PDO::FETCH_ASSOC);
				if($res3)
				{
					$mark = $res3['mark'];
				}
			}
			else
			{
				echo "Query failure. Couldn't update attendance in $table_name table<br>";
			}
		}
		else
		{
			$mark = "null";
		}
	}
	else
	{
		echo "Query failure. Couldn't update attendance for s_id corresponding to given s_rfid<br>";	
	}
	echo "$mark<br>";
}
/*if ($age < 21) {
echo "<p> $name, You're not old enough to drink.</p>\n";
} else {
echo "<p> Hi $name. You're old enough to have a drink, ";
echo "but do so responsibly.</p>\n";
}*/
?>