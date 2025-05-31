import java.math.BigInteger;
import java.util.Random;

public class IsPrime {

    public static boolean isPrime(long n, int k) {
        if (n <= 1) {
            return false;
        }
        if (n == 2 || n == 3) {
            return true;
        }
        if (n % 2 == 0) {
            return false;
        }

        long d = n - 1;
        int s = 0;
        while (d % 2 == 0) {
            d /= 2;
            s++;
        }

        BigInteger bigN = BigInteger.valueOf(n);

        Random rand = new Random();
        for (int i = 0; i < k; i++) {
            long a = 2 + (Math.abs(rand.nextLong()) % (n - 4));
            BigInteger bigA = BigInteger.valueOf(a);
            BigInteger x = bigA.modPow(BigInteger.valueOf(d), bigN);

            if (x.equals(BigInteger.ONE) || x.equals(bigN.subtract(BigInteger.ONE))) {
                continue;
            }
            boolean composite = true;
            for (int r = 0; r < s - 1; r++) {
                x = x.modPow(BigInteger.valueOf(2), bigN);
                if (x.equals(bigN.subtract(BigInteger.ONE))) {
                    composite = false;
                    break;
                }
            }
            if (composite) {
                return false;
            }
        }
        return true;
    }

    public static void findPrime(long n) {
        for (long i = n; i >= 2; i--) {
            if (isPrime(i, 1)) {
                System.out.println(i + " = Prime");
                break;
            }
        }
    }

    public static void main(String[] args) {
        long ns, st, en;
        ns = Long.parseLong(args[0]);
        st = System.currentTimeMillis();
        findPrime(ns);
        en = System.currentTimeMillis();
        System.out.println("   [" + (en - st) + "ミリ秒]");
    }
}
