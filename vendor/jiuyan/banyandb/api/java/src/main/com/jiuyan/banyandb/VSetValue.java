package com.jiuyan.banyandb;

public class VSetValue {
    private long score = 0;
    private String value = "";

    public VSetValue(long score, String value) {
        this.score = score;
        this.value = value;
    }

    public long getScore() {
        return this.score;
    }

    public String getValue() {
        return this.value;
    }
}



