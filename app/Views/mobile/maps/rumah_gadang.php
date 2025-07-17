<?= $this->extend('web/layouts/main'); ?>
<?= $this->section('head') ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css" integrity="sha512-34s5cpvaNG3BknEWSuOncX28vz97bRI59UnVtEEpFX536A7BtZSJHsDyFoCl8S7Dt2TPzcrCEoHBGeM4SUBDBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js" integrity="sha512-LsnSViqQyaXpD4mBBdRYeP6sRwJiJveh2ZIbW41EBrNmKxgr/LFZIiWT6yr+nycvhvauz8c2nYMhrP80YhG7Cw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<!-- Modal reservation -->
<div class="modal fade text-left" id="reservationModal" tabindex="-1" aria-labelledby="myModalLabel1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary ">
                <h5 class="modal-title text-white" id="modalTitle"></h5>
                <button type="button" class="close rounded-pill" data-bs-dismiss="modal" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="modal-body" id="modalBody">
            </div>
            <div class="modal-footer" id="modalFooter">
            </div>
        </div>
    </div>
</div>

<!-- Modal rute -->
<!-- Modal Route All -->
<div class="modal fade text-left" id="routeAllModal" tabindex="-1" aria-labelledby="routeAllModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="routeAllModalLabel">Daftar Rute</h5>
                <button type="button" class="close rounded-pill" data-bs-dismiss="modal" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="modal-body" id="routeAllModalBody">
                <!-- Tabel akan di-generate dari JS -->
            </div>
            <div class="modal-footer" id="routeAllModalFooter">
                <!-- Tombol Reset All akan di sini -->
            </div>
        </div>
    </div>
</div>



<div class="card-body">
    <div class="mt-3" id="check-nearby-col">
        <label for="inputRadiusNearbyMobile" class="form-label">Radius: </label>
        <label id="radiusValueNearbyMobile" class="form-label">0 m</label>
        <input type="range" class="form-range" min="0" max="20" value="0" id="inputRadiusNearbyMobile" name="inputRadiusNearbyMobile" onchange="updateRadiusMobile();">
    </div>
</div>
<?= $this->include('web/layouts/map-body'); ?>
<script>
    $("#check-nearby-col").hide();
    UserIdManager.saveUserIdToSessionStorage('<?= user_id(); ?>')
    console.log('<?= user_id(); ?>')
    currentUrl = '<?= $currentUrl ?>';
</script>
<?php

if (isset($data)):
    foreach ($data as $item): ?>
        <script>
            currentUrl = currentUrl + "<?= esc($item['id']); ?>"
        </script>
        <script>
            objectMarker("<?= esc($item['id']); ?>", <?= esc($item['lat']); ?>, <?= esc($item['lng']); ?>);
        </script>
    <?php
    endforeach; ?>
    <script>
        boundToObject();
    </script>
<?php
endif;

?>

<?= $this->endSection() ?>