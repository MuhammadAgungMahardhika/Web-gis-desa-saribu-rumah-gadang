<?= $this->extend('maps/main'); ?>

<?= $this->section('content') ?>

<?= $this->include('maps/map-body'); ?>
<script>
    currentUrl = "mobile";
</script>
<?php

if (isset($data)):
    // Iterasi untuk menampilkan objectMarker berdasarkan detail_package
    foreach ($data['detail_package'] as $item):
        // Memeriksa apakah 'object' ada dan merupakan array
        if (isset($item['object']) && is_array($item['object'])): ?>
            <script>
                // Menambahkan marker untuk setiap object dalam detail_package
                objectMarker("<?= esc($item['object']['id']); ?>", <?= esc($item['object']['lat']); ?>, <?= esc($item['object']['lng']); ?>);
            </script>
        <?php endif; // Tutup if untuk cek object 
        ?>
    <?php endforeach; // Tutup foreach untuk detail_package 
    ?>
    <script>
        boundToObject();
    </script>
<?php endif; // Tutup if untuk cek data 
?>

<?= $this->endSection() ?>