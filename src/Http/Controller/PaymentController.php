<?php

namespace PaymentApi\Http\Controller;

use Carbon\Carbon;
use PaymentApi\Domain\Action\NewPaymentAction;
use PaymentApi\Domain\Entity\Payment\Payment;
use PaymentApi\Domain\Entity\Restaurant\RestaurantLocation;
use PaymentApi\Http\Request;
use PaymentApi\Http\Response;
use PaymentApi\Persist\Condition;
use PaymentApi\Structure\Collection;

class PaymentController
{
    /** @var Payment */
    private $payment;

    /** @var RestaurantLocation */
    private $location;

    /**
     * @param Payment            $payment
     * @param RestaurantLocation $location
     */
    public function __construct(Payment $payment, RestaurantLocation $location)
    {
        $this->payment = $payment;
        $this->location = $location;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function postPayment(Request $request): Response
    {
        $input = $request->json()
            ->put('chain', $request->matches()->get(1))
            ->put('location', $request->matches()->get(2));

        $action = new NewPaymentAction($input);

        $payment = $action->commit();

        if (!$payment->exists()) {
            return new Response(
                400,
                new Collection(['errors' => $action->errors()->all()])
            );
        }

        return new Response(
            201,
            new Collection(['payment' => $payment])
        );
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function reportChainPayments(Request $request): Response
    {
        $locations = $this->location->loadWhere(
            'restaurant_chain_id',
            $request->matches()->get(1)
        );
        $payments = $this->payment->loadWhereConditions(
            new Collection(
                [
                    new Condition(
                        'restaurant_location_id',
                        'IN',
                        $locations->map(
                            function (RestaurantLocation $location) {
                                return $location->id();
                            }
                        )->all()
                    ),
                    new Condition(
                        'created_at',
                        '>=',
                        [Carbon::now()->subDay()->toDateTimeString()]
                    ),
                ]
            )
        );

        return new Response(200, $payments);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function reportLocationPayments(Request $request): Response
    {
        $payments = $this->payment->loadWhereConditions(
            new Collection(
                [
                    new Condition(
                        'restaurant_location_id',
                        '=',
                        [$request->matches()->get(2)]
                    ),
                    new Condition(
                        'created_at',
                        '>=',
                        [Carbon::now()->subDay()->toDateTimeString()]
                    ),
                ]
            )
        );

        return new Response(200, $payments);
    }
}
