
import java.awt.*;
import java.awt.event.*;
import java.awt.image.BufferedImage;
import javax.swing.*;

public class MD extends JFrame implements ActionListener, ItemListener, KeyListener, MouseListener, Runnable {

    BufferedImage image;
    JButton leftButton, rightButton, upButton, downButton, zoominButton, zoomoutButton, drawButton, changeColorButton;
    Choice limitChoice, spotChoice;
    String arySpot[] = {"全体図", "領域 1", "領域 2", "領域 3", "領域 4", "領域 5", "領域 6", "領域 7"};
    double spot[][] = {{-2.0, -2.0, 4.0}, {-2.0, -1.25, 2.5}, {-0.7465, 0.1045, 0.0015},
    {-0.7717, 0.11653, 0.00002}, {-0.77175, 0.11653, 0.0001}, {-1.255, 0.0237, 0.002}, {0.2, -0.1, 0.2}, {0.286706, 0.014961, 0.000061}};
    double rmin = -2.0, imin = -2.0, range = 4.0;
    int aryLimit[] = {500, 1000, 2000, 5000, 10000};
    int limit = 500;
    Graphics image_g;
    Thread thread;
    int size, shift = 90;
    static volatile int reentrant = 0; // volatile を指定します（重要）
    int colorFlag = 0;

