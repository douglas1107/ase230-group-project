<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'scorekeeper') {
    header('Location: login.php');
    exit();
}

function loadUsers($filename) {
    $users = [];
    if (($handle = fopen($filename, 'r')) !== FALSE) {
        while (($data = fgetcsv($handle)) !== FALSE) {
            $users[] = [
                'id' => $data[0],
                'name' => $data[1],
                'email' => $data[2],
                'role' => $data[4],
            ];
        }
        fclose($handle);
    }
    return $users;
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

$users = loadUsers('../data/users.csv');
$teams = loadTeams('../data/teams.json');
$games = loadGames('../data/games.json');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Scorekeeper Dashboard</h1>
        <a href="manage-games.php" class="btn btn-primary">Add/Edit a Game</a>
        <a href="manage-teams.php" class="btn btn-primary">Add/Edit a Team</a>
        <h2>Teams</h2>
        <ul class="list-group">
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
        <a href="teams.php" class="btn btn-secondary">View All Teams</a>

        <h2>Games</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Game ID</th>
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
                        <td><?php echo htmlspecialchars($game['game_id']); ?></td>
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
        <a href="../pages/home.php" class="btn btn-primary">Back to Home</a>
        <a href="../pages/logout.php" class="btn btn-danger">Logout</a>
    </div>
</body>
</html>
