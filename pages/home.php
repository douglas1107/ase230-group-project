<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$name = $_SESSION['name'];
$email = $_SESSION['email'];
$role = $_SESSION['role'];

require_once '../lib/db.php';

function loadTeams($db) {
    $query = "SELECT team_name, player_name, player_number 
              FROM teams 
              ORDER BY team_name ASC, player_number ASC";
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
    $query = "SELECT g.game_date AS date, 
                     home.team_name AS home_team, home.score AS home_score, 
                     away.team_name AS away_team, away.score AS away_score
              FROM games g
              JOIN game_teams home ON g.game_id = home.game_id AND home.is_home = 1
              JOIN game_teams away ON g.game_id = away.game_id AND away.is_home = 0
              ORDER BY g.game_date DESC LIMIT 3";
    $result = $db->query($query);

    $games = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $games[] = [
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

$teams = loadTeams($db);
$games = loadGames($db);
?>

<?php include('../theme/header.php'); ?>


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
            <a href="../admin/index.php" class="btn btn-primary">Go to Scorekeeper Dashboard</a>
        <?php elseif ($role === 'viewer'): ?>
            <h2>Viewer Dashboard</h2>

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
            <a href="scoreboard.php" class="btn btn-secondary">View All Games</a>
        <?php endif; ?>

        <a href="logout.php" class="btn btn-danger mt-3">Log Out</a>
    </div>
</body>
</html>

<?php include('../theme/footer.php'); ?>
