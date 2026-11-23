<?php
/** FreshCart Store - Log out */
require_once __DIR__ . '/includes/functions.php';
session_unset();
session_destroy();
session_start();
flash('success', 'You have been logged out.');
redirect('index.php');
