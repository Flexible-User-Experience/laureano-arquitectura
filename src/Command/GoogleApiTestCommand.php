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
            if ($user->getGoogleCredentialsAccepted() && $user->getGoogleAccessToken()) {
                $this->gam->fetchYesterdayVisits($user);
                $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

            } else {
                $io->error('User not getGoogleCredentialsAccepted or getGoogleAccessToken');

            }
        }

        return Command::SUCCESS;
    }
}
