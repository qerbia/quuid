<?php

declare(strict_types=1);

namespace Qerbia\Quuid;

class InvalidQuuidException extends \Exception
{
    private $invalidQuuid;

    public function __construct(string $invalidQuuid)
    {
        $this->invalidQuuid = $invalidQuuid;
        parent::__construct('The uuid ' . $this->invalidQuuid . ' is invalid.');
    }

    public function getInvalidQuuid(): string
    {
        return $this->invalidQuuid;
    }
}
