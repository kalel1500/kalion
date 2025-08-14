<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Parameters\ThemeVo;

final class CookiePreferencesDto extends AbstractDataTransferObject
{
    public function __construct(
        protected string  $version,
        protected ThemeVo $theme,
        protected bool    $sidebar_collapsed,
        protected bool    $sidebar_state_per_page
    )
    {
    }

    protected static function createFromArray(array $data): static
    {
        return new static(
            $data['version'],
            ThemeVo::new($data['theme'] ?? ThemeVo::system),
            $data['sidebar_collapsed'],
            $data['sidebar_state_per_page']
        );
    }

    public function version(): string
    {
        return $this->version;
    }

    public function theme(): ThemeVo
    {
        return $this->theme;
    }

    public function sidebar_collapsed(): bool
    {
        return $this->sidebar_collapsed;
    }

    public function sidebar_state_per_page(): bool
    {
        return $this->sidebar_state_per_page;
    }

    public function set_version(string $value): void
    {
        $this->version = $value;
    }

    public function set_theme(string $value): void
    {
        $this->theme = $value;
    }

    public function set_sidebar_collapsed(bool $value): void
    {
        $this->sidebar_collapsed = $value;
    }

    public function set_sidebar_state_per_page(bool $value): void
    {
        $this->sidebar_state_per_page = $value;
    }
}
