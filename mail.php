#!/usr/bin/php
<?php
$email = "j520329b@mails.cc.ehime-u.ac.jp"; // 自分のメールアドレスでテストすること 
$from = "j520329b@mails.cc.ehime-u.ac.jp"; // 自分のメールアドレスでテストすること 
$header = "From: $from";
$sendmail_param = "-f$from"; //エラーメールの戻り先 ★超重要★ 
$subject = "メールの件名";
$body = "メールの本文はここに記入";
if (mb_send_mail($email, $subject, $body, $header, $sendmail_param)) {
    echo "メール送信に成功しました。\n";
} else {
    echo "メール送信に失敗しました。\n";
}