<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'scorekeeper') {
    header('Location: login.php');
    exit();
}

require_once '../lib/db.php';

function loadGames($db) {
    $query = "SELECT g.game_id, g.game_date AS date, 
                     gt1.team_name AS home_team, gt1.score AS home_score, 
                     gt2.team_name AS away_team, gt2.score AS away_score
              FROM games g
              JOIN game_teams gt1 ON g.game_id = gt1.game_id AND gt1.is_home = 1
              JOIN game_teams gt2 ON g.game_id = gt2.game_id AND gt2.is_home = 0
              ORDER BY g.game_date ASC";

    $result = $db->query($query);
    $games = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $games[] = $row;
        }
    }

    return $games;
}

// Add a new game
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_game'])) {
    $homeTeam = htmlspecialchars(trim($_POST['home_team']));
    $homeScore = htmlspecialchars(trim($_POST['home_score']));
    $awayTeam = htmlspecialchars(trim($_POST['away_team']));
    $awayScore = htmlspecialchars(trim($_POST['away_score']));
    $date = htmlspecialchars(trim($_POST['date']));

    try {
        $db->autocommit(false);

        $stmt = $db->prepare("INSERT INTO games (game_date) VALUES (?)");
        $stmt->bind_param('s', $date);
        $stmt->execute();
        $gameId = $db->insert_id;

        $stmt = $db->prepare("INSERT INTO game_teams (game_id, team_name, score, is_home) VALUES (?, ?, ?, 1)");
        $stmt->bind_param('isi', $gameId, $homeTeam, $homeScore);
        $stmt->execute();

        $stmt = $db->prepare("INSERT INTO game_teams (game_id, team_name, score, is_home) VALUES (?, ?, ?, 0)");
        $stmt->bind_param('isi', $gameId, $awayTeam, $awayScore);
        $stmt->execute();

        $db->commit();
        $success = "Game added successfully!";
    } catch (Exception $e) {
        $db->rollback();
        $error = "Failed to add the game: " . $e->getMessage();
    } finally {
        $db->autocommit(true);
    }
}


// Delete a game
if (isset($_GET['delete_game'])) {
    $gameId = htmlspecialchars(trim($_GET['delete_game']));

    try {
        $stmt = $db->prepare("DELETE FROM games WHERE game_id = ?");
        $stmt->bind_param('i', $gameId);
        $stmt->execute();
        $success = "Game deleted successfully!";
    } catch (Exception $e) {
        $error = "Failed to delete the game: " . $e->getMessage();
    }
}


// Edit a game
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_game'])) {
    $gameId = htmlspecialchars(trim($_POST['game_id']));
    $homeTeam = htmlspecialchars(trim($_POST['home_team']));
    $homeScore = htmlspecialchars(trim($_POST['home_score']));
    $awayTeam = htmlspecialchars(trim($_POST['away_team']));
    $awayScore = htmlspecialchars(trim($_POST['away_score']));
    $date = htmlspecialchars(trim($_POST['date']));

    try {
        $stmt = $db->prepare("UPDATE games SET game_date = ? WHERE game_id = ?");
        $stmt->bind_param('si', $date, $gameId);
        $stmt->execute();

        $stmt = $db->prepare("UPDATE game_teams SET team_name = ?, score = ? 
                              WHERE game_id = ? AND is_home = 1");
        $stmt->bind_param('sii', $homeTeam, $homeScore, $gameId);
        $stmt->execute();

        $stmt = $db->prepare("UPDATE game_teams SET team_name = ?, score = ? 
                              WHERE game_id = ? AND is_home = 0");
        $stmt->bind_param('sii', $awayTeam, $awayScore, $gameId);
        $stmt->execute();

        $success = "Game updated successfully!";
    } catch (Exception $e) {
        $error = "Failed to update the game: " . $e->getMessage();
    }
}


$games = loadGames($db);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Games</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Manage Games</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form action="manage-games.php" method="post" class="mb-4">
            <div class="mb-3">
                <label for="home_team" class="form-label">Home Team</label>
                <input type="text" class="form-control" id="home_team" name="home_team" required>
            </div>
            <div class="mb-3">
                <label for="home_score" class="form-label">Home Score</label>
                <input type="number" class="form-control" id="home_score" name="home_score" required>
            </div>
            <div class="mb-3">
                <label for="away_team" class="form-label">Away Team</label>
                <input type="text" class="form-control" id="away_team" name="away_team" required>
            </div>
            <div class="mb-3">
                <label for="away_score" class="form-label">Away Score</label>
                <input type="number" class="form-control" id="away_score" name="away_score" required>
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <button type="submit" name="add_game" class="btn btn-primary">Add Game</button>
        </form>

        <h2>Existing Games</h2>
        <ul class="list-group">
            <?php foreach ($games as $game): ?>
                <li class="list-group-item">
                    <strong><?php echo htmlspecialchars($game['home_team']); ?></strong> vs <strong><?php echo htmlspecialchars($game['away_team']); ?></strong>
                    <p>Score: <?php echo htmlspecialchars($game['home_score']); ?> - <?php echo htmlspecialchars($game['away_score']); ?></p>
                    <p>Date: <?php echo htmlspecialchars($game['date']); ?></p>
                    <a href="manage-games.php?delete_game=<?php echo htmlspecialchars($game['game_id']); ?>" class="btn btn-danger btn-sm float-end">Delete</a>

                    <form action="manage-games.php" method="post" class="mt-2">
                        <input type="hidden" name="game_id" value="<?php echo htmlspecialchars($game['game_id']); ?>">
                        <div class="input-group">
                            <input type="text" class="form-control" name="home_team" value="<?php echo htmlspecialchars($game['home_team']); ?>" required>
                            <input type="number" class="form-control" name="home_score" value="<?php echo htmlspecialchars($game['home_score']); ?>" required>
                            <input type="text" class="form-control" name="away_team" value="<?php echo htmlspecialchars($game['away_team']); ?>" required>
                            <input type="number" class="form-control" name="away_score" value="<?php echo htmlspecialchars($game['away_score']); ?>" required>
                            <input type="date" class="form-control" name="date" value="<?php echo htmlspecialchars($game['date']); ?>" required>
                            <button type="submit" name="edit_game" class="btn btn-warning">Edit</button>
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
