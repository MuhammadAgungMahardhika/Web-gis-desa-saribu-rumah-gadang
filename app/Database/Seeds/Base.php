<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Base extends Seeder
{
    public function run()
    {
        // Account Seed
        // $this->call('Users');
        // $this->call('Group');
        // $this->call('UsersGroup');
        // $this->call('Regional');

        // Rumah Gadang Seed
        $this->call('RecommendationPlace');
        $this->call('RumahGadang');
        $this->call('FacilityRumahGadang');
        $this->call('DetailFacilityRumahGadang');
        $this->call('RumahGadangGallery');

        //Package Seed
        $this->call('Package');
        $this->call('PackageDay');
        // Culinary Place Seed
        $this->call('CulinaryPlace');
        $this->call('CulinaryPlaceGallery');

        // Worship Place Seed
        $this->call('WorshipPlace');
        $this->call('WorshipPlaceGallery');

        // Souvenir Seed
        $this->call('Souvenir');
        $this->call('SouvenirGallery');

        // Event Seed
        $this->call('EventCategory');
        $this->call('Event');
        $this->call('EventGallery');

        // Unique Place Seed
        $this->call('UniquePlace');
        $this->call('UniquePlaceGallery');

        // Other Seed
        $this->call('Comment');
    }
}
