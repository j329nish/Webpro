<pre>
<?php
function normalize($v) // GPS 情報は "35/1" のような書式となるので、小数に変換 
{
    $frac = explode("/", $v);
    if (count($frac) != 2)
        return $v;
    if ($frac[1] == 0)
        return 0; // ←この行を挿入する 
    return $frac[0] / $frac[1];
}
function normalizeLatLng($value) // 基本的に、緯度と経度は「度;分;秒」に分かれているので合成 
{
    if (count($value) == 1) { //「度;分;秒」に分かれていない 
        return normalize($value); // 配列ではなく変数として扱う 
    } else if (count($value) == 3) {
        $degrees = normalize($value[0]);
        $minutes = normalize($value[1]);
        $seconds = normalize($value[2]);
        return $degrees + ($minutes / 60.0) + ($seconds / 3600);
    }
}
exec("find /home/[jk]*/public_html/photo -type d", $find); // 各アカウントの中の写真を漁る 
echo count($find) . "個のディレクトリが見つかりました。\n";
$fw = fopen("13.csv", "w");
for ($i = 0; $i < count($find); $i++) {
    if (file_exists("$find[$i]/list.txt")) {
        $listfile = file("$find[$i]/list.txt");
        $db = [];
        for ($j = 0; $j < count($listfile); $j++) {
            $a = explode(',', rtrim($listfile[$j]));
            if (count($a) > 1)
                $db["$find[$i]/$a[0]"] = $a[1];
        }
    }
    foreach (glob("$find[$i]/*.*") as $full_filename) {
        $a = explode('/', $full_filename, 6);
        $account = $a[2];
        $filename = $a[5];
        if (isset($db[$full_filename]))
            $comment = $db[$full_filename];
        else
            $comment = '';
        $exif = @exif_read_data($full_filename, 0, true); //Exif 情報の取得 
        if ($exif) {
            $date = $lat = $lng = $alt = '';
            foreach ($exif as $key => $section) {
                foreach ($section as $name => $value) {
                    if ("$key.$name" == 'EXIF.DateTimeOriginal')
                        $date = $value;
                    else if ("$key.$name" == 'GPS.GPSLatitude')
                        $lat = normalizeLatLng($value);
                    else if ("$key.$name" == 'GPS.GPSLongitude')
                        $lng = normalizeLatLng($value);
                    else if ("$key.$name" == 'GPS.GPSAltitude')
                        $alt = normalize($value);
                }
            }
            fwrite($fw, "$account,$filename,$date,$lat,$lng,$alt,$comment\n");
            echo "$account,$filename,$date,$lat,$lng,$alt,$comment\n";
        }
    }
}
fclose($fw);
?> 
</pre>