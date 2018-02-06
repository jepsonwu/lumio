package com.jiuyan.banyandb;

import java.util.LinkedList;

public class LinkPool {
    private final static int MAX_FAIL = 3;
    private final static int FAIL_INTVAL = 10;
    private boolean isDebug = false;
    private boolean down = false;
    private String ip;
    private int port;
    //private int maxLinkSize;
    private int maxPoolSize;
    private int fails = 0;
    private long last_fail_timer = 0;
    public LinkedList<Link> links = new LinkedList<Link>();    // queue

    public LinkPool(String ip, int port, int maxPoolSize, boolean isDebug) {
        this.ip = ip;
        this.port = port;
        this.maxPoolSize = maxPoolSize;
        //this.maxLinkSize = 10 * maxPoolSize;
        this.isDebug = isDebug;
    }

    public boolean isDown() {
        return this.down;
    }

    public void setDown(boolean down) {
        this.down = down;
    }

    public Link getLink() {
        if (this.isDebug) {
            System.out.printf("pool %s:%d maxPoolSize:%d poolSize:%d getLink\n", 
                    this.ip, this.port, this.maxPoolSize, this.links.size());
        }
        Link link = null;
        synchronized (this) {
            if (this.links.size() > 0) {
                link = this.links.pop();
                link.pool = this;
                return link;
            }
        }

        try {
            link = new Link(this.ip, this.port, 3000);
            link.pool = this;
            if (isDebug) {
                String err = String.format("link %s:%d success", this.ip, this.port);
                System.out.println(err);
            }
            return link;
        } catch (Exception e) {
            if (isDebug) {
                String err = String.format("link %s:%d failed:", this.ip, this.port);
                err += e.getMessage();
                System.out.println(err);
            }
            return null;
        }
    }

    public void returnLink(Link link, boolean drop) {
        if (this.isDebug) {
            System.out.printf("pool %s:%d maxPoolSize:%d poolSize:%d returnLink %b\n", 
                this.ip, this.port, this.maxPoolSize, this.links.size(), drop);
        }
        synchronized (this) {
            if (this.down) {
                link.close();
                return;
            }
            if (link != null) {
                if (drop) {
                    link.close();
                    long now = System.currentTimeMillis() / 1000;
                    if (now - this.last_fail_timer > FAIL_INTVAL) {
                        this.last_fail_timer = now;
                        this.fails = 1;
                    } else {
                        this.fails++;
                    }
                    if (this.fails >= MAX_FAIL) {
                        while (true) {
                            if (this.links.size() == 0) {
                                break;
                            }
                            Link l = this.links.pop();
                            l.close();
                        }
                        this.fails = 0;
                        this.down = true;
                    }
                    return;
                } else {
                    if (this.links.size() >= this.maxPoolSize) { 
                        link.close();
                    } else {
                        this.links.push(link);
                    }
                }
            }
        }
    }
}
