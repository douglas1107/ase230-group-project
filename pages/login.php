<?php
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: home.php');
    exit;
}
 
$email = $password = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {

        $file_path = '../data/users.csv';
        
        if (($handle = fopen($file_path, 'r')) !== FALSE) {
            $user_found = false;

            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $csv_id = $data[0];   
                $csv_name = $data[1];   
                $csv_email = $data[2];    
                $csv_password = $data[3]; 
                $csv_role = $data[4];  

                if ($csv_email === $email && $csv_password === $password) {
                    $_SESSION['loggedin'] = true;
                    $_SESSION['user_id'] = $csv_id;
                    $_SESSION['name'] = $csv_name;
                    $_SESSION['email'] = $csv_email;
                    $_SESSION['role'] = $csv_role;
        
                    $login_success = true;

                    header('Location: home.php');
                    exit;
                }
            }
            fclose($handle);

            if (!$user_found) {
                $error = 'Email not found.';
            }
        } else {
            $error = 'Unable to open user database.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sports Scorekeeping App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Login to Sports Our Scorekeeping App</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" name="email" class="form-control" id="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" id="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
            <a href="register.php" class="btn btn-primary">Click Here To Register</a>
        </form>
    </div>
</body>
</html>
