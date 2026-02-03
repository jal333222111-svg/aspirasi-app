<?php
session_start();
function checkLogin($type = null)
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../auth/login.php");
        exit();
    }
    if ($type && $_SESSION['user_type'] !== $type) {
        header("Location: ../index.php");
        exit();
    }
}
function redirect($path)
{
    header("Location: $path");
    exit();
}
function flash($name, $message = null)
{
    if ($message) {
        $_SESSION['flash'][$name] = $message;
    } else {
        $msg = $_SESSION['flash'][$name] ?? null;
        unset($_SESSION['flash'][$name]);
        return $msg;
    }
}
function formatDate($date)
{
    if (!$date) return '-';
    return date('d/m/Y', strtotime($date));
}