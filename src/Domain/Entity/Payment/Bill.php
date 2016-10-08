<?php

namespace PaymentApi\Domain\Entity\Payment;

use PaymentApi\Domain\Entity\Entity;
use PaymentApi\Domain\Entity\Restaurant\RestaurantTable;

class Bill extends Entity
{
    /**
     * @return string
     */
    public function servedBy(): string
    {
        return (string) $this->values->get('served_by');
    }

    /**
     * @return RestaurantTable|Entity
     */
    public function restaurantTable(): RestaurantTable
    {
        return $this->belongsTo(new RestaurantTable(), 'restaurant_table_id');
    }
}
