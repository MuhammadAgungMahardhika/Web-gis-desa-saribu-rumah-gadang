<?php
$uri = service('uri')->getSegments();
$users = in_array('users', $uri);
?>

<?= $this->extend('dashboard/layouts/main'); ?>

<?= $this->section('content') ?>
<section class="section">
    <div class="card p-4">
        <div class="card-header text-center">
            <h2 class="text-primary">Dashboard</h2>
        </div>
        <div class="card-body">
            <hr class="text-primary">
            <div class="row">
                <div class="col ">
                    <a href="<?= base_url('dashboard/reservation') ?>" class="btn btn-outline-primary">
                        <div class="card ">
                            <div class="card-header mb-4">
                                <h4>Manage Reservation</h4>
                            </div>
                            <div class="card-body ">
                                <img class="img-fluid " src="<?= base_url("media/photos/r1c.jpeg") ?>?" alt="" with="200">
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col ">
                    <a href="<?= base_url('dashboard/rumahGadang') ?>" class="btn btn-outline-primary">
                        <div class="card ">
                            <div class="card-header mb-4">
                                <h4>Manage Rumah Gadang</h4>
                            </div>
                            <div class="card-body ">
                                <img class="img-fluid " src="<?= base_url("media/photos/r1c.jpeg") ?>?" alt="" with="200">
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col">
                    <a href="<?= base_url('dashboard/facility') ?>" class="btn btn-outline-primary">
                        <div class="card">
                            <div class="card-header mb-4">
                                <h4>Manage Rumah Gadang Facility</h4>
                            </div>
                            <div class="card-body">
                                <img class="img-fluid" src="<?= base_url("media/photos/r2c.jpg") ?>?" alt="" with="200">
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="<?= base_url('dashboard/package') ?>" class="btn btn-outline-primary">
                        <div class="card">
                            <div class="card-header mb-4">
                                <h4>Manage Tourism Package</h4>
                            </div>
                            <div class="card-body ">
                                <img class="img-fluid " src="<?= base_url("media/photos/r10a.jpeg") ?>?" alt="" with="200">
                            </div>
                        </div>
                    </a>
                </div>
            </div>

        </div>

    </div>

</section>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
    $(document).ready(function() {
        $('#table-manage').DataTable({
            columnDefs: [{
                targets: ['_all'],
                className: 'dt-head-center'
            }],
            lengthMenu: [5, 10, 20, 50, 100]
        });
    });
</script>
<?= $this->endSection() ?>