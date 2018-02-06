package com.jiuyan.banyandb;

import java.util.List;
import java.util.ArrayList;
import java.io.InputStream;
import java.io.OutputStream;
import java.io.IOException;
import java.net.Socket;
import java.net.InetSocketAddress;
//import org.apache.commons.codec.binary.Hex;

class Link {
    private Socket sock = null;
    private String input = "";
    private String output = "";
    public LinkPool pool = null;

    public Link(String ip, int port, int timeout_ms) throws Exception {
        sock = new Socket();
        sock.connect(new InetSocketAddress(ip, port), timeout_ms);
        if (timeout_ms > 0) {
            sock.setSoTimeout(timeout_ms);
        }
        sock.setTcpNoDelay(true);
    }

    public void close() {
        try {
            sock.close();
        } catch (Exception e) {
            //
        }
    }

    public void printRequestResponse(long latency) {
        System.out.printf("lantency:%d request:[%s] response:[%s]\n", latency, output, input);
    }

    public Response request(String cmd, String s, List<String> params) throws Exception {
        output = s;
        byte[] buf = output.getBytes();
        send(buf, output.length());
        List<String> list = this.recv();
        return new Response(cmd, params, list);
    }

    private void send(byte[] buf, int len) throws IOException {
        OutputStream os = sock.getOutputStream();
        os.write(buf, 0, len);
        os.flush();
    }

    private List<String> recv() throws IOException {
        input = "";
        InputStream is = sock.getInputStream();
        while (true) {
            List<String> ret = parse();
            if (ret != null) {
                return ret;
            }
            byte[] bs = new byte[8192];
            int len = is.read(bs);
            //System.out.println("read byte: " + Hex.encodeHexString(bs));
            //System.out.printf("read byte: %d\n", len);
            if (len == -1) {
                throw new IOException("byte size = -1");
            }
            String s = new String(bs);
            input += s;
            //System.out.println(input);
        }
    }

    private List<String> parse() {
        ArrayList<String> list = new ArrayList<String>(8);
        int idx = 0, pos = 0, len = 0;
        while (true) {
            pos = input.indexOf('\n', idx);
            if (pos == -1) {
                break;
            }
            //System.out.printf("idx:%d pos:%d\n", idx, pos);
            if (pos == idx || (pos == idx + 1 && input.charAt(idx) == '\r')) {
                    if (list.isEmpty()) {
                        idx += 1;
                        continue;
                    } else {
                        // input.decr(idex + 1);
                        return list;
                    }
            }
            String str = input.substring(idx, pos);
            len = Integer.parseInt(str);
            idx = pos + 1;
            if (idx + len >= input.length()) {
                break;
            }
            String data = input.substring(idx, idx + len);
            idx += len + 1;
            //System.out.printf("str:%s data:%s\n", str, data);
            //System.out.printf("idx:%d pos:%d\n", idx, pos);
            list.add(data);
        }
        return null;
    }
}
