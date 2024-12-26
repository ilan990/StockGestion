<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\Organization;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Creates an organization with admin user'
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Organisation interactive
        $io->section('Creating Organization');
        $orgName = $io->ask('Organization name', 'Default Organization');
        $orgAddress = $io->ask('Organization address', '1 rue example');
        $orgPhone = $io->ask('Organization phone', '0123456789');
        $orgEmail = $io->ask('Organization email', 'contact@organization.com');

        $organization = new Organization();
        $organization->setName($orgName);
        $organization->setAdress($orgAddress);
        $organization->setPhone($orgPhone);
        $organization->setEmail($orgEmail);

        $this->entityManager->persist($organization);

        // Admin User interactive
        $io->section('Creating Admin User');
        $adminEmail = $io->ask('Admin email', 'admin@test.com');
        $adminPassword = $io->askHidden('Admin password', function ($input) {
            if (empty($input)) {
                throw new \Exception('Password cannot be empty');
            }
            return $input;
        });
        $adminFirstName = $io->ask('Admin first name', 'Admin');
        $adminLastName = $io->ask('Admin last name', 'User');

        $user = new User();
        $user->setEmail($adminEmail);
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $user->setOrganization($organization);
        $user->setFirstName($adminFirstName);
        $user->setLastName($adminLastName);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $adminPassword);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);

        try {
            $this->entityManager->flush();
            $io->success('Admin user and organization created successfully!');
            $io->table(
                ['Organization', 'Admin User'],
                [
                    [
                        sprintf('%s (%s)', $organization->getName(), $organization->getEmail()),
                        sprintf('%s %s (%s)', $user->getFirstName(), $user->getLastName(), $user->getEmail())
                    ]
                ]
            );
        } catch (\Exception $e) {
            $io->error('Error creating admin: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}