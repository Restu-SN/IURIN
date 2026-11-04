package com.iurin.util;

/** Dilempar saat data melanggar aturan bisnis (mis. nomor HP / pembayaran ganda). */
public class ValidasiException extends Exception {

    public ValidasiException(String pesan) {
        super(pesan);
    }
}
