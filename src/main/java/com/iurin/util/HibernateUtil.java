package com.iurin.util;

import java.util.function.Consumer;
import java.util.function.Function;
import org.hibernate.Session;
import org.hibernate.SessionFactory;
import org.hibernate.Transaction;
import org.hibernate.cfg.Configuration;

public final class HibernateUtil {

    private static final SessionFactory SESSION_FACTORY =
            new Configuration().configure("hibernate.cfg.xml").buildSessionFactory();

    private HibernateUtil() {
    }

    public static SessionFactory getSessionFactory() {
        return SESSION_FACTORY;
    }

    /** Menjalankan query baca dengan session yang langsung ditutup. */
    public static <T> T baca(Function<Session, T> kerja) {
        try (Session session = SESSION_FACTORY.openSession()) {
            return kerja.apply(session);
        }
    }

    /** Menjalankan perubahan data di dalam transaksi; dibatalkan jika gagal. */
    public static void tulis(Consumer<Session> kerja) {
        try (Session session = SESSION_FACTORY.openSession()) {
            Transaction tx = session.beginTransaction();
            try {
                kerja.accept(session);
                tx.commit();
            } catch (RuntimeException e) {
                tx.rollback();
                throw e;
            }
        }
    }

    public static void tutup() {
        SESSION_FACTORY.close();
    }
}
