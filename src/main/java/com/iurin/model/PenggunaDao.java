package com.iurin.model;

import com.iurin.util.HibernateUtil;
import com.iurin.util.PasswordUtil;

public class PenggunaDao {

    /** Mengembalikan pengguna jika username & password cocok, selain itu null. */
    public Pengguna login(String username, String password) {
        Pengguna p = HibernateUtil.baca(s -> s
                .createQuery("from Pengguna where username = :u", Pengguna.class)
                .setParameter("u", username)
                .uniqueResult());
        if (p != null && password != null && PasswordUtil.verify(password, p.getPasswordHash())) {
            return p;
        }
        return null;
    }

    public void gantiPassword(int penggunaId, String passwordBaru) {
        HibernateUtil.tulis(s -> s.get(Pengguna.class, penggunaId).setPasswordHash(PasswordUtil.hash(passwordBaru)));
    }
}
