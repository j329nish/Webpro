
public class Prime {

    public static void factorize(long n) {
        long nn = n;
        long i = 3;
        while (nn % 2 == 0) {
            nn = nn / 2;
            System.out.print(2 + "*");
        }
        while (i * i <= nn) {
            if (nn % i == 0) {
                System.out.print(i + "*");
                nn = nn / i;
            } else {
                i += 2;
            }
        }
        if (nn == n) {
            System.out.print("素数");
        } else {
            System.out.print(nn);
        }
    }

    public static void main(String[] args) {
        long n, st, en;
        n = Long.parseLong(args[0]);
        System.out.print(n + " = ");
        st = System.currentTimeMillis();
        factorize(n);
        en = System.currentTimeMillis();
        System.out.println("   [" + (en - st) + "ﾐﾘ秒]");
    }
}
