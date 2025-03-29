<?= $this->extend('web/layouts/main'); ?>

<?= $this->section('content') ?>

<?= $this->include('web/layouts/map-body'); ?>
<script>
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