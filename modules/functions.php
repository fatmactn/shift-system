<?php
$configPath = __DIR__ . '/../config.php';
include $configPath;
$config = include $configPath;

$host = $config['database']['host'];
$username = $config['database']['username'];
$password = $config['database']['password'];
$name = $config['database']['name'];

/**
 * @return PDO
 */
function dbConnection()
{
    global $host, $username, $password, $name;

    $conn = new PDO("mysql:host=$host;dbname=$name", $username, $password);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
}

/**
 * @param array $parameters
 * @param $sql
 * @return bool
 */
function insertData(array $parameters, $sql)
{
    $conn = dbConnection();

    $stmt = $conn->prepare($sql);

    //parametreler bind edilir
    foreach ($parameters as $key => &$value) {
        $paramKey = ':' . ltrim($key, ':');

        $stmt->bindParam($paramKey, $value);
    }

    return $stmt->execute();
}

/**
 * @param $tableName
 * @param null $sql
 * @param $page
 * @param $limit
 * @return array
 */
function getData($tableName, $page = null, $limit = null, $sql = null): array
{
    $conn = dbConnection();

    $limitStart = ($page - 1) * $limit;

    if ($sql == null) {
        $sql = "SELECT * FROM {$tableName}" . (($limit && $page) ? " LIMIT :limitStart, :itemsPerPage" : "");
    }

    $stmt = $conn->prepare($sql);

    if ($page && $limit) {
        $stmt->bindParam(':limitStart', $limitStart, PDO::PARAM_INT);
        $stmt->bindParam(':itemsPerPage', $limit, PDO::PARAM_INT);
    }

    $stmt->execute();

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalItems = $conn->query("SELECT COUNT(*) FROM $tableName")->fetchColumn();

    $totalPages = $limit ? ceil($totalItems / $limit) : 1;

    return [
        'items' => $items,
        'total' => $totalItems ?? 0,
        'pages' => $totalPages ?? 0,
    ];
}

/**
 * @param $sql
 * @param $parameters
 * @return array|false
 */
function getCustomData($sql, $parameters)
{
    $conn = dbConnection();

    try {
        $stmt = $conn->prepare($sql);

        foreach ($parameters as $key => &$value) {
            $paramKey = ':' . ltrim($key, ':');
            $stmt->bindParam($paramKey, $value);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("PDO Error: " . $e->getMessage());
    }
}


/**
 * @param string $divClass
 * @param string $id
 * @param string $label
 * @param string $type
 * @param bool $required
 * @return string
 */
function renderInput(string $divClass, string $id, string $label, string $type, bool $required): string
{
    $html = "<div class='$divClass'>";
    $html .= "<label for=\"$id\">$label</label>";
    $html .= "<input type=\"$type\" id=\"$id\" name=\"$id\"";
    if ($required) {
        $html .= " required";
    }
    $html .= "></div>";

    return $html;
}

/**
 * @param string $name
 * @param string $labelText
 * @param array|null $options
 * @return string
 */
function renderSelect(string $name, string $labelText, array $options = null): string
{
    $html = '<div class="form-group">';
    $html .= "<label for=\"$name\">$labelText</label>";
    $html .= "<select id=\"$name\" name=\"$name\"";

    if ($name == 'city') {
        $html .= ' onchange="getDistricts(this.value)"';
    }

    $html .= '>';
    $html .= "<option value=\"\">Seçiniz</option>";

    if ($options !== null) {
        foreach ($options as $option) {
            $value = $option["id"];
            $name = $option["name"];
            $html .= "<option value=\"$value\">$name</option>";
        }
    }

    $html .= "</select>";
    $html .= '</div>';

    return $html;
}

/**
 * @param string $name
 * @param string $type
 * @param string $class
 * @return string
 */
function renderButton(string $name, string $type, string $class): string
{
    $html = '<div class="form-group">';
    $html .= "<button type=\"$type\" class=\"$class\">$name</button>";
    $html .= '</div>';

    return $html;
}

function generatePagination($totalPages, $currentPage, $contentParam, $maxPagesToShow = 3)
{
    $startPage = max(1, $currentPage - floor($maxPagesToShow / 2));
    $endPage = min($totalPages, $startPage + $maxPagesToShow - 1);

    $paginationHtml = '<div class="pagination">';

    if ($startPage > 1) {
        $paginationHtml .= '<a href="?content=' . $contentParam . '&page=1">&#171;</a>';
    }

    for ($i = $startPage; $i <= $endPage; $i++) {
        $isActive = ($i == $currentPage) ? 'active' : '';
        $paginationHtml .= '<a href="?content=' . $contentParam . '&page=' . $i . '" class="' . $isActive . '">' . $i . '</a>';
    }

    if ($endPage < $totalPages) {
        $paginationHtml .= '<a href="?content=' . $contentParam . '&page=' . $totalPages . '">&#187;</a>';
    }

    $paginationHtml .= '</div>';

    return $paginationHtml;
}