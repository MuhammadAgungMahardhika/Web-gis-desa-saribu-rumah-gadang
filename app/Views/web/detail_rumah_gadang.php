<?= $this->extend('web/layouts/main'); ?>

<?= $this->section('head') ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css" integrity="sha512-34s5cpvaNG3BknEWSuOncX28vz97bRI59UnVtEEpFX536A7BtZSJHsDyFoCl8S7Dt2TPzcrCEoHBGeM4SUBDBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js" integrity="sha512-LsnSViqQyaXpD4mBBdRYeP6sRwJiJveh2ZIbW41EBrNmKxgr/LFZIiWT6yr+nycvhvauz8c2nYMhrP80YhG7Cw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/resources/demos/style.css"> -->

<!-- <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script> -->
<style>
    input[type=date]::-webkit-inner-spin-button,
    input[type=date]::-webkit-calendar-picker-indicator {
        display: none;
    }
</style>
<?= $this->endSection(); ?>
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

<section class="section">
    <div class="row">
        <script>
            currentUrl = '<?= current_url(); ?>';
        </script>

        <!-- Object Detail Information -->
        <div class="col-md-6 col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title text-center"><?= $data['id_homestay'] == null ?  'Rumah Gadang Information' : 'Rumah Gadang & Homestay Information'  ?> </h4>
                    <?php if ($data['id_homestay'] != null && isset($data['homestayData']['status'])) : ?>
                        <a class="btn btn-primary" onclick="showReservationModal()" data-bs-toggle="modal" data-bs-target="#reservationModal"> Reservation <i class="fa fa-ticket"></i> </a>

                    <?php endif; ?>
                    <div class="text-center">
                        <?php for ($i = 0; $i < (int)esc($data['avg_rating']); $i++) { ?>
                            <span class="material-symbols-outlined rating-color">star</span>
                        <?php } ?>
                        <?php for ($i = 0; $i < (5 - (int)esc($data['avg_rating'])); $i++) { ?>
                            <span class="material-symbols-outlined">star</span>
                        <?php } ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row shadow-sm mb-4">
                        <div class="col table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td class="fw-bold">Name</td>
                                        <td><?= esc($data['name']); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Homestay status</td>
                                        <?php if ($data['id_homestay'] != null && isset($data['homestayData']['status'])) : ?>
                                            <td class="<?= $data['homestayData']['status'] == 1 ? 'text-success' : '' ?>"><?= esc($data['homestayData']['status']  == 1 ? 'Homestay Available ( Book now )' : 'Homestay Not Available'); ?></td>
                                        <?php else : ?>
                                            <td> Homestay Not Available</td>
                                        <?php endif; ?>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Address</td>
                                        <td><?= esc($data['address']); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Open</td>
                                        <td><?= date('H:i', strtotime(esc($data['open']))) . ' WIB'; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Close</td>
                                        <td><?= date('H:i', strtotime(esc($data['close']))) . ' WIB'; ?></td>
                                    </tr>
                                    <!-- <tr>
                                        <td class="fw-bold">Ticket Price</td>
                                        <td><?= 'Rp ' . number_format(esc($data['ticket_price']), 0, ',', '.'); ?></td>
                                    </tr> -->
                                    <tr>
                                        <td class="fw-bold">Contact Person</td>
                                        <td><?= esc($data['contact_person']); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row shadow-sm mb-4 p-2">
                        <div class="col">
                            <p class="fw-bold">Rumah Gadang Description</p>
                            <p><?= esc($data['description']); ?></p>
                        </div>
                        <?php if ($data['id_homestay'] != null && isset($data['homestayData']['description'])) : ?>
                            <div class="col">
                                <p class="fw-bold">Homestay Description</p>
                                <p><?= esc($data['homestayData']['description']); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="row shadow-sm p-2">
                        <div class="col">
                            <a role="button" class="fw-bold text-secondary " data-bs-toggle="collapse" href="#collapseExample" aria-controls="collapseExample">Rumah Gadang Facilities <i class="fa fa-caret-down ms-2" aria-hidden="true"></i></a>
                            <?php $i = 1; ?>
                            <div class="collapse show my-2" id="collapseExample" style="">
                                <?php if (isset($data['facilities']) && $data['facilities'] != null) : ?>
                                    <?php foreach ($data['facilities'] as $facility) : ?>
                                        <p><?= esc($i) . '. ' . esc($facility); ?></p>
                                        <?php $i++; ?>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <p> No Facilities Found</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if ($data['id_homestay'] != null && isset($data['homestayData']['homestay_facility'])) : ?>
                            <div class="col">
                                <a role="button" class="fw-bold text-secondary " data-bs-toggle="collapse" href="#collapseExample2" aria-controls="collapseExample2">Homestay Facilities <i class="fa fa-caret-down ms-2" aria-hidden="true"></i></a>
                                <?php $i = 1; ?>
                                <div class="collapse show my-2" id="collapseExample2" style="">
                                    <?php foreach ($data['homestayData']['homestay_facility']  as $facility) : ?>
                                        <p><?= esc($i) . '. ' . esc($facility['name']); ?></p>
                                        <?php $i++; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!--Rating and Review Section-->
            <?= $this->include('web/layouts/review'); ?>
        </div>

        <div class="col-md-6 col-12">
            <!-- Object Location on Map -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Google Maps</h5>
                </div>

                <?= $this->include('web/layouts/map-body'); ?>
                <script>
                    initMap(<?= esc($data['lat']); ?>, <?= esc($data['lng']); ?>)
                </script>
                <script>
                    objectMarker("<?= esc($data['id']); ?>", <?= esc($data['lat']); ?>, <?= esc($data['lng']); ?>);
                </script>
            </div>

            <!-- Object Media -->
            <?= $this->include('web/layouts/gallery_video'); ?>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>

