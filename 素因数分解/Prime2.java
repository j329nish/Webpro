
public class Prime2 {

    public static int primes(int n) {
        if (n < 2) {
            return 0;
        }
        if (n == 2 || n == 3) {
            return n - 1;
        }

        boolean[] a = new boolean[n + 1];
        int i, j, count = 2;
        for (i = 5; i <= n; i += 6) {
            a[i] = true;
            if (i + 2 <= n) {
                a[i + 2] = true;
            }
        }

        for (i = 5; (j = i * i) <= n; i++) {
            if (a[i]) {
                for (j = j; j <= n; j += i) {
                    a[j] = false;
                }
            }
        }

        for (i = 5; i <= n; i += 6) {
            if (a[i]) {
                count++;
            }
            if (i + 2 <= n && a[i + 2]) {
                count++;
            }
        }

        return count;
    }

    public static void main(String[] args) {
        long ns, st, en;
        ns = Long.parseLong(args[0]);
        st = System.currentTimeMillis();
        int n;
        n = Integer.parseInt(args[0]);
        System.out.println(primes(n));
        en = System.currentTimeMillis();
        System.out.println("   [" + (en - st) + "ミリ秒]");
    }
}
