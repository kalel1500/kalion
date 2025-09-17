<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout;

use Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\Collections\SidebarItemCollection;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\Abstracts\NavigationItem;

final class SidebarItemDto extends NavigationItem
{
    protected int  $counter;
    protected bool $hasCounter;

    public function __construct(
        ?string                $code,
        ?string                $icon,
        ?string                $text,
        ?string                $tooltip,
        ?string                $route_name,
        ?bool                  $is_post,
        public ?string         $counter_action,
        public ?bool           $collapsed,
        public ?bool           $is_separator,
        ?SidebarItemCollection $dropdown
    )
    {
        parent::__construct(
            $code,
            $icon,
            $text,
            $tooltip,
            $route_name,
            $is_post,
            $dropdown,
        );
        $this->hasCounter = !is_null($counter_action);
    }

    public function hasCounter(): bool
    {
        return $this->hasCounter;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }

    public function setCounter(int $counter): void
    {
        $this->counter = $counter;
    }
}
