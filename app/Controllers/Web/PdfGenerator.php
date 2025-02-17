<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Database\Migrations\DetailFacilityHomeStay;
use App\Models\DetailFacilityHomestayModel;
use App\Models\DetailPackageModel;
use App\Models\detailServicePackageModel;
use App\Models\HomestayModel;
use App\Models\packageDayModel;
use App\Models\packageModel;
use App\Models\reservationModel;
use Dompdf\Dompdf;

class PdfGenerator extends BaseController
{
    protected $modelPackage, $modalHomestay, $packageDayModel, $detailPackageModel, $detailServicePackageModel, $detailFacilityHomestay;
    protected $reservationModel;
    public function __construct()
    {
        $this->modelPackage = new PackageModel();
        $this->modalHomestay = new HomestayModel();
        $this->packageDayModel = new PackageDayModel();
        $this->detailPackageModel = new DetailPackageModel();
        $this->detailServicePackageModel = new DetailServicePackageModel();
        $this->detailFacilityHomestay = new DetailFacilityHomestayModel();
        $this->reservationModel = new ReservationModel();
    }
    public function invoice($parse)
    {
        $invoiceData = json_decode($parse, true);
        $dompdf = new Dompdf();
        $data = [
            'imageSrc'    => $this->imageToBase64(ROOTPATH . '\public\media\photos\landing-page\pesona_saribu.jpg'),
            'itemData' => $invoiceData
        ];

        $html = view('web/invoice', $data);
        $dompdf->loadHtml($html);
        $dompdf->render();
        $dompdf->stream('invoice.pdf', ['Attachment' => false]);
    }
    public function ticket($parse)
    {
        $invoiceData = json_decode($parse, true);
        $dompdf = new Dompdf();
        $data = [
            'imageSrc'    => $this->imageToBase64(ROOTPATH . '\public\media\photos\landing-page\pesona_saribu.jpg'),
            'itemData' => $invoiceData
        ];

        $html = view('web/ticket', $data);
        $dompdf->loadHtml($html);
        $dompdf->render();
        $dompdf->stream('ticket.pdf', ['Attachment' => false]);
    }
    public function getInvoiceData()
    {
        if ($this->request->isAJAX()) {
            $request = $this->request->getPost('id_reservation');
            $packages = array();
            foreach ($request as $id_reservation) {
                $reservation  = $this->reservationModel->get_r_by_id_api($id_reservation)->getRowArray();

                if ($reservation['id_package'] != null) {
                    $id = $reservation['id_package'];
                    // each package
                    $item = $this->modelPackage->get_tp_by_id_api($id)->getRowArray();
                    // service
                    $list_service = $this->detailServicePackageModel->get_service_by_package_api($id)->getResultArray();
                    $services = array();
                    foreach ($list_service as $service) {
                        $services[] = $service['name'];
                    }

                    // service exclude
                    $list_service_exclude = $this->detailServicePackageModel->get_service_by_package_api_exclude($id)->getResultArray();
                    $servicesExclude = array();
                    foreach ($list_service_exclude as $serviceEx) {
                        $servicesExclude[] = $serviceEx['name'];
                    }

                    $package_day = $this->packageDayModel->get_pd_by_package_id_api($id)->getResultArray();

                    for ($i = 0; $i < count($package_day); $i++) {
                        $package_day[$i]['package_day_detail'] = $this->detailPackageModel->get_detail_package_by_dp_api($package_day[$i]['day'])->getResultArray();
                    }
                    $item['reservation'] = $reservation;
                    $item['services'] = $services;
                    $item['services_exclude'] = $services;
                    $item['package_day'] = $package_day;
                } else if ($reservation['id_homestay'] != null) {
                    $id = $reservation['id_homestay'];
                    // each package
                    $item = $this->modalHomestay->get_hm_by_id_api($id)->getRowArray();
                    // service
                    $list_service = $this->detailFacilityHomestay->get_facility_by_a_api($id)->getResultArray();
                    $services = array();
                    foreach ($list_service as $service) {
                        $services[] = $service['name'];
                    }
                    $item['reservation'] = $reservation;
                    $item['services'] = $services;
                }


                array_push($packages, $item);
            }
            return json_encode($packages);
        }
    }
    private function imageToBase64($path)
    {
        $path = $path;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }
    public function getTicketData()
    {
        if ($this->request->isAJAX()) {
            $request = $this->request->getPost('id_reservation');
            $packages = array();
            foreach ($request as $id_reservation) {
                $reservation  = $this->reservationModel->get_r_by_id_api($id_reservation)->getRowArray();

                if ($reservation['id_package'] != null) {
                    $id = $reservation['id_package'];
                    // each package
                    $item = $this->modelPackage->get_tp_by_id_api($id)->getRowArray();
                    // service
                    $list_service = $this->detailServicePackageModel->get_service_by_package_api($id)->getResultArray();
                    $services = array();
                    foreach ($list_service as $service) {
                        $services[] = $service['name'];
                    }

                    $package_day = $this->packageDayModel->get_pd_by_package_id_api($id)->getResultArray();

                    for ($i = 0; $i < count($package_day); $i++) {
                        $package_day[$i]['package_day_detail'] = $this->detailPackageModel->get_detail_package_by_dp_api($package_day[$i]['day'])->getResultArray();
                    }
                    $item['reservation'] = $reservation;
                    $item['services'] = $services;
                    $item['package_day'] = $package_day;
                } else if ($reservation['id_homestay'] != null) {
                    $id = $reservation['id_homestay'];
                    // each package
                    $item = $this->modalHomestay->get_hm_by_id_api($id)->getRowArray();
                    // service
                    $list_service = $this->detailFacilityHomestay->get_facility_by_a_api($id)->getResultArray();
                    $services = array();
                    foreach ($list_service as $service) {
                        $services[] = $service['name'];
                    }
                    $item['reservation'] = $reservation;
                    $item['services'] = $services;
                }


                array_push($packages, $item);
            }
            return json_encode($packages);
        }
    }
}
