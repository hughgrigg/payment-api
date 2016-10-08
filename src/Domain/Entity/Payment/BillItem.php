<?php

namespace PaymentApi\Domain\Entity\Payment;

use PaymentApi\Domain\Entity\Entity;
use PaymentApi\Domain\Money;

class BillItem extends Entity
{
    /**
     * @return Money
     */
    public function amount(): Money
    {
        return new Money((int) $this->values->get('amount'));
    }

    /**
     * @return string
     */
    public function description(): string
    {
        return (string) $this->values->get('description');
    }

    /**
     * @return Bill|Entity
     */
    public function bill(): Bill
    {
        return $this->belongsTo(new Bill(), 'bill_id');
    }
}
