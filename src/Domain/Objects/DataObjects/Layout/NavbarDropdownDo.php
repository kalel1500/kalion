<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout;

use Thehouseofel\Kalion\Domain\Objects\DataObjects\ContractDataObject;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\Collections\NavbarItemCollection;

final class NavbarDropdownDo extends ContractDataObject
{
    public ?UserInfoDo $userInfo;

    public function __construct(
        public readonly ?bool                $is_list,
        public readonly ?bool                $is_square,
        public readonly ?string              $get_data_action,
        public readonly ?string              $header,
        public readonly ?NavbarItemDo        $footer,
        public NavbarItemCollection $items
    )
    {
    }

    protected static function createFromArray(array $data): static
    {
        return new static(
            $data['is_list'] ?? null,
            $data['is_square'] ?? null,
            $data['get_data_action'] ?? null,
            $data['header'] ?? null,
            NavbarItemDo::fromArray($data['footer'] ?? null),
            NavbarItemCollection::fromArray($data['items'] ?? [])
        );
    }

    public function setItems(NavbarItemCollection $items): void
    {
        $this->items = $items;
    }

    public function setUserInfo(?UserInfoDo $userInfo): void
    {
        $this->userInfo = $userInfo;
    }
}
