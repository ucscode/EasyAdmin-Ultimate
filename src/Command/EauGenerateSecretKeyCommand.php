<?php

namespace App\Command;

use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Ucscode\KeyGenerator\KeyGenerator;

#[AsCommand(
    name: 'eau:generate-secret-key',
    description: 'Generate a unique secret key for your application',
)]
class UssGenerateSecretKeyCommand extends Command
{
    protected KeyGenerator $keyGenerator;

    public function __construct(protected KernelInterface $kernel)
    {
        parent::__construct();

        $this->keyGenerator = new KeyGenerator();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('envFilename', InputArgument::OPTIONAL, 'The env file to update with the generated key (.env|.env.local...)')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->generateSecretKey($io, $input->getArgument('envFilename') ?? '.env');
        } catch(Exception $e) {
            $io->error(sprintf('%s on %s:%s', $e->getMessage(), $e->getFile(), $e->getLine()));
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    protected function generateSecretKey(SymfonyStyle $io, string $envFilename): void
    {
        if(substr($envFilename, 0, 4) !== '.env') {
            throw new InvalidArgumentException('Provided argument must be a valid environmental file such as .env, .env.local, .env.test');
        }

        $io->title(sprintf('Generating APP_SECRET in %s file', $envFilename));

        $secretKey = $this->keyGenerator
            ->applySpecialCharacters()
            ->generateKey(32)
        ;

        $envPath = sprintf('%s/%s', $this->kernel->getProjectDir(), $envFilename);
        $envContent = file_get_contents($envPath);

        $pattern = sprintf('/^%s=.*/m', preg_quote('APP_SECRET', '/'));
        $replacement = sprintf('%s="%s"', 'APP_SECRET', $secretKey);

        $newEnvContent = preg_replace($pattern, $replacement, $envContent);

        file_put_contents($envPath, $newEnvContent);

        $io->success(sprintf('New APP_SECRET was generated in %s: %s', $envFilename, $secretKey));
    }
}
