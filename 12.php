<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <title>英単語学習サービス</title>
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
    <h1 align="center" class="glowing-text">
        <font size="7">英単語学習サービス</font>
    </h1>
    <table align="center">
        <tr>
            <td>
                <table align="center">
                    <tr>
                        <td>
                            <form action="12-1.php" method="POST">
                                <button type="submit" name="submit_button" value="eiwa" id="eiwa">英和辞典</button>
                            </form>
                        </td>
                    </tr>
                </table>
            </td>
            <td>
                <table align="center">
                    <tr>
                        <td>
                            <form action="12-3.php" method="POST">
                                <button type="submit" id="waei">和英辞典</button>
                            </form>
                        </td>
                    </tr>
                </table>
            </td>
            <td>
                <table align="center">
                    <tr>
                        <td>
                            <form action="12-2.php" method="POST">
                                <button type="submit" id="EnToJa">語彙力テスト<br>(英単語から日本語)</button>
                            </form>
                        </td>
                    </tr>
                </table>
            </td>
            <td>
                <table align="center">
                    <tr>
                        <td>
                            <form action="12-4.php" method="POST">
                                <button type="submit" id="JaToEn">語彙力テスト<br>(日本語から英単語)</button>
                            </form>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <script>
        const Type = ["eiwa", "waei", "EnToJa", "JaToEn"];
        const Contents = ["英和辞典", "和英辞典", "語彙力テスト<br>(英単語から日本語)", "語彙力テスト<br>(日本語から英単語)"];
        const Instructions = ["英単語を入力すると、(1)英単語、(2)レベル、(3)意味が表示されます。",
            "日本語の入力で、部分一致した英単語の(1)英単語、(2)レベル、(3)意味が表示されます。",
            "英単語の意味を答える5択問題です。", "英単語の意味からその英単語を入力して答える問題です。"];
        const btn = new Array(4);
        var a1 = new Array(12);
        for (var i = 0; i < 12; i++) {
            a1[i] = new Audio("effect/click.mp3");
            a1[i].volume = 0.5;
        }

        let j = 0;
        for (let i = 0; i < 4; i++) {
            btn[i] = document.getElementById(Type[i]);
            btn[i].addEventListener('mouseover', function () {
                this.innerHTML = Contents[i] + "<br><br>" + Instructions[i];
                a1[j = (j + 1) % 12].play();
            });
            btn[i].addEventListener('mouseleave', function () {
                this.innerHTML = Contents[i];
            });
        }
    </script>
</body>

</html>