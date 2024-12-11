<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'viewer') {
    header('Location: login.php');
    exit();
}

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

$teams = loadTeams($db);
?>
<?php include('../themes/header.php'); ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teams</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>All Teams</h1>
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
<?php include('../themes/footer.php'); ?>
