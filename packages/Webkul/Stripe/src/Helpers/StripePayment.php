<?php


namespace Webkul\Stripe\Helpers;

use Stripe\Charge;
use Stripe\Customer;
use Stripe\Stripe;

/**
 * Class StripePayment
 * @package Webkul\Stripe\Helpers
 */
class StripePayment
{
    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var Charge
     */
    private $charge;

    /**
     * @var
     */
    private $apiKey;

    /**
     * @var Stripe
     */
    private $stripeService;

    /**
     * StripePayment constructor.
     * @param Stripe $stripe
     * @param Customer $customer
     * @param Charge $charge
     */
    public function __construct(Stripe $stripe, Customer $customer, Charge $charge)
    {
        $this->apiKey = 'sk_test_7e0t6y28cGUZZreEicHkSaVE';
        $this->stripeService = $stripe;
        $this->customer = $customer;
        $this->charge = $charge;
        $this->stripeService->setVerifySslCerts(false);
        $this->stripeService->setApiKey($this->apiKey);
    }

    /**
     * @param $customerDetailsArray
     * @return \Stripe\ApiResource
     */
    public function addCustomer($customerDetailsArray) {
        return $this->customer->create($customerDetailsArray);
    }

    /**
     * @param $cardDetails
     * @return array|mixed
     */
    public function chargeCard($cardDetails) {
        $customerDetailsArray = array(
            'email' => $cardDetails['email'],
            'source' => $cardDetails['stripeToken']
        );
        $customerResult = $this->addCustomer($customerDetailsArray);
        $charge = new Charge();
        $cardDetailsAry = array(
            'customer' => $customerResult->id,
            'amount' => $cardDetails['amount']*100 ,
            'currency' => $cardDetails['currency_code'],
            'description' => $cardDetails['item_name'],
            'metadata' => array(
                'order_id' => $cardDetails['invoice']
            )
        );
        $result = $charge->create($cardDetailsAry);

        return $result->jsonSerialize();
    }
}