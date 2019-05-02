<?php

namespace Webkul\Stripe\Http\Controllers;

use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Stripe\Helpers\Ipn;
use Webkul\Stripe\Helpers\StripePayment;

/**
 * Stripe Standard controller
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class StandardController extends Controller
{
    /**
     * OrderRepository object
     *
     * @var array
     */
    protected $orderRepository;

    /**
     * Ipn object
     *
     * @var array
     */
    protected $ipnHelper;

    /**
     * StripePayment object
     *
     * @var StripePayment
     */
    protected $stripePayment;

    /**
     * Create a new controller instance.
     *
     * @param  Webkul\Attribute\Repositories\OrderRepository  $orderRepository
     * @return void
     */
    public function __construct(
        OrderRepository $orderRepository,
        Ipn $ipnHelper,
        StripePayment $stripePayment
    )
    {
        $this->orderRepository = $orderRepository;

        $this->ipnHelper = $ipnHelper;

        $this->stripePayment = $stripePayment;
    }

    /**
     * Redirects to the Stripe payment form.
     *
     * @return \Illuminate\Http\Response
     */
    public function details()
    {
        return view('stripe::standard-redirect');
    }

    /**
     * Cancel payment from paypal.
     *
     * @return \Illuminate\Http\Response
     */
    public function cancel()
    {
        session()->flash('error', 'Stripe payment has been canceled.');

        return redirect()->route('shop.checkout.cart.index');
    }

    /**
     * Success payment
     *
     * @return \Illuminate\Http\Response
     */
    public function success()
    {
        $order = $this->orderRepository->create(Cart::prepareDataForOrder());

        Cart::deActivateCart();

        session()->flash('order', $order);

        return redirect()->route('shop.checkout.success');
    }

    /**
     * Stripe Ipn listener
     *
     * @return \Illuminate\Http\Response
     */
    public function doPayment()
    {
        try {
            $stripeResponse = $this->stripePayment->chargeCard(request()->all());
            return $stripeResponse;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
