<?php

namespace PaymentApi\Domain\Entity\Restaurant;

use PaymentApi\Domain\Entity\Entity;

class RestaurantAccount extends Entity
{
    /**
     * @return string
     * @throws \InvalidArgumentException
     */
    public function passwordHash(): string
    {
        return (string) $this->values->get('password_hash');
    }

    /**
     * @return RestaurantChain|Entity
     */
    public function restaurantChain(): RestaurantChain
    {
        return $this->belongsTo(new RestaurantChain(), 'restaurant_chain_id');
    }
}
