<?php

$db = new mysqli("oniddb.cws.oregonstate.edu", "liux5-db", "tJ2GhtEQK8tz8Dip", "liux5-db");
if($db->connect_errno) {
//echo "Failed to connect to MySQL: (" . $db->connect_errno . ") " .$db->connect_error; 
}
else {
	//echo "Connection Established!<br/>";
}

?>