<script>
    function showReservationModal() {
        <?php if (in_groups('user') && isset($data['homestayData'])) : ?>
            $('#modalTitle').html("Reservation form")
            $('#modalBody').html(`
            <div class=" p-2">
                <div class="mb-2 shadow-sm p-4 rounded">
                    <p class="text-center fw-bold text-dark"> Homestay Informations </p>
                    <div class="text-center">
                        <?php for ($i = 0; $i < (int)esc($data['homestayData']['avg_homestay_rating']['avg_rating']); $i++) { ?>
                            <span class="material-symbols-outlined rating-color">star</span>
                        <?php } ?>
                        <?php for ($i = 0; $i < (5 - (int)esc($data['homestayData']['avg_homestay_rating']['avg_rating'])); $i++) { ?>
                            <span class="material-symbols-outlined">star</span>
                        <?php } ?>
                    </div>
                    <table class="table table-borderless text-dark ">
                                        <tbody>
                                            <?php if (isset($data['homestayGalleries'])) : ?>
                                            <tr>
                                                <td colspan="2">
                                                    <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                                                    <ol class="carousel-indicators">
                                                        <?php $i = 0; ?>
                                                        <?php foreach ($data['homestayGalleries'] as $gallery) : ?>
                                                            <li data-bs-target="#carouselExampleCaptions" data-bs-slide-to="<?= esc($i); ?>" class="<?= ($i == 0) ? 'active' : ''; ?>"></li>
                                                            <?php $i++; ?>
                                                        <?php endforeach; ?>
                                                    </ol>
                                                    <div class="carousel-inner">
                                                        <?php $i = 0; ?>
                                                        <?php foreach ($data['homestayGalleries'] as $gallery) : ?>
                                                            <div class="carousel-item<?= ($i == 0) ? ' active' : ''; ?>">
                                                                <img src="<?= base_url('media/photos/homestay/' . esc($gallery['url'])); ?>" class="d-block w-100" alt="<?= esc($gallery['url']); ?>">
                                                            </div>
                                                            <?php $i++; ?>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-bs-slide="prev">
                                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                        <span class="visually-hidden">Previous</span>
                                                    </a>
                                                    <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-bs-slide="next">
                                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                        <span class="visually-hidden">Next</span>
                                                    </a>
                                                    </div>
                                                </td>
                                          
                                            </tr>
                                            <?php endif; ?>
                                            <tr>
                                                <td class="fw-bold">Homestay Name</td>
                                                <td><?= esc($data['name']); ?> ( <?= esc($data['homestayData']['name']); ?> )</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Status</td>
                                                <td><?= $data['homestayData']['status'] == 1 ? 'available' : 'not available' ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Price / day </td>
                                                <td><?= 'Rp ' . number_format(esc($data['homestayData']['ticket_price']), 0, ',', '.'); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Address</td>
                                                <td><?= esc($data['homestayData']['address']) ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Check in Time</td>
                                                <td><?= esc($data['homestayData']['checkin']) ?> AM</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Check out Time</td>
                                                <td><?= esc($data['homestayData']['checkout']) ?> AM (Next day)</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Contact Person</td>
                                                <td><?= esc($data['homestayData']['contact_person']); ?></td>
                                            </tr>
                                           
                                        </tbody>
                    </table>
                </div>
                <div class="shadow p-4 rounded">
                    <div class="form-group mb-2">
                        <label for="reservation_date" class="mb-2"> Reservation date </label>
                        <input onchange="changeMinDate(this.value)" type="date" id="reservation_date" class="form-control" required >
                    </div>
                    <div class="form-group mb-2" id="reservation_date_end_container">
                       
                    </div>
                    <div class="form-group mb-2">
                        <label for="number_people" class="mb-2"> Number of people </label>
                        <input type="number" id="number_people" value="0" class="form-control" required >
                    </div>
                    <div class="form-group mb-2">
                        <label for="comment" class="mb-2"> Additional information </label>
                        <input type="text" id="comment" class="form-control" >
                    </div>
                </div>
            </div>
            `)

            let dateNow = new Date();
            $('#reservation_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                startDate: new Date(dateNow.getFullYear(), dateNow.getMonth(), dateNow.getDate() + 1),
                todayHighlight: false
            });

            $('#modalFooter').html(`<a class="btn btn-success" onclick="makeReservation(${<?= user()->id ?>})"> Make reservation </a>`)
        <?php else : ?>
            $('#modalTitle').html('Login required')
            $('#modalBody').html('Login as user for reservation')
            $('#modalFooter').html(`<a class="btn btn-primary" href="/login"> Login </a> <a class="btn btn-primary" href="/regiter"> Register </a>`)
        <?php endif; ?>

    }

    function changeMinDate(value) {
        let valueDate = new Date(value)
        let maxDate = new Date(valueDate.getFullYear(), valueDate.getMonth(), valueDate.getDate() + 3)
        console.log(maxDate)
        $("#reservation_date_end_container").html(`
        <label for="reservation_date_end" class="mb-2"> Until <span class="text-sm text-primary"> ( Max 4 days ) </span> </label>
        <input type="date" id="reservation_date_end" class="form-control" required >`)

        let dateNow = new Date();
        let yearNow = dateNow.getFullYear();
        let nextYear = yearNow + 2
        let yearRange = `${yearNow}:${nextYear}`
        console.log(yearRange)
        // date picker
        // let datesForDisable = ["2023-11-08", "2023-11-09", "2023-11-10", "2023-11-11"]

        $('#reservation_date_end').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            yearRange: yearRange,
            startDate: valueDate,
            endDate: maxDate,
            defaultViewDate: valueDate,
            // datesDisabled: datesForDisable
        });
    }

    function makeReservation(user_id) {
        let reservationDate = $("#reservation_date").val()
        let reservationDateEnd = $("#reservation_date_end").val()

        console.log(reservationDate)
        let startDate = new Date(reservationDate).getDate()
        let endDate = new Date(reservationDateEnd).getDate()
        let countDate = endDate - startDate + 1

        let numberPeople = $("#number_people").val()
        let comment = $("#comment").val()
        let dateCheckResult = checkIsDateExpired(reservationDate)
        let sameDateCheckResult = "true"
        if (reservationDate) {
            console.log("reserve date")
            sameDateCheckResult = checkIsDateDuplicate(user_id, reservationDate, reservationDateEnd)
        }
        if (!reservationDate) {

            Swal.fire('Please select reservation date', '', 'warning');
        } else if (numberPeople <= 0) {

            Swal.fire('Need 1 people at least', '', 'warning');
        } else if (dateCheckResult == false) {

            Swal.fire('Cannot Reserve, out of date, maksimal H-1 reservation', '', 'warning');
        } else if (sameDateCheckResult == "true") {
            Swal.fire('The date is booked, please select another date', '', 'warning');
        } else {
            <?php if (in_groups('user') && isset($data['homestayData'])) : ?>
                let totalPrice = '<?= $data['homestayData']['ticket_price'] ?>'
                let requestData = {
                    reservation_date: reservationDate,
                    reservation_date_end: reservationDateEnd,
                    id_user: user_id,
                    id_homestay: '<?= $data['homestayData']['id'] ?>',
                    id_reservation_status: 1, // pending status
                    number_people: numberPeople,
                    total_price: parseInt(totalPrice) * countDate,
                    comment: comment
                }
                $.ajax({
                    url: `<?= base_url('web/reservation/create'); ?>`,
                    type: "POST",
                    data: requestData,
                    async: false,
                    contentType: "application/json",
                    success: function(response) {
                        Swal.fire(
                            'Success to make reservation request',
                            '',
                            'success'
                        ).then(() => {
                            window.location.replace(baseUrl + '/web/reservation/' + user_id)
                        });

                    },
                    error: function(err) {
                        console.log(err.responseText)
                    }
                });
            <?php endif; ?>
        }
    }



    function checkIsDateExpired(reservation_date) {
        let result

        let today = new Date();
        let dd = String(today.getDate()).padStart(2, '0');
        let mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        let yyyy = today.getFullYear();

        today = yyyy + '-' + mm + '-' + dd;

        if (reservation_date > today) {
            result = true
        } else {
            result = false
        }
        return result
    }

    function checkIsDateDuplicate(user_id, reservation_date, reservation_date_end) {
        let result
        $.ajax({
            url: `<?= base_url('web/reservation') ?>/checkHomestay/${user_id}/${reservation_date}/${reservation_date_end}`,
            type: "GET",
            async: false,
            success: function(response) {
                result = response
                console.log(result)
            },
            error: function(err) {
                console.log(err.responseText)
            }
        })
        return result
    }
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
<?= $this->endSection() ?>