<?php
//took out the password, since the files on the OSU server will still work and is hidden from public
$db = new mysqli("oniddb.cws.oregonstate.edu", "liux5-db", "xxxxxxxxxxxxxx", "liux5-db");
if($db->connect_errno) {
//echo "Failed to connect to MySQL: (" . $db->connect_errno . ") " .$db->connect_error; 
}
else {
	//echo "Connection Established!<br/>";
}

?>
