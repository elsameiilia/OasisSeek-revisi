CREATE DATABASE sirmesir;

USE sirmesir;

CREATE TABLE users(
    email       VARCHAR(50)     PRIMARY KEY,
    password    VARCHAR(255)    NOT NULL,
    username    VARCHAR(30)     UNIQUE NOT NULL,
    name        VARCHAR(30)     NOT NULL,
    phone       VARCHAR(15)     NULL,
    photo       VARCHAR(255)    NULL,
    role        enum('admin','user') DEFAULT 'user'
);

INSERT INTO users(email, password, username, name, role) VALUES ('admin@gmail.com', '$2y$10$3LhGm4lnfxtVI2y7/DHJGuXnBU.dd5yCIIvKC9cJH7QnQC.aPr.F2', 'admin', 'admin', 'admin');

CREATE TABLE destinations(
    des_id      INT PRIMARY KEY AUTO_INCREMENT,
    name        VARCHAR(50)     NOT NULL,
    title       VARCHAR(100)    NOT NULL,
    description TEXT            NULL,
    banner      VARCHAR(255)    NOT NULL
);

CREATE TABLE img_destinations(
    img_des_id  INT PRIMARY KEY AUTO_INCREMENT,
    des_id      INT             NOT NULL,
    photo       VARCHAR(255)    NOT NULL,

    FOREIGN KEY(des_id) REFERENCES destinations(des_id)
);

CREATE TABLE events(
    event_id    INT PRIMARY KEY AUTO_INCREMENT,
    name        VARCHAR(50)     NOT NULL,
    title       VARCHAR(100)    NOT NULL,
    description TEXT            NULL,
    location    VARCHAR(50)     NOT NULL,
    date        VARCHAR(50)     NOT NULL,
    time        VARCHAR(50)     NOT NULL,
    banner      VARCHAR(255)    NOT NULL
);

CREATE TABLE bookmark(
    bm_id       INT PRIMARY KEY AUTO_INCREMENT,
    des_id      INT             NOT NULL,
    username    VARCHAR(30)     NOT NULL,

    FOREIGN KEY(des_id) REFERENCES destinations(des_id) ON DELETE CASCADE,
    FOREIGN KEY(username) REFERENCES users(username) ON DELETE CASCADE
);