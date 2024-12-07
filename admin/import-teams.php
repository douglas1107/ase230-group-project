<?php
require_once '../lib/db.php';


$jsonFilePath = '../data/teams.json';


if (!file_exists($jsonFilePath)) {
    die('Error: teams.json file not found.');
}


$jsonData = file_get_contents($jsonFilePath);
$teams = json_decode($jsonData, true);

if ($teams === null) {
    die('Error: Invalid JSON format.');
}


$stmt = $db->prepare("INSERT INTO teams (team_name, player_name, player_number) VALUES (?, ?, ?)");


foreach ($teams as $team) {
    $teamName = $team['team_name'];
    foreach ($team['players'] as $player) {
        $playerName = $player['name'];
        $playerNumber = $player['number'];

        $stmt->bind_param('ssi', $teamName, $playerName, $playerNumber);
        $stmt->execute();
    }
}

echo "Teams and players imported successfully.";
?>