    public MD() {
        int i;
        setTitle("不思議図形");
        setSize(600, 690);
        leftButton = new JButton("←");
        rightButton = new JButton("→");
        upButton = new JButton("↑");
        downButton = new JButton("↓");
        zoominButton = new JButton("拡大");
        zoomoutButton = new JButton("縮小");
        drawButton = new JButton("再描画");
        changeColorButton = new JButton("色変更");
        limitChoice = new Choice();
        for (i = 0; i < aryLimit.length; i++) {
            limitChoice.add("" + aryLimit[i]);
        }
        spotChoice = new Choice();
        for (i = 0; i < arySpot.length; i++) {
            spotChoice.add(arySpot[i]);
        }
        addKeyListener(this);
        addMouseListener(this);
        JPanel panel1 = new JPanel(new GridLayout(2, 7, 4, 4));
        panel1.add(leftButton);
        panel1.add(rightButton);
        panel1.add(upButton);
        panel1.add(downButton);
        panel1.add(zoominButton);
        panel1.add(zoomoutButton);
        panel1.add(drawButton);
        panel1.add(changeColorButton);
        panel1.add(new Label("反復回数"));
        panel1.add(limitChoice);
        panel1.add(new Label("お気に入り"));
        panel1.add(spotChoice);
        leftButton.addActionListener(this);
        rightButton.addActionListener(this);
        upButton.addActionListener(this);
        downButton.addActionListener(this);
        zoominButton.addActionListener(this);
        zoomoutButton.addActionListener(this);
        drawButton.addActionListener(this);
        changeColorButton.addActionListener(this);
        limitChoice.addItemListener(this);
        limitChoice.select(0);
        spotChoice.addItemListener(this);
        spotChoice.select(0);
        add(panel1, BorderLayout.NORTH);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE); // ×ボタンで終了する 
        setVisible(true);
        setFocusable(true);
        int width = getWidth();
        int height = getHeight();
        size = Math.min(width, height - shift);
        image = new BufferedImage(size, size, BufferedImage.TYPE_INT_RGB);
        image_g = image.getGraphics();
        thread = new Thread(this); //スレッドを作成して実行する 
        thread.start();
    }

    @Override
    public void actionPerformed(ActionEvent e) { // ボタンが押された
        Object source = e.getSource();
        if (source == drawButton) {
            // do nothing
        } else if (source == leftButton) {
            double width = rmin + range;
            rmin -= 0.1 * range;
        } else if (source == rightButton) {
            double width = rmin + range;
            rmin += 0.1 * range;
        } else if (source == upButton) {
            double width = rmin + range;
            imin += 0.1 * range;
        } else if (source == downButton) {
            double width = rmin + range;
            imin -= 0.1 * range;
        } else if (source == zoominButton) {
            double centerX = rmin + range / 2.0;
            double centerY = imin + range / 2.0;
            range /= 2; // 拡大
            rmin = centerX - range / 2.0;
            imin = centerY - range / 2.0;
            BufferedImage tmp = new BufferedImage(size, size, BufferedImage.TYPE_INT_RGB);
            Graphics tmp_g = tmp.getGraphics();
            tmp_g.drawImage(image, 0, 0, null); // tmp に一旦イメージデータをコピーする
            image_g.drawImage(tmp, 0, 0, size, size, size / 4, size / 4, size / 4 + size / 2, size / 4 + size / 2, null); //拡大する
        } else if (source == zoomoutButton) {
            double centerX = rmin + range / 2.0;
            double centerY = imin + range / 2.0;
            range *= 2; // 縮小
            rmin = centerX - range / 2.0;
            imin = centerY - range / 2.0;
            BufferedImage tmp = new BufferedImage(size, size, BufferedImage.TYPE_INT_RGB);
            Graphics tmp_g = tmp.getGraphics();
            tmp_g.drawImage(image, 0, 0, null); // tmp に一旦イメージデータをコピーする
            image_g.clearRect(0, 0, size, size); //縮小するためイメージをクリアする(拡大の場合は不要) 
            image_g.drawImage(tmp, size / 4, size / 4, size / 2, size / 2, null); //縮小する
        } else if (source == changeColorButton) {
            colorFlag = (colorFlag + 1) % 10;
        }
        thread = new Thread(this); //スレッドを作成して実行する 
        thread.start();
    }

    public void itemStateChanged(ItemEvent e) { // チョイスの項目が変更された 
        Object source = e.getSource();
        if (source == limitChoice) {
            limit = aryLimit[limitChoice.getSelectedIndex()];
        } else if (source == spotChoice) {
            int i = spotChoice.getSelectedIndex();
            rmin = spot[i][0];
            imin = spot[i][1];
            range = spot[i][2];
        }
    }

    @Override
    public void paint(Graphics g) {
        if (reentrant == 0) { // 画面のチラツキが気になる人は計算スレッドが実行中は super.paint() を呼ばないようにして！
            super.paint(g);
        }
        g.drawImage(image, 0, shift, this);
        requestFocusInWindow();
    }

    public void run() {
        long st = System.currentTimeMillis();
        int shift = 90, i, j, k;
        double cr, ci, zr, zi, zr2, zi2, diff;
        int width = getWidth();
        int height = getHeight();
        int size = Math.min(width, height - shift);

        reentrant++;
        while (reentrant > 1) {
        }
        int[] pixels = new int[size * size];
        BufferedImage line = new BufferedImage(size, 1, BufferedImage.TYPE_INT_RGB); //1 ライン用のバッファ
        diff = range / size;

        for (i = 0; i < size; i++) {
            ci = imin + range - diff * i; // 複素数 c の虚部
            for (j = 0; j < size; j++) {
                cr = rmin + diff * j; // 複素数 c の実部
                zr = cr; // z の初期値(実部) 
                zi = ci; // z の初期値(虚部) 
                for (k = 0; k < limit; k++) {
                    zr2 = zr * zr;
                    zi2 = zi * zi;
                    if (zr2 + zi2 >= 4.0) {
                        break; // |z|>=2 になったら繰り返しは終了 
                    }
                    zi = 2 * zr * zi + ci;
                    zr = (zr2 - zi2 + cr);
                }
                if (k == limit) {
                    pixels[i * size + j] = 0x000000;
                } else {
                    double ratio = Math.pow((double) k / limit, 0.25); // 0.25 は 4 乗根 
                    if (colorFlag == 0) {
                        pixels[i * size + j] = Color.HSBtoRGB((float) ratio, 1.0f, (k % 2 == 0) ? 1.0f : 0.9f);
                    } else if (colorFlag == 1) {
                        pixels[i * size + j] = Color.HSBtoRGB((float) (1.0 - ratio), 1.0f, 1.0f);
                    } else if (colorFlag == 2) {
                        pixels[i * size + j] = Color.HSBtoRGB((float) ratio, 0.5f, 1.0f);
                    } else if (colorFlag == 3) {
                        pixels[i * size + j] = Color.HSBtoRGB((float) ratio, 1.0f, 0.5f);
                    } else if (colorFlag == 4) {
                        pixels[i * size + j] = Color.HSBtoRGB((float) ratio, 0.5f, (k % 2 == 0) ? 1.0f : 0.5f);
                    } else if (colorFlag == 5) {
                        pixels[i * size + j] = Color.HSBtoRGB((float) (1.0 - ratio), 0.5f, (k % 2 == 0) ? 1.0f : 0.5f);
                    } else if (colorFlag == 6) {
                        pixels[i * size + j] = Color.HSBtoRGB((float) (0.5 + ratio / 2.0), 1.0f, 1.0f);
                    } else if (colorFlag == 7) {
                        pixels[i * size + j] = Color.HSBtoRGB((float) (0.5 - ratio / 2.0), 1.0f, 1.0f);
                    } else if (colorFlag == 8) {
                        pixels[i * size + j] = Color.HSBtoRGB((float) (ratio), 0.75f, 1.0f);
                    } else {
                        pixels[i * size + j] = Color.HSBtoRGB((float) (1.0 - ratio), 0.75f, 1.0f);
                    }
                }
            }
            if (reentrant > 1) {
                reentrant--;
                return; //強制終了 
            }
            line.setRGB(0, 0, size, 1, pixels, i * size, size);
            image_g.drawImage(line, 0, i, null); // 1 ライン毎に描画する
            repaint();
        }
        reentrant--;
        long en = System.currentTimeMillis();

        setTitle("不思議図形  右下の座標: (" + String.format("%.1f", rmin) + ", " + String.format("%.1f", imin)
                + ")  左下の座標: (" + String.format("%.1f", (rmin + range)) + ", " + String.format("%.1f", (imin + range)) + ")  [" + (en - st) + "ミリ秒]");

    }

    @Override
    public void keyPressed(KeyEvent e) {
        double centerX = rmin + range / 2.0;
        double centerY = imin + range / 2.0;
        int key = e.getKeyCode();
        switch (key) {
            case KeyEvent.VK_LEFT:
                rmin -= range * 0.1;
                break;
            case KeyEvent.VK_RIGHT:
                rmin += range * 0.1;
                break;
            case KeyEvent.VK_UP:
                imin += range * 0.1;
                break;
            case KeyEvent.VK_DOWN:
                imin -= range * 0.1;
                break;
            case KeyEvent.VK_PLUS:
            case KeyEvent.VK_SPACE:
                range /= 2; // 拡大
                rmin = centerX - range / 2.0;
                imin = centerY - range / 2.0;
                break;
            case KeyEvent.VK_MINUS:
                range *= 2; // 縮小
                rmin = centerX - range / 2.0;
                imin = centerY - range / 2.0;
                break;
        }
        thread = new Thread(this); //スレッドを作成して実行する 
        thread.start();
    }

    @Override
    public void keyReleased(KeyEvent e) {
    }

    @Override
    public void keyTyped(KeyEvent e) {
    }

    @Override
    public void mouseClicked(MouseEvent e) {
        Point point = e.getPoint();
        int shift = 90;
        int size = Math.min(getWidth(), getHeight() - shift);

        if (point.y < shift) {
            return;
        }

        double currentSize = range / size;
        rmin = rmin + (point.x * currentSize) - (range / 2);
        imin = imin + ((size - (point.y - shift)) * currentSize) - (range / 2);

        thread = new Thread(this); //スレッドを作成して実行する 
        thread.start();
    }

    @Override
    public void mousePressed(MouseEvent e) {
    }

    @Override
    public void mouseReleased(MouseEvent e) {
    }

    @Override
    public void mouseEntered(MouseEvent e) {
    }

    @Override
    public void mouseExited(MouseEvent e) {
    }

    public static void main(String[] args) {
        new MD();
    }
}
