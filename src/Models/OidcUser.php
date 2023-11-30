<?php

namespace OidcAuth\Models;

use OidcAuth\HasJwtToken;
use OidcAuth\Repository\Payload;

class OidcUser
{
    use HasJwtToken;

    protected ?string $rt;

    protected ?Payload $payload = null;

    public function __construct($data = [])
    {
        $this->token = $data['jwt'] ?? null;
        $this->rt = $data['rt'] ?? null;
        if (! empty($data['payload'])) {
            $this->payload = new Payload($data['payload']);
        }
    }

    public function setPayload(Payload $payload): static
    {
        $this->payload = $payload;

        return $this;
    }

    public function getId()
    {
        return $this->payload->getBptUserId();
    }

    public function getKey()
    {
        return $this->payload->getKey();
    }

    public function rt()
    {
        return $this->rt;
    }
}
