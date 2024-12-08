<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
</head>

<body>
    <h3>注文確認</h3>
    <a href="javascript:history.back();">前のページに戻る</a><br><br>
    <?php
    error_reporting(E_ALL ^ E_NOTICE);
    if ($_POST['send'] == 'メールを送信する') {
        $email = $_POST['電子メール'];
        $uketsuke = $_POST['uketsuke'];
        $header = "From: $uketsuke\n";
        $header .= "Cc: $uketsuke\n";
        $sendmail_param = "-f$uketsuke";
        $subject = '注文受付';
        $body = "注文ありがとうございます。\n 下記の注文を受け付けました。\n\n";
        foreach ($_POST as $key => $val) {
            $body .= "$key = $val\n";
        }
        if (mb_send_mail($email, $subject, $body, $header, $sendmail_param)) {
            echo '注文を確かに受け付けました。<br>' . "\n";
        } else {
            echo 'メールの送信に失敗しました。<br>' . "\n";
        }
    } else {
        echo '<form action="3.php" method="POST">' . "\n";
        foreach ($_POST as $key => $val) {
            $_val = htmlspecialchars($val);
            echo "$key = $_val<br>\n";
            echo "<input type=\"hidden\" name=\"$key\" value=\"$_val\">\n";
        }
        echo '<br><input type="submit" name="send" value="メールを送信する"></form>' . "\n";
    }
    ?>
</body>

</html>