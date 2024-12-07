<!-- header.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scorekeeper App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="style.css"> <!-- Link to your custom CSS -->
</head>
<body>
    <!-- header Section with Navbar -->
    <div class="container-fluid bg-primary text-white py-3">
        <header>
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3">Scorekeeper App</h1>
                <nav>
                    <ul class="nav">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="home.php">Home</a>
                        </li>
                        
                        <?php if (isset($_SESSION['email'])): // Check if the user is logged in ?>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="logout.php">Log Out</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </header>
    </div>
    <!-- End of Header Section --> 
