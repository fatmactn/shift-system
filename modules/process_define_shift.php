<?php

include 'functions.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $sql = "INSERT INTO locations (name, start_time, end_time, overnight) VALUES (:name, :start_time, :end_time, :overnight)";

        $parameters = [
            ':name' => $_POST['shiftName'],
            ':start_time' => $_POST['startTime'],
            ':end_time' => $_POST['endTime'],
            ':overnight' => $_POST['overnight']
        ];

        insertData($parameters, $sql);

        $message = "success";
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $message = $e->getMessage();
    }
}

header("Location: ../index.php?content=define_shift&message=" . urlencode($message));
exit;
?>
