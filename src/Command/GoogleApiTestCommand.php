<?php

namespace App\Command;

use App\Manager\GoogleAnalyticsManager;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:google-api-test',
    description: 'Execute a Google Books Api call test',
)]
class GoogleApiTestCommand extends Command
{
    private GoogleAnalyticsManager $gam;
    private UserRepository $ur;

    public function __construct(GoogleAnalyticsManager $gam, UserRepository $ur)
    {
        parent::__construct();
        $this->gam = $gam;
        $this->ur = $ur;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('uid', InputArgument::REQUIRED, 'User ID')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $uid = (int) $input->getArgument('uid');
        $user = $this->ur->find($uid);
        if (!$user) {
            $io->error('User ID# '.$uid.' not found!');
        } else {
            if ($user->getGoogleAccessToken()) {
                $this->gam->getGoogleApiClient()->setAccessToken($user->getGoogleAccessToken());
            }
            // if there is no previous user access token or it's expired
            if ($this->gam->getGoogleApiClient()->isAccessTokenExpired()) {
                // refresh the token if possible, else fetch a new one
                if ($this->gam->getGoogleApiClient()->getRefreshToken()) {
                    $this->gam->getGoogleApiClient()->fetchAccessTokenWithRefreshToken($this->gam->getGoogleApiClient()->getRefreshToken());
                } else {
                    // request authorization from the user
                    $authUrl = $this->gam->getGoogleApiClient()->createAuthUrl();
                    $io->text('Open the following link in your browser >>> '.$authUrl);
                    $helper = $this->getHelper('question');
                    $question = new Question('Enter verification code: ');
                    $authCode = $helper->ask($input, $output, $question);
                    // exchange authorization code for an access token
                    $accessToken = $this->gam->getGoogleApiClient()->fetchAccessTokenWithAuthCode($authCode);
                    $this->gam->getGoogleApiClient()->setAccessToken($accessToken);
                    // check to see if there was an error
                    if (array_key_exists('error', $accessToken)) {
                        throw new \RuntimeException(implode(', ', $accessToken));
                    }
                }
            }
            $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
        }

        return Command::SUCCESS;
    }
}
