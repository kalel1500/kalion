
# Launch optimize in console
```php

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

private function optimize(OutputInterface $outputBuffer): void
{
    // En web PHP_BINARY es php-fpm/apache, hay que localizar el binario CLI
    $php = (new PhpExecutableFinder())->find(false);
    if (! $php || ! str_contains(basename($php), 'php') || str_contains(basename($php), 'fpm')) {
        $php = is_executable(PHP_BINDIR . '/php') ? PHP_BINDIR . '/php' : 'php';
    }

    $process = new Process([$php, 'artisan', 'optimize'], base_path());
    $process->setTimeout(120)->run();

    $outputBuffer->writeln("php: $php | exit code: " . $process->getExitCode());
    $outputBuffer->writeln($process->getOutput());
    $outputBuffer->writeln($process->getErrorOutput());
}
```
