<?= $this->extend('web/layouts/main'); ?>
<?= $this->section('content') ?>
<!-- Modal  -->
<div class="modal fade text-left" id="reservationModal" tabindex="-1" aria-labelledby="myModalLabel1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"></h5>
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
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Cart</h3>
        </div>
        <div class="card-body">
            <div class="reservation-list">
                <?php if (!empty($data) && is_array($data)) : ?>
                    <?php foreach ($data as $item) : ?>
                        <div class="reservation-item mb-3 border rounded p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input item-checkbox" value="<?= $item['id'] ?>" <?= $item['id_reservation_status'] != '1' ? 'disabled' : ''; ?>>
                                    <label class="form-check-label fw-bold"><?= $item['package_name'] ?? 'N/A' ?></label>
                                </div>
                                <span class="badge <?= getStatusBadgeClass($item['id_reservation_status']) ?>"><?= $item['status'] ?></span>
                            </div>

                            <div class="row reservation-details">
                                <div class="col-6 mb-2">
                                    <small class="text-muted">Request Date:</small>
                                    <div><?= date('d M Y', strtotime($item['request_date'])) ?></div>
                                </div>
                                <div class="col-6 mb-2">
                                    <small class="text-muted">Price:</small>
                                    <div><?= number_format($item['total_price'] ?? 0, 0, ',', '.') ?></div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-2">
                                <a class="btn btn-info me-1" title="confirm" data-bs-toggle="modal" data-bs-target="#reservationModal" onclick="showInfoReservation('<?= $item['id'] ?>')">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php if ($item['id_reservation_status'] != 3 && $item['id_reservation_status'] != 5) : ?>
                                    <button class="btn btn-sm btn-danger delete-item" data-id="<?= $item['id'] ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="mt-3 border-top pt-3">
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="fw-bold">Total Selected:</span>
                                <span id="grandTotal" class="fw-bold">0</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="fw-bold">Total Prices:</span>
                                <span id="priceTotal" class="fw-bold">0</span>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button id="checkoutSelected" class="btn btn-primary ">Checkout</button>
                        </div>
                    </div>

                <?php else : ?>
                    <div class="text-center py-4">
                        <p>No reservation items found</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</section>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-ZBFmbRHLtloBk5Sc"></script>

