<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'scorekeeper') {
    header('Location: login.php');
    exit();
}

function loadTeams($filename) {
    if (file_exists($filename)) {
        return json_decode(file_get_contents($filename), true);
    }
    return [];
}

function loadGames($filename) {
    if (file_exists($filename)) {
        return json_decode(file_get_contents($filename), true);
    }
    return [];
}

$teams = loadTeams('../data/teams.json');
$games = loadGames('../data/games.json');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoreboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Scoreboard</h1>
        <h2>All Games</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Home Team</th>
                    <th>Home Score</th>
                    <th>Away Team</th>
                    <th>Away Score</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($games as $game): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($game['teams']['home']['name']); ?></td>
                        <td><?php echo htmlspecialchars($game['teams']['home']['score']); ?></td>
                        <td><?php echo htmlspecialchars($game['teams']['away']['name']); ?></td>
                        <td><?php echo htmlspecialchars($game['teams']['away']['score']); ?></td>
                        <td><?php echo htmlspecialchars($game['date']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="index.php" class="btn btn-primary">Back to Scorekeeper Dashboard</a>
    </div>
</body>
</html>
