
public class Sum {
    public static void main(String[] args) {
        long n = Long.parseLong(args[0]);
        long sum = 0;
        for(long i = 1; i <= n; i++){
           sum = sum + i;
        }
        System.out.println("sum = " + sum);
    }
}
