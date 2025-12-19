<?php
session_start();
require_once "../config/db.php";
require_once "../helpers/log_helper.php";

/* ===============================
   CATAT LOG AKTIVITAS
   =============================== */
if (isset($_SESSION['username'])) {
    log_activity($conn, "Logout dari sistem");
}

/* ===============================
   HAPUS SESSION
   =============================== */
session_unset();
session_destroy();

/* ===============================
   REDIRECT KE LOGIN
   =============================== */
header("Location: login_form.php");
exit;
