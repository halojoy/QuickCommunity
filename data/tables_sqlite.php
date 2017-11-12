<?php if(!defined('QCOM1'))exit() ?>
<?php

// Create SQLite database tables.

$sql = (
    "CREATE TABLE IF NOT EXISTS settings (
    setkey TEXT,
    setvalue TEXT);");
$pdo->exec($sql);

$sql = (
    "CREATE TABLE IF NOT EXISTS forums (
    fid INTEGER PRIMARY KEY,
    f_name TEXT,
    f_desc TEXT,
    f_order INTEGER);");
$pdo->exec($sql);

$sql = (
    "CREATE TABLE IF NOT EXISTS topics (
    tid INTEGER PRIMARY KEY,
    t_fid INTEGER,
    t_fname TEXT,
    t_subject TEXT,
    t_uid INTEGER,
    t_uname TEXT,
    t_time INTEGER,
    t_lastpid INTEGER,
    t_lastpuid INTEGER,
    t_lastpuname TEXT,
    t_lastptime INTEGER);");
$pdo->exec($sql);

$sql = (
    "CREATE TABLE IF NOT EXISTS posts (
    pid INTEGER PRIMARY KEY,
    p_fid INTEGER,
    p_fname TEXT,
    p_tid INTEGER,
    p_tsubj TEXT,
    p_message TEXT,
    p_image TEXT DEFAULT NULL,
    p_uid INTEGER,
    p_uname TEXT,
    p_time INTEGER,
    p_ip TEXT);");
$pdo->exec($sql);

$sql = (
    "CREATE TABLE IF NOT EXISTS users (
    uid INTEGER PRIMARY KEY,
    u_name TEXT,
    u_pass TEXT,
    u_mail TEXT,
    u_type TEXT,
    u_posts INTEGER DEFAULT 0,
    u_ip TEXT,
    u_joined INTEGER,
    u_active INTEGER);");
$pdo->exec($sql);
