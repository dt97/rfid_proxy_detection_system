<?php
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=attendance_system', 'admin', 'admin123');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>