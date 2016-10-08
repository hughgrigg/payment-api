<?php

namespace PaymentApi\Domain\Entity\Restaurant;

use PaymentApi\Domain\Entity\Entity;

class RestaurantTable extends Entity
{
    /**
     * @return int
     */
    public function number(): int
    {
        return (int) $this->values->get('table_number');
    }

    /**
     * @return RestaurantLocation|Entity
     */
    public function restaurantLocation(): RestaurantLocation
    {
        return $this->belongsTo(
            new RestaurantLocation(),
            'restaurant_location_id'
        );
    }
}
