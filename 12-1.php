<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <title>英和辞典</title>
    <style>
        body {
            overflow: hidden;
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
    </script>
</head>

<body>
    <h1 align="center">
        <span class="glowing-text" align="center" style="display: inline-block; width: 50%;">
            <font size="7">英和辞典</font>
        </span>
        <span align="right" style="display: inline-block; width: 50%;">
            <a href="12.php">戻る</a>
        </span>
    </h1>
    <form action="12-1.php" method="POST">
        <table class="table3" align="center" cellpadding=5>
            <tr>
                <td>
                    <input type="text" name="word" placeholder="英単語を入力してください" size=20>
                </td>
                <td>
                    <input type="submit" value="意味を表示する">
                </td>
            </tr>
        </table>
    </form><br>
    <?php
    $con = mysqli_connect('localhost', 'j329nish', '') or die("接続失敗");
    mysqli_select_db($con, 'j329nish') or die("選択失敗");
    mysqli_query($con, 'SET NAMES utf8');
    $word = $_POST['word'];
    $word = addslashes($word);
    $sql = "SELECT * FROM svl5000 WHERE word = '$word'";
    $res = mysqli_query($con, $sql) or die("エラー");
    echo "<table align='center'><tr>";
    $i = 0;
    while ($db = mysqli_fetch_assoc($res)) {
        echo "<td><table class='table1' border=1 cellpadding=0 cellspacing=0><tr><td>英単語</td><td>${db['word']}</td></tr>";
        echo "<tr><td>レベル</td><td>${db['level']}</td></tr>";
        echo "<tr><td>意味</td><td>${db['meaning']}</td></tr></table></td>";
        $i++;
    }
    if (isset($_POST['submit_button'])) {
        echo "<td><table class='table1' border=1 cellpadding=0 cellspacing=0><tr><td>何か英単語を入力してください。</td></tr></table></td>";
    } else if ($i == 0) {
        echo "<td><table class='table1' border=1 cellpadding=0 cellspacing=0><tr><td>英単語が見つかりませんでした...</td></tr></table></td>";
    }
    echo "</tr></table>";
    mysqli_close($con);
    ?>
    <br><br>
</body>

</html>