<?php

namespace PaymentApi\Domain\Entity\Payment;

use PaymentApi\Domain\Entity\Entity;
use PaymentApi\Structure\Collection;

class PaymentProvider extends Entity
{
    /**
     * @return string
     */
    public function name(): string
    {
        return (string) $this->values->get('name');
    }

    /**
     * @return Collection
     */
    public function payments(): Collection
    {
        return $this->hasMany(new Payment(), 'payment_provider_id');
    }
}
