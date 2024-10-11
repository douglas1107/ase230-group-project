<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'scorekeeper') {
    header('Location: login.php');
    exit();
}

function loadGames($filename) {
    if (file_exists($filename)) {
        return json_decode(file_get_contents($filename), true);
    }
    return [];
}

function saveGames($filename, $games) {
    file_put_contents($filename, json_encode($games, JSON_PRETTY_PRINT));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_game'])) {
    $homeTeam = htmlspecialchars(trim($_POST['home_team']));
    $homeScore = htmlspecialchars(trim($_POST['home_score']));
    $awayTeam = htmlspecialchars(trim($_POST['away_team']));
    $awayScore = htmlspecialchars(trim($_POST['away_score']));
    $date = htmlspecialchars(trim($_POST['date']));

    $games = loadGames('../data/games.json');

    $newGameId = end($games)['game_id'] + 1; // Increment last game ID

    $games[] = [
        'game_id' => $newGameId,
        'teams' => [
            'home' => ['name' => $homeTeam, 'score' => $homeScore],
            'away' => ['name' => $awayTeam, 'score' => $awayScore],
        ],
        'date' => $date,
    ];
    saveGames('../data/games.json', $games);
}


if (isset($_GET['delete_game'])) {
    $gameIndex = htmlspecialchars(trim($_GET['delete_game']));


    $games = loadGames('../data/games.json');


    if (isset($games[$gameIndex])) {
        unset($games[$gameIndex]);
        $games = array_values($games);
    }

    saveGames('../data/games.json', $games);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_game'])) {
    $gameIndex = htmlspecialchars(trim($_POST['game_index']));
    $homeTeam = htmlspecialchars(trim($_POST['home_team']));
    $homeScore = htmlspecialchars(trim($_POST['home_score']));
    $awayTeam = htmlspecialchars(trim($_POST['away_team']));
    $awayScore = htmlspecialchars(trim($_POST['away_score']));
    $date = htmlspecialchars(trim($_POST['date']));

    $games = loadGames('../data/games.json');

    $games[$gameIndex] = [
        'game_id' => $games[$gameIndex]['game_id'], // Keep the same game_id
        'teams' => [
            'home' => ['name' => $homeTeam, 'score' => $homeScore],
            'away' => ['name' => $awayTeam, 'score' => $awayScore],
        ],
        'date' => $date,
    ];

    saveGames('../data/games.json', $games);
}

$games = loadGames('../data/games.json');
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
            <?php foreach ($games as $index => $game): ?>
                <li class="list-group-item">
                    <strong><?php echo htmlspecialchars($game['teams']['home']['name']); ?></strong> vs <strong><?php echo htmlspecialchars($game['teams']['away']['name']); ?></strong>
                    <p>Score: <?php echo htmlspecialchars($game['teams']['home']['score']); ?> - <?php echo htmlspecialchars($game['teams']['away']['score']); ?></p>
                    <p>Date: <?php echo htmlspecialchars($game['date']); ?></p>
                    <a href="manage-games.php?delete_game=<?php echo $index; ?>" class="btn btn-danger btn-sm float-end">Delete</a>

                    <form action="manage-games.php" method="post" class="mt-2">
                        <input type="hidden" name="game_index" value="<?php echo $index; ?>">
                        <div class="input-group">
                            <input type="text" class="form-control" name="home_team" value="<?php echo htmlspecialchars($game['teams']['home']['name']); ?>" required>
                            <input type="number" class="form-control" name="home_score" value="<?php echo htmlspecialchars($game['teams']['home']['score']); ?>" required>
                            <input type="text" class="form-control" name="away_team" value="<?php echo htmlspecialchars($game['teams']['away']['name']); ?>" required>
                            <input type="number" class="form-control" name="away_score" value="<?php echo htmlspecialchars($game['teams']['away']['score']); ?>" required>
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
