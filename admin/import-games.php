<?php
require_once '../lib/db.php'; 

$jsonFilePath = '../data/games.json'; 

if (!file_exists($jsonFilePath)) {
    die('Error: games.json file not found.');
}

$jsonData = file_get_contents($jsonFilePath);
$games = json_decode($jsonData, true);

if ($games === null) {
    die('Error: Invalid JSON format.');
}

$checkGameStmt = $db->prepare("SELECT COUNT(*) FROM games WHERE game_id = ?");
$insertGameStmt = $db->prepare("INSERT INTO games (game_id, game_date) VALUES (?, ?)");
$insertTeamStmt = $db->prepare("INSERT INTO game_teams (game_id, team_name, score, is_home) VALUES (?, ?, ?, ?)");

foreach ($games as $game) {
    $gameId = $game['game_id'];
    $gameDate = $game['date'];

    $checkGameStmt->bind_param('i', $gameId);
    $checkGameStmt->execute();
    $checkGameStmt->bind_result($count);
    $checkGameStmt->fetch();

    if ($count > 0) {
        echo "Skipping duplicate game_id: $gameId<br>";
        $checkGameStmt->free_result(); 
        continue;
    }

    $checkGameStmt->free_result(); 


    $insertGameStmt->bind_param('is', $gameId, $gameDate);
    $insertGameStmt->execute();

    $homeTeam = $game['teams']['home'];
    $homeTeamName = $homeTeam['name'];
    $homeTeamScore = $homeTeam['score'];
    $isHome = 1;
    $insertTeamStmt->bind_param('isii', $gameId, $homeTeamName, $homeTeamScore, $isHome);
    $insertTeamStmt->execute();

    $awayTeam = $game['teams']['away'];
    $awayTeamName = $awayTeam['name'];
    $awayTeamScore = $awayTeam['score'];
    $isHome = 0; 
    $insertTeamStmt->bind_param('isii', $gameId, $awayTeamName, $awayTeamScore, $isHome);
    $insertTeamStmt->execute();
}

$checkGameStmt->close();
$insertGameStmt->close();
$insertTeamStmt->close();

echo "Games and teams imported successfully.";
?>
