
public class N {

    public static void main(String[] args) {
        long sum = Long.parseLong(args[0]);
        long n = 0;
        while (sum > 0) {
            n++;
            sum -= n;
        }
        if (sum != 0) {
            n--;
        }
        System.out.println("n = " + n);
    }
}
