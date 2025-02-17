<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\ReservationModel;
use App\Models\ReviewModel;
use CodeIgniter\I18n\Time;

class Review extends BaseController
{
    protected $reviewModel;
    protected $reservationModel;
    public function __construct()
    {
        $this->reviewModel = new ReviewModel();
        $this->reservationModel = new ReservationModel();
    }

    public function add()
    {

        $request = $this->request->getPost();

        $requestData = [
            'id_comment' => $this->reviewModel->get_new_id_api(),
            'comment' => $request['comment'],
            'date' => Time::now(),
            'rating' => $request['rating'],
            'id_user' => user()->id,
        ];

        if (substr($request['object_id'], 0, 1) == 'R') {
            $requestData['id_rumah_gadang'] = $request['object_id'];
            $addReview = $this->reviewModel->add_review_api($requestData);
            if ($addReview) {
                return redirect()->to(base_url('web/rumahGadang') . '/' . $requestData['id_rumah_gadang'] . '#reviews');
            }
        }

        if (substr($request['object_id'], 0, 1) == 'E') {
            $requestData['id_event'] = $request['object_id'];
            $addReview = $this->reviewModel->add_review_api($requestData);
            if ($addReview) {
                return redirect()->to(base_url('web/event') . '/' . $requestData['id_event'] . '#reviews');
            }
        }

        if (substr($request['object_id'], 0, 1) == 'E') {
            $requestData['id_unique_place'] = $request['object_id'];
            $addReview = $this->reviewModel->add_review_api($requestData);
            if ($addReview) {
                return redirect()->to(base_url('web/uniquePlace') . '/' . $requestData['id_unique_place'] . '#reviews');
            }
        }
        if (substr($request['object_id'], 0, 1) == 'P') {
            $requestData['id_package'] = $request['object_id'];
            $addReview = $this->reviewModel->add_review_api($requestData);
            if ($addReview) {
                return redirect()->to(base_url('web/package') . '/' . $requestData['id_package'] . '#reviews');
            }
        }

        return redirect()->to(base_url('web'));
    }

    public function ratingCommentPackage()
    {
        $data = $this->request->getPOST();
        $reservation_id = $data['id_reservation'];
        $user_id = $data['id_user'];
        $review = $data['review'];
        $rating = $data['rating'];
        // dd($data);
        $requestData = [
            'rating' =>  $rating,
            'review' => $review
        ];

        $this->reservationModel->update_r_api($reservation_id, $requestData);

        return redirect()->to(base_url('web/reservation') . '/' . $user_id);
    }
}
