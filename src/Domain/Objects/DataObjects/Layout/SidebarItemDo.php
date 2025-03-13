<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout;

use Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\Collections\SidebarItemCollection;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\Contracts\NavigationItem;

final class SidebarItemDo extends NavigationItem
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

    protected static function createFromArray(array $data): static
    {
        return new static(
            $data['code'] ?? null,
            $data['icon'] ?? null,
            $data['text'] ?? null,
            $data['tooltip'] ?? null,
            $data['route_name'] ?? null,
            $data['is_post'] ?? null,
            $data['counter_action'] ?? null,
            $data['collapsed'] ?? null,
            $data['is_separator'] ?? null,
            SidebarItemCollection::fromArray($data['dropdown'] ?? null)
        );
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
