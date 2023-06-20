<?php

namespace App\Manager;

use App\Entity\PagePathVisit;
use App\Entity\User;
use App\Repository\PagePathVisitRepository;
use App\Repository\UserRepository;
use Google\Client as GoogleApiClient;
use Google\Exception;
use Google\Service\AnalyticsData as GoogleAnalyticsDataService;
use Google\Service\AnalyticsData\DateRange;
use Google\Service\AnalyticsData\Dimension;
use Google\Service\AnalyticsData\Metric;
use Google\Service\AnalyticsData\RunReportRequest;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class GoogleAnalyticsManager
{
    public const GOOGLE_APIS_CREDENTIALS_FILENAME = 'google_apis_credentials.json';

    private ParameterBagInterface $pb;
    private Security $ss;
    private UserRepository $ur;
    private PagePathVisitRepository $ppvr;
    private GoogleApiClient $gac;
    private ?GoogleAnalyticsDataService $gads = null;

    /**
     * @throws Exception
     */
    public function __construct(AssetsManager $am, RouterInterface $rs, ParameterBagInterface $pb, Security $ss, UserRepository $ur, PagePathVisitRepository $ppvr)
    {
        $this->pb = $pb;
        $this->ss = $ss;
        $this->ur = $ur;
        $this->ppvr = $ppvr;
        $this->gac = new GoogleApiClient();
        $this->gac->setApplicationName($pb->get('project_web_title').' Google Analytics API Integration');
        $this->gac->addScope(GoogleAnalyticsDataService::ANALYTICS_READONLY);
        $credentialsFilePath = $am->getProjectRootDir().self::GOOGLE_APIS_CREDENTIALS_FILENAME;
        if ($am->fileExists($credentialsFilePath)) {
            $this->gac->setAuthConfig($credentialsFilePath);
            $this->gac->setRedirectUri($rs->generate('admin_google_user_oauth_token', [], UrlGeneratorInterface::ABSOLUTE_URL));
            $this->gac->setAccessType('offline');
            $this->gac->setPrompt('select_account consent');
        } else {
            throw new Exception('Google Analytics API Credentials file '.$credentialsFilePath.' not found');
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

    public function get365DaysAgoTotalVisitsAmount(): int
    {
        return $this->getVisitsAmountByDateAgo('365daysAgo');
    }

    public function get30DaysAgoTotalVisitsAmount(): int
    {
        return $this->getVisitsAmountByDateAgo('30daysAgo');
    }

    private function getVisitsAmountByDateAgo(string $dateAgo): int
    {
        /** @var User $user */
        $user = $this->ss->getUser();
        if ($user->getGoogleCredentialsAccepted()) {
            $this->reFetchUserGoogleApiClientAccessToken($user);
            // Create the DateRange object
            $range = new DateRange();
            $range->setStartDate($dateAgo);
            $range->setEndDate('today');
            // Create the Metrics object
            $metric = new Metric();
            $metric->setName('screenPageViews');
            // Create the Dimension object
            $dimension = new Dimension();
            $dimension->setName('fullPageUrl');
            // Create the ReportRequest object
            $req = new RunReportRequest();
            $req->setDateRanges($range);
            $req->setMetrics([$metric]);
            $req->setDimensions([$dimension]);
            $req->setKeepEmptyRows(true);
            $data = $this->getGoogleAnalyticsDataService()->properties->runReport(
                'properties/'.$this->pb->get('google_analytics_property_id'),
                $req,
            );
            if ($data->getRowCount() > 0) {
                $totalVisits = 0;
                foreach ($data->getRows() as $row) {
                    foreach ($row->getMetricValues() as $value) {
                        $totalVisits += (int) $value->getValue();
                    }
                }

                return $totalVisits;
            }

            return -1;
        }

        return 0;
    }

    public function fetchYesterdayVisits(User $user): bool
    {
        $hasBeenFetched = false;
        if ($user->getGoogleCredentialsAccepted()) {
            $this->reFetchUserGoogleApiClientAccessToken($user);
            // Create the DateRange object
            $range = new DateRange();
            $range->setStartDate('yesterday');
            $range->setEndDate('yesterday');
            // Create the Metrics object
            $metric = new Metric();
            $metric->setName('screenPageViews');
            // Create the Dimension object
            $dimension = new Dimension();
            $dimension->setName('pagePath');
            // Create the ReportRequest object
            $req = new RunReportRequest();
            $req->setDateRanges($range);
            $req->setMetrics([$metric]);
            $req->setDimensions([$dimension]);
            $req->setKeepEmptyRows(true);
            $data = $this->getGoogleAnalyticsDataService()->properties->runReport(
                'properties/'.$this->pb->get('google_analytics_property_id'),
                $req,
            );
            if ($data->getRowCount() > 0) {
                $hasBeenFetched = true;
                foreach ($data->getRows() as $row) {
                    if (count($row->getDimensionValues()) > 0 && count($row->getMetricValues()) > 0) {
                        $pagePathDimension = $row->getDimensionValues()[0]->getValue();
                        $screenPageViewsValue = (int) $row->getMetricValues()[0]->getValue();
                        $pagePathVisit = new PagePathVisit();
                        $pagePathVisit
                            ->setName($pagePathDimension)
                            ->setScreenPageViews($screenPageViewsValue)
                        ;
                        $this->ppvr->add($pagePathVisit, true);
                    }
                }
            }
        }

        return $hasBeenFetched;
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
