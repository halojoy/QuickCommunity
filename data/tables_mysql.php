<?php if(!defined('QCOM1'))exit();

// Create MySQL database tables.

$sql = (
    "CREATE TABLE IF NOT EXISTS settings (
    setkey TEXT,
    setvalue TEXT)
    COLLATE utf8mb4_unicode_ci");
$pdo->exec($sql);

$sql = (
    "CREATE TABLE IF NOT EXISTS forums (
    fid INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    f_name TEXT,
    f_desc TEXT,
    f_order INT)
    COLLATE utf8mb4_unicode_ci");
$pdo->exec($sql);

$sql = (
    "CREATE TABLE IF NOT EXISTS topics (
    tid INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    t_fid INT,
    t_fname TEXT,
    t_subject TEXT,
    t_uid INT,
    t_uname TEXT,
    t_time INT,
    t_lastpid INT,
    t_lastpuid INT,
    t_lastpuname TEXT,
    t_lastptime INT,
    t_sticky TEXT,
    t_lock TEXT)
    COLLATE utf8mb4_unicode_ci");
$pdo->exec($sql);

$sql = (
    "CREATE TABLE IF NOT EXISTS posts (
    pid INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    p_fid INT,
    p_fname TEXT,
    p_tid INT,
    p_tsubj TEXT,
    p_message TEXT,
    p_file TEXT DEFAULT NULL,
    p_cat TEXT DEFAULT NULL,
    p_uid INT,
    p_uname TEXT,
    p_time INT,
    p_ip TEXT)
    COLLATE utf8mb4_unicode_ci");
$pdo->exec($sql);

$sql = (
    "CREATE TABLE IF NOT EXISTS users (
    uid INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    u_name TEXT,
    u_pass TEXT,
    u_mail TEXT,
    u_type TEXT,
    u_code TEXT,
    u_posts INT DEFAULT 0,
    u_ip TEXT,
    u_joined INT,
    u_active INT)
    COLLATE utf8mb4_unicode_ci");
$pdo->exec($sql);
