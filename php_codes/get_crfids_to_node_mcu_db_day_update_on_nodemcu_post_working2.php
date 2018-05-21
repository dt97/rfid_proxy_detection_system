<!DOCTYPE html>
<html>
<body>
<?php //to check whether connection to database here "attendance_system" is successfully established or not
$servername = "localhost";
$username = "root";
$password = "";//default username and password of phpmyadmin
try 
{
    $pdo = new PDO("mysql:host=$servername;dbname=attendance_system", $username, $password);
    // set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to database successfully"; 
}
catch(PDOException $e)
{
    echo "Connection failed: " . $e->getMessage();
}
//require_once "pdo.php";
//session_start();
echo "<h2>Course RFID Verification!</h2>";
static $c_rfid = "";//As we need to store value of c_rfid once proff has been detected for attendance everytime a new student passes through the system
//static $cur_day;
//static $next_day;
foreach ($_REQUEST as $key => $value)
{
	if($key == "c_rfid")//when crfid is sent from node mcu to this php code
	{
		$c_rfid = $value;
	}
}
if($c_rfid!="")
{
	$stmt1 = $pdo->prepare("SELECT c_rfid FROM courses where c_rfid = :c_rfid");
	//key should match actual sql parameter c_rfid
	if($stmt1->execute(array('c_rfid' => $c_rfid)))
	{
		$res1 = $stmt1->fetch(PDO::FETCH_ASSOC);
		if($res1)//if c_rfid is valid course id $res1 is not null 
		{
			$c_rfid = $res1['c_rfid'];
			//$cur_day = $res1['day'];
			//$next_day = $cur_day+1;
			//echo "Query success c_rfid = $c_rfid detected<br>";
			/*$stmt2 = $pdo->prepare("UPDATE courses SET day = :next_day where c_rfid = :c_rfid");
			if($stmt2->execute(array('c_rfid' => $c_rfid, 'next_day' => $next_day)))//key should match actual sql parameter c_rfid 
			{
				echo "Success<br>";
				/*$stmt3 = $pdo->prepare("SELECT day FROM courses where c_rfid = :c_rfid");
				if($stmt3->execute(array('c_rfid' => $c_rfid)))//key should match actual sql parameter c_rfid
				{
					$res2 = $stmt3->fetch(PDO::FETCH_ASSOC);
					if($res2)
					{
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
					else
						echo "Error see line 52<br>";
				}
				else
					echo "Error see line 49<br>";
			*/
			//}
			//else
			//	echo "Error see line 46<br>";
		}
		else
		{
			$c_rfid = "null";
			//echo "Error!!! No such c_rfid = $c_rfid detected in courses table<br>";		
		}
	}
	else
	{
		//echo "Query failure<br>";
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
</body>
</html>