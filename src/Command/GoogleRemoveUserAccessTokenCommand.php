<?php

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:google:remove-user-access-token',
    description: 'Remove previously Google Access Token from user account',
)]
class GoogleRemoveUserAccessTokenCommand extends Command
{
    private UserRepository $ur;

    public function __construct(UserRepository $ur)
    {
        parent::__construct();
        $this->ur = $ur;
    }

    protected function configure(): void
    {
        $this->addArgument('user', InputArgument::REQUIRED, 'User ID to remove Google Access Token');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $uid = (int) $input->getArgument('user');

        $user = $this->ur->find($uid);
        if (!$user) {
            $io->error('User ID# '.$uid.' not found!');
        } else {
            $user
                ->setGoogleAccessToken(null)
                ->setGoogleCredentialsAccepted(false)
            ;
            $this->ur->update(true);
            $io->success('Removed Google Access Token from user '.$user->getUsername().' and disabled Google Analytics Sync');
        }

        return Command::SUCCESS;
    }
}
