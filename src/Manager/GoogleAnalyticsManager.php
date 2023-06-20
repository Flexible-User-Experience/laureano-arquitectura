<?php

namespace App\Manager;

use App\Entity\User;
use App\Repository\UserRepository;
use Google\Client as GoogleApiClient;
use Google\Exception;
use Google\Service\Analytics as GoogleAnalyticsDataService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class GoogleAnalyticsManager
{
    public const GOOGLE_APIS_CREDENTIALS_FILENAME = 'google_apis_credentials.json';

    private UserRepository $ur;
    private GoogleApiClient $gac;
    private ?GoogleAnalyticsDataService $gads = null;

    /**
     * @throws Exception
     */
    public function __construct(AssetsManager $am, ParameterBagInterface $pb, RouterInterface $rs, UserRepository $ur)
    {
        $this->ur = $ur;
        $this->gac = new GoogleApiClient();
        $this->gac->setApplicationName($pb->get('project_web_title').' Google Analytics API Integration');
        $this->gac->addScope(GoogleAnalyticsDataService::ANALYTICS_READONLY);
//        $this->gac->addScope(GoogleAnalyticsDataService::ANALYTICS);
        $credentialsFilePath = $am->getProjectRootDir().self::GOOGLE_APIS_CREDENTIALS_FILENAME;
        if ($am->fileExists($credentialsFilePath)) {
            $this->gac->setAuthConfig($credentialsFilePath);
            $this->gac->setRedirectUri($rs->generate('admin_google_user_oauth_token', [], UrlGeneratorInterface::ABSOLUTE_URL));
            $this->gac->setAccessType('offline');
            $this->gac->setPrompt('select_account consent');
        } else {
            throw new Exception('Google Calendar API Credentials file '.$credentialsFilePath.' not found');
        }
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

    public function setGoogleAnalyticsServiceByGoogleApiClient(GoogleApiClient $gac): self
    {
        $this->gads = new GoogleAnalyticsDataService($gac);

        return $this;
    }

    public function getTotalVisitsAmount(): int
    {
//        $this->reFetchUserGoogleApiClientAccessToken();
//        $data = $this->getGoogleAnalyticsDataService()->data_ga->get(
//            'ga:12',
//            '2023-06-15',
//            '2023-06-19',
//            'ga:sessions,ga:pageviews'
//        );
//        $data = $this->getGoogleAnalyticsDataService()->
        return 0;
    }

    public function reFetchUserGoogleApiClientAccessToken(User $user): void
    {
        if ($user->getGoogleAccessToken()) {
            $this->getGoogleApiClient()->setAccessToken($user->getGoogleAccessToken());
        }
        // if there is no previous user access token or it's expired
        if ($this->getGoogleApiClient()->isAccessTokenExpired()) {
            // refresh the token if possible, else fetch a new one
            $refreshedToken = $this->getGoogleApiClient()->getRefreshToken();
            if ($refreshedToken) {
                $newAuthToken = $this->getGoogleApiClient()->fetchAccessTokenWithRefreshToken($refreshedToken);
                $user->setGoogleAccessToken($newAuthToken);
                $this->ur->update(true);
            }
        }
    }
}
