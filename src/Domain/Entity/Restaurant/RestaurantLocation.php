<?php

namespace PaymentApi\Domain\Entity\Restaurant;

use PaymentApi\Domain\Entity\Entity;
use PaymentApi\Structure\Collection;

class RestaurantLocation extends Entity
{
    /**
     * @return string
     */
    public function name(): string
    {
        return (string) $this->values->get('name');
    }

    /**
     * @return RestaurantChain|Entity
     */
    public function restaurantChain(): RestaurantChain
    {
        return $this->belongsTo(new RestaurantChain(), 'restaurant_chain_id');
    }

    /**
     * @return Collection
     */
    public function tables(): Collection
    {
        return $this->hasMany(new RestaurantTable(), 'restaurant_location_id');
    }
}
