<?php declare(strict_types=1);

namespace Frosh\MailValidation;

use Shopware\Core\Framework\Plugin;

class FroshMailValidation extends Plugin
{
    public function executeComposerCommands(): bool
    {
        return true;
    }
}
