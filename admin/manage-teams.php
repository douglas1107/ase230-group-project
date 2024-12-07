<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'scorekeeper') {
    header('Location: login.php');
    exit();
}

require_once '../lib/db.php';

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
            if ($row['player_name'] && $row['player_number']) {
                $teams[$teamName]['players'][] = [
                    'name' => $row['player_name'],
                    'number' => $row['player_number']
                ];
            }
        }
    }
    return array_values($teams);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_team'])) {
    $teamName = htmlspecialchars(trim($_POST['team_name']));

    if (!empty($teamName)) {
        $stmt = $db->prepare("INSERT INTO teams (team_name) VALUES (?)");
        $stmt->bind_param('s', $teamName);
        if ($stmt->execute()) {
            $success = "Team added successfully!";
        } else {
            $error = "Failed to add team: " . $stmt->error;
        }
    }
}

if (isset($_GET['delete_team'])) {
    $teamToDelete = htmlspecialchars(trim($_GET['delete_team']));

    $stmt = $db->prepare("DELETE FROM teams WHERE team_name = ?");
    $stmt->bind_param('s', $teamToDelete);
    if ($stmt->execute()) {
        $success = "Team deleted successfully!";
    } else {
        $error = "Failed to delete team: " . $stmt->error;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_player'])) {
    $teamName = htmlspecialchars(trim($_POST['team_name']));
    $playerName = htmlspecialchars(trim($_POST['player_name']));
    $playerNumber = htmlspecialchars(trim($_POST['player_number']));

    if (!empty($teamName) && !empty($playerName) && !empty($playerNumber)) {
        $stmt = $db->prepare("INSERT INTO teams (team_name, player_name, player_number) VALUES (?, ?, ?)");
        $stmt->bind_param('ssi', $teamName, $playerName, $playerNumber);
        if ($stmt->execute()) {
            $success = "Player added successfully!";
        } else {
            $error = "Failed to add player: " . $stmt->error;
        }
    }
}

$teams = loadTeams($db);
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

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

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
