<?php

namespace PaymentApi\Domain\Entity\Payment;

use PaymentApi\Domain\Entity\Entity;
use PaymentApi\Domain\Entity\User;
use PaymentApi\Domain\Money;

class Payment extends Entity
{
    /**
     * @return Money
     */
    public function amount(): Money
    {
        $amount = $this->values->get('amount');
        if ($amount instanceof Money) {
            return $amount;
        }

        return new Money((int) $amount);
    }

    /**
     * @return \PaymentApi\Structure\Collection
     */
    public function jsonSerialize()
    {
        return parent::jsonSerialize()->put('amount', $this->amount());
    }

    /**
     * @return Bill|Entity
     */
    public function bill(): Bill
    {
        return $this->belongsTo(new Bill(), 'bill_id');
    }

    /**
     * @return User|Entity
     */
    public function user(): User
    {
        return $this->belongsTo(new User(), 'user_id');
    }
}
