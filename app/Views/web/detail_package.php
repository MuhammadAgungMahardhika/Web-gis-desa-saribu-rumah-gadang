<?= $this->extend('web/layouts/main'); ?>
<?= $this->section('head') ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css" integrity="sha512-34s5cpvaNG3BknEWSuOncX28vz97bRI59UnVtEEpFX536A7BtZSJHsDyFoCl8S7Dt2TPzcrCEoHBGeM4SUBDBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js" integrity="sha512-LsnSViqQyaXpD4mBBdRYeP6sRwJiJveh2ZIbW41EBrNmKxgr/LFZIiWT6yr+nycvhvauz8c2nYMhrP80YhG7Cw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<style>
    input[type=date]::-webkit-inner-spin-button,
    input[type=date]::-webkit-calendar-picker-indicator {
        display: none;
    }
</style>
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
<section class="section">
    <div class="row">
        <script>
            currentUrl = '<?= current_url(); ?>';
        </script>

        <!-- Object Detail Information -->
        <div class="col-md-6 col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title text-center">Tourism Package Information</h4>
                    <a class="btn btn-primary" onclick="showReservationModal()" data-bs-toggle="modal" data-bs-target="#reservationModal"> Reservation <i class="fa fa-ticket"></i> </a>
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
                    <div class="row">
                        <div class="col table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td class="fw-bold">Name</td>
                                        <td><?= esc($data['name']); ?></td>
                                    </tr>
                                    <?php if ($data['id_package_type'] != null) : ?>
                                        <tr>
                                            <td class="fw-bold">Package Type </td>
                                            <td><?= esc($data['type_name']); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td class="fw-bold">Price</td>
                                        <td><?= 'Rp ' . number_format(esc($data['price']), 0, ',', '.'); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Capacity</td>
                                        <td><?= esc($data['capacity']) ?> people</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Contact Person</td>
                                        <td><?= esc($data['cp']); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col">
                            <p class="fw-bold">Description</p>
                            <p><?= esc($data['description']); ?></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <p class="fw-bold">Service (Include)</p>
                            <?php $i = 1; ?>
                            <?php foreach ($data['services'] as $service) : ?>
                                <p class="px-1"><?= esc($i) . '. ' . esc($service); ?></p>
                                <?php $i++; ?>
                            <?php endforeach; ?>
                        </div>
                        <div class="col">
                            <p class="fw-bold">Service (Exclude)</p>
                            <?php $i = 1; ?>
                            <?php foreach ($data['servicesExclude'] as $service) : ?>
                                <p class="px-1"><?= esc($i) . '. ' . esc($service); ?></p>
                                <?php $i++; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col border p-2">
                            <p class="fw-bold">Detail Packages </p>
                            <?php if ($data['package_day'] != null) : ?>
                                <a href="<?= base_url('web/package/costumExisting') . '/' . $data['id'] ?>" class="btn btn-outline-primary mb-2">Costume this package</a>
                                <div class="list-group list-group-horizontal-sm mb-4 text-center" role="tablist">
                                    <?php $dayNumber = 1; ?>
                                    <?php foreach ($data['package_day'] as $day) : ?>
                                        <a onclick="getObjectsByPackageDayId('<?= $day['day'] ?>')" class="list-group-item list-group-item-action <?= $dayNumber == 1 ? "active" : "" ?>" id="list-<?= $dayNumber; ?>-list" data-bs-toggle="list" href="#list-<?= $dayNumber; ?>" role="tab" aria-selected="<?= $dayNumber == 1 ? "true" : "false" ?>"> Day <?= $dayNumber; ?></a>
                                        <?php $dayNumber++; ?>
                                    <?php endforeach; ?>
                                </div>
                                <p class="fw-bold">Activities </p>
                                <div class="tab-content text-justify px-1">
                                    <?php $detailNumber = 1 ?>
                                    <?php foreach ($data['package_day'] as $day) : ?>
                                        <?php $i = 1; ?>
                                        <div class="tab-pane fade <?= $detailNumber == 1 ? "active show" : "" ?> " id="list-<?= $detailNumber ?>" role="tabpanel" aria-labelledby="list-<?= $detailNumber ?>-list">
                                            <?php foreach ($day['package_day_detail'] as $activities) : ?>
                                                <p><?= esc($i) . '. ' . esc($activities['detailDescription']); ?></p>
                                                <?php $i++; ?>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php $detailNumber++; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php else : ?>
                                <p>Activities not found</p>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>

            <!--Rating and Review Section-->
            <?= $this->include('web/layouts/reviewPackage'); ?>
        </div>

        <div class="col-md-6 col-12">
            <!-- Object Location on Map -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Google Maps</h5>
                </div>

                <?= $this->include('web/layouts/map-body'); ?>

            </div>

            <!-- Object Media -->

        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
    let latBefore = ''
    let lngBefore = ''

    <?php if ($data['package_day'] != null) : ?>
        getObjectsByPackageDayId('<?= $data['package_day'][0]['day'] ?>')
    <?php endif; ?>

    function getObjectsByPackageDayId(id_day) {
        $.ajax({
            url: `<?= base_url('api'); ?>/objects/package_day/${id_day}`,
            type: "GET",
            async: false,
            contentType: "application/json",
            success: function(response) {
                let objects = response['data']
                getObjectById(objects)
            },
            error: function(err) {
                console.log(err.responseText)
            }
        });
    }

    function getObjectById(objects = null) {
        let objectNumber = 1
        let flightPlanCoordinates = []
        clearMarker()
        clearRadius()
        clearRoute()
        objects.forEach(object => {
            let id_object = object['id_object']

            let URI = "<?= base_url('api') ?>";

            if (id_object.charAt(0) == 'R') {
                URI = URI + '/rumahGadang/' + `${id_object}`
            } else if (id_object.charAt(0) == 'E') {
                URI = URI + '/event/' + `${id_object}`
            } else if (id_object.charAt(0) == 'C') {
                URI = URI + '/culinaryPlace/' + `${id_object}`
            } else if (id_object.charAt(0) == 'W') {
                URI = URI + '/worshipPlace/' + `${id_object}`
            } else if (id_object.charAt(0) == 'S') {
                URI = URI + '/souvenirPlace/' + `${id_object}`
            } else if (id_object.charAt(0) == 'A') {
                URI = URI + '/atraction/' + `${id_object}`
            } else if (id_object.charAt(0) == 'H') {
                URI = URI + '/homestay/' + `${id_object}`
            }


            currentUrl = '';
            $.ajax({
                url: URI,
                type: "GET",
                async: false,
                dataType: 'json',
                success: function(response) {
                    let data = response.data
                    currentUrl = currentUrl + data.id
                    console.log(data)
                    // flightPlanCoordinates.push(new google.maps.LatLng(data.lat, data.lng))
                    showObjectOnMap(objectNumber, data.id, data.lat, data.lng)
                    boundToObject()

                }
            })
            objectNumber++
        })
        // flightPath(flightPlanCoordinates)
    }


    // Display marker for loaded object
    function showObjectOnMap(objectNumber, id, lat, lng, anim = true) {

        google.maps.event.clearListeners(map, 'click');
        let pos = new google.maps.LatLng(lat, lng);
        let marker = new google.maps.Marker();
        let icon = `https://raw.githubusercontent.com/Concept211/Google-Maps-Markers/master/images/marker_red${objectNumber}.png`;

        markerOption = {
            position: pos,
            icon: icon,
            animation: google.maps.Animation.DROP,
            map: map,
        }
        marker.setOptions(markerOption);
        if (!anim) {
            marker.setAnimation(null);
        }
        marker.addListener('click', () => {
            infoWindow.close();
            objectInfoWindow(id);
            infoWindow.open(map, marker);
        });
        markerArray[id] = marker;
        if (objectNumber == 1) {
            latBefore = lat
            lngBefore = lng
        } else {
            routeAll(lat, lng)
        }
    }

    function routeAll(lat, lng) {
        google.maps.event.clearListeners(map, 'click')

        let start, end;
        start = new google.maps.LatLng(latBefore, lngBefore);
        end = new google.maps.LatLng(lat, lng)
        let request = {
            origin: start,
            destination: end,
            travelMode: 'DRIVING'
        };
        directionsService.route(request, function(result, status) {
            if (status == 'OK') {
                directionsRenderer = new google.maps.DirectionsRenderer({
                    suppressMarkers: true
                })
                directionsRenderer.setDirections(result);
                // showSteps(result);
                directionsRenderer.setMap(map);
                routeArray.push(directionsRenderer);
            }
        });
        boundToRoute(start, end);
    }


    function showReservationModal() {
        <?php if (in_groups('user')) : ?>
            $('#modalTitle').html("Reservation form")
            $('#modalBody').html(`
            <div class=" p-2">
                <div class="mb-2 shadow-sm p-4 rounded">
                    <p class="text-center fw-bold text-dark"> Package Information </p>
                    <table class="table table-borderless text-dark ">
                                        <tbody>
                                        <?php if ($data['url'] != null) : ?>
                                            <tr>
                                                <td colspan="2"><img class="img-fluid img-thumbnail rounded" src="<?= base_url('media/photos/package') . '/' . $data['url'] ?>" width="100%"></td>
                                            </tr>
                                            <?php endif; ?>
                                            <tr>
                                                <td class="fw-bold">Name</td>
                                                <td><?= esc($data['name']); ?></td>
                                            </tr>
                                            <?php if (isset($data['id_homestay'])) : ?>
                                                <?php if ($data['id_homestay'] != null) : ?>
                                                    <tr>
                                                        <td class="fw-bold">Homestay </td>
                                                        <td><?= esc($data['homestay_name']); ?></td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <tr>
                                                <td class="fw-bold">Price</td>
                                                <td><?= 'Rp ' . number_format(esc($data['price']), 0, ',', '.'); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Maks Capacity</td>
                                                <td><?= esc($data['capacity']) ?> people</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Contact Person</td>
                                                <td><?= esc($data['cp']); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Day total</td>
                                                <td><?= esc(count($data['package_day'])); ?></td>
                                            </tr>
                                        </tbody>
                    </table>
                </div>
                <div class="shadow p-4 rounded">
                    <input type="hidden" value="<?= $data['price'] ?>" id="package_price">
                    <div class="form-group mb-2">
                        <label for="reservation_date" class="mb-2"> Select reservation date </label>
                        <span class="text-primary"> ( Min H-7 ) </span></label>
                        <input type="date" id="reservation_date" class="form-control" required >
                    </div>
                    <div class="form-group mb-2">
                        <label for="number_people" class="mb-2"> Number of people </label>
                        <input type="number" id="number_people" placeholder="masimum capacity is <?= esc($data['capacity']) ?>" class="form-control" required >
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
                startDate: new Date(dateNow.getFullYear(), dateNow.getMonth(), dateNow.getDate() + 7),
                todayHighlight: true
            });

            $('#modalFooter').html(`<a class="btn btn-success" onclick="makeReservation(${<?= user()->id ?>})"> Make reservation </a>`)
        <?php else : ?>
            $('#modalTitle').html('Login required')
            $('#modalBody').html('Login as user for reservation')
            $('#modalFooter').html(`<a class="btn btn-primary" href="/login"> Login </a> <a class="btn btn-primary" href="/regiter"> Register </a>`)
        <?php endif; ?>
    }

    function makeReservation(user_id) {
        let reservationDate = $("#reservation_date").val()
        let numberPeople = $("#number_people").val()
        let packagePrice = $("#package_price").val()
        console.log(packagePrice)
        let comment = $("#comment").val()
        let package_id = '<?= $data['id'] ?>';
        let numberCheckResult = checkNumberPeople(numberPeople)
        let dateCheckResult = checkIsDateExpired(reservationDate)
        let sameDateCheckResult = "true"
        if (reservationDate) {
            sameDateCheckResult = checkIsDateDuplicate(user_id, reservationDate)
        }

        if (!reservationDate) {
            Swal.fire('Please select reservation date', '', 'warning');
        } else if (numberPeople <= 0) {
            Swal.fire('Need 1 people at least', '', 'warning');
        } else if (numberCheckResult == false) {
            Swal.fire('Out of capacity, maksimal ' + '<?= $data['capacity'] ?>' + 'people', '', 'warning');
        } else if (dateCheckResult == false) {
            Swal.fire('Cannot Reserve, out of date, maksimal H-1 reservation', '', 'warning');
        } else if (sameDateCheckResult == "true") {
            Swal.fire('Already chose the same date! please select another date', '', 'warning');
        } else {
            <?php if (in_groups('user')) : ?>
                let requestData = {
                    reservation_date: reservationDate,
                    id_user: user_id,
                    id_package: '<?= $data['id'] ?>',
                    id_reservation_status: 1, // pending status
                    number_people: numberPeople,
                    total_price: '<?= $data['price'] ?>',
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

    function checkNumberPeople(numberPeople) {
        let packageCapacity = parseInt('<?= $data['capacity'] ?>')
        let peopleNumberRequest = parseInt(numberPeople)

        if (peopleNumberRequest > packageCapacity) {
            return false
        } else {
            return true
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

    function checkIsDateDuplicate(user_id, reservation_date) {
        let result
        $.ajax({
            url: `<?= base_url('web/reservation') ?>/check/${user_id}/${reservation_date}`,
            type: "GET",
            async: false,
            success: function(response) {
                result = response
            },
            error: function(err) {
                console.log(err.responseText)
            }
        })
        return result
    }

    // Set star by user input
    function setStar(star) {
        switch (star) {
            case '1':
                $("#star-1").addClass('star-checked')
                $("#star-2,#star-3,#star-4,#star-5").removeClass('star-checked')
                break
            case '2':
                $("#star-1,#star-2").addClass('star-checked')
                $("#star-3,#star-4,#star-5").removeClass('star-checked')
                break
            case '3':
                $("#star-1,#star-2,#star-3").addClass('star-checked')
                $("#star-4,#star-5").removeClass('star-checked')
                break
            case '4':
                $("#star-1,#star-2,#star-3,#star-4").addClass('star-checked')
                $("#star-5").removeClass('star-checked')
                break
            case '5':
                $("#star-1,#star-2,#star-3,#star-4,#star-5").addClass('star-checked')
                break
        }
    }

    // function openMultipleCheckOut() {
    //     $("#multipleButton").html(`
    //     <a title="closeAll" class="btn btn-danger" onclick="closeMultipleCheckOut()"><i class="fa fa-x"></i> Cancel Group </a>
    //     <a title="Print All" class="btn btn-primary" onclick="openInvoice()"><i class="fa fa-print"></i> Print selected</a>
    //     `)
    //     $(".checkAll").removeClass("d-none")
    //     $(".checkSingle").addClass("d-none")
    // }

    // function closeMultipleCheckOut() {
    //     $("#multipleButton").html(`
    //     <a title="Print multiple reservation" class="btn btn-primary" onclick="openMultipleCheckOut()"><i class="fa-solid fa-print"></i> Print in Group </a>
    //     `)
    //     $(".checkAll").addClass("d-none")
    //     $(".checkSingle").removeClass("d-none")
    // }
</script>
<?= $this->endSection() ?>