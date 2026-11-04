package com.iurin.util;

import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.security.SecureRandom;
import java.security.spec.InvalidKeySpecException;
import java.util.Base64;
import javax.crypto.SecretKeyFactory;
import javax.crypto.spec.PBEKeySpec;

/**
 * Hash password dengan PBKDF2-HMAC-SHA256. Format tersimpan: iterasi:salt:hash (Base64).
 */
public final class PasswordUtil {

    private static final int ITERASI = 65536;
    private static final int PANJANG_BIT = 256;

    private PasswordUtil() {
    }

    public static String hash(String password) {
        byte[] salt = new byte[16];
        new SecureRandom().nextBytes(salt);
        byte[] hash = pbkdf2(password.toCharArray(), salt, ITERASI);
        Base64.Encoder b64 = Base64.getEncoder();
        return ITERASI + ":" + b64.encodeToString(salt) + ":" + b64.encodeToString(hash);
    }

    public static boolean verify(String password, String tersimpan) {
        String[] bagian = tersimpan.split(":");
        if (bagian.length != 3) {
            return false;
        }
        Base64.Decoder b64 = Base64.getDecoder();
        byte[] salt = b64.decode(bagian[1]);
        byte[] hashLama = b64.decode(bagian[2]);
        byte[] hashBaru = pbkdf2(password.toCharArray(), salt, Integer.parseInt(bagian[0]));
        return MessageDigest.isEqual(hashLama, hashBaru);
    }

    private static byte[] pbkdf2(char[] password, byte[] salt, int iterasi) {
        try {
            PBEKeySpec spec = new PBEKeySpec(password, salt, iterasi, PANJANG_BIT);
            return SecretKeyFactory.getInstance("PBKDF2WithHmacSHA256").generateSecret(spec).getEncoded();
        } catch (NoSuchAlgorithmException | InvalidKeySpecException e) {
            throw new IllegalStateException(e);
        }
    }
}
