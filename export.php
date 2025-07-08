<?php
include 'config.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="users.csv"');

$output = fopen("php://output", "w");
fputcsv($output, array('ID', 'Name', 'Email', 'Phone'));

$result = mysqli_query($conn, "SELECT * FROM users");
while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, $row);
}
fclose($output);
exit;
?>
