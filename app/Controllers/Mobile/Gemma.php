<?php

namespace App\Controllers\Mobile;

use App\Models\PackageModel;
use CodeIgniter\RESTful\ResourcePresenter;
use App\Models\AtractionModel;
use App\Models\CulinaryPlaceModel;
use App\Models\SouvenirPlaceModel;
use App\Models\WorshipPlaceModel;
use App\Models\PackageDayModel;
use App\Models\DetailPackageModel;
use App\Models\ServiceModel;
use App\Models\HomestayModel;
use App\Models\DetailServicePackageModel;
use App\Models\PackageTypeModel;
use App\Models\ReservationModel;
use App\Models\ReviewPackageModel;

class Gemma extends ResourcePresenter
{

    protected $currentUrl;
    protected $helpers = ['auth', 'url', 'filesystem'];

    public function __construct()
    {
        $this->currentUrl = 'mobile';
    }

    public function index()
    {
        $data = [
            'title' => 'Gemini AI',
            'currentUrl' => $this->currentUrl
        ];

        return view('mobile/package', $data);
    }
}
