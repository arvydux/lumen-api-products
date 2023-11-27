test no php
<?php

echo 123456;
$conn = new mysqli("mysql", "root", ".sweetpwd.", "my_db");
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT name FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each rowoc
    while($row = $result->fetch_assoc()) {
        echo $row['name']."<br>";
    }
} else {
    echo "0 results";
}
$conn->close();
