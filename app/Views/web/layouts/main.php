<!doctype html>
<?php $uri = service('uri')->getSegments(); ?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title); ?> - Desa Wisata Saribu Rumah Gadang</title>

    <!-- Template CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/main/app.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/main/app-dark.css'); ?>">

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
    <!-- datatable -->
    <link rel="stylesheet" href="<?= base_url('assets/css/extensions/jquery.dataTables.min.css') ?> " />

    <!-- Jquery -->
    <script src="<?= base_url('assets/js/extensions/jquery-3.6.0.min.js') ?>"></script>
    <!-- Sweet alert -->
    <script src="<?= base_url('assets/js/extensions/sweetalert2.js'); ?>"></script>

    <!-- Animate css -->
    <link rel="stylesheet" href="<?= base_url('assets/css/extensions/animate.min.css') ?> " />

    <!-- GSAP -->
    <script src="<?= base_url('assets/js/extensions/gsap.min.js') ?>"></script>
    <!-- Google Maps API and Custom JS -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB8B04MTIk7abJDVESr6SUF6f3Hgt1DPAY&libraries=drawing"></script>
    <script src="<?= base_url('js/geom.js') ?>"></script>
    <script src="<?= base_url('js/web.js'); ?>"></script>
    <?= $this->renderSection('head'); ?>
</head>

<body>
    <div id="app">

        <?php if (isset($currentUrl) != 'mobile'): ?>
            <!-- Sidebar -->
            <?php if (isset($data) && array_key_exists('id', $data)) : ?>
                <?= $this->include('web/layouts/sidebar_detail'); ?>
            <?php else : ?>
                <?= $this->include('web/layouts/sidebar'); ?>
            <?php endif; ?>
            <!-- End Sidebar -->
        <?php endif; ?>
        <!-- Main -->
        <div id="main">
            <?php if (isset($currentUrl) != 'mobile'): ?>
                <?= $this->include('web/layouts/header'); ?>
            <?php endif; ?>
            <!-- Content -->
            <?= $this->renderSection('content') ?>
            <!-- End Content -->

            <!-- Footer -->
            <?= $this->include('web/layouts/footer') ?>
            <!-- End Footer -->
        </div>
        <!-- End Main -->

    </div>

    <!-- Template CSS -->

    <script src="<?= base_url('assets/js/app.js'); ?>"></script>
    <!-- datatable -->
    <script src="<?= base_url('assets/js/extensions/jquery.dataTables.min.js') ?>"></script>
    <!-- Custom JS -->
    <?= $this->renderSection('javascript') ?>

</body>

</html>