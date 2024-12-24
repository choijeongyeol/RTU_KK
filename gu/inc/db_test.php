<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/db_connection.php');

$conn = getDbConnection();

if ($conn) {
    echo "Database connection is successful.";
} else {
    echo "Failed to connect to the database.";
}
?>
