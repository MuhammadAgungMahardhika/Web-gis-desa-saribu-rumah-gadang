<?php

namespace App\Controllers\Mobile;


use App\Models\RumahGadangModel;
use CodeIgniter\RESTful\ResourcePresenter;
use App\Models\DetailFacilityHomestayModel;
use App\Models\DetailFacilityRumahGadangModel;
use App\Models\FacilityRumahGadangModel;
use App\Models\GalleryHomestayModel;
use App\Models\GalleryRumahGadangModel;
use App\Models\HomestayModel;
use App\Models\ReservationModel;
use App\Models\ReviewModel;

class RumahGadang extends ResourcePresenter
{
    protected $rumahGadangModel;
    protected $homestayModel;
    protected $reservationModel;
    protected $galleryHomestayModel;
    protected $galleryRumahGadangModel;
    protected $detailFacilityRumahGadangModel;
    protected $detailFacilityHomestayModel;
    protected $reviewModel;
    protected $facilityRumahGadangModel;
    protected $helpers = ['auth', 'url', 'filesystem'];
    protected $currentUrl;
    public function __construct()
    {
        $this->rumahGadangModel = new RumahGadangModel();
        $this->rumahGadangModel = new RumahGadangModel();
        $this->homestayModel  = new HomestayModel();
        $this->reservationModel = new ReservationModel();
        $this->galleryHomestayModel = new GalleryHomestayModel();
        $this->galleryRumahGadangModel = new GalleryRumahGadangModel();
        $this->detailFacilityRumahGadangModel = new DetailFacilityRumahGadangModel();
        $this->detailFacilityHomestayModel = new DetailFacilityHomestayModel();
        $this->reviewModel = new ReviewModel();
        $this->facilityRumahGadangModel = new FacilityRumahGadangModel();
        $this->currentUrl = 'mobile';
    }


    public function maps()
    {
        $contents = $this->rumahGadangModel->get_list_rg_api()->getResultArray();
        $data = [
            'title' => 'Rumah Gadang',
            'data' => $contents,
            'currentUrl' => $this->currentUrl
        ];

        return view('mobile/maps/rumah_gadang', $data);
    }

    public function detail($id = null)
    {
        $rumahGadang = $this->rumahGadangModel->get_rg_by_id_api($id)->getRowArray();

        if (empty($rumahGadang)) {
            return redirect()->to(substr(current_url(), 0, -strlen($id)));
        }

        // homestay 

        $homestayId = $rumahGadang['id_homestay'];
        $galleryHomestay = $this->galleryHomestayModel->get_gallery_api($homestayId)->getResultArray();
        if ($homestayId != null) {
            $homestayData =  $this->homestayModel->get_hm_by_id_api($homestayId)->getRowArray();
            $homestayRating = $this->reservationModel->getAvgHRating($homestayId)->getRowArray();
            $homestayFacility = $this->detailFacilityHomestayModel->get_facility_by_a_api($homestayId)->getResultArray();
            $rumahGadang['homestayData'] = $homestayData;
            $rumahGadang['homestayData']['avg_homestay_rating'] = $homestayRating;
            $rumahGadang['homestayData']['homestay_facility'] = $homestayFacility;
            $rumahGadang['homestayGalleries'] = $galleryHomestay;
        }

        $avg_rating = $this->reviewModel->get_rating('id_rumah_gadang', $id)->getRowArray()['avg_rating'];

        $list_facility = $this->detailFacilityRumahGadangModel->get_facility_by_rg_api($id)->getResultArray();
        $facilities = array();
        foreach ($list_facility as $facility) {
            $facilities[] = $facility['facility'];
        }

        $list_review = $this->reviewModel->get_review_object_api('id_rumah_gadang', $id)->getResultArray();

        $list_gallery = $this->galleryRumahGadangModel->get_gallery_api($id)->getResultArray();
        $galleries = array();
        foreach ($list_gallery as $gallery) {
            $galleries[] = $gallery['url'];
        }


        $rumahGadang['avg_rating'] = $avg_rating;
        $rumahGadang['facilities'] = $facilities;
        $rumahGadang['reviews'] = $list_review;
        $rumahGadang['gallery'] = $galleries;

        $data = [
            'title' => $rumahGadang['name'],
            'data' => $rumahGadang,
            'currentUrl' => $this->currentUrl
        ];

        if (url_is('*dashboard*')) {
            return view('dashboard/detail_rumah_gadang', $data);
        }
        return view('mobile/detail_rumah_gadang', $data);
    }
}
