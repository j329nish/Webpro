<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <title>和英辞典</title>
    <style>
        body {
            overflow-x: hidden;
        }
    </style>
    <link rel="stylesheet" href="12.css">
    <script>
        function adjustScale() {
            var heightScale = window.innerHeight / 550;
            var widthScale = window.innerWidth / 1230;
            var scale = Math.min(heightScale, widthScale);
            document.body.style.transform = 'scale(' + scale + ')';
        }

        window.addEventListener('resize', adjustScale);
        window.addEventListener('load', adjustScale);
        window.history.scrollRestoration = 'manual';
    </script>
</head>

<body>
    <h1 style="text-align: center;">
        <span style="display: inline-block; text-align: center; width: 50%;" class="glowing-text">
            <font size="7">和英辞典</font>
        </span>
        <span style="display: inline-block; text-align: right; width: 50%;">
            <a href="12.php">戻る</a>
        </span>
    </h1>
    <form action="12-3.php" method="POST">
        <table class="table3" align="center" cellpadding=5>
            <tr>
                <td>
                    <input type="text" name="meaning" placeholder="日本語を入力してください" size=20>
                </td>
                <td>
                    <input type="submit" value="意味を表示する">
                </td>
                <td>
                    <span id="number"></span>
                </td>
            </tr>
        </table>
    </form><br>
    <?php
    $con = mysqli_connect('localhost', 'j329nish', '') or die("接続失敗");
    mysqli_select_db($con, 'j329nish') or die("選択失敗");
    mysqli_query($con, 'SET NAMES utf8');
    $meaning = $_POST['meaning'];
    $meaning = addslashes($meaning);
    $sql = "SELECT * FROM svl5000 WHERE meaning LIKE '%$meaning%'";
    $res = mysqli_query($con, $sql) or die("エラー");
    echo "<table align='center'><tr>";
    $i = 0;
    while ($db = mysqli_fetch_assoc($res)) {
        if ($i % 3 == 0 && $i != 0) {
            echo "</tr><tr>";
        }
        if ($i < 45) {
            echo "<td><table class='table1' border=1 cellpadding=0 cellspacing=0><tr><td>英単語</td><td>${db['word']}</td></tr>";
            echo "<tr><td>レベル</td><td>${db['level']}</td></tr>";
            echo "<tr><td>意味</td><td>${db['meaning']}</td></tr></table></td>";
        } else {
            echo "<td><table class='table2' border=1 cellpadding=0 cellspacing=0><tr><td>英単語</td><td>${db['word']}</td></tr>";
            echo "<tr><td>レベル</td><td>${db['level']}</td></tr>";
            echo "<tr><td>意味</td><td>${db['meaning']}</td></tr></table></td>";
        }
        $i++;
    }
    if ($i == 0) {
        echo "<td><table class='table1' border=1 cellpadding=0 cellspacing=0><tr><td>英単語が見つかりませんでした...</td></tr></table></td>";
    }
    echo "</tr></table>";
    mysqli_close($con);
    echo "<script type='text/javascript'>document.getElementById('number').innerHTML = '表示数:' + $i;</script>"
        ?>
    <br><br>
</body>

</html>