<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <title>レースゲーム</title>
    <style>
        body {
            color: #ffffff;
            background-color: #202020;
        }

        input[value="スタート"],
        input[value="リセット"],
        input[value="車変更"],
        input[value="再生する"] {
            border: 2px solid #ffffff;
            height: 40px;
            width: 160px;
            font-size: 20px;
            border-radius: 10px;
            color: #ffffff;
            background-color: #202020;
        }

        input[type="text"] {
            border: 2px solid #202020;
            width: 10;
            color: white;
            font-size: 25px;
            background-color: #202020;
        }
    </style>
    <script type="text/javascript" src="zepto.min.js"></script>
    <script type="text/javascript">
        var xs = { x: 123456789, y: 362436069, z: 521288629, w: 88675123 };
        function myrand_init(seed) {
            xs.x = 123456789; // xs['x'] = 123456789; のように書くこともできる 
            xs.y = 362436069;
            xs.z = 521288629;
            xs.w = (seed == undefined) ? 88675123 : seed;
        }
        function myrand() {
            var t;
            t = (xs.x ^ (xs.x << 11));
            xs.x = xs.y;
            xs.y = xs.z;
            xs.z = xs.w;
            xs.w = (xs.w ^ (xs.w >>> 19)) ^ (t ^ (t >>> 8));
            return (xs.w >>> 0) / 4294967296; // 「>>> 0」を付けると符号なし整数に変換できる 
        } 
    </script>
</head>

