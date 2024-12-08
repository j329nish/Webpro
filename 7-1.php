<?php
if (isset($_GET['name']) && isset($_GET['score'])) {
    $fw = fopen("7-1.txt", "a"); // 追記する
    fwrite($fw, date("y/m/d H:i:s") . "|" . $_GET['name'] . "|" . $_GET['score'] . "\n");
    fclose($fw);
}
?>