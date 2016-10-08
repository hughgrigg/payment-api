<?php

namespace PaymentApi\Domain\Entity\Restaurant;

use PaymentApi\Domain\Entity\Entity;
use PaymentApi\Structure\Collection;

class RestaurantChain extends Entity
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
    public function restaurantAccounts(): Collection
    {
        return $this->hasMany(new RestaurantAccount(), 'restaurant_chain_id');
    }

    /**
     * @return Collection
     */
    public function restaurantLocations(): Collection
    {
        return $this->hasMany(new RestaurantLocation(), 'restaurant_chain_id');
    }
}
