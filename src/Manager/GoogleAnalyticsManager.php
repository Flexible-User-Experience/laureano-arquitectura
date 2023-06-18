<?php

namespace App\Manager;

use Google\Client as GoogleApiClient;
use Google\Service\AnalyticsData as GoogleAnalyticsDataService;

class GoogleAnalyticsManager
{
    public const GOOGLE_APIS_CREDENTIALS_FILENAME = 'google_apis_credentials.json';

    private GoogleApiClient $gac;
    private ?GoogleAnalyticsDataService $gads = null;

    public function __construct()
    {
        $this->gac = new GoogleApiClient();
    }

    public function getGoogleApiClient(): GoogleApiClient
    {
        return $this->gac;
    }

    public function getGoogleAnalyticsDataService(): GoogleAnalyticsDataService
    {
        if (is_null($this->gads)) {
            $this->gads = new GoogleAnalyticsDataService($this->gac);
        }

        return $this->gads;
    }

    public function getTotalVisitsAmount(): int
    {
        return 0;
    }
}
