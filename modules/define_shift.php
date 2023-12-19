<?php
include 'functions.php';
?>

<div class="shift-form">
    <h2>Nöbet Yeri Tanımlama</h2>
    <div id="message" style='color: green;'></div>
    <?php
    $overnightOptions = [
        [
            "id" => 0,
            "name" => "Hayır"
        ], [
            "id" => 1,
            "name" => "Evet",
        ]
    ];
    ?>

    <form action="./modules/process_define_shift.php" method="POST">
        <div class="form-row">
            <?php echo renderInput('form-group', "shiftName", "Nöbet Yeri Adı:", "text", true); ?>
            <?php echo renderInput('form-group', "startTime", "Başlangıç Saati:", "time", true); ?>
            <?php echo renderInput('form-group', "endTime", "Bitiş Saati:", "time", true); ?>
            <?php echo renderSelect("overnight", "Gece Nöbeti", $overnightOptions); ?>
        </div>

        <?php echo renderButton("Kaydet", "submit", "submit-button") ?>
    </form>
</div>

<?php
$data = getData('locations', $_GET['page'] ?? 1, $_GET['limit'] ?? 10);
$items = $data['items'];
$totalPages = $data['pages'];
$itemCount = $data['total'];
?>

<div class="shift-list-form">
    <h2>Nöbet Yeri Listesi</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Nöbet Yeri Adı</th>
            <th>Başlangıç Saati</th>
            <th>Bitiş Saati</th>
            <th>Gece Nöbeti</th>
        </tr>

        <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo $item['start_time']; ?></td>
                <td><?php echo $item['end_time']; ?></td>
                <td><?php echo $item['overnight'] ? "Evet" : "Hayır"; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php
        echo generatePagination($totalPages, $_GET['page'] ?? 1, 'define_shift')
    ?>
</div>