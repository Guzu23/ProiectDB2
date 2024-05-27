<?php
require_once 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $price = $_POST['price'];
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imgName = $_FILES['image']['name'];
        $imgTmpName = $_FILES['image']['tmp_name'];
        $imgSize = $_FILES['image']['size'];
        $imgError = $_FILES['image']['error'];
        $imgType = $_FILES['image']['type'];

        $imgExt = explode('.', $imgName);
        $imgActualExt = strtolower(end($imgExt));

        $allowed = array('jpg', 'jpeg', 'png', 'gif');

        if (in_array($imgActualExt, $allowed)) {
            if ($imgError === 0) {
                if ($imgSize < 1000000) { 
                    $imgNewName = uniqid('', true).".".$imgActualExt;
                    $imgDestination = 'assets/'.$imgNewName;
                    move_uploaded_file($imgTmpName, $imgDestination);
                } else {
                    echo "Your file is too big!";
                    exit();
                }
            } else {
                echo "There was an error uploading your file!";
                exit();
            }
        } else {
            echo "You cannot upload files of this type!";
            exit();
        }
    } else {
        echo "No file uploaded!";
        exit();
    }

    $bulk = new MongoDB\Driver\BulkWrite;
    $car = [
        'brand' => $brand,
        'model' => $model,
        'price' => $price,
        'image' => $imgDestination
    ];
    $bulk->insert($car);

    $client->executeBulkWrite('carsDB.cars', $bulk);

    header("Location: index.php");
    exit();
}
?>
