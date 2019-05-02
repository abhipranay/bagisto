<?php

namespace Webkul\Stripe\Payment;

/**
 * Stripe Standard payment method class
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class Standard extends Stripe
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code  = 'stripe_standard';

    /**
     * Line items fields mapping
     *
     * @var array
     */
    protected $itemFieldsFormat = [
        'id'       => 'item_number_%d',
        'name'     => 'item_name_%d',
        'quantity' => 'quantity_%d',
        'price'    => 'amount_%d',
    ];

    /**
     * Return paypal redirect url
     *
     * @var string
     */
    public function getRedirectUrl()
    {
        return route('stripe.standard.doPayment');
    }

    /**
     * Return form field array
     *
     * @return array
     */
    public function getFormFields()
    {
        $cart = $this->getCart();

        $fields = [
            'invoice'         => $cart->id,
            'currency_code'   => $cart->cart_currency_code,
            'item_name'       => core()->getCurrentChannel()->name,
            'amount'          => $cart->sub_total + $cart->tax_total + $cart->selected_shipping_rate->price - $cart->discount
        ];

        /*if ($this->getIsLineItemsEnabled()) {
            $fields = array_merge($fields, array(
                'cmd'    => '_cart',
                'upload' => 1,
            ));

            $this->addLineItemsFields($fields);

            $this->addShippingAsLineItems($fields, $cart->items()->count() + 1);

            if (isset($fields['tax'])) {
                $fields['tax_cart'] = $fields['tax'];
            }

            if (isset($fields['discount_amount'])) {
                $fields['discount_amount_cart'] = $fields['discount_amount'];
            }
        } else {
            $fields = array_merge($fields, array(
                'cmd'           => '_ext-enter',
                'redirect_cmd'  => '_xclick',
            ));
        }*/

        $this->addAddressFields($fields);

        return $fields;
    }

    /**
     * Add shipping as item
     *
     * @param array $fields
     * @param int $i
     * @return void
     */
    protected function addShippingAsLineItems(&$fields, $i)
    {
        $cart = $this->getCart();

        $fields[sprintf('item_number_%d', $i)] = $cart->selected_shipping_rate->carrier_title;
        $fields[sprintf('item_name_%d', $i)] = 'Shipping';
        $fields[sprintf('quantity_%d', $i)] = 1;
        $fields[sprintf('amount_%d', $i)] = $cart->selected_shipping_rate->price;
    }
}
