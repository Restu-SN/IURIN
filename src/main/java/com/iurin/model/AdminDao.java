package com.iurin.model;

import com.iurin.util.HibernateUtil;
import com.iurin.util.PasswordUtil;

public class AdminDao {

    /** Mengembalikan admin jika username & password cocok, selain itu null. */
    public Admin login(String username, String password) {
        Admin admin = HibernateUtil.baca(s -> s
                .createQuery("from Admin where username = :u", Admin.class)
                .setParameter("u", username)
                .uniqueResult());
        if (admin != null && PasswordUtil.verify(password, admin.getPasswordHash())) {
            return admin;
        }
        return null;
    }

    public void gantiPassword(int adminId, String passwordBaru) {
        HibernateUtil.tulis(s -> s.get(Admin.class, adminId).setPasswordHash(PasswordUtil.hash(passwordBaru)));
    }
}
