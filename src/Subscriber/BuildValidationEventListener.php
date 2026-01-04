<?php declare(strict_types=1);

namespace Frosh\MailAddressTester\Subscriber;

use Frosh\MailAddressTester\Constraint\TestEmail;
use Shopware\Core\Framework\Validation\BuildValidationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BuildValidationEventListener implements EventSubscriberInterface
{
    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            'framework.validation.customer.create' => 'buildValidationEvent',
            'framework.validation.customer.email.update' => 'buildValidationEvent',
            'framework.validation.customer.guest.convert' => 'buildValidationEvent',
            'framework.validation.contact_form.create' => 'buildValidationEvent',
        ];
    }

    public function buildValidationEvent(BuildValidationEvent $event): void
    {
        $event->getDefinition()->add('email', new TestEmail());
    }
}
