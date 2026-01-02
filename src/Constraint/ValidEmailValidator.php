<?php declare(strict_types=1);

namespace Frosh\MailValidation\Constraint;

use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shyim\CheckIfEmailExists\EmailChecker;
use Shyim\CheckIfEmailExists\SMTP;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

#[AutoconfigureTag('monolog.logger', ['channel' => 'frosh-mail-validation'])]
#[AutoconfigureTag(name: 'validator.constraint_validator')]
class ValidEmailValidator extends ConstraintValidator
{
    public function __construct(
        private readonly SystemConfigService $systemConfigService,
        private readonly LoggerInterface $froshMailValidationLogger,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof ValidEmail)) {
            return;
        }

        if ($value === '' || !\is_string($value)) {
            return;
        }

        // TODO: add cache layer here to avoid multiple checks for the same email in short time!
        // TODO: add option to accept the email address on second submit!

        $verifyEmail = $this->systemConfigService->getString('FroshMailValidation.config.verifyEmail');

        $checker = new EmailChecker(smtp: new SMTP($verifyEmail));
        $result = $checker->check($value);

        if ($result->isReachable === true && $result->isDisabled === false && $result->hasFullInbox === false) {
            return;
        }

        $this->froshMailValidationLogger->error('Email validation failed', $result->toArray());

        $this->context->buildViolation($constraint->getMessage())
            ->setParameter('{{ email }}', $this->formatValue($value))
            ->setCode(ValidEmail::CODE)
            ->addViolation();
    }
}
