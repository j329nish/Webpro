<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <title>語彙力テスト(英単語から日本語)</title>
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
    <h1 style="text-align: center;">
        <span style="display: inline-block; text-align: center; width: 100%;" class="glowing-text">
            <font size="7">語彙力テスト(英単語から日本語)</font>
        </span>
        <span style="display: inline-block; text-align: right; width: 50%;">
            <a href="12.php">戻る</a>
        </span>
    </h1>
    <form action="12-2.php" method="POST">
        <?php
        session_start();
        if (isset($_POST['r'])) {
            list($index, $word) = explode(".", $_POST['r']);
            if ($index == $_SESSION['answer']) {
                echo "<table class='table3' align='center' cellpadding=5><tr><td><span id='istrue'>正解です。</span></td><td>";
                echo "<form action='12-2.php' method='POST'>";
                echo "<input type='submit' value='語彙力テスト(英単語から日本語)へ戻る'>";
                echo "</form></td></tr></table>";
                $con = mysqli_connect('localhost', 'j329nish', '') or die("接続失敗");
                mysqli_select_db($con, 'j329nish') or die("選択失敗");
                mysqli_query($con, 'SET NAMES utf8');
                $level = $_SESSION['level'];
                $id = $_SESSION['id'];
                $date = date("Y-m-d H:i:s");
                $sql = "INSERT INTO user(level, id, dt1) VALUES('$level','$id','$date')";
                $res = mysqli_query($con, $sql) or die("エラー");
                mysqli_close($con);
            } else {
                $con = mysqli_connect('localhost', 'j329nish', '') or die("接続失敗");
                mysqli_select_db($con, 'j329nish') or die("選択失敗");
                mysqli_query($con, 'SET NAMES utf8');
                $word = $_SESSION['word'];
                $word = addslashes($word);
                $sql = "SELECT * FROM svl5000 WHERE word = '$word'";
                $res = mysqli_query($con, $sql) or die("エラー");
                echo "<table class='table3' align='center' cellpadding=5><tr><td><span id='istrue'>不正解です。答えを表示します。</span></td><td>";
                echo "<form action='12-2.php' method='POST'>";
                echo "<input type='submit' value='語彙力テスト(英単語から日本語)へ戻る'>";
                echo "</form></td></tr></table><br>";
                while ($db = mysqli_fetch_assoc($res)) {
                    echo "<table align='center'><tr><td>";
                    echo "<table class='table1' border=1 cellpadding=0 cellspacing=0><tr><td>英単語</td><td>${db['word']}</td></tr>";
                    echo "<tr><td>レベル</td><td>${db['level']}</td></tr>";
                    echo "<tr><td>意味</td><td>${db['meaning']}</td></tr></table>";
                    echo "</td></tr></table>";
                }
                mysqli_close($con);
            }
            session_unset();
        } else {
            $con = mysqli_connect('localhost', 'j329nish', '') or die("接続失敗");
            mysqli_select_db($con, 'j329nish') or die("選択失敗");
            mysqli_query($con, 'SET NAMES utf8');
            $sql = "SELECT * FROM user WHERE dt1 is not null";
            $res = mysqli_query($con, $sql) or die("エラー");
            $d = array();
            while ($db = mysqli_fetch_assoc($res)) {
                $d[$db['level'] * 1000 + $db['id'] * 1000 - 1] = 1;
            }
            while (1) {
                $r = rand(1000, 5999);
                if (!isset($d[$r]))
                    break;
            }
            $level = (int) ($r / 1000);
            $id = $r % 1000 + 1;
            $sql = "SELECT * FROM svl5000 WHERE level=$level and id=$id";
            $res = mysqli_query($con, $sql) or die("エラー");
            $db = mysqli_fetch_assoc($res);
            echo "<table class='table3' align='center' cellpadding=5><tr><td>";
            echo "<span id='question'>" . $db['word'] . "の意味は下記の中のどれでしょうか？</span></td><td>";
            echo "<span id='number'>あなたの得点：" . count($d) . "</span></td></tr></table><br>";
            $meaning = $db['meaning'];
            $rp = rand(1, 5);
            $_SESSION['level'] = $level;
            $_SESSION['id'] = $id;
            $_SESSION['answer'] = $rp;
            $_SESSION['word'] = $db['word'];
            echo "<table class='table3' align='center' cellpadding=0>";
            for ($i = 1; $i <= 5; $i++) {
                if ($i == $rp) {
                    echo "<tr><td><input type='submit' name='r' value='" . $i . "." . $meaning . "'></td></tr>";
                } else {
                    $r = rand(1000, 5999);
                    $level = (int) ($r / 1000);
                    $id = $r % 1000 + 1;
                    $sql = "SELECT * FROM svl5000 WHERE level=$level and id=$id";
                    $res = mysqli_query($con, $sql) or die("エラー");
                    $db = mysqli_fetch_assoc($res);
                    echo "<tr><td><input type='submit' name='r' value='" . $i . "." . $db['meaning'] . "'></td></tr>";
                }
            }
            echo "</tr></table>";
            mysqli_close($con);
        }
        ?>
    </form>
    <br><br>
</body>

</html>