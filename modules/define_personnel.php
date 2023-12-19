<?php
include 'functions.php';
?>

<script>
    function getDistricts(cityId) {
        $.ajax({
            type: 'GET',
            url: 'modules/get_districts.php?city_id=' + cityId,
            success: function (response) {
                $('#district').html(response).show();
            }
        });
    }
</script>

<div class="shift-form">
    <h2>Personel Tanımlama</h2>
    <div id="message" style='color: green;'></div>

    <?php
    $cities = getData('cities')['items'];
    ?>

    <form action="./modules/process_define_personnel.php" method="post">
        <div class="form-row">
            <?php echo renderInput("form-group", "identity_number", "TC", "text", true); ?>
            <?php echo renderInput("form-group", "first_name", "Ad", "text", true); ?>
            <?php echo renderInput("form-group", "last_name", "Soyad", "text", true); ?>
            <?php echo renderInput("form-group", "birth_date", "Doğum Tarihi", "date", true); ?>
            <?php echo renderInput("form-group", "phone", "Telefon", "text", true); ?>
            <?php echo renderInput("form-group", "email", "Email", "text", true); ?>
            <?php echo renderSelect("city", "İl", $cities); ?>
            <?php echo renderSelect("district", "İlçe"); ?>
            <?php echo renderInput("form-group", "address", "Adres", "text", true); ?>
        </div>

        <?php echo renderButton("Kaydet", "submit", "submit-button") ?>
    </form>
</div>

<?php
$data = getData('users', page: $_GET['page'] ?? 1, limit: $_GET['limit'] ?? 10);
$items = $data['items'];
$totalPages = $data['pages'];
$itemCount = $data['total'];
?>

<div class="shift-list-form">
    <h2>Personel Listesi</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Tc</th>
            <th>Ad Soyad</th>
            <th>Doğum Tarihi</th>
            <th>Telefon</th>
            <th>Email</th>
        </tr>

        <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td><?php echo $item['identity_number']; ?></td>
                <td><?php echo $item['first_name'] . " " . $item['last_name']; ?></td>
                <td><?php echo $item['birth_date']; ?></td>
                <td><?php echo $item['phone']; ?></td>
                <td><?php echo $item['email']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php
        echo generatePagination($totalPages, $_GET['page'] ?? 1, 'define_personnel')
    ?>
</div>