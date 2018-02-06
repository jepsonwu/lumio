import com.jiuyan.banyandb.ClusterLink;
import com.jiuyan.banyandb.BanyanDBClient;

public class Demo {
    public static void main(String[] args) throws Exception {
        ClusterLink clusterlink = new ClusterLink("10.10.105.5:10025,10.10.105.5:10024", 1000, true);
        BanyanDBClient cli = new BanyanDBClient(clusterlink, "test", "api_test");
        //cli.setDebug(true);
        boolean resbool = false;
        String  resStr = "";

        for (int i = 0; i < 20; i++) {
            String key = String.format("k%d", i);
            String val = String.format("v%d", i);
            resbool = cli.set(key, val);
            System.out.println(resbool);
            resStr = cli.get(key);
            System.out.println(resStr);
            Thread.sleep(500);
        }

        clusterlink.close();
    }
}
