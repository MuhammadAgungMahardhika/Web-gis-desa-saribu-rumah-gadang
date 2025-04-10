<?php
// app/Config/Midtrans.php

// Require semua file Midtrans
require_once APPPATH . 'ThirdParty/midtrans-php-master/Midtrans/Config.php';
require_once APPPATH . 'ThirdParty/midtrans-php-master/Midtrans/Snap.php';
require_once APPPATH . 'ThirdParty/midtrans-php-master/Midtrans/ApiRequestor.php';
require_once APPPATH . 'ThirdParty/midtrans-php-master/Midtrans/SnapApiRequestor.php';
require_once APPPATH . 'ThirdParty/midtrans-php-master/Midtrans/Notification.php';
require_once APPPATH . 'ThirdParty/midtrans-php-master/Midtrans/Transaction.php';
require_once APPPATH . 'ThirdParty/midtrans-php-master/Midtrans/CoreApi.php';
require_once APPPATH . 'ThirdParty/midtrans-php-master/Midtrans/Sanitizer.php';
// Tambahkan file lain yang dibutuhkan

// Jika ada Exception class
if (file_exists(APPPATH . 'ThirdParty/midtrans-php-master/Midtrans/Exception/MidtransException.php')) {
    require_once APPPATH . 'ThirdParty/midtrans-php-master/Midtrans/Exception/MidtransException.php';
}
