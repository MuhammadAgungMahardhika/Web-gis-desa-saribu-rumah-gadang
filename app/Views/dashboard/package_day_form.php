<?php
$uri = service('uri')->getSegments();
$edit = in_array('edit', $uri);
?>

<?= $this->extend('dashboard/layouts/main'); ?>

<?= $this->section('content') ?>

<section class="section">
    <div class="row">
        <script>
            currentUrl = '<?= current_url(); ?>';
        </script>

        <!-- Object Detail Information -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title text-center"><?= $title; ?></h4>
                </div>
                <div class="card-body">
                    <form class="form form-vertical" action="<?= ($edit) ? base_url('dashboard/packageDay/update') . '/' . $data['id'] : base_url('dashboard/packageDay'); ?>" method="post" enctype="multipart/form-data">
                        <div class="form-body">
                            <div class="form-group mb-4">
                                <label for="id" class="mb-2">ID</label>
                                <input type="text" id="id" class="form-control" name="id" placeholder="ID" readonly="readonly" required value='<?= ($edit) ? $data['id'] : $id; ?>'>
                            </div>
                            <fieldset class="form-group mb-4">
                                <label for="id_package" class="mb-2">Tourism Package</label>
                                <select class="form-select" id="id_package" name="id_package">
                                    <?php if ($packageData) : ?>
                                        <?php foreach ($packageData as $package) : ?>
                                            <option value="<?= $package['id'] ?>" <?= (esc($package['name']) == $package['name']) ? 'selected' : ''; ?>><?= $package['name']; ?></option>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <option value=" ">Package not found</option>
                                    <?php endif; ?>
                                </select>
                            </fieldset>
                            <div class="form-group mb-4">
                                <table class="table table-border">
                                    <thead>
                                        <tr>
                                            <th>Object name</th>
                                            <th>Activity type</th>
                                            <th>Description</th>
                                        </tr>

                                    </thead>
                                    <tbody id="body-detail-package">

                                    </tbody>
                                </table>
                            </div>
                            <div class="form-group mb-4">
                                <label for="description" class="mb-2">Package Day Description</label>
                                <input type="text" id="description" class="form-control" name="description" placeholder="description" value="<?= ($edit) ? $data['description'] : old('description'); ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary me-1 mb-1">Submit</button>
                            <button type="reset" class="btn btn-light-secondary me-1 mb-1">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
<?= $this->section('javascript') ?>
<script>
    reloadDetailPackageData()

    function reloadDetailPackageData() {
        $("#body-detail-package").html(`
            <tr>
            <td>
                <input type="text" id="id_object" class="form-control" name="id_object" placeholder="id object" required>
            </td>
            <td>
                <input type="text" id="activity_type" class="form-control" name="activity_type" placeholder="activity type" required>
            </td>
            <td>
                <input type="text" id="detail_description" class="form-control" name="detail_description" placeholder="detail description ">
            </td>
            </tr>
        `)
    }

    function addDetailPackage() {

    }
</script>

<?= $this->endSection(); ?>