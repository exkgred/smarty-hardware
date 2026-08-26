<?php

namespace App\Modules\Common\Domain;

class DomainException extends \DomainException
{
    public function __construct(
        string $message,
        public readonly int $status = 422,
    ) {
        parent::__construct($message);
    }
}
