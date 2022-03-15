CREATE TABLE system_access_notification_log
(
    id         INTEGER PRIMARY KEY NOT NULL,
    login      TEXT,
    email      TEXT,
    ip_address TEXT,
    login_time TEXT
);