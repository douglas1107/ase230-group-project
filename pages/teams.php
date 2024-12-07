<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'viewer') {
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
<?php include('../theme/header.php'); ?>

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
        <h1></h1>
        <h2>All Teams</h2>
        <ul class="list-group mb-4">
            <?php foreach ($teams as $team): ?>
                <li class="list-group-item">
                    <strong><?php echo htmlspecialchars($team['team_name']); ?></strong>
                    <ul>
                        <?php foreach ($team['players'] as $player): ?>
                            <li><?php echo htmlspecialchars($player['name']) . " (#" . htmlspecialchars($player['number']) . ")"; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>
        <a href="home.php" class="btn btn-primary">Back to Home</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</body>
</html>
<?php include('../theme/footer.php'); ?>
