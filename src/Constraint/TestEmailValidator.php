<?php declare(strict_types=1);

namespace Frosh\MailAddressTester\Constraint;

use Frosh\MailAddressTester\Service\Tester;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

#[AutoconfigureTag('monolog.logger', ['channel' => 'frosh_mail_tester'])]
#[AutoconfigureTag(name: 'validator.constraint_validator')]
class TestEmailValidator extends ConstraintValidator
{
    public function __construct(
        private readonly Tester $emailAddressTester,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof TestEmail)) {
            return;
        }

        if ($value === '' || !\is_string($value)) {
            return;
        }

        if ($this->emailAddressTester->validateEmail($value)) {
            return;
        }

        $this->context->buildViolation($constraint->getMessage())
            ->setParameter('{{ email }}', $this->formatValue($value))
            ->setCode(TestEmail::CODE)
            ->addViolation();
    }
}
