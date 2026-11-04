package com.iurin.model;

public class Admin {

    private final int id;
    private final String username;
    private final String nama;

    public Admin(int id, String username, String nama) {
        this.id = id;
        this.username = username;
        this.nama = nama;
    }

    public int getId() { return id; }
    public String getUsername() { return username; }
    public String getNama() { return nama; }
}
