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
static $table_name = "";
foreach ($_REQUEST as $key => $value)
{
	if($key == "c_rfid")//when crfid is sent from node mcu to this php code
	{
		$c_rfid = $value;
	}
}
if($c_rfid!="")
{
	$stmt1 = $pdo->prepare("SELECT c_id FROM courses where c_rfid = :c_rfid");
	if($stmt1->execute(array('c_rfid' => $c_rfid)))//key should match actual sql parameter c_rfid 
	{
		$res1 = $stmt1->fetch(PDO::FETCH_ASSOC);
		if($res1)//if $res1 is not null i.e data fetched successfully
		{
			$c_id = $res1['c_id'];
			$table_name = "course_".$c_id."_table";
			$stmt2 = $pdo->prepare("UPDATE $table_name SET mark = '0'");
			if($stmt2->execute())
			{
				echo "successfully reset mark field of course table $table_name";
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
/*if ($age < 21) {
echo "<p> $name, You're not old enough to drink.</p>\n";
} else {
echo "<p> Hi $name. You're old enough to have a drink, ";
echo "but do so responsibly.</p>\n";
}*/
?>