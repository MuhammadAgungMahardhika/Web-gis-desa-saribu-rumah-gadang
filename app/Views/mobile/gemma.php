<?= $this->extend('web/layouts/main'); ?>

<?= $this->section('content') ?>

<section class="section">
    <div class="container-fluid">
        <div class="card p-2 shadow-sm">
            <div class="card-header text-center card-title  mb-2">Gemini AI</div>
            <div class="card-body">
                <div class="row d-flex">
                    <textarea name="" id="" class="mb-4"></textarea>
                    <input type="text" class="input">
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
<?= $this->section('javascript') ?>
<script>

</script>
<?= $this->endSection() ?>