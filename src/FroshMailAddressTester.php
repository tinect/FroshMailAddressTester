<?php declare(strict_types=1);

namespace Frosh\MailAddressTester;

use Shopware\Core\Framework\Plugin;

class FroshMailAddressTester extends Plugin
{
    public function executeComposerCommands(): bool
    {
        return true;
    }
}
