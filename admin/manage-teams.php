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

function saveTeams($filename, $teams) {
    file_put_contents($filename, json_encode($teams, JSON_PRETTY_PRINT));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_team'])) {
    $teamName = htmlspecialchars(trim($_POST['team_name']));
    $players = []; 

    $teams = loadTeams('../data/teams.json');

    if (!empty($teamName) && !array_key_exists($teamName, array_column($teams, 'team_name', 'team_name'))) {
        $teams[] = ['team_name' => $teamName, 'players' => $players];
        saveTeams('../data/teams.json', $teams);
    }
}

if (isset($_GET['delete_team'])) {
    $teamToDelete = htmlspecialchars(trim($_GET['delete_team']));

    $teams = loadTeams('../data/teams.json');

    $teams = array_filter($teams, function ($team) use ($teamToDelete) {
        return $team['team_name'] !== $teamToDelete;
    });

    saveTeams('../data/teams.json', $teams);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_player'])) {
    $teamName = htmlspecialchars(trim($_POST['team_name']));
    $playerName = htmlspecialchars(trim($_POST['player_name']));
    $playerNumber = htmlspecialchars(trim($_POST['player_number']));

    $teams = loadTeams('../data/teams.json');

    foreach ($teams as &$team) {
        if ($team['team_name'] === $teamName) {
            $team['players'][] = ['name' => $playerName, 'number' => $playerNumber];
            break;
        }
    }

    saveTeams('../data/teams.json', $teams);
}

$teams = loadTeams('../data/teams.json');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teams</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Manage Teams</h1>
        <form action="manage-teams.php" method="post" class="mb-4">
            <div class="mb-3">
                <label for="team_name" class="form-label">Team Name</label>
                <input type="text" class="form-control" id="team_name" name="team_name" required>
            </div>
            <button type="submit" name="add_team" class="btn btn-primary">Add Team</button>
        </form>

        <h2>Existing Teams</h2>
        <ul class="list-group">
            <?php foreach ($teams as $team): ?>
                <li class="list-group-item">
                    <strong><?php echo htmlspecialchars($team['team_name']); ?></strong>
                    <a href="manage-teams.php?delete_team=<?php echo urlencode($team['team_name']); ?>" class="btn btn-danger btn-sm float-end">Delete</a>

                    <h5 class="mt-3">Players</h5>
                    <ul>
                        <?php foreach ($team['players'] as $player): ?>
                            <li><?php echo htmlspecialchars($player['name']) . " (#" . htmlspecialchars($player['number']) . ")"; ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <form action="manage-teams.php" method="post" class="mb-4">
                        <input type="hidden" name="team_name" value="<?php echo htmlspecialchars($team['team_name']); ?>">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="player_name" placeholder="Player Name" required>
                            <input type="number" class="form-control" name="player_number" placeholder="Player Number" required>
                            <button type="submit" name="add_player" class="btn btn-success">Add Player</button>
                        </div>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
        <a href="index.php" class="btn btn-primary mt-4">Back to Scorekeeper Dashboard</a>
        <a href="../pages/logout.php" class="btn btn-danger mt-4">Logout</a>
    </div>
</body>
</html>
