<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <title>今はまだ開発中です</title>
    <style>
        body {
            font-family: Georgia, serif;
            background-color: #333;
            color: #fff;
            padding: 20px;
        }

        .blackboard {
            background-color: #0a5429;
            padding: 20px;
            border: 10px solid sienna;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            width: 830px;
            margin: 0 auto;
        }

        input[name="name"],
        input[type="password"],
        select[name="delete"] {
            border: 2px solid #333;
            height: 20px;
            color: white;
            background-color: #0a5429;
        }

        input[value=" 記事を投稿する "],
        input[value=" 記事を削除する "] {
            font-family: Georgia, serif;
            border: 2px solid #333;
            height: 40px;
            width: 180px;
            font-size: 20px;
            border-radius: 10px;
            color: #333;
            background-color: sienna;
        }

        textarea[name="text"] {
            font-family: Georgia, serif;
            border: 2px solid #333;
            color: white;
            background-color: #0a5429;
        }

        input[value=" 記事を投稿する "]:hover,
        input[value=" 記事を削除する "]:hover {
            background-color: #bdbdbd;
        }

        input[value=" 記事を投稿する "]:active,
        input[value=" 記事を削除する "]:active {
            background-color: dimgray;
        }
    </style>
</head>

<body>
    <div class="blackboard">
        <font size="5"><b>Web 掲示板 ここにテーマを記入(18 文字以下)</b></font>
        <table style="background-color: #333;">
            <tr>
                <td>
                    <?php
                    $fctxt = fopen("counter.txt", "r+");
                    $counter = rtrim(fgets($fctxt));
                    $counter++;
                    rewind($fctxt);
                    fputs($fctxt, $counter);
                    fclose($fctxt);
                    echo 'あなたは ' . $counter . '人目の訪問者です。';
                    $d = date("Y/m/d H:i:s");
                    echo "現在の日時：$d";
                    ?>
                </td>
            </tr>
        </table>
        <table cellpadding=10 cellspacing=0 width=825 style="border: 2px solid #333;">
            <tr>
                <td>
                    <pre>
<?php
$f = file_get_contents("5.txt");
$item = explode("\n<END>\n", $f);
for ($i = 0; $i < count($item) - 1; $i++) {
    list($header, $body) = explode("\n", $item[$i], 2);
    list($date, $ip, $host, $name) = explode(',', $header);
    list($text, $imgfile) = explode("\n<IMG>\n", $body);
    $extention = explode('.', $imgfile);
    $imgfile2 = explode('/', $imgfile);
    $showtext = trim($text);
    echo '<font size="4" color="midnightblue"><u><b>' . ($i + 1) . ": $date $host($ip) $name</b></u></font>\n";
    echo '<div style="font-family: Georgia, serif;">' . htmlspecialchars("$text\n") . '</div>';
    if ($imgfile != '') {
        if (!in_array($extention[1], ['png', 'jpg', 'jpeg', 'gif', 'svg'])) {
            echo "<a href='$imgfile'>" . $imgfile2[1] . "</a>";
        } else {
            echo "<img src='$imgfile' width='300'>";
        }
        echo "\n";
    }
    echo "\n";
}
?>
</pre>
                </td>
            </tr>
        </table><br>
        <table cellpadding=0 cellspacing=0>
            <tr>
                <td>
                    <form enctype="multipart/form-data" action="5_submit.php" method="POST">
                        <table cellpadding=10 cellspacing=0 style="border: 2px solid #333;">
                            <form enctype="multipart/form-data" action="5_submit.php" method="POST">
                                <table cellpadding=10 cellspacing=0 style="border: 2px solid #333;">
                                    <tr>
                                        <td>氏名：<input type="text" name="name" size=20>　(15文字以内で入力)</td>
                                    </tr>
                                    <tr>
                                        <td><textarea name="text" rows=5 cols=80></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>添付ファイル：<input type="file" name="upfile" size=100></td>
                                    </tr>
                                    <tr>
                                        <td>パスワード：<input type="password" name="passwd" size=10></td>
                                    </tr>
                                    <tr>
                                        <td><input type="submit" name="send" value=" 記事を投稿する "></td>
                                    </tr>
                                </table>
                </td>
                <td>　</td>
                <td>
                    <table cellpadding=10 cellspacing=0 style="border: 2px solid #333;">
                        <tr>
                            <td>削除する記事：<br>
                                <select name="delete">
                                    <option value="">記事番号</option>
                                    <?php
                                    for ($i = 1; $i < count($item); $i++) {
                                        echo "<option value=\"$i\">$i</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>パスワード：<br><input type="password" name="adminpasswd" size=10></td>
                        </tr>
                        <tr>
                            <td><input type="submit" name="send" value=" 記事を削除する "></td>
                        </tr>
                    </table>
                    <?php
                    for ($i = 0; $i < 6; $i++) {
                        echo "<br>";
                    }
                    ?>
                    </form>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>