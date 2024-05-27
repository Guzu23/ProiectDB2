<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email'])) {
        $newUsername = htmlspecialchars($_POST['username']);
        $newPassword = htmlspecialchars($_POST['password']);
        $email = htmlspecialchars($_POST['email']);

        require_once 'connection.php';

        $filter = ['username' => $newUsername];
        $query = new MongoDB\Driver\Query($filter);
        $result = $client->executeQuery("carsDB.users", $query);

        if (count($result->toArray()) > 0) {
            header("location: login.php?error=username_taken");
            exit();
        } else {
            $newUserData = [
                'username' => $newUsername,
                'password' => password_hash($newPassword, PASSWORD_DEFAULT), 
                'email' => $email
            ];

            $bulk = new MongoDB\Driver\BulkWrite();
            $bulk->insert($newUserData);
            $client->executeBulkWrite("carsDB.users", $bulk);

            $_SESSION["loggedin"] = true;
            $_SESSION["username"] = $newUsername;
            header("location: index.php");
            exit();
        }
    } else {
        header("location: login.php?error=missing_registration_data");
        exit();
    }
} else {
    header("location: login.php");
    exit();
}
?>