<script>
    $(document).ready(function() {
        // Check/uncheck all checkboxes
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const priceTotal = document.getElementById('priceTotal');
        const checkoutBtn = document.getElementById('checkoutSelected');

        function updateTotal() {
            let total = 0;
            let selectedItems = [];

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const item = checkbox.closest('.reservation-item');
                    const priceText = item.querySelector('.reservation-details div:nth-child(2) div').innerText;
                    const price = parseInt(priceText.replace(/\./g, ''));

                    if (!isNaN(price)) {
                        total += price;
                        selectedItems.push(checkbox.value);
                    }
                }
            });

            priceTotal.textContent = number_format(total, 0, ',', '.');
            checkoutBtn.disabled = selectedItems.length === 0;
        }

        function number_format(number, decimals, dec_point, thousands_sep) {
            return number.toFixed(decimals).replace(/\d(?=(\d{3})+\.)/g, '$&' + thousands_sep).replace(/\.$/, '');
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateTotal);
        });

        updateTotal();

        // Delete item
        $(document).on('click', '.delete-item', function() {
            const itemId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "This reservation will be cancelled!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteReservation(itemId);
                }
            });
        });

        // Track selected items and calculate total price
        let selectedItemIds = [];
        let totalPrice = 0;

        // Add event handler for checkbox changes
        $(document).on('change', '.item-checkbox', function() {
            const itemId = $(this).val();
            const priceText = $(this).closest('.reservation-item').find('.reservation-details .col-6:nth-child(2) div').text();
            const price = parseInt(priceText.replace(/\./g, '')) || 0;

            if ($(this).is(':checked')) {
                selectedItemIds.push(itemId);
                totalPrice += price;
            } else {
                selectedItemIds = selectedItemIds.filter(id => id !== itemId);
                totalPrice -= price;
            }

            // Update the total selected count
            $('#grandTotal').text(selectedItemIds.length);

            // Enable/disable checkout button based on selection
            $('#checkoutSelected').prop('disabled', selectedItemIds.length === 0);
        });


        $(document).on('click', '#checkoutSelected', function() {
            const itemId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "This reservation will  continue to payment",
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, checkout it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    proceedToCheckout(selectedItemIds);
                }
            });
        });

        function proceedToCheckout(itemIds) {
            console.log(itemIds);
            $.ajax({
                url: '<?= base_url('mobile/reservation/checkout') ?>',
                type: 'POST',
                data: {
                    reservation_ids: itemIds
                },
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        // Buka halaman pembayaran Midtrans
                        snap.pay(response.snap_token, {
                            onSuccess: function(result) {
                                console.log('success', result);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Pembayaran Berhasil',
                                    text: 'Terima kasih atas pembayaran Anda!'
                                }).then(() => {
                                    window.location.href = '<?= base_url('mobile/reservation/paymentSuccess') ?>?order_id=' + response.order_id;
                                });
                            },
                            onPending: function(result) {
                                console.log('pending', result);
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Pembayaran Tertunda',
                                    text: 'Silakan selesaikan pembayaran Anda!'
                                });
                            },
                            onError: function(result) {
                                console.log('error', result);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Pembayaran Gagal',
                                    text: 'Terjadi kesalahan saat memproses pembayaran!'
                                });
                            },
                            onClose: function() {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Pembayaran Dibatalkan',
                                    text: 'Anda menutup halaman pembayaran!'
                                });
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Gagal melakukan checkout. Silakan coba lagi.'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal memproses checkout. Silakan coba lagi.'
                    });
                }
            });
        }

        function deleteReservation(id_reservation) {
            $.ajax({
                url: `<?= base_url('api/reservation') ?>/${id_reservation}`,
                type: "DELETE",
                async: false,
                contentType: "application/json",
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Reservation cancelled successfully!'
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(err) {
                    console.error(error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to cancel reservation. Please try again.'
                    });
                }
            });
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(amount);
        }

    });

    function showInfoReservation(id) {
        // Fetch reservation data
        let result = fetchReservationData(id);
        if (!result) return;

        const reservationStatus = result['id_reservation_status'];
        const isPackage = result['id_package'] != null;
        const isHomestay = result['id_homestay'] != null;

        // Set modal title
        $('#modalTitle').html("Reservation Information");

        // Render the modal body with basic reservation info
        renderReservationInfo(result, isPackage, isHomestay);

        // Conditionally render additional components based on status
        if (reservationStatus == '2') {
            renderPaymentUpload(result, id, isPackage);
        }

        if (reservationStatus == '4' && result['payment_accepted_date'] != null) {
            renderRefundButton(id);
            renderTicketPrinting(id);
        }

        if (reservationStatus == '3' && result['proof_of_refund'] != null) {
            renderRefundProof(result);
        }

        if (result['rating'] != null) {
            renderUserRating(result);
        }

        // Clear modal footer
        $('#modalFooter').html('');
    }

    // Helper function to fetch reservation data
    function fetchReservationData(id) {
        let result;
        $.ajax({
            url: `<?= base_url('api'); ?>/reservation/${id}`,
            type: "GET",
            async: false,
            contentType: "application/json",
            success: function(response) {
                result = JSON.parse(response);
            },
            error: function(err) {
                console.log(err.responseText);
                alert('Failed to load reservation data');
            }
        });
        return result;
    }

    // Render basic reservation information
    function renderReservationInfo(result, isPackage, isHomestay) {
        let contentHtml = `
    <div class="p-2">
        <div id="userRating"></div>
        <div id="adminRefund"></div>
        <div id="userTicket"></div>
        <div id="userDeposit"></div>
        <div class="mb-2 shadow-sm p-4 rounded">
            <p class="text-center fw-bold text-dark">Reservation Information</p>
            <table class="table table-borderless text-dark">
                <tbody>
                    <tr>
                        <td id="buttonRefund"></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Status</td>
                        <td>${result['status']}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Name</td>
                        <td>${result['item_name']}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Date</td>
                        <td>${result['request_date']}</td>
                    </tr>`;

        // Add fields specific to package or homestay
        if (isPackage) {
            contentHtml += `
                    <tr>
                        <td class="fw-bold">Total people</td>
                        <td>${result['number_people']}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Custom</td>
                        <td class="${result['item_costum'] == '1' ? 'badge bg-success' : ''}">${result['item_costum'] == '1' ? 'yes' : 'no'}</td>
                    </tr>`;
        } else if (isHomestay) {
            // Hitung jumlah hari hanya jika end_date tidak kosong
            let countDay = 1; // Default 1 hari jika tidak ada end_date
            let endDateDisplay = result['request_date_end'] != null ? result['request_date_end'] : 'Not specified';

            if (result['request_date_end'] != null) {
                const startDate = new Date(result['request_date']).getDate();
                const endDate = new Date(result['request_date_end']).getDate();
                countDay = endDate - startDate + 1;

                // Handle kasus jika hasil perhitungan negatif atau tidak valid
                if (isNaN(countDay) || countDay < 1) {
                    countDay = 1;
                }
            }

            contentHtml += `
                    <tr>
                        <td class="fw-bold">Until</td>
                        <td>${result['request_date_end'] != null ? result['request_date_end'] : ''}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Total Day</td>
                        <td>${countDay} Days</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Total people</td>
                        <td>${result['number_people']} People</td>
                    </tr>`;
        }

        // Add common closing rows
        contentHtml += `
                    <tr>
                        <td class="fw-bold">Additional Information</td>
                        <td>${result['comment'] != null ? result['comment'] : '-'}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>`;

        $('#modalBody').html(contentHtml);
    }

    // Render payment upload section
    function renderPaymentUpload(result, id, isPackage) {
        const deposit = result['deposit'];
        const proofDeposit = result['proof_of_deposit'];

        // Calculate deadline date
        let dat = new Date(result.request_date);
        let deadline;

        if (isPackage) {
            // H-3 for package
            let dd = String(dat.getDate() - 3).padStart(2, '0');
            let mm = String(dat.getMonth() + 1).padStart(2, '0');
            let yyyy = dat.getFullYear();
            deadline = `${yyyy}-${mm}-${dd}`;
        } else {
            // Same day for homestay
            let dd = String(dat.getDate()).padStart(2, '0');
            let mm = String(dat.getMonth() + 1).padStart(2, '0');
            let yyyy = dat.getFullYear();
            deadline = `${yyyy}-${mm}-${dd}`;
        }

        $("#userDeposit").addClass("mb-2 shadow-sm p-4 rounded");
        $("#userDeposit").html(`
        <p class="text-center fw-bold text-dark">Upload Your Payment</p>
        <p>Note <br>
           <span class="text-danger">*</span> You must pay before <span class="text-primary">${deadline}</span> ${isPackage ? '(H-3)' : ''} or booking will be canceled by the system<br>
           <span class="text-danger">*</span> Before uploading proof of deposit, make sure the payment amount is the same as the invoice, please print the invoice to see the deposit amount
        </p>
        <div class="text-start mb-4">
            <a class="btn btn-primary" onclick="openInvoice('${id}')"><i class="fa fa-print"></i> Print Invoice</a>
        </div>
        <div class="form-group mb-4">
            <label for="deposit" class="mb-2">Deposit <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="number" id="deposit" class="form-control" name="deposit" placeholder="deposit" value="${deposit}" required>
            </div>
        </div>
        <div class="form-group mb-4">
            <label for="gallery" class="form-label">Upload Proof of Deposit <span class="text-danger">*</span></label>
            <input class="form-control" accept="image/*" type="file" name="gallery[]" id="gallery">
        </div>
        <div class="text-end">
            <a class="btn btn-success" onclick="saveDeposit('${id}')">Save</a>
        </div>
    `);

        // Initialize FilePond
        setupFilePond(proofDeposit);
    }

    // Set up FilePond plugin
    function setupFilePond(proofDeposit) {
        FilePond.registerPlugin(
            FilePondPluginFileValidateType,
            FilePondPluginImageExifOrientation,
            FilePondPluginImagePreview,
            FilePondPluginImageResize,
            FilePondPluginMediaPreview
        );

        const photo = document.querySelector('input[id="gallery"]');
        const pond = FilePond.create(photo, {
            maxFileSize: '1920MB',
            maxTotalFileSize: '1920MB',
            imageResizeTargetHeight: 720,
            imageResizeUpscale: false,
            credits: false
        });

        if (proofDeposit != null) {
            pond.addFiles(`${baseUrl}/media/photos/reservation/${proofDeposit}`);
        }

        pond.setOptions({
            server: {
                timeout: 3600000,
                process: {
                    url: '<?= base_url("upload/photo") ?>',
                    onload: (response) => {
                        galleryValue = response;
                        console.log("processed:", response);
                        return response;
                    },
                    onerror: (response) => {
                        console.log("error:", response);
                        return response;
                    }
                },
                revert: {
                    url: '<?= base_url("upload/photo") ?>',
                    onload: (response) => {
                        console.log("reverted:", response);
                        return response;
                    },
                    onerror: (response) => {
                        console.log("error:", response);
                        return response;
                    }
                }
            }
        });
    }

    // Render refund button
    function renderRefundButton(id) {
        $("#buttonRefund").html(`<a class="btn btn-outline-danger" onclick="openModalCancelAndRefund('${id}')">Cancel and Refund</a>`);
    }

    // Render refund proof
    function renderRefundProof(result) {
        const deposit = result['deposit'];
        const refundTotal = deposit / 2;
        const proofRefund = result['proof_of_refund'];

        $("#adminRefund").addClass("mb-2 shadow-sm p-4 rounded");
        $("#adminRefund").html(`
        <p class="text-center fw-bold text-dark">Proof of Refund</p>
        <p>Note <br><span class="text-danger">*</span> Refund only 50% of your payment (50%*${rupiah(deposit)})</p>
        <p>Refund total: <span class="text-primary">${rupiah(refundTotal)}</span></p>
        <div class="mb-2">
            <img class="img-fluid img-thumbnail rounded" src="${'<?= base_url() ?>' + '/media/photos/refund/' + proofRefund}" width="100%">
        </div>
    `);
    }

    // Render ticket printing section
    function renderTicketPrinting(id) {
        $("#userTicket").addClass("background-effect mb-2 shadow-sm p-4 rounded border border-warning");
        $("#userTicket").css('height', '250px');
        $("#userTicket").html(`
        <a class="btn" onclick="printTicket('${id}')">
            <h1 class="gold text-center fw-bold text-dark">Print Your Ticket Here</h1>
        </a>
    `);
    }

    // Render user rating
    function renderUserRating(result) {
        const rating = result['rating'];
        const updatedRating = result['updated_at'];
        const review = result['review'] != null ? result['review'] : '';

        $("#userRating").addClass("mb-2 shadow-sm p-4 rounded");
        $("#userRating").html(`
        <p class="text-center fw-bold text-dark">Rated And Reviewed</p>
        <p>Rated on: ${updatedRating}</p>
        <div class="star-containter mb-3 text-start">
            <i class="fa-solid fa-star fs-10" id="star-1"></i>
            <i class="fa-solid fa-star fs-10" id="star-2"></i>
            <i class="fa-solid fa-star fs-10" id="star-3"></i>
            <i class="fa-solid fa-star fs-10" id="star-4"></i>
            <i class="fa-solid fa-star fs-10" id="star-5"></i>
        </div>
        <p>${review}</p>
    `);

        setStar(rating);
    }
</script>
<?= $this->endSection() ?>

<?php
// Helper function to get status badge class
function getStatusBadgeClass($statusId)
{
    switch ($statusId) {
        case 1:
            return 'bg-warning'; // Pending
        case 2:
            return 'bg-info'; // Confirmed
        case 3:
            return 'bg-danger'; // Cancelled
        case 4:
            return 'bg-primary'; // Paid
        case 5:
            return 'bg-success'; // Completed
        default:
            return 'bg-secondary';
    }
}
?>