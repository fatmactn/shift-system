<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'functions.php';

if (isset($_GET['city_id'])) {
    try {
        $cityId = $_GET['city_id'];

        $districts = getCustomData("SELECT * FROM districts WHERE city_id = :city_id", ['city_id' => $cityId]);

        echo '<label for="district">İlçe</label>';
        echo '<select id="district" name="district">';
        echo '<option value="">Seçiniz</option>';

        foreach ($districts as $district) {
            $value = $district["id"];
            $name = $district["name"];
            echo "<option value=\"$value\">$name</option>";
        }

        echo '</select>';
    } catch (PDOException $e) {
        echo 'PDO Error: ' . $e->getMessage();
    }
} else {
    echo 'Invalid request';
}

