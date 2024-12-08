<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <title>写真データベース</title>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCzK1MNll10T76kaYCf3eFxhzmvbQ6Hf0c&libraries=geometry&langu
age=ja">
</script>
    <script type="text/javascript" src="https://zeptojs.com/zepto.min.js"></script>
    <style type="text/css">
        * {
            margin: 0;
        }

        html {
            margin: 0;
            height: 100%
        }

        body {
            margin: 0;
            height: 100%;
            background-color: rgb(0, 167, 100);
        }

        input[type="button"],
        select {
            color: white;
            background-color: black;
        }

        select {
            height: 25px;
        }

        .header {
            border-bottom: double 5px black;
        }
    </style>
</head>

<body>
    <h1 align="center">
        <font size="5"><span class="header">写真データベース</span></font>
    </h1><br>
    <div>
        <input type="button" value="初期位置"
            onclick="zoom=8;map.setOptions({center:new google.maps.LatLng(clat, clng),zoom:8})">
        <input type="button" value="全域表示"
            onclick="zoom=2;map.setOptions({center:new google.maps.LatLng(39.65085050604078, 66.96707527277876),zoom:2})">
        <input type="button" value="広域" onclick="zoom--;if(zoom<0)zoom=0;map.setOptions({zoom:zoom})">
        <input type="button" value="詳細" onclick="zoom++;if(zoom>21)zoom=21;map.setOptions({zoom:zoom})">　
        <select name="time" onchange="display()">
            <option value="時間検索">時間検索</option>
        </select>
        <select name="alt" onchange="display()">
            <option value="高度検索">高度検索</option>
        </select>　
        <script>
            var time = ["2024年", "2023年", "2022年", "2021年", "2020年", "2019年以前"];
            var alt = ["0m未満", "0~149m", "150~299m", "300~449m", "450~599m", "600~749m", "750~899m", "900m以上"];

            var select1 = document.querySelector('select[name="time"]');
            var select2 = document.querySelector('select[name="alt"]');

            for (var ti = 0; ti < time.length; ti++) {
                var option = document.createElement('option');
                option.value = time[ti];
                option.text = time[ti];
                select1.appendChild(option);
            }

            for (var al = 0; al < alt.length; al++) {
                var option = document.createElement('option');
                option.value = alt[al];
                option.text = alt[al];
                select2.appendChild(option);
            }
        </script>
        <input type="button" value="表示エリア内の写真を見る" onClick="update()">
        <input type="button" value="写真データベースを更新する" onClick="update_all()">
        <input type="button" value="説明" onclick="explain()">
    </div>
    <div id="map" style="float:left;height:83.4%;width:65%;border:solid 1px;"></div>
    <div id="list" style="float:left;height:83.4%;width:34%;overflow-x:scroll;overflow-y:scroll;"></div>
    <script type="text/javascript">
        var clat = 33.849231;
        var clng = 132.769846;
        var zoom = 8;
        var swlat; //南西の角の緯度 
        var swlng; //南西の角の経度 
        var nelat; //北東の角の緯度 
        var nelng; //北東の角の経度 
        var marker = []; //マーカー 
        var db = []; //写真データベース 
        var cnt = 0;
        function update() {
            cnt = 0;
            var table = "<table border=1 cellspacing=0>\n<tr>";
            for (i = ii = 0; i < db.length; i += 7, ii++) {
                if (marker[ii] && (swlat < db[i + 3]) && (db[i + 3] < nelat) && (swlng < db[i + 4]) && (db[i + 4] < nelng) && (db[i + 6] != '')) {
                    table += "<td><a target=\"photo\" href=\"/~" + db[i] + "/photo/" + db[i + 1] + "\">";
                    table += "<img src=\"/~" + db[i] + "/photo/" + db[i + 1] + "\" width=192></a><br>";
                    table += '<font size="-1">' + db[i + 6] + '</font></td>';
                    cnt++;
                    if (cnt == 20) break;
                    if (cnt % 2 == 0) table += "</tr>\n<tr>";
                }
            }
            for (j = i + 7; j < db.length; j += 7) {
                if ((swlat < db[j + 3]) && (db[j + 3] < nelat) && (swlng < db[j + 4]) && (db[j + 4] < nelng) && (db[j + 6] != '')) {
                    cnt++;
                }
            }
            table += "</tr>\n</table>\n";
            table += (db.length / 7) + "件中 " + cnt + "件ありました。（最大 20 件の表示)";
            table += "<input type='button' value='次の20件の表示' onClick='next()'></input>\n<br><br>";
            document.getElementById("list").innerHTML = table;
        }
        function next() {
            var table = "<table border=1 cellspacing=0>\n<tr>";
            var cntNext = 0;
            for (i = i + 7; i < db.length; i += 7) {
                if ((swlat < db[i + 3]) && (db[i + 3] < nelat) && (swlng < db[i + 4]) && (db[i + 4] < nelng) && (db[i + 6] != '')) {
                    table += "<td><a target=\"photo\" href=\"/~" + db[i] + "/photo/" + db[i + 1] + "\">";
                    table += "<img src=\"/~" + db[i] + "/photo/" + db[i + 1] + "\" width=192></a><br>";
                    table += '<font size="-1">' + db[i + 6] + '</font></td>';
                    cntNext++;
                    if (cntNext == 20) break;
                    if (cntNext % 2 == 0) table += "</tr>\n<tr>";
                }
            }
            table += "</tr>\n</table>\n";
            table += (db.length / 7) + "件中 " + cnt + "件ありました。（最大 20 件の表示)";
            table += "<input type='button' value='次の20件の表示' onClick='next()'></input>\n<br><br>";
            document.getElementById("list").innerHTML = table;
        }
        function explain() {
            listId = 1;
            var explain = '<h1 align="center"><font size="3">説明</font></h1>' +
                '<table><tr><td>1.これはデータベース上の写真が表示されるマップです。</td></tr><tr><td>' +
                '2.赤いピンを押すと、そのピンの写真がズームされて表示されます。</td></tr><tr><td>' +
                '3.「初期位置」ボタンを押すと、サイトを更新したときのマップ位置に戻ります。</td></tr><tr><td>' +
                '4.「全域表示」ボタンを押すと、データベースにある写真の位置全体が表示されます。</td></tr><tr><td>' +
                '5.「広域」ボタンを押すとより大きい範囲の地図が表示され、「詳細」ボタンを押すとより小さい範囲のマップが表示されます。</td></tr><tr><td>' +
                '6.「表示エリア内の写真を見る」ボタンを押すと、マップの右側に表示範囲内の写真が最大20件表示されます。<br>' +
                '「次の20件の表示」ボタンを押すと、表示された写真以外のマップ内にある写真が最大20件表示されます。</td></tr><tr><td>' +
                '7.「写真データベースを更新する」ボタンを押すと、データベースの写真が更新されます。</td></tr><tr><td>' + 
                '8.「時間検索」メニューと「高度検索」メニューで表示する写真を絞ることができます。</td></tr></table>';
            document.getElementById("list").innerHTML = explain;
        }
        function set_db() {
            var _d = new Date().getTime();
            $.get("13.csv" + "?_d=" + _d, function (data) {
                db = [];
                var a = data.split("\n"); //改行で区切る 
                for (i = 0; i < a.length - 1; i++) {
                    var b = a[i].split(","); //カンマで区切る 
                    for (j = 0; j < b.length; j++) {
                        db[i * 7 + j] = b[j];
                    }
                }
                display();
            });
        }
        function update_all() {
            var _d = new Date().getTime();
            $.get("13_makecsv.php" + "?_d=" + _d, function (data) {
                document.getElementById("list").innerHTML = data;
                set_db();
            });
        }
        function bchanged() {
            var b = map.getBounds();
            var sw = b.getSouthWest();
            var ne = b.getNorthEast();
            swlat = sw.lat();
            swlng = sw.lng();
            nelat = ne.lat();
            nelng = ne.lng();
            zoom = map.getZoom();
        }
        var map = new google.maps.Map(document.getElementById("map"), {
            zoom: zoom, center: new google.maps.LatLng(clat, clng), mapTypeId: google.maps.MapTypeId.ROADMAP
        });
        google.maps.event.addListener(map, 'bounds_changed', bchanged);
    </script>
    <script type="text/javascript">
        var infowindow;
        function attachMessage(marker, msg) {
            google.maps.event.addListener(marker, 'click', function (event) {
                if (infowindow) infowindow.close(); // 開いているウィンドウを閉じる 
                infowindow = new google.maps.InfoWindow({
                    content: msg,
                    pixelOffset: new google.maps.Size(-28, 12)
                });
                infowindow.open(map, marker);
                marker.get('map').setZoom(10);
                marker.get('map').setCenter(marker.getPosition());
            });
        }

        function display() {
            var selectTime = document.querySelector('select[name="time"]').value;
            var year = parseInt(selectTime.substring(0, 4));
            var selectAlt = document.querySelector('select[name="alt"]').value;
            if (selectTime == "時間検索" && selectAlt == "高度検索") change = 0;
            var ii;
            for (ii = 0; ii < marker.length; ii++) if (marker[ii]) marker[ii].setMap(null);
            marker = [];
            for (i = ii = 0; i < db.length; i += 7, ii++) {
                var alt = ["0m未満", "0~149m", "150~299m", "300~449m", "450~599m", "600~749m", "750~899m", "900m以上"];
                if (selectTime == "時間検索") {
                    if (selectAlt == alt[0] && db[i + 5] < 0) makePin(i, ii);
                    if (selectAlt == alt[1] && db[i + 5] >= 0 && db[i + 5] < 150) makePin(i, ii);
                    if (selectAlt == alt[2] && db[i + 5] >= 150 && db[i + 5] < 300) makePin(i, ii);
                    if (selectAlt == alt[3] && db[i + 5] >= 300 && db[i + 5] < 450) makePin(i, ii);
                    if (selectAlt == alt[4] && db[i + 5] >= 450 && db[i + 5] < 600) makePin(i, ii);
                    if (selectAlt == alt[5] && db[i + 5] >= 600 && db[i + 5] < 750) makePin(i, ii);
                    if (selectAlt == alt[6] && db[i + 5] >= 750 && db[i + 5] < 900) makePin(i, ii);
                    if (selectAlt == alt[7] && db[i + 5] >= 900) makePin(i, ii);
                    else if (selectAlt == "高度検索") makePin(i, ii);
                } else if (year == 2024 && db[i + 2].substring(0, 4) == 2024) {
                    if (selectAlt == alt[0] && db[i + 5] < 0) makePin(i, ii);
                    if (selectAlt == alt[1] && db[i + 5] >= 0 && db[i + 5] < 150) makePin(i, ii);
                    if (selectAlt == alt[2] && db[i + 5] >= 150 && db[i + 5] < 300) makePin(i, ii);
                    if (selectAlt == alt[3] && db[i + 5] >= 300 && db[i + 5] < 450) makePin(i, ii);
                    if (selectAlt == alt[4] && db[i + 5] >= 450 && db[i + 5] < 600) makePin(i, ii);
                    if (selectAlt == alt[5] && db[i + 5] >= 600 && db[i + 5] < 750) makePin(i, ii);
                    if (selectAlt == alt[6] && db[i + 5] >= 750 && db[i + 5] < 900) makePin(i, ii);
                    if (selectAlt == alt[7] && db[i + 5] >= 900) makePin(i, ii);
                    else if (selectAlt == "高度検索") makePin(i, ii);
                } else if (year == 2023 && db[i + 2].substring(0, 4) == 2023) {
                    if (selectAlt == alt[0] && db[i + 5] < 0) makePin(i, ii);
                    if (selectAlt == alt[1] && db[i + 5] >= 0 && db[i + 5] < 150) makePin(i, ii);
                    if (selectAlt == alt[2] && db[i + 5] >= 150 && db[i + 5] < 300) makePin(i, ii);
                    if (selectAlt == alt[3] && db[i + 5] >= 300 && db[i + 5] < 450) makePin(i, ii);
                    if (selectAlt == alt[4] && db[i + 5] >= 450 && db[i + 5] < 600) makePin(i, ii);
                    if (selectAlt == alt[5] && db[i + 5] >= 600 && db[i + 5] < 750) makePin(i, ii);
                    if (selectAlt == alt[6] && db[i + 5] >= 750 && db[i + 5] < 900) makePin(i, ii);
                    if (selectAlt == alt[7] && db[i + 5] >= 900) makePin(i, ii);
                    else if (selectAlt == "高度検索") makePin(i, ii);
                } else if (year == 2022 && db[i + 2].substring(0, 4) == 2022) {
                    if (selectAlt == alt[0] && db[i + 5] < 0) makePin(i, ii);
                    if (selectAlt == alt[1] && db[i + 5] >= 0 && db[i + 5] < 150) makePin(i, ii);
                    if (selectAlt == alt[2] && db[i + 5] >= 150 && db[i + 5] < 300) makePin(i, ii);
                    if (selectAlt == alt[3] && db[i + 5] >= 300 && db[i + 5] < 450) makePin(i, ii);
                    if (selectAlt == alt[4] && db[i + 5] >= 450 && db[i + 5] < 600) makePin(i, ii);
                    if (selectAlt == alt[5] && db[i + 5] >= 600 && db[i + 5] < 750) makePin(i, ii);
                    if (selectAlt == alt[6] && db[i + 5] >= 750 && db[i + 5] < 900) makePin(i, ii);
                    if (selectAlt == alt[7] && db[i + 5] >= 900) makePin(i, ii);
                    else if (selectAlt == "高度検索") makePin(i, ii);
                } else if (year == 2021 && db[i + 2].substring(0, 4) == 2021) {
                    if (selectAlt == alt[0] && db[i + 5] < 0) makePin(i, ii);
                    if (selectAlt == alt[1] && db[i + 5] >= 0 && db[i + 5] < 150) makePin(i, ii);
                    if (selectAlt == alt[2] && db[i + 5] >= 150 && db[i + 5] < 300) makePin(i, ii);
                    if (selectAlt == alt[3] && db[i + 5] >= 300 && db[i + 5] < 450) makePin(i, ii);
                    if (selectAlt == alt[4] && db[i + 5] >= 450 && db[i + 5] < 600) makePin(i, ii);
                    if (selectAlt == alt[5] && db[i + 5] >= 600 && db[i + 5] < 750) makePin(i, ii);
                    if (selectAlt == alt[6] && db[i + 5] >= 750 && db[i + 5] < 900) makePin(i, ii);
                    if (selectAlt == alt[7] && db[i + 5] >= 900) makePin(i, ii);
                    else if (selectAlt == "高度検索") makePin(i, ii);
                } else if (year == 2020 && db[i + 2].substring(0, 4) == 2020) {
                    if (selectAlt == alt[0] && db[i + 5] < 0) makePin(i, ii);
                    if (selectAlt == alt[1] && db[i + 5] >= 0 && db[i + 5] < 150) makePin(i, ii);
                    if (selectAlt == alt[2] && db[i + 5] >= 150 && db[i + 5] < 300) makePin(i, ii);
                    if (selectAlt == alt[3] && db[i + 5] >= 300 && db[i + 5] < 450) makePin(i, ii);
                    if (selectAlt == alt[4] && db[i + 5] >= 450 && db[i + 5] < 600) makePin(i, ii);
                    if (selectAlt == alt[5] && db[i + 5] >= 600 && db[i + 5] < 750) makePin(i, ii);
                    if (selectAlt == alt[6] && db[i + 5] >= 750 && db[i + 5] < 900) makePin(i, ii);
                    if (selectAlt == alt[7] && db[i + 5] >= 900) makePin(i, ii);
                    else if (selectAlt == "高度検索") makePin(i, ii);
                } else if (year == 2019 && db[i + 2].substring(0, 4) <= 2019) {
                    if (selectAlt == alt[0] && db[i + 5] < 0) makePin(i, ii);
                    if (selectAlt == alt[1] && db[i + 5] >= 0 && db[i + 5] < 150) makePin(i, ii);
                    if (selectAlt == alt[2] && db[i + 5] >= 150 && db[i + 5] < 300) makePin(i, ii);
                    if (selectAlt == alt[3] && db[i + 5] >= 300 && db[i + 5] < 450) makePin(i, ii);
                    if (selectAlt == alt[4] && db[i + 5] >= 450 && db[i + 5] < 600) makePin(i, ii);
                    if (selectAlt == alt[5] && db[i + 5] >= 600 && db[i + 5] < 750) makePin(i, ii);
                    if (selectAlt == alt[6] && db[i + 5] >= 750 && db[i + 5] < 900) makePin(i, ii);
                    if (selectAlt == alt[7] && db[i + 5] >= 900) makePin(i, ii);
                    else if (selectAlt == "高度検索") makePin(i, ii);
                }
            }
        }

        function makePin(i, ii) {
            marker[ii] = new google.maps.Marker({
                position: new google.maps.LatLng(db[i + 3], db[i + 4]),
                map: map,
                title: db[i] + ' ' + db[i + 6],
                icon: {
                    url: 'effect/map-pin.png',
                    size: new google.maps.Size(80, 50),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(12, 40)
                },
                animation: google.maps.Animation.DROP
            });
            var msg = '<a target="photo" href="/~' + db[i] + '/photo/' + db[i + 1] + '">'
                + '<img width="480" src="/~' + db[i] + '/photo/' + db[i + 1] + '"></a><br>'
                + '場所:(' + db[i + 3] + ', ' + db[i + 4] + ')' + ' 時間:' + db[i + 2].substring(0, 4) + '年' + ' 高度:' + db[i + 5];
            attachMessage(marker[ii], msg);
            console.log("a");
        }

        update_all(); 
    </script>
</body>

</html>