<?php
require_once 'config/functions.php';
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_type'] === 'admin') {
        redirect('admin/index.php');
    } else {
        redirect('student/index.php');
    }
} else {
    redirect('auth/login.php');
}