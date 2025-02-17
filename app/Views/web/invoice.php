<!DOCTYPE html>
<html>


<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Invoice</title>
    <style type="text/css">
        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: normal;
            src: local('Open Sans'), local('OpenSans'), url(http://themes.googleusercontent.com/static/fonts/opensans/v7/yYRnAC2KygoXnEC8IdU0gQLUuEpTyoUstqEm5AMlJo4.ttf) format('truetype');
        }

        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: bold;
            src: local('Open Sans Bold'), local('OpenSans-Bold'), url(http://themes.googleusercontent.com/static/fonts/opensans/v7/k3k702ZOKiLJc3WVjuplzMDdSZkkecOE1hvV7ZHvhyU.ttf) format('truetype');
        }

        @font-face {
            font-family: 'Open Sans';
            font-style: italic;
            font-weight: normal;
            src: local('Open Sans Italic'), local('OpenSans-Italic'), url(http://themes.googleusercontent.com/static/fonts/opensans/v7/O4NhV7_qs9r9seTo7fnsVCZ2oysoEQEeKwjgmXLRnTc.ttf) format('truetype');
        }

        @font-face {
            font-family: 'Open Sans';
            font-style: italic;
            font-weight: bold;
            src: local('Open Sans Bold Italic'), local('OpenSans-BoldItalic'), url(http://themes.googleusercontent.com/static/fonts/opensans/v7/PRmiXeptR36kaC0GEAetxrQhS7CD3GIaelOwHPAPh9w.ttf) format('truetype');
        }

        @page {
            margin-top: 1cm;
            margin-bottom: 3cm;
            margin-left: 2cm;
            margin-right: 2cm;
        }

        body {
            background: #fff;
            color: #000;
            margin: 0cm;
            font-family: 'Open Sans', sans-serif;
            font-size: 9pt;
            line-height: 100%;
        }

        h1,
        h2,
        h3,
        h4 {
            font-weight: bold;
            margin: 0;
        }

        h1 {
            font-size: 16pt;
            margin: 5mm 0;
        }

        h2 {
            font-size: 14pt;
        }

        h3,
        h4 {
            font-size: 9pt;
        }

        ol,
        ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        li,
        ul {
            margin-bottom: 0.75em;
        }

        p {
            margin: 0;
            padding: 0;
        }

        p+p {
            margin-top: 1.25em;
        }

        a {
            border-bottom: 1px solid;
            text-decoration: none;
        }

        /* Basic Table Styling */
        table {
            border-collapse: collapse;
            border-spacing: 0;
            page-break-inside: always;
            border: 0;
            margin: 0;
            padding: 0;
        }

        th,
        td {
            vertical-align: top;
            text-align: left;
        }

        table.container {
            width: 100%;
            border: 0;
        }

        tr.no-borders,
        td.no-borders {
            border: 0 !important;
            border-top: 0 !important;
            border-bottom: 0 !important;
            padding: 0 !important;
            width: auto;
        }

        /* Header */
        table.head {
            margin-bottom: 2mm;
        }

        td.header img {
            max-height: 3cm;
            width: auto;
        }

        td.header {
            font-size: 16pt;
            font-weight: 700;
        }

        td.shop-info {
            width: 40%;
        }

        .document-type-label {
            text-transform: uppercase;
        }

        table.order-data-addresses {
            width: 100%;
            margin-bottom: 10mm;
        }

        td.order-data {
            width: 40%;
        }

        .invoice .shipping-address {
            width: 30%;
        }

        .packing-slip .billing-address {
            width: 30%;
        }

        td.order-data table th {
            font-weight: normal;
            padding-right: 2mm;
        }

        table.order-details {
            width: 100%;
            margin-bottom: 8mm;
        }

        .quantity,
        .price {
            width: 20%;
        }

        .order-details tr {
            page-break-inside: always;
            page-break-after: auto;
        }

        .order-details td,
        .order-details th {
            border-bottom: 1px #ccc solid;
            border-top: 1px #ccc solid;
            padding: 0.375em;
        }

        .order-details th {
            font-weight: bold;
            text-align: left;
        }

        .order-details thead th {
            color: black;

            border-color: black;
        }

        .order-details tr.bundled-item td.product {
            padding-left: 5mm;
        }

        .order-details tr.product-bundle td,
        .order-details tr.bundled-item td {
            border: 0;
        }

        dl {
            margin: 4px 0;
        }

        dt,
        dd,
        dd p {
            display: inline;
            font-size: 7pt;
            line-height: 7pt;
        }

        dd {
            margin-left: 5px;
        }

        dd:after {
            content: "\A";
            white-space: pre;
        }

        .customer-notes {
            margin-top: 5mm;
        }

        table.totals {
            width: 100%;
            margin-top: 5mm;
        }

        table.totals th,
        table.totals td {
            border: 0;
            border-top: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
        }

        table.totals th.description,
        table.totals td.price {
            width: 50%;
        }

        table.totals tr:last-child td,
        table.totals tr:last-child th {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            font-weight: bold;
        }

        table.totals tr.payment_method {
            display: none;
        }

        #footer {
            position: absolute;
            bottom: -2cm;
            left: 0;
            right: 0;
            height: 2cm;
            text-align: center;
            border-top: 0.1mm solid gray;
            margin-bottom: 0;
            padding-top: 2mm;
        }

        .pagenum:before {
            content: counter(page);
        }

        .pagenum,
        .pagecount {
            font-family: sans-serif;
        }
    </style>
</head>

<body class="invoice">
    <table class="head container">
        <tr>
            <td class="header">
                <img src="" alt="">
            </td>
            <td class="shop-info">
                <div class="shop-name">
                    <h3>INVOICE </h3>
                    <h3>DESA WISATA SARIBU RUMAH GADANG</h3>
                    <p>Kec.saribu_rumah_gadang, Kab. Tanah Datar, Prov. Sumatera Barat</p>
                </div>
                <div class="shop-name">
                    <p>
                        0822-1814-1289 <br>
                        Instagram/@pokdarwis.saribu_rumah_gadang <br>
                        Tiktok/pokdarwis.saribu_rumah_gadang
                    </p>
                </div>
            </td>
        </tr>
    </table>
    <h1 class="document-type-label"> DESA WISATA SARIBU RUMAH GADANG</h1>
    <table class="order-data-addresses">
        <tr>
            <td class="address billing-address">
                <h3>Billing To:</h3>
                Ranggi
            </td>

            <td class="order-data">
                <table>
                    <tr>
                        <th> Reservation status </th>
                        <td style="color: green;"> : Confirmed</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table class="order-details">
        <thead>

            <tr>
                <th> # </th>
                <th colspan="2"> Package name / id</th>
                <?php if (isset($itemData[0]['package_day'])) : ?>
                    <th colspan="4"> Activities</th>
                <?php endif; ?>
                <th> Services </th>
                <?php if (isset($item['services_exclude'])) : ?>
                    <th> Services (exclude)</th>
                <?php endif; ?>
                <?php if (isset($itemData[0]['capacity'])) : ?>
                    <th> Capacity</th>
                <?php endif; ?>
                <th colspan="3"> Price</th>
                <?php if (isset($itemData[0]['reservation']['request_date_end'])) : ?>
                    <th> From</th>
                <?php else : ?>
                    <th> Reservation Date</th>
                <?php endif; ?>

                <?php if (isset($itemData[0]['reservation']['request_date_end'])) : ?>
                    <th> To</th>
                <?php endif; ?>
            </tr>

        </thead>
        <tbody>
            <?php $noPackage = 1 ?>
            <?php foreach ($itemData as $item) : ?>
                <tr>
                    <td><?= $noPackage; ?></td>
                    <td colspan="2"><?= $item['name']  ?> / <?= $item['reservation']['id']; ?></td>
                    <?php if (isset($item['package_day'])) : ?>
                        <td colspan="4">
                            <?php $noDay = 1; ?>
                            <?php foreach ($item['package_day'] as $packageDay) : ?>
                                Day <?= $noDay  ?> : <br><br>
                                <?php $noDetail = 1; ?>
                                <?php foreach ($packageDay['package_day_detail'] as $packageDetail) : ?>
                                    <?= $noDetail; ?>. <?= $packageDetail['detailDescription']; ?><br>
                                    <?php $noDetail++ ?>
                                <?php endforeach; ?>
                                <br><br>
                                <?php $noDay++ ?>
                            <?php endforeach; ?>
                        </td>
                    <?php endif; ?>
                    <td>
                        <?php $noService = 1; ?>
                        <?php foreach ($item['services'] as $service) : ?>
                            <?= $noService ?>. <?= $service; ?> <br>
                            <?php $noService++ ?>
                        <?php endforeach; ?>
                    </td>
                    <?php if (isset($item['services_exclude'])) : ?>
                        <td>
                            <?php $noServiceExclude = 1; ?>
                            <?php foreach ($item['services_exclude'] as $serviceExclude) : ?>
                                <?= $noServiceExclude ?>. <?= $serviceExclude; ?> <br>
                                <?php $noServiceExclude++ ?>
                            <?php endforeach; ?>
                        </td>
                    <?php endif; ?>
                    <?php if (isset($item['capacity'])) : ?>
                        <td><?= $item['capacity']; ?></td>
                    <?php endif; ?>
                    <?php if (isset($item['reservation']['total_price'])) : ?>
                        <td colspan="3"><?= "Rp " . number_format($item['reservation']['total_price'], 0, ",", ".") ?></td>
                    <?php endif; ?>
                    <td>
                        <h3> <?= $item['reservation']['request_date']; ?> </h3>
                    </td>
                    <?php if (isset($item['reservation']['request_date_end'])) : ?>
                        <td>
                            <h3> <?= $item['reservation']['request_date_end']; ?> </h3>
                        </td>
                    <?php endif; ?>
                </tr>
                <?= $noPackage++; ?>
            <?php endforeach; ?>
        </tbody>

    </table>
    <div>
        <h1 style="color: green;">Amount Due : <?= "Rp " . number_format($item['reservation']['total_price']) ?></h1>
    </div>
    <p>
        Payment Instruction <br>
        BNI 12345678 a.n Desa Wisata saribu_rumah_gadang
    </p>
    <p>NOTE: The costumers agrees to the services and conditions described in this document</p>
</body>

</html>