<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'scorekeeper') {
    header('Location: login.php');
    exit();
}

require_once '../lib/db.php';

function loadUsers($db) {
    $query = "SELECT id, name, email, role FROM users ORDER BY id ASC";
    $result = $db->query($query);

    $users = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    return $users;
}

function loadTeams($db) {
    $query = "SELECT team_name, player_name, player_number FROM teams ORDER BY team_name ASC, player_number ASC";
    $result = $db->query($query);

    $teams = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $teamName = $row['team_name'];
            if (!isset($teams[$teamName])) {
                $teams[$teamName] = ['team_name' => $teamName, 'players' => []];
            }
            $teams[$teamName]['players'][] = [
                'name' => $row['player_name'],
                'number' => $row['player_number']
            ];
        }
    }
    return array_values($teams);
}

function loadGames($db) {
    $query = "SELECT g.game_id, g.game_date AS date, 
                     home.team_name AS home_team, home.score AS home_score, 
                     away.team_name AS away_team, away.score AS away_score
              FROM games g
              JOIN game_teams home ON g.game_id = home.game_id AND home.is_home = 1
              JOIN game_teams away ON g.game_id = away.game_id AND away.is_home = 0
              ORDER BY g.game_date ASC";
    $result = $db->query($query);

    $games = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $games[] = [
                'game_id' => $row['game_id'],
                'teams' => [
                    'home' => ['name' => $row['home_team'], 'score' => $row['home_score']],
                    'away' => ['name' => $row['away_team'], 'score' => $row['away_score']]
                ],
                'date' => $row['date']
            ];
        }
    }
    return $games;
}

$users = loadUsers($db);
$teams = loadTeams($db);
$games = loadGames($db);
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
