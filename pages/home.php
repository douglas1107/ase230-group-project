<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
$name = $_SESSION['name'];
$email = $_SESSION['email'];
$role = $_SESSION['role'];

 
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
    <title>Home</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Welcome, <?php echo htmlspecialchars($name); ?>!</h1>
        <p>Your email: <?php echo htmlspecialchars($email); ?></p>
        <p>Your role: <?php echo htmlspecialchars($role); ?></p>

        <?php if ($role === 'scorekeeper'): ?>
            <h2>Scorekeeper Dashboard</h2>
            <ul>
                <a href="../admin/index.php" class="btn btn-primary">Go to Scorekeeper Dashboard</a>
            </ul>
        <?php elseif ($role === 'viewer'): ?>
            <h2>Viewer Dashboard</h2>
            <div class="container mt-5">
        <h1>Scoreboard</h1>

        <h2>Teams</h2>
        <ul class="list-group mb-4">
            <?php foreach (array_slice($teams, 0, 3) as $team): ?>
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
        <a href="teams.php" class="btn btn-secondary">View More Teams</a>

        <h2>Recent Games</h2>
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
                <?php foreach (array_slice($games, 0, 3) as $game): ?>
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
        <a href="scoreboard.php" class="btn btn-secondary">View All Games</a>
    </div>
        <?php endif; ?>
        <a href="logout.php" class="btn btn-danger mt-3">Log Out</a>
    </div>
</body>
</html>
