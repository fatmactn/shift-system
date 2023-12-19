<?php

include 'functions.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $sql = "INSERT INTO users (identity_number, first_name, last_name, birth_date, phone, email, address, city_id, district_id) VALUES (:identity_number, :first_name, :last_name, STR_TO_DATE(:birth_date, '%Y-%m-%d'), :phone, :email, :address, :city_id, :district_id)";

        $parameters = [
            ':identity_number' => $_POST['identity_number'],
            ':first_name' => $_POST['first_name'],
            ':last_name' => $_POST['last_name'],
            ':birth_date' => date('Y-m-d', strtotime($_POST['birth_date'])),
            ':phone' => $_POST['phone'],
            ':email' => $_POST['email'],
            ':address' => $_POST['address'],
            ':city_id' => $_POST['city'],
            ':district_id' => $_POST['district'],
        ];

        insertData($parameters, $sql);

        $message = "success";
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $message = $e->getMessage();
    }

    $conn = null;
}

header("Location: ../index.php?content=define_personnel&message=" . urlencode($message));
exit;
?>
