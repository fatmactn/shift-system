<?php
include 'functions.php';
?>

<div class="shift-form">
    <h2>Nöbet Sihirbazı</h2>
    <div id="message" style='color: green;'></div>

    <form action="./modules/process_assign_shift.php" method="post">
        <div class="form-row" id="shift-form-wrapper">
            <?php echo renderInput('form-group', "start_date", "Başlangıç Tarihi:", "date", true); ?>
            <?php echo renderInput('form-group', "end_date", "Bitiş Tarihi:", "date", true); ?>
        </div>

        <?php echo renderButton("Çizelge Oluştur", "submit", "submit-button") ?>
    </form>
</div>

<?php
$sql = "SELECT sa.id, l.name as location_name, CONCAT(u.first_name, ' ', u.last_name) AS full_name, u.identity_number, sa.start_date, sa.end_date
        FROM shift_assignments sa
        INNER JOIN locations l ON sa.location_id = l.id
        INNER JOIN users u ON sa.user_id = u.id order by sa.id
        LIMIT :limitStart, :itemsPerPage";

$data = getData('shift_assignments', $_GET['page'] ?? 1, $_GET['limit'] ?? 10, $sql);
$items = $data['items'];
$totalPages = $data['pages'];
$itemCount = $data['total'];

?>

<div class="shift-list-form">
    <h2>Nöbet Listesi</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Tc</th>
            <th>Ad Soyad</th>
            <th>Lokasyon</th>
            <th>Başlangıç Tarihi</th>
            <th>Bitiş Tarihi</th>
        </tr>

        <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td><?php echo $item['identity_number']; ?></td>
                <td><?php echo $item['full_name']; ?></td>
                <td><?php echo $item['location_name']; ?></td>
                <td><?php echo $item['start_date']; ?></td>
                <td><?php echo $item['end_date']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php
    echo generatePagination($totalPages, $_GET['page'] ?? 1, 'define_shift_assignment')
    ?>

</div>