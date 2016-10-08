<?php

namespace PaymentApi\Domain\Action;

use PaymentApi\Domain\Entity\Entity;
use PaymentApi\Domain\Entity\Payment\Bill;
use PaymentApi\Domain\Entity\Payment\BillItem;
use PaymentApi\Domain\Entity\Payment\Payment;
use PaymentApi\Domain\Entity\Payment\PaymentProvider;
use PaymentApi\Domain\Entity\Restaurant\RestaurantLocation;
use PaymentApi\Domain\Entity\Restaurant\RestaurantTable;
use PaymentApi\Domain\Entity\User;
use PaymentApi\Structure\Collection;

class NewPaymentAction
{
    /** @var Collection */
    private $input;

    /** @var Collection */
    private $errors;

    /** @var RestaurantTable */
    private $restaurantTable;

    /**
     * NewPaymentAction constructor.
     *
     * @param Collection $input
     */
    public function __construct(Collection $input)
    {
        $this->input = $input;
    }

    /**
     * @return Payment|Entity
     */
    public function commit(): Payment
    {
        $this->validate();
        if (!$this->isValid()) {
            return new Payment();
        }

        $bill = Bill::create(
            [
                'restaurant_table_id' => $this->restaurantTable->id(),
                'served_by'           => $this->input->get('served_by')->get(
                    'name'
                ),
            ]
        );

        $this->input->get('items')->each(
            function (Collection $itemData) use ($bill) {
                BillItem::create(
                    $itemData->keep(['amount', 'description'])
                        ->put('bill_id', $bill->id())
                        ->all()
                );
            }
        );

        $method = $this->input->get('method');
        $device = $this->input->get('device');
        $payment = Payment::create(
            [
                'amount'                 => $this->input->get('total_amount'),
                'payment_provider_id'    => $this->input->get('provider'),
                'restaurant_location_id' => $this->input->get('location'),
                'bill_id'                => $bill->id(),
                'user_id'                => $this->input->get('user'),
                'organisation'           => $method->get('organisation'),
                'last_4_digits'          => $method->get('last_4_digits'),
                'fraud_risk'             => $method->get('fraud_risk'),
                'device_os'              => $device->get('operating_system'),
                'device_model'           => $device->get('model'),
            ]
        );

        return $payment;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->errors()->isEmpty();
    }

    /**
     * @return Collection
     */
    public function errors(): Collection
    {
        if ($this->errors === null) {
            $this->errors = new Collection();
        }

        return $this->errors;
    }

    /**
     * Check valid conditions.
     *
     */
    private function validate()
    {
        $this->validateTotal();
        $this->validateProvider();
        $this->validateTable();
        $this->validateUser();
    }

    /**
     * Confirm given total matches itemised total.
     */
    private function validateTotal()
    {
        $givenTotal = (int) $this->input->get('total_amount');
        $itemsTotal = $this->input->get('items')->reduce(
            function (int $total, array $item) {
                return $total + (int) $item['amount'];
            },
            0
        );

        if ($givenTotal !== $itemsTotal) {
            $this->errors()->push(
                sprintf(
                    'Given total `%s` does not match itemised total `%s`.',
                    $givenTotal,
                    $itemsTotal
                )
            );
        }
    }

    /**
     * Check given payment provider.
     */
    private function validateProvider()
    {
        if (!$this->input->has('provider')) {
            $this->errors()->push(
                'A payment provider id must be provided.'
            );

            return;
        }

        $provider = PaymentProvider::load($this->input->get('provider'));
        if (!$provider->exists()) {
            $this->errors()->push(
                sprintf(
                    'No payment provider found with id `%s`.',
                    $this->input->get('provider')
                )
            );
        }
    }

    /**
     * Check given restaurant table.
     *
     */
    private function validateTable()
    {
        if (!$this->input->has('location')) {
            $this->errors()->push(
                'A restaurant location id must be provided.'
            );

            return;
        }

        /** @var RestaurantLocation $location */
        $location = RestaurantLocation::load($this->input->get('location'));
        if (!$location->exists()) {
            $this->errors()->push(
                sprintf(
                    'No restaurant location found with id `%s`.',
                    $this->input->get('provider')
                )
            );
        }

        /** @var RestaurantTable $table */
        $this->restaurantTable = $location->tables()->first(
            function (RestaurantTable $table) {
                return $table->number() === (int) $this->input->get('table');
            }
        );
        if (!$this->restaurantTable || !$this->restaurantTable->exists()) {
            $this->errors->push(
                sprintf(
                    'Restaurant location `%s` has no table number `%s`.',
                    $location->id(),
                    $this->input->get('table')
                )
            );
        }
    }

    /**
     * Check given user.
     */
    private function validateUser()
    {
        if (!$this->input->has('user')) {
            $this->errors()->push(
                'A user id must be provided.'
            );

            return;
        }

        /** @var User $user */
        $user = User::load($this->input->get('user'));
        if (!$user->exists()) {
            $this->errors()->push(
                sprintf(
                    'No user found with id `%s`.',
                    $this->input->get('user')
                )
            );
        }
    }
}
