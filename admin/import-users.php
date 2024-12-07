<?php
require_once '../lib/db.php';

$csvFilePath = '../data/users.csv';

if (!file_exists($csvFilePath)) {
    die('Error: users.csv file not found.');
}

if (($handle = fopen($csvFilePath, 'r')) !== false) {
    fgetcsv($handle);

    $stmt = $db->prepare("INSERT INTO users (id, name, email, password, role) VALUES (?, ?, ?, ?, ?)");

    while (($row = fgetcsv($handle)) !== false) {
        $id = $row[0];
        $name = $row[1];
        $email = $row[2];
        $password = $row[3];
        $role = $row[4];

        $stmt->bind_param('issss', $id, $name, $email, $password, $role);
        $stmt->execute();
    }

    fclose($handle);
    echo "Users imported successfully.";
} else {
    echo "Error: Unable to open the CSV file.";
}
?>
