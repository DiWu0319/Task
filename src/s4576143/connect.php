<?php
$conn = @mysqli_connect("localhost","php","php","project");
if (!$conn) {
    echo "failed to connect database: ". mysqli_error();
}
?>
