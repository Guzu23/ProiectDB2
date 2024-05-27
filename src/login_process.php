<?php
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['credential']) && isset($_POST['password'])) {
        $credential = htmlspecialchars($_POST['credential']);
        $password = htmlspecialchars($_POST['password']);

        if ($credential === 'admin' && $password === 'admin') {
            $_SESSION["loggedin"] = true;
            $_SESSION["username"] = 'admin';
            $_SESSION["admin"] = true;
            header("location: index.php");
            exit();
        } else {
            require_once 'connection.php';

            $filter = ['$or' => [['username' => $credential], ['email' => $credential]]];
            $query = new MongoDB\Driver\Query($filter);
            $result = $client->executeQuery("carsDB.users", $query);

            $resultArray = $result->toArray();
            if (count($resultArray) > 0) {
                $userData = $resultArray[0];
                if (password_verify($password, $userData->password)) {
                    $_SESSION["loggedin"] = true;
                    $_SESSION["username"] = $userData->username;
                    header("location: index.php");
                    exit();
                } else {
                    header("location: login.php?error=invalid_password");
                    exit();
                }
            } else {
                header("location: login.php?error=user_not_found");
                exit();
            }
        }
    } else {
        header("location: login.php?error=missing_credentials");
        exit();
    }
} else {
    header("location: login.php");
    exit();
}
?>
