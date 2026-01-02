<?php declare(strict_types=1);

namespace Frosh\MailValidation\Constraint;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

class ValidEmail extends Constraint
{
    final public const CODE = 'f5926730-bcf9-4462-ba58-b9fc5c7665c3';

    protected const ERROR_NAMES = [
        self::CODE => 'EMAIL_NOT_VALID',
    ];

    public string $message = 'Please double-check your given email address "{{ email }}" for any input errors.';

    #[HasNamedArguments]
    public function __construct(string $message = 'Please double-check your given email address "{{ email }}" for any input errors.')
    {
        parent::__construct();
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
