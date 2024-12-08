<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
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

        input[value="本当に投稿しますか?"],
        input[value="本当に削除しますか?"] {
            font-family: Georgia, serif;
            border: 2px solid #333;
            height: 40px;
            width: 210px;
            font-size: 20px;
            border-radius: 10px;
            color: #333;
            background-color: sienna;
        }

        input:hover {
            background-color: #bdbdbd;
        }

        input:active {
            background-color: dimgray;
        }
    </style>
</head>

<body>
    <b>Web 掲示板 ここにテーマを記入(18 文字以下)</b><br><br>
    <div class="blackboard">
        <?php
        $f = file_get_contents("5.txt");
        $item = explode("\n<END>\n", $f);
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $text = isset($_POST['text']) ? $_POST['text'] : '';
        $upfile = isset($_FILES['upfile']['tmp_name']) ? $_FILES['upfile']['tmp_name'] : '';
        $delete = isset($_POST['delete']) ? $_POST['delete'] : '';
        $_POST['send'] = isset($_POST['send']) ? $_POST['send'] : '';
        if ($_POST['send'] == '本当に削除しますか?') { //確認画面で記事を削除したとき
            $data = file("5.txt");
            $f = fopen("5.txt", "w");
            $end = 1;
            $i = 0;
            while (isset($data[$i]) && $data[$i] !== '') {
                if ($end != $delete) {
                    fwrite($f, $data[$i]);
                } else if (trim($data[$i]) === "<IMG>") {
                    $deletefile = trim($data[$i + 1]);
                    if (file_exists($deletefile)) {
                        echo "削除されたファイル -> $deletefile<br><br>";
                        unlink($deletefile);
                    }
                    $i++;
                }
                if (trim($data[$i]) === "<END>") {
                    $end++;
                }
                $i++;
            }
            fclose($f);
            echo "記事が削除されました。<br><br>";
        } else if ($delete != '' && $_POST['adminpasswd'] != '' && $_POST['send'] == ' 記事を削除する ') { //記事を削除するとき
            $data = file("5.txt");
            $end = 1;
            $i = 0;
            while (isset($data[$i]) && $data[$i] !== '') {
                if ($end == $delete) {
                    break;
                }
                if (trim($data[$i]) === "<END>") {
                    $end++;
                }
                $i++;
            }
            $info = explode(',', trim($data[$i]));
            if (md5($_POST['adminpasswd']) == '8d3d28731b3cbb6ef8dd8902119fa730' || md5($_POST['adminpasswd']) == $info[4]) {
                echo '<form action="5_submit.php" method="POST">';
                echo "$delete";
                echo "番目の記事を削除しますか?<br><br>\n";
                echo "<input type=\"hidden\" name=\"delete\" value=\"$delete\">\n";
                echo '<br><input type="submit" name="send" value="本当に削除しますか?"></form>' . "<br>\n";
            } else {
                echo "パスワードが違います。<br><br>\n";
                echo '<a href="javascript:history.back()">戻る</a>';
                echo "</body></html>\n";
                exit;
            }
        } else if ($_POST['send'] == '本当に投稿しますか?') { //確認画面で投稿を押したとき
            $date = date("Y/m/d H:i:s");
            $ip = $_SERVER['REMOTE_ADDR'];
            $host = gethostbyaddr($ip);
            $f = fopen("5.txt", "a");
            fwrite($f, "$date,$ip,$host,$name," . md5($_POST['passwd']) . "\n");
            fwrite($f, "$text");
            fwrite($f, "\n<IMG>\n");
            if ($_POST['uploadfilename'] != '') {
                fwrite($f, "upload/{$_POST['uploadfilename']}");
            }
            fwrite($f, "\n<END>\n");
            fclose($f);
            echo "投稿が終わりました。<br><br>";
        } else if ($name != '' && $text != '' && $_POST['passwd'] != '' && $_POST['send'] == ' 記事を投稿する ') {  //投稿フォームに書き込んで投稿を押したとき
            if (strlen($name) > 15) {
                echo "名前は15文字以内で入力して下さい。<br><br>\n";
                echo '<a href="javascript:history.back()">戻る</a>';
                echo "</body></html>\n";
                exit;
            }
            $upfile_name = '';
            $upfile_type = '';
            $uploadfilename = '';
            if ($upfile != '') {
                $upfile_name = $_FILES['upfile']['name'];
                $upfile_type = $_FILES['upfile']['type'];
                $uploadfilename = date("ymdHis") . $upfile_name;
                move_uploaded_file($upfile, "upload/$uploadfilename");
            }
            echo '<form action="5_submit.php" method="POST">';
            foreach ($_POST as $key => $val) {
                $_val = htmlspecialchars($val);
                if ($key == 'name') {
                    echo "投稿主 -> $_val<br>\n";
                } else if ($key == 'text') {
                    echo "投稿内容 -> $_val<br>\n";
                }
                echo "<input type=\"hidden\" name=\"$key\" value=\"$_val\">\n";
            }
            if ($upfile_name != '') {
                echo "ファイル -> $uploadfilename<br>\n";
                echo "<img src='upload/$uploadfilename' width='300'><br>\n";
            }
            echo "<input type=\"hidden\" name=\"uploadfilename\" value=\"$uploadfilename\">\n";
            echo '<br><br><input type="submit" name="send" value="本当に投稿しますか?"></form>' . "<br>\n";
        } else { //投稿フォームに全て入力していないとき
            echo "全ての項目を記入して下さい。<br><br>\n";
            echo '<a href="javascript:history.back()">戻る</a>';
            echo "</body></html>\n";
            exit;
        }
        ?>
        <a href="5.php">掲示板に戻る</a><br><br>
    </div>
</body>

</html>