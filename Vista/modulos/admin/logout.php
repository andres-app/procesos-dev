<?php
unset($_SESSION['admin_user']);
session_regenerate_id(true);
header("Location: /public/admin/login");
exit;