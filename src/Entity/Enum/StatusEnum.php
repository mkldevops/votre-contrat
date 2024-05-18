<?php

namespace App\Entity\Enum;

enum StatusEnum: string
{
    case Draft = 'draft';
    case Ready = 'ready';
    case MailSent = 'mail_sent';
    case Signed = 'signed';
    case Cancelled = 'cancelled';
    case Archived = 'archived';

    public function getColor(): string
    {
        return match ($this) {
            self::Draft => 'secondary',
            self::Ready => 'primary',
            self::MailSent => 'info',
            self::Signed => 'success',
            self::Cancelled => 'danger',
            self::Archived => 'dark',
        };
    }
}
