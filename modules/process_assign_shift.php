<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'functions.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $result = false;
        $start_date = date("Y-m-d H:i:s", strtotime($_POST["start_date"]));
        $end_date = date("Y-m-d H:i:s", strtotime($_POST["end_date"]));

        $users = getData('users');
        $locations = getData('locations');

        $currentDate = strtotime($start_date);
        $endDate = strtotime($end_date);

        while ($currentDate <= $endDate) {
            $currentDateFormatted = date("Y-m-d", $currentDate);

            try {
                foreach ($locations['items'] as $location) {
                    if (!isset($location['start_time']) || !isset($location['end_time'])) {
                        continue;
                    }

                    $shiftStart = strtotime($currentDateFormatted . ' ' . $location['start_time']);
                    $shiftEnd = strtotime($currentDateFormatted . ' ' . $location['end_time']);

                    if ($location['overnight']) {
                        $shiftEnd = strtotime('+1 day', $shiftEnd);
                    }

                    $sql = "INSERT INTO shift_assignments (user_id, location_id, start_date, end_date) VALUES (:user_id, :location_id, :start_date, :end_date)";

                    $intervalStart = $shiftStart;
                    while ($intervalStart < $shiftEnd) {
                        // Uygun kullanıcıları seçmek için filtreleme
                        $availableUsers = getAvailableUsers($users['items'], $currentDateFormatted, $location);
                        if (!empty($availableUsers)) {
                            $randomUser = $availableUsers[array_rand($availableUsers)];
                            $startTime = date("Y-m-d H:i:s", $intervalStart);
                            $endTime = date("Y-m-d H:i:s", $intervalStart + 3600);

                            $parameters = [
                                ':user_id' => $randomUser['id'],
                                ':location_id' => $location['id'],
                                ':start_date' => $startTime,
                                ':end_date' => $endTime
                            ];

                            insertData($parameters, $sql);
                            $result = true;

                            $intervalStart += 3600;
                        } else {
                            $errorCode = 100;
                            $message = "Yeterli personel bulunamadı";
                        }
                    }

                }
            } catch (PDOException $e) {
                $message = $e->getMessage();
                error_log($e->getMessage());
            }

            $currentDate = strtotime('+1 day', $currentDate);
        }

    } catch (PDOException $e) {
        error_log($e->getMessage());
        $message = $e->getMessage();
    }
}

header("Location: ../index.php?content=define_shift_assignment&message=" . urlencode($message));
exit;

/**
 * @param $userId
 * @param $date
 * @return array|false
 */
function getSameDayShifts($userId, $date)
{
    $sql = "SELECT * FROM shift_assignments WHERE user_id = :user_id AND DATE(start_date) = :date";

    $parameters = [
        ':user_id' => $userId,
        ':date' => $date
    ];

    return getCustomData($sql, $parameters);
}

/**
 * @param $userId
 * @param $date
 * @param $location
 * @return array|false
 */
function getOvernightShifts($userId, $date, $location)
{
    $sql = "SELECT * FROM shift_assignments WHERE user_id = :user_id AND DATE(start_date) = :date AND location_id = :location_id AND end_date > start_date";

    $parameters = [
        ':user_id' => $userId,
        ':date' => $date,
        ':location_id' => $location['id'],
    ];

    $overnightShifts = getCustomData($sql, $parameters);

    if ($location['overnight']) {
        $overnightShifts = array_filter($overnightShifts, function ($shift) {
            $startDate = strtotime($shift['start_date']);
            $endDate = strtotime($shift['end_date']);
            $dateDifference = $endDate - $startDate;
            $oneDayInSeconds = 24 * 60 * 60;

            return $dateDifference == $oneDayInSeconds;
        });
    }

    return $overnightShifts;
}

/**
 * @param $shifts
 * @param $startDate
 * @return bool
 */
function isConsecutiveShift($shifts, $startDate)
{
    if (empty($shifts)) {
        return false;
    }

    $endDates = array_column($shifts, 'end_date');
    sort($endDates);

    $startDateTimestamp = strtotime($startDate);

    $lastEndDateTimestamp = strtotime('+1 day', strtotime(end($endDates)));

    return $startDateTimestamp >= $lastEndDateTimestamp;
}

/**
 * @param array $users
 * @param string $startDate
 * @param array $location
 * @return array
 */
function getAvailableUsers(array $users, string $startDate, array $location): array
{
    return array_filter($users, function ($user) use ($startDate, $location) {
        return isUserAvailable($user, $startDate, $location);
    });
}

/**
 * Kullanıcının nöbet atanabilirliğini kontrol eden fonksiyon
 *
 * @param array $user
 * @param string $currentDateFormatted
 * @param array $location
 * @return bool
 */
function isUserAvailable(array $user, string $currentDateFormatted, array $location): bool
{
    $sameDayShifts = getSameDayShifts($user['id'], $currentDateFormatted);
    $overnightShifts = getOvernightShifts($user['id'], $currentDateFormatted, $location);

    $isUnderMaxShifts = count($sameDayShifts) < 3;
    $isNotConsecutive = !isConsecutiveShift($sameDayShifts, $currentDateFormatted);
    $isNotInOvernight = empty($overnightShifts) || !$location['overnight'];

    return $isUnderMaxShifts && $isNotConsecutive && $isNotInOvernight;
}