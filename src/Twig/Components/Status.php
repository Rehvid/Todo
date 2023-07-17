<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use App\Enum\Status as EnumStatus;

#[AsTwigComponent()]
final class Status
{

    public EnumStatus $status;
    public string $color;
    public function mount(EnumStatus $status): void
    {
        $this->status = $status;
        $this->color = match($status->name) {
            EnumStatus::OPEN->name => 'bg-primary',
            EnumStatus::IN_PROGRESS->name => 'bg-warning',
            EnumStatus::IN_REVIEW->name => 'bg-info',
            EnumStatus::DONE->name => 'bg-success',
            EnumStatus::CANCELLED->name => 'bg-danger',
            default => 'bg-light'
        };
    }
}
