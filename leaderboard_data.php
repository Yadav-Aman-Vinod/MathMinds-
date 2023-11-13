<?php
require_once('dbconfig/config.php');

$query = "SELECT email, MAX(tscore) as highest_tscore FROM records GROUP BY email ORDER BY highest_tscore DESC LIMIT 20";
$result = $con->query($query);

$leaderboardData = array();
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $leaderboardData[] = array(
            "username" => $row['email'],
            "highest_tscore" => $row['highest_tscore']
        );
    }
}

echo json_encode($leaderboardData);
?>
