<?php

namespace App\Message;

class ValidateTransactionMessage
{
    private string $type;
    private string $uuid;

    public function __construct(
        $type,
        $uuid,
    )
    {
        $this->setType($type);
        $this->setUuid($uuid);
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $uuid
     */
    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }
}