<body onload="play(0)">
    <h4 align="center">
        <font size=5>レースゲーム</font>
    </h4>
    <table align="center" cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
        <tr>
            <td colspan=2><input type="button" onClick="play(1)" value="スタート">　<input type="button" onClick="play(0)"
                    value="リセット">　<input type="button" onClick="change()" value="車変更"></td>
        </tr>
        <tr>
            <td><span id="restart"></span></td>
            <td>　</td>
        </tr>
    </table>
    <table align="center" cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
        <tr>
            <td>
                <font size="+2">
                    名前：<input type="text" id="name"
                        onChange='document.cookie = "name=" + this.value + "; max-age=86400"'><br>
                    <script type="text/javascript">
                        var name = document.cookie.replace(/(?:(?:^|.*;\s*)name\s*\=\s*([^;]*).*$)|^.*$/, "$1");
                        if (name) { //クッキーに名前が保存されているなら名前を入れる
                            document.getElementById("name").value = name;
                        }
                    </script>
                    タイム：<span id="time" style="display:inline-block;width:68px;color:green"></span>
                    スピード：<span id="speed" style="display:inline-block;width:44px;color:red;"></span>km/h&ensp;
                    残り：<span id="rest" style="display:inline-block;width:64px;color:blue;"></span>km
                </font>
            </td>
        </tr>
    </table>
    <table align="center" border=0 cellspacing=0>
        <tr>
            <td>
                <canvas id="c1" width="500px" height="500px" style="display: block;"></canvas>
            </td>
        </tr>
        <tr>
            <td>
                <input type="button" value="再生する" onclick="play(2)">
            </td>
        </tr>
    </table>
    <script type="text/javascript">
        var canvas = document.getElementById('c1');
        var ctx = canvas.getContext("2d");
        var i;
        var mycar = new Image(); mycar.src = "effect/mycar.png"; // 自機の画像 
        var car0 = new Image(); car0.src = "effect/car0.png"; // 敵機の画像
        var car1 = new Image(); car1.src = "effect/car1.png"; // 敵機の画像
        var jx, jy, jspeed; // 自機の情報 
        var enemy; // 敵機の配列 
        var rest; // 残りの距離 
        var key; // キー入力配列 
        var flag; // 衝突判定フラグ 
        var elapsed; // 経過時間 
        var start; // ゲーム開始時刻 
        var tick; // ゲーム開始後の刻み数 update() が実行される毎に +1 される 
        var rec = {
            <?php
            $r = $_GET['r'];
            $file = file("9.txt");
            foreach ($file as $f) {
                list($date, $name, $score, $rec_key, $rec) = explode('|', rtrim($f));
                if ($score == $r)
                    break;
            }
            $rec_key = explode("/", $rec_key);
            $rec = explode("/", $rec);
            for ($i = 0; $i < count($rec_key); $i++) {
                echo "$rec_key[$i]:$rec[$i]";
                if ($i < count($rec_key) - 1)
                    echo "|";
            }
            ?>
};
        var playback; // 1 だと再生モード 
        var draw_start_screen; // 1 だとスタート画面の描画のみ 
        var a1 = new Audio("effect/brake.mp3");
        var a2 = new Audio("effect/siren.mp3");
        var a3 = new Audio("effect/crash.mp3");
        var a4 = new Audio("effect/kanariaskip.mp3");
        var a5 = new Audio("effect/keikoku.mp3");
        a4.volume = 0.3;
        var kindpng = 0;
        var probability;
        var init = 0;
        var mycars = 0;
        var flash = 0;
        const whichranking = ["_d", "m", "d"];
        function play(mode) {
            if (init != 0) {
                if (mode == 0 || init == 2) {
                    document.getElementById("restart").innerHTML = "Initializing...";
                } else if (init == 1) {
                    document.getElementById("restart").innerHTML = "Getting ready for a restart...";
                }
                a4.currentTime = 0;
                a4.pause();
                init = 0;
                setTimeout(function () { play(mode); }, 1000);
            } else {
                document.getElementById("restart").innerHTML = "";
                jx = 234;
                jy = 360;
                jspeed = 0; // 自機の情報 
                enemy = []; // 敵機の配列(最初は空) 
                rest = 100000; // 残りの距離 
                key = []; // キー入力状態の初期化 
                flag = 0; // 衝突判定フラグ 
                start = new Date().getTime();
                tick = 0;
                elapsed = 0;
                draw_start_screen = playback = 0;
                probability = 0.003;
                flash = 0;
                if (mode == 0) {
                    draw_start_screen = 1;
                } else if (mode == 1) {
                    rec = {}; //キー操作記録用の連想配列を初期化
                    init++;
                } else if (mode == 2) {
                    playback = 1;
                    init++;
                }
                a4.currentTime = 0;
                a4.pause();
                myrand_init();
                update();
            }
        }

        function change() {
            mycars = (mycars + 1) % 3;
            if (mycars == 0) mycar.src = "effect/mycar.png";
            else if (mycars == 1) mycar.src = "effect/mycar2.png";
            else mycar.src = "effect/mycar3.png";
            play(0);
        }

        function update() {
            if (init == 1 || draw_start_screen) {
                if (elapsed != 0) a4.play();
                if (flag == 1 || flag == 2) {
                    a4.currentTime = 0;
                    a4.pause();
                    // 衝突時のメッセージを表示する 
                    ctx.fillStyle = '#000000'; // 文字の色 
                    ctx.font = "60px 'ＭＳ ゴシック'";
                    ctx.textAlign = "center";
                    ctx.textBaseline = "bottom";
                    ctx.fillText('衝突しました', 250, 250);
                    if (flag == 1) {
                        a2.play();
                    } else {
                        a3.play();
                    }
                    init = 2;
                    setTimeout("a2.pause()", 1500);
                    setTimeout(function () { a2.currentTime = 0; }, 1500);
                    setTimeout("play(0)", 2000);
                    return;
                }
                var isPush = 0;
                if (playback == 0) {
                    if (key[37] || key[65]) {
                        if (mycars == 0) mycar.src = "effect/Lmycar.png";
                        else if (mycars == 1) mycar.src = "effect/Lmycar2.png";
                        else mycar.src = "effect/Lmycar3.png";
                        jx = Math.max(-2, jx - 4); // ←が押されている
                        rec[tick] |= 1;
                        isPush++;
                    }
                    if (key[38] || key[87]) {
                        jspeed = Math.min(250, jspeed + 1); // ↑が押されている 
                        if (mycars == 0) {
                            mycar.src = "effect/mycar.png";
                            if (jspeed >= 150) jspeed = 150;
                        } else if (mycars == 1) {
                            mycar.src = "effect/mycar2.png";
                        } else {
                            mycar.src = "effect/mycar3.png";
                        }
                        rec[tick] |= 2;
                        isPush++;
                    }
                    if (key[39] || key[68]) {
                        if (mycars == 0) mycar.src = "effect/Rmycar.png";
                        else if (mycars == 1) mycar.src = "effect/Rmycar2.png";
                        else mycar.src = "effect/Rmycar3.png";
                        jx = Math.min(470, jx + 4); // →が押されている 
                        rec[tick] |= 4;
                        isPush++;
                    }
                    if (key[40] || key[83]) {
                        if (mycars == 0) mycar.src = "effect/mycar.png";
                        else if (mycars == 1) mycar.src = "effect/mycar2.png";
                        else mycar.src = "effect/mycar3.png";
                        jspeed = Math.max(-1, jspeed - 2); // ↓が押されている 
                        rec[tick] |= 8;
                        isPush++;
                        a1.play();
                    }
                    if (isPush == 0) {
                        if (mycars == 0) mycar.src = "effect/mycar.png";
                        else if (mycars == 1) mycar.src = "effect/mycar2.png";
                        else mycar.src = "effect/mycar3.png";
                        jspeed = Math.max(0, jspeed - 1);
                    }
                } else { //プレイバックモード
                    if (rec[tick] & 1) {
                        if (mycars == 0) mycar.src = "effect/Lmycar.png";
                        else if (mycars == 1) mycar.src = "effect/Lmycar2.png";
                        else mycar.src = "effect/Lmycar3.png";
                        jx = Math.max(-2, jx - 4); isPush++;
                    }// ←が押されている 
                    if (rec[tick] & 2) {
                        jspeed = Math.min(250, jspeed + 1);
                        if (mycars == 0) {
                            mycar.src = "effect/mycar.png";
                            if (jspeed >= 150) jspeed = 150;
                        } else if (mycars == 1) {
                            mycar.src = "effect/mycar2.png";
                        } else {
                            mycar.src = "effect/mycar3.png";
                        }
                        isPush++;
                    }// ↑が押されている 
                    if (rec[tick] & 4) {
                        if (mycars == 0) mycar.src = "effect/Rmycar.png";
                        else if (mycars == 1) mycar.src = "effect/Rmycar2.png";
                        else mycar.src = "effect/Rmycar3.png";
                        jx = Math.min(470, jx + 4);
                        isPush++;
                    }// →が押されている 
                    if (rec[tick] & 8) {
                        if (mycars == 0) mycar.src = "effect/mycar.png";
                        else if (mycars == 1) mycar.src = "effect/mycar2.png";
                        else mycar.src = "effect/mycar3.png";
                        jspeed = Math.max(-1, jspeed - 2);
                        isPush++;
                    } // ↓が押されている 
                    if (isPush == 0) {
                        if (mycars == 0) mycar.src = "effect/mycar.png";
                        else if (mycars == 1) mycar.src = "effect/mycar2.png";
                        else mycar.src = "effect/mycar3.png";
                        jspeed = Math.max(0, jspeed - 1);
                    }
                }
                isPush = 0;
                tick++;
                elapsed = parseInt((new Date().getTime() - start) / 10) / 100;
                document.getElementById("speed").innerHTML = jspeed;
                document.getElementById("time").innerHTML = elapsed;
                document.getElementById("rest").innerHTML = parseInt(rest / 100);

                rest -= jspeed; //残りの距離を更新 
                if (rest <= 0) {
                    if (!document.getElementById("name").value) { //名前がまだ登録されていないときは名前を尋ねる 
                        name = prompt("無事にゴールしました。\n" + "あなたのタイムは" + elapsed +
                            "秒でした\n あなたのお名前を入れてください", "");
                        document.cookie = "name=" + name + "; max-age=86400";
                        document.getElementById("name").value = name;
                    } else {
                        alert("無事にゴールしました。\n" + document.getElementById("name").value + "さんのタイムは" +
                            elapsed + "秒でした。");
                    }
                    var rec_key = Object.keys(rec); //連想配列 rec のキーの部分を配列として取り出す 
                    $.post("9.php", {
                        name: document.getElementById("name").value, score: elapsed,
                        rec_key: rec_key, rec: rec
                    },
                        function () {
                            ranking();
                        })
                    setTimeout("play(0)", 1000);
                    return;
                }

                ctx.fillStyle = '#a0a0a0'; // 背景(道路の色)を指定 
                ctx.fillRect(0, 0, 500, 500); // 背景色で塗る
                if (rest > 99850) {
                    ctx.fillStyle = '#ffffff'; // 文字の色 
                    ctx.font = "40px 'ＭＳ ゴシック'";
                    ctx.textAlign = "left";
                    ctx.textBaseline = "bottom";
                    ctx.fillText('START', 0, 100350 - rest);
                    ctx.fillRect(0, 100350 - rest, 500, 10);
                }
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(245, (100000 - rest) % 750 - 250, 10, 250);
                ctx.drawImage(mycar, jx, jy); // 自機を描画 
                for (i = 0; i < enemy.length; i++) { // 敵機を動かす 
                    if (jx > enemy[i].x) enemy[i].x += (myrand() - 0.3) * 3;
                    else enemy[i].x -= (myrand() - 0.3) * 3;
                    enemy[i].y += (jspeed - enemy[i].speed) / 10;
                    if (enemy[i].y <= -100 || enemy[i].y >= 2000) { // 敵機が遠くに離れた 
                        enemy.splice(i, 1); // 敵機を配列から削除する 
                        i--; // ループ変数を一つ減らす 
                    }
                }
                kindpng = (kindpng + 1) % 2; // 敵機の種類
                if (kindpng == 0 && jspeed < 200) var kind = car0;
                else var kind = car1;
                if (jspeed >= 200) probability = 0.3;
                else probability = 0.003;
                if (myrand() < 0.0003 * jspeed + probability) { // 敵機をランダムに出現させる 
                    enemy.push({ kind: kind, x: myrand() * 470, y: -32, size: 16, speed: myrand() * 50 }); // 配列に要素を追加 
                    //kind:敵機の種類 x:X 座標 y:Y 座標 size:半分のサイズ speed:スピード 
                }
                if (myrand() < probability) { // 後方からも敵機をランダムに出現させる 
                    enemy.push({ kind: kind, x: myrand() * 470, y: 500, size: 16, speed: myrand() * 50 });
                }
                for (i = 0; i < enemy.length; i++) { // 敵機を描画 
                    ctx.drawImage(enemy[i].kind, enemy[i].x, enemy[i].y);
                    if ((jx - enemy[i].x) * (jx - enemy[i].x) + (jy - enemy[i].y) * (jy - enemy[i].y)
                        < (14 + enemy[i].size) * (14 + enemy[i].size)) flag = 1; // 衝突判定
                    if (flag == 1 && enemy[i].kind == car0) flag = 2;
                }

                if (jspeed >= 200) {
                    flash = (flash + 1) % 25;
                } else {
                    flash = 0;
                }
                if (flash >= 1 && flash <= 10) {
                    a5.play();
                    ctx.fillStyle = 'rgba(255, 0, 0, 0.5)';
                    ctx.fillRect(0, 0, 500, 500); // 背景色で塗る
                }

                if (draw_start_screen) return; //スタート画面を描画して終了 
                setTimeout("update()", 20); // 20 ミリ秒経過後に update() を実行する 
            }
        }

        function ranking() {
            _d = new Date().getTime(); //キャッシュ回避のため日時を利用する
            for (let i = 0; i < 3; i++) {
                $.get("9ranking.php?" + whichranking[i] + "=" + _d, function (data) {
                    var a = data.split("\n"); //改行で区切る
                    var table = "<table border=1 cellspacing=0 cellpadding=2>";
                    table += "<tr><td>Rank</td><td>Time</td><td>Name</td><td>Session Date</td></tr>";
                    var rankmax = 10;
                    if (a.length <= 10) {
                        rankmax = a.length - 1;
                    }
                    for (j = 0; j < rankmax; j++) {
                        var b = a[i].split("|");
                        table += '<tr><td><a href="9play.php?r=' + b[2] + '">' + (i + 1) + "</a></td><td>" + b[2] +
                            "</td><td>"
                            + b[1] + "</td><td>" + b[0] + "</td></tr>";
                    }
                    for (j = rankmax; j < 10; j++) {
                        table += "<tr><td>#" + (j + 1) + "</td><td></td><td></td><td>!??/??/?? !??:??:??</td></tr>";
                    }
                    table += "</table>";
                    document.getElementById("ranking" + whichranking[i]).innerHTML = table;
                });
            }
        }
        function ranking() {
            _d = new Date().getTime(); //キャッシュ回避のため日時を利用する 
            $.get("9ranking.php?_d=" + _d, function (data) {
                var a = data.split("\n"); //改行で区切る 
                var table = "<table border=1 cellspacing=0 cellpadding=2>";
                table += "<tr><td>順位</td><td>時間</td><td>名前</td><td>日時</td></tr>";
                for (i = 0; i < a.length - 1; i++) {

                }
                table += "</table>";
                document.getElementById("ranking").innerHTML = table;
            });
        }

        window.addEventListener('keydown', function (e) { key[e.keyCode] = true; }); //キーが押された 
        window.addEventListener('keyup', function (e) { key[e.keyCode] = false; }); //キーが離された 
    </script>
    <table align="center">
        <tr>
            <td><b>ランキング</b></td>
            <td><b>月間ランキング</b></td>
            <td><b>当日ランキング</b></td>
        </tr>
        <tr>
            <td>
                <div id="ranking_d"></div>
            </td>
            <td>
                <div id="rankingm"></div>
            </td>
            <td>
                <div id="rankingd"></div>
            </td>
        </tr>
        <script type="text/javascript">
            ranking();
        </script>
    </table><br>
    <table align="center" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <b>説明</b>
            </td>
        </tr>
        <tr>
            <td colspan=3>1. これは1kmを走った時の時間を競うゲームです。<br>
                2. 「スタート」を押すとレースゲームが始まります。<br>
                3. 「リセット」を押すとレースが止まり、全て初期化されます。<br>
                4. 「車変更」を押すと自分の車が変更され、以下の様々な車に乗ることができます。<br>
                5. 速度が200km/hを超えるとたくさんのパトカーが追いかけてきます。<br>
                6. ↑←↓→ボタンで、それぞれの方向に動きますが、wasdボタンでも同じ動作が行えます。<br>
                7. 兄弟、さあ早く私の車に乗りましょう!<br><br></td>
        </tr>
        <tr>
            <td><img src="effect/mycar.png" height="100" draggable="false"></td>
            <td><img src="effect/mycar2.png" height="100" draggable="false"></td>
            <td><img src="effect/mycar3.png" height="100" draggable="false"></td>
        </tr>
        <tr>
            <td>最高時速150km/hの車</td>
            <td>最高時速250km/hの車</td>
            <td>最高時速250km/hのUFO</td>
        </tr>
    </table>
    <br><br>
</body>

</html>