<?php
$uri = service('uri')->getSegments();
$edit = in_array('edit', $uri);
?>

<?= $this->extend('dashboard/layouts/main'); ?>

<?= $this->section('styles') ?>
<link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/filepond-plugin-media-preview@1.0.11/dist/filepond-plugin-media-preview.min.css">
<link rel="stylesheet" href="<?= base_url('assets/css/pages/form-element-select.css'); ?>">
<style>
    .filepond--root {
        width: 100%;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section class="section">
    <div class="row">
        <script>
            currentUrl = '<?= current_url(); ?>';
        </script>

        <!-- Object Detail Information -->
        <div class="col-md-6 col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title text-center"><?= $title; ?></h4>
                </div>
                <div class="card-body">
                    <form class="form form-vertical" action="<?= ($edit) ? base_url('dashboard/homestayUnit/update') . '/' . $data['id'] : base_url('dashboard/homestayUnit'); ?>" method="post" enctype="multipart/form-data">
                        <div class="form-body">
                            <fieldset class="form-group mb-4">
                                <label for="id_unit_type" class="mb-2">Unit type</label>
                                <select class="form-select" id="id_unit_type" name="id_unit_type">
                                    <?php if ($unitTypeData) : ?>
                                        <?php foreach ($unitTypeData as $type) : ?>
                                            <?php if ($edit && $type['id']) : ?>
                                                <option value="<?= $type['id'] ?>" <?= (esc($type['id']) == $data['id_unit_type']) ? 'selected' : ''; ?>><?= $type['name']; ?></option>
                                            <?php else : ?>
                                                <option value="<?= esc($type['id']); ?>"><?= esc($type['name']); ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <option value=" ">Unit type not foumd</option>
                                    <?php endif; ?>
                                </select>
                            </fieldset>
                            <fieldset class="form-group mb-4">
                                <label for="id_homestay" class="mb-2">Homestay <span class="text-danger">*</span></label>
                                <select class="form-select" id="id_homestay" name="id_homestay" required>
                                    <?php if ($homestayData) : ?>
                                        <?php foreach ($homestayData as $homestay) : ?>
                                            <?php if ($edit && $homestay['id'] && $data['id_homestay']) : ?>
                                                <option value="<?= $homestay['id'] ?>" <?= (esc($homestay['id']) == $data['id_homestay']) ? 'selected' : ''; ?>><?= $homestay['name']; ?></option>
                                            <?php else : ?>
                                                <option value="<?= esc($homestay['id']); ?>"><?= esc($homestay['name']); ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <option value="">Homestay not found</option>
                                    <?php endif; ?>
                                </select>
                            </fieldset>
                            <div class="form-group mb-4">
                                <label for="name" class="mb-2">Homestay Unit Name <span class="text-danger">*</span> </label>
                                <input type="text" id="name" class="form-control" name="name" placeholder="Homestay Name" value="<?= ($edit) ? $data['name'] : old('name'); ?>" required>
                            </div>
                            <div class="form-group mb-4">
                                <label for="price" class="mb-2">Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp </span>
                                    <input type="number" id="price" class="form-control" name="price" placeholder="price" aria-label="price" aria-describedby="price" value="<?= ($edit) ? $data['price'] : old('price'); ?>" required>
                                </div>
                            </div>
                            <div class="form-group mb-4">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"><?= ($edit) ? $data['description'] : old('description'); ?></textarea>
                            </div>
                            <div class="form-group mb-4">
                                <label for="facilities" class="mb-2">Facilities</label>
                                <select class="choices form-select multiple-remove" multiple="multiple" id="facilities" name="facilities[]">
                                    <?php foreach ($facilities as $facility) : ?>
                                        <?php if ($edit && in_array(esc($facility['name']), $data['facilities'])) : ?>
                                            <option value="<?= esc($facility['id']); ?>" selected><?= esc($facility['name']); ?></option>
                                        <?php else : ?>
                                            <option value="<?= esc($facility['id']); ?>"><?= esc($facility['name']); ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mb-4">
                                <label for="gallery" class="form-label">Gallery</label>
                                <input class="form-control" accept="image/*" type="file" name="gallery[]" id="gallery">
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
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-resize/dist/filepond-plugin-image-resize.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="https://cdn.jsdelivr.net/npm/filepond-plugin-media-preview@1.0.11/dist/filepond-plugin-media-preview.min.js"></script>
<script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
<script src="<?= base_url('assets/js/extensions/form-element-select.js'); ?>"></script>
<script>
    getFacility();
</script>

<script>
    const myModal = document.getElementById('videoModal');
    const videoSrc = document.getElementById('video-play').getAttribute('data-src');

    myModal.addEventListener('shown.bs.modal', () => {
        console.log(videoSrc);
        document.getElementById('video').setAttribute('src', videoSrc);
    });
    myModal.addEventListener('hide.bs.modal', () => {
        document.getElementById('video').setAttribute('src', '');
    });
</script>
<script>
    FilePond.registerPlugin(
        FilePondPluginFileValidateType,
        FilePondPluginImageExifOrientation,
        FilePondPluginImagePreview,
        FilePondPluginImageResize,
        FilePondPluginMediaPreview,
    );

    // Get a reference to the file input element
    const photo = document.querySelector('input[id="gallery"]');

    // Create a FilePond instance
    const pond = FilePond.create(photo, {
        maxFileSize: '1920MB',
        maxTotalFileSize: '1920MB',
        imageResizeTargetHeight: 720,
        imageResizeUpscale: false,
        credits: false,
    });

    <?php if ($edit && count($data['gallery']) > 0) : ?>
        pond.addFiles(
            <?php foreach ($data['gallery'] as $gallery) : ?> `<?= base_url('media/photos/' . $gallery); ?>`,
            <?php endforeach; ?>
        );
    <?php endif; ?>
    pond.setOptions({
        server: {
            timeout: 3600000,
            process: {
                url: '<?= base_url("upload/photo") ?>',
                onload: (response) => {
                    console.log("processed:", response);
                    return response
                },
                onerror: (response) => {
                    console.log("error:", response);
                    return response
                },
            },
            revert: {
                url: '<?= base_url("upload/photo") ?>',
                onload: (response) => {
                    console.log("reverted:", response);
                    return response
                },
                onerror: (response) => {
                    console.log("error:", response);
                    return response
                },
            },
        }
    })
</script>
<?= $this->endSection() ?>