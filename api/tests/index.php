<?php
/*define('DB_SERVER', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_DATABASE', 'booking-engine-yp');
$db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
$result = mysqli_query($db,"SELECT * FROM category ORDER BY leftBound");
$hloubka = -1;
while ($row = mysqli_fetch_assoc($result)) {
    if ($hloubka < $row["level"]) {
        echo "<ul>";
    } else {
        echo str_repeat("</li></ul>", $hloubka - $row["level"]) . "</li>";
    }
    echo "<li>\n" . htmlspecialchars($row["name"]);
    $hloubka = $row["level"];
}
echo str_repeat("</li></ul>", $hloubka + 1) . "\n";
mysqli_free_result($result);*/

// Uncomment this line if you must temporarily take down your site for maintenance.
// require '.maintenance.php';
$container = require __DIR__ . '/bootstrap.php';

$container->getService('application')->run();