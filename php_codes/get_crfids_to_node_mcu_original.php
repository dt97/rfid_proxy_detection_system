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
	$stmt1->execute(array('c_rfid' => $c_rfid));//key should match actual sql parameter c_rfid
	if($stmt1===true)
	{
		$res1 = $stmt1->fetch(PDO::FETCH_ASSOC);
		$c_rfid = $res1['c_rfid'];
		echo "Query success c_rfid = $c_rfid detected<br>";	
	}
	else
	{
		echo "Query failure<br>";
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