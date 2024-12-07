<?php
session_start();

require_once '../lib/db.php';

$nameErr = $emailErr = $passwordErr = $roleErr = '';
$name = $email = $password = $role = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isValid = true;

    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
        $isValid = false;
    } else {
        $name = htmlspecialchars($_POST["name"]);
    }

    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
        $isValid = false;
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
        $isValid = false;
    } else {
        $email = htmlspecialchars($_POST["email"]);
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
        $isValid = false;
    } else {
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    }

    if (empty($_POST["role"])) {
        $roleErr = "Role is required";
        $isValid = false;
    } else {
        $role = htmlspecialchars($_POST["role"]);
    }

    if ($isValid) {
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $name, $email, $password, $role);

        if ($stmt->execute()) {
            header('Location: login.php');
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}
?>

<?php include('../theme/header.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Register</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>">
                <small class="text-danger"><?php echo $nameErr; ?></small>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>">
                <small class="text-danger"><?php echo $emailErr; ?></small>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" id="password">
                <small class="text-danger"><?php echo $passwordErr; ?></small>
            </div>
            
            <div class="form-group">
                <label for="role">Role</label>
                <select class="form-control" name="role" id="role">
                    <option value="scorekeeper" <?php if ($role == 'scorekeeper') echo 'selected'; ?>>Scorekeeper</option>
                    <option value="viewer" <?php if ($role == 'viewer') echo 'selected'; ?>>Viewer</option>
                </select>
                <small class="text-danger"><?php echo $roleErr; ?></small>
            </div>

            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>

</body>
</html>
<?php include('../theme/footer.php'); ?>
