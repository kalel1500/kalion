<?php

declare(strict_types=1);

use Illuminate\Support\ServiceProvider;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Title;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\StepBase;

#[Step(
    paths      : '',
    title      : '%s providers al archivo "bootstrap/providers.php"',
    skip       : false,
    getPathFrom: null
)]
class AddProvidersToBootstrapFile extends StepBase
{
    protected string $provider = 'App\Providers\DependencyServiceProvider';

    #[Title('Añadiendo')]
    public function up(): void
    {
        if ($this->data->withExamples) {
            ServiceProvider::addProviderToBootstrapFile($this->provider);
        } else {
            $this->callDown();
        }
    }

    #[Title('Eliminando')]
    public function down(): void
    {
        ServiceProvider::removeProviderFromBootstrapFile($this->provider);
    }
}
