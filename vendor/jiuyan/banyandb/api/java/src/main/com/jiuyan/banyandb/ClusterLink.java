package com.jiuyan.banyandb;

import java.util.ArrayList;
import java.util.LinkedList;
import java.util.Random;

public class ClusterLink {
    private boolean isDebug = false;
    private static final int DEFAULT_TIMOUT_MS = 3000;
    private int maxRetry = 3;
    public ArrayList<LinkPool> pools = new ArrayList<LinkPool>();
    private CheckThread checkWorker = null;

    /*public ClusterLink() {
        this._maxRetry = 2;
    }*/

    public ClusterLink(String hosts, int maxPoolSize) throws Exception {
        this(hosts, maxPoolSize, false, DEFAULT_TIMOUT_MS);
    }

    public ClusterLink(String hosts, int maxPoolSize, boolean isDebug) throws Exception {
        this(hosts, maxPoolSize, isDebug, DEFAULT_TIMOUT_MS);
    }

    public ClusterLink(String hosts, int maxPoolSize, boolean isDebug, int timeoutMs) throws Exception {
        this.isDebug = isDebug;
        String[] hostList = hosts.split(",");
        int loop = 0;
        Link link = null;
        LinkedList<Link> ls = new LinkedList<Link>();
        for (String host : hostList) {
            String[] IPPort = host.split(":");
            if (IPPort.length == 2) {
                LinkPool pool = new LinkPool(IPPort[0], Integer.valueOf(IPPort[1].trim()), maxPoolSize, this.isDebug);           
                loop = 0;
                ls.clear();
                while (loop < 3) {
                    link = pool.getLink();
                    if (link == null) {
                        break;
                    }
                    ls.push(link);
                    loop++;
                }
                if (ls.size() == 3) {
                    pool.links.addAll(ls);
                    pool.setDown(false);
                    if (this.isDebug) {
                        System.out.printf("init connect %s:%s success\n", IPPort[0], IPPort[1]);
                    }
                } else {
                    for (Link l : ls) {
                        l.close();
                    }
                    pool.setDown(true);
                    if (this.isDebug) {
                        System.out.printf("init connect %s:%s failed\n", IPPort[0], IPPort[1]);
                    }
                } 
                this.pools.add(pool);
            } else {
                String err = "banyandb hosts error: " + hosts;
                throw new Exception(err);
            }
        }
        this.checkWorker = new CheckThread(this);
        this.checkWorker.start();
    }

    public boolean getDebug() {
        return this.isDebug;
    }

    public int getMaxRetry() {
        return this.maxRetry;
    }

    public void close() {
        try { 
            this.checkWorker.stopCheck();
            this.checkWorker.join();
            System.out.println("CheckThread stop");
        } catch(Exception e) {

        }
    }

    public Link getLink() {
        Link link = null;
        Random rand = new Random(System.nanoTime());
        int x = rand.nextInt(pools.size());
        for (int i = x; i < this.pools.size(); i++) {
            if (!this.pools.get(i).isDown()) {
                link = this.pools.get(i).getLink();
                if (link != null) {
                    return link;
                }
            }
        }
        for (int i = 0; i < x; i++) {
            if (!this.pools.get(i).isDown()) {
                link = this.pools.get(i).getLink();
                if (link != null) {
                    return link;
                }
            }
        }
        return null;
    }
}

class CheckThread extends Thread {
    private boolean isDebug = false;
    private boolean stop = false;
    private ClusterLink links = null;
    public CheckThread(ClusterLink links) {
        this.isDebug = links.getDebug();
        this.links = links;
    }

    public void stopCheck() {
        this.stop = true;
    }

    public void run() {
        if (this.isDebug) {
            System.out.println("CheckThread run start");
        }
        while (!this.stop) {
            if (this.isDebug) {
                System.out.println("CheckThread run xxx");
            }

            int loop = 0;
            Link link = null;
            LinkedList<Link> ls = new LinkedList<Link>();
            for (LinkPool pool : this.links.pools) {
                if (pool.isDown()) {
                    loop = 0;
                    ls.clear();
                    while (loop < 3) {
                        link = pool.getLink();
                        if (link == null) {
                            break;
                        }
                        ls.push(link);
                        loop++;
                    }
                    if (ls.size() == 3) {
                        pool.links.addAll(ls);
                        pool.setDown(false);
                    } else {
                        for (Link l : ls) {
                            l.close();
                        }
                    } 
                }   
            }

            try {
                Thread.sleep(1000);
            } catch (Exception e) {
            }
        }
        if (this.isDebug) {
            System.out.println("CheckThread run exit");
        }
    }
}
