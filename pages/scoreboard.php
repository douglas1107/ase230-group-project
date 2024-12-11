<?php
session_start();

// Ensure the user is authenticated and has the "viewer" role
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'viewer') {
    header('Location: login.php');
    exit();
}


// Include the database connection
require_once '../lib/db.php';

// Function to load games from the database
function loadGames($db) {
    $query = "SELECT g.game_date AS date, 
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

// Fetch games from the database
$games = loadGames($db);
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

        <a href="home.php" class="btn btn-primary">Back to Home</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</body>
</html>
