<!doctype html>
<?php $uri = service('uri')->getSegments(); ?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title); ?> - Desa Wisata Kampuang Minang Nagari Sumpu</title>

    <!-- Template CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/main/app.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/main/app-dark.css'); ?>">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('css/web.css'); ?>">
    <?= $this->renderSection('styles') ?>
    <link rel="shortcut icon" href="<?= base_url('media/icon/favicon.svg'); ?>" type="image/x-icon">

    <!-- Third Party CSS and JS -->
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('css/web.css'); ?>">
    <?= $this->renderSection('styles') ?>
    <link rel="shortcut icon" href="<?= base_url('media/icon/favicon.svg'); ?>" type="image/x-icon">

    <!-- Icon iconly -->
    <link rel="stylesheet" href="<?= base_url('assets/css/shared/iconly.css'); ?>">
    <!-- Icon materialize -->
    <link rel="stylesheet" href="<?= base_url('assets/css/extensions/font.css') ?> " />
    <link rel="stylesheet" href="<?= base_url('assets/css/extensions/icon.css') ?> " />
    <!-- Icon Font awesome -->
    <script src="<?= base_url('assets/js/extensions/font-awesome.js'); ?>"></script>
    <!-- Jquery -->
    <script src="<?= base_url('assets/js/extensions/jquery-3.6.0.min.js') ?>"></script>
    <!-- Sweet alert -->
    <script src="<?= base_url('assets/js/extensions/sweetalert2.js'); ?>"></script>

    <!-- Google Maps API and Custom JS -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB8B04MTIk7abJDVESr6SUF6f3Hgt1DPAY&libraries=drawing"></script>
    <script src="<?= base_url('js/geom.js') ?>"></script>
    <script src="<?= base_url('js/web.js'); ?>"></script>
    <style>
        #googlemaps {
            height: 100%;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body>
    <div id="app">
        <?= $this->renderSection('content') ?>
    </div>
    <!-- Template CSS -->
    <script src="<?= base_url('assets/js/app.js'); ?>"></script>

    <!-- Custom JS -->
    <?= $this->renderSection('javascript') ?>

</body>

</html>