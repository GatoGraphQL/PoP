<?php

declare(strict_types=1);

namespace PoP\PoP\Extensions\Symplify\MonorepoBuilder\Command;

use PoP\PoP\Extensions\Symplify\MonorepoBuilder\Json\PackageOwnersProvider;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;

final class LocalPackageOwnersCommand extends AbstractSymplifyCommand
{
    public function __construct(
        private PackageOwnersProvider $packageOwnersProvider,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Space-separated list of local package owners in the monorepo');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $localPackageOwners = $this->packageOwnersProvider->providePackageOwners();

        $this->symfonyStyle->writeln(implode(' ', $localPackageOwners));

        return ShellCode::SUCCESS;
    }
}
