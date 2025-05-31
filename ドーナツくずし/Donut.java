
import java.awt.*;
import java.awt.image.BufferedImage;
import javax.swing.*;

class FoolBall {

    double x, y;
    private int width, height;
    protected double vx, vy; // この 2 つの変数は継承したクラスからは直接アクセス可 

    FoolBall(double x, double y, int w, int h) {
        this.x = x;
        this.y = y;
        width = w; //X 軸方向の移動範囲 
        height = h; //Y 軸方向の移動範囲 
        double rad = Math.random() * 2 * Math.PI;
        vx = Math.cos(rad); //X 軸方向の速度 
        vy = Math.sin(rad); //Y 軸方向の速度 
    }

    void move() { //ボールの移動 
        x += vx;
        y += vy;
        if (x < 0) {
            vx = -vx;
            x = -x;
        }
        if (x >= width) {
            vx = -vx;
            x = width - (x - width);
        }
        if (y < 0) {
            vy = -vy;
            y = -y;
        }
        if (y >= height) {
            vy = -vy;
            y = height - (y - height);
        }
    }

    void hit() { //ボールの方向変換（初心者向けの単純な方法） 
        vx = -vx;
        vy = -vy;
        move();
    }
}

class Ball extends FoolBall { // FoolBall を継承した新しいクラスを作成する

    Color color;

    Ball(double x, double y, int w, int h) {
        super(x, y, w, h); //スーパークラスのコンストラクタを呼ぶ 
        changeColor();
    }

    void hit() { //ボールの方向変換（プロ向け） 
        double in = Math.atan2(vy, vx); //ボールの進行方向のラジアン 
        double nv = Math.atan2(x - 300, 300 - y); //接線方向のラジアン 
        // Math.atan2(300 - x, y - 300); //接線方向の向きが逆でも同じ 
        double re = nv * 2 - in;
        vx = Math.cos(re);
        vy = Math.sin(re);
        move();
        changeColor();
    }

    void changeColor() {
        if (vx > 0 && vy > 0) {
            color = Color.RED;
        } else if (vx < 0 && vy > 0) {
            color = Color.GREEN;
        } else if (vx > 0 && vy < 0) {
            color = Color.YELLOW;
        } else {
            color = Color.PINK;
        }
    }
}

public class Donut extends JFrame implements Runnable {

    Ball[] ball = new Ball[1000];
    int[] pixels = new int[600 * 600];
    int cnt, total = 0; //残りのピクセル数と最初のピクセル数 
    BufferedImage image = new BufferedImage(600, 600, BufferedImage.TYPE_INT_RGB);
    Color[] colors = {Color.BLUE, Color.CYAN, Color.ORANGE, Color.MAGENTA, Color.LIGHT_GRAY};

    Donut() {
        // ドーナツの設置 
        for (int i = 0; i < 600; i++) {
            for (int j = 0; j < 600; j++) {
                double d = (i - 300) * (i - 300) + (j - 300) * (j - 300);
                if (d >= 100 * 100 && d <= 240 * 240) {
                    pixels[600 * i + j] = colorfulColor(d);
                    total++;
                } else {
                    pixels[600 * i + j] = 0x000000;
                }
            }
        }
        cnt = total;

        // 緑ボールの設置 
        for (int i = 0; i < 1000; i++) {
            while (true) {
                double x = Math.random() * 600;
                double y = Math.random() * 600;
                double ballXY = (x - 300) * (x - 300) + (y - 300) * (y - 300);
                if (ballXY < 80 * 80 || ballXY > 260 * 260) {
                    ball[i] = new Ball(x, y, 600, 600);
                    pixels[600 * (int) x + (int) y] = 0x00ff00;
                    break;
                }
            }
        }
        setSize(600 + 8 + 8, 600 + 31 + 8);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setVisible(true);
        Thread thread = new Thread(this);
        thread.start();
    }

    private int colorfulColor(double distance) {
        int index = (int) ((distance - 100 * 100) / (140 * 140) * colors.length) % colors.length;
        return colors[index].getRGB();
    }

    private boolean isDonutColor(int color) {
        for (Color c : colors) {
            if (color == c.getRGB()) {
                return true;
            }
        }
        return false;
    }

    public void paint(Graphics g) {
        for (int i = 0; i < 1000; i++) {
            pixels[600 * (int) ball[i].x + (int) ball[i].y] = 0x000000;
            ball[i].move(); //ボールを動かす
            ball[i].changeColor();
            if (isDonutColor(pixels[600 * (int) ball[i].x + (int) ball[i].y])) {
                pixels[600 * (int) ball[i].x + (int) ball[i].y] = 0x000000; //ドーナツ除去 
                cnt--;
                ball[i].hit(); //ボールの方向変換 
                if (isDonutColor(pixels[600 * (int) ball[i].x + (int) ball[i].y])) {
                    cnt--; //ドーナツ除去 
                }
            }
            pixels[600 * (int) ball[i].x + (int) ball[i].y] = ball[i].color.getRGB(); //ボールを置く 
        }
        image.setRGB(0, 0, 600, 600, pixels, 0, 600);
        g.drawImage(image, 8, 31, null);
        if (cnt <= 1000) {
            setTitle("お腹いっぱい！");
        } else if ((double) cnt / total <= 0.3) {
            setTitle("ドーナツくずし " + cnt + "/" + total + "　あと少し！");
        } else if ((double) cnt / total <= 0.6) {
            setTitle("ドーナツくずし " + cnt + "/" + total + "　その調子！");
        } else {
            setTitle("ドーナツくずし " + cnt + "/" + total + "　頑張ろう！");
        }
    }

    public void run() {
        while (true) {
            repaint();
            try {
                Thread.sleep(10);
            } catch (Exception e) {
            }
        }
    }

    public static void main(String[] args) {
        new Donut();
    }
}
