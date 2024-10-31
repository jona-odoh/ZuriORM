<?php

use App\ZuriORM;


// methods handle database connection, disconnection, and status checking.
$db = new ZuriORM("localhost", "my_database", "username", "password");
echo $db->getConnectionStatus();
$db->closeConnection();
echo $db->getConnectionStatus();
