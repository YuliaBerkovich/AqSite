<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="icon" href="fish.png" type="image/x-icon">
    <link rel="stylesheet" href="forAll.css">
	<link rel="stylesheet" href="tbl.css">
    <link href="https://fonts.googleapis.com/css?family=Cinzel|Montserrat|Permanent+Marker|Quicksand" rel="stylesheet">
</head>

<body>
    <header>
        <nav class="topnav" id="myTopnav">
           <a href="connection.php" class="logo"> <img src="log.png" width=100% alt="Logo" /></a>
            <a href="connection.php">home</a>
            <a href="registration.php">sign up</a>
            <a id="menu" href="#" class="icon">&#9776;</a>
        </nav>
    </header>
	<h1>Your aquarium</h1>
<?php //Alexey Masyuk,Yulia Berkovich Aquarium Control System  
 // Main Page To Shpw User Data
require_once('dbClass.php');
require_once('userClass.php');
require_once('classMSG.php');

if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

$user=$_SESSION['user'];         // Save user passed data to lacal varieblr
$msg=new MSG();                  // Initialize relevant Objects
$sql=new dbClass($user);

$result = $sql->getData($user);  // Get user data from Database
$_SESSION['data']=$result;       // Save user data for file creating later        

echo "<table border='1' color='red'>";     // HTML table tags
echo "<tr>";
echo "<th>Time</th>";
echo "<th>Temperature</th>";
echo "</tr>";                              // HTML table tags 

foreach($result as $k=>$val)
{
	echo "<tr>";
	echo "<td>" . $val['time'] . "</td>"; // Show user data from DataBase in HTML table 
	echo "<td>" . $val['tmp'] . "</td>";  
	echo "</tr>";
}
echo "</table>";                           // HTML table tag close

if(isset($_SESSION['flag']))               // Checking if some error flag returned from redirected pages
{
	echo $_SESSION['flag'];                // Print the error ocured
	unset($_SESSION['flag']);
}
?>
        <form method='POST' action='fileCreateAndDataDel.php'>
            <br/>Date to delete:<input type='text' name="date"/>
			     <input type="submit" name="del" value="DELETE" />
        </form>
		<form method='POST' action='fileCreateAndDataDel.php'>
                <input type="submit" value="Create Data File" 
                   onclick="window.location='fileCreateAndDataDel.php';" />
				<input type="submit" name="tableDel" value="Delete All Data" />
        </form>
        <form method='POST' action='accountHandle.php'>
            <input type="submit" value="SignOut" 
                onclick="window.location='accountHandle.php';" />
            <input type="submit" name="accountDel" value="DELETE USER" />
    </body>
</html>	