<?php $stripeStandard = app('Webkul\Stripe\Payment\Standard') ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Stripe Payment Gateway</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/css/bootstrap.css" rel="stylesheet">
    <style>
        .mt40{
            margin-top: 40px;
        }
        /**
 * The CSS shown here will not be introduced in the Quickstart guide, but shows
 * how you can use CSS to style your Element's container.
 */
        .StripeElement {
            box-sizing: border-box;

            height: 40px;

            padding: 10px 12px;

            border: 1px solid transparent;
            border-radius: 4px;
            background-color: white;

            box-shadow: 0 1px 3px 0 #e6ebf1;
            -webkit-transition: box-shadow 150ms ease;
            transition: box-shadow 150ms ease;
        }

        .StripeElement--focus {
            box-shadow: 0 1px 3px 0 #cfd7df;
        }

        .StripeElement--invalid {
            border-color: #fa755a;
        }

        .StripeElement--webkit-autofill {
            background-color: #fefde5 !important;
        }
    </style>
</head>
<body>
   
<div class="container">
 
<div class="row">
    <div class="col-lg-12 mt40">
        <div class="text-center">
            <h2>Pay for Cart</h2>
            <br>
        </div>
    </div>
</div>
    
@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Whoops!</strong> Something went wrong<br>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="row">  
    <div class="col-md-3">
         
    </div>
<div class="col-md-6"> 
{{--<form accept-charset="UTF-8" action="{{ $stripeStandard->getRedirectUrl() }}" class="require-validation"
    data-cc-on-file="false"
    data-stripe-publishable-key="test_public_key"
    id="payment-stripe" method="post">
    {{ csrf_field() }}
    <div class='row'>
        <div class='col-xs-12 form-group'>
            <label class='control-label'>Name on Card</label> <input
                class='form-control' size='4' type='text'>
        </div>
    </div>
    <div class='row'>
        <div class='col-xs-12 form-group'>
            <label class='control-label'>Card Number</label> <input
                autocomplete='off' class='form-control' size='20'
                type='text' name="card_no">
        </div>
    </div>
    <div class='row'>
        <div class='col-xs-4 form-group'>
            <label class='control-label'>CVC</label> <input autocomplete='off'
                class='form-control' placeholder='ex. 311' size='3'
                type='text' name="ccv">
        </div>
        <div class='col-xs-4 form-group'>
            <label class='control-label'>Expiration</label> <input
                class='form-control' placeholder='MM' size='2'
                type='text' name="expiry_month">
        </div>
        <div class='col-xs-4 form-group'>
            <label class='control-label'> </label> <input
                class='form-control' placeholder='YYYY' size='4'
                type='text' name="expiry_year">
        </div>
    </div>
    <div class='row'>
        <div class='col-md-12'>
            <div class='form-control total btn btn-info'>
                Total: <span class='amount'>$20</span>
            </div>
        </div>
    </div>
    <div class='row'>
        <div class='col-md-12 form-group'>
            <button class='form-control btn btn-primary submit-button'
                type='submit' style="margin-top: 10px;">Pay »</button>
        </div>
    </div>
 
</form>--}}


    <form action="{{ $stripeStandard->getRedirectUrl() }}" method="post" id="payment-form">
        <div class="form-row">
            <label for="card-element">
                Credit or debit card
            </label>
            {!! csrf_field() !!}
            @foreach ($stripeStandard->getFormFields() as $name => $value)

                <input type="hidden" name="{{ $name }}" value="{{ $value }}">

            @endforeach
            <div id="card-element">
                <!-- A Stripe Element will be inserted here. -->
            </div>

            <!-- Used to display form errors. -->
            <div id="card-errors" role="alert"></div>
        </div>

        <button>Submit Payment</button>
    </form>
</div>
</div>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        // Create a Stripe client.
        var stripe = Stripe('pk_test_hF0ZHe4a3fuRAn3wuhutVZG2');

        // Create an instance of Elements.
        var elements = stripe.elements();

        // Custom styling can be passed to options when creating an Element.
        // (Note that this demo uses a wider set of styles than the guide below.)
        var style = {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };

        // Create an instance of the card Element.
        var card = elements.create('card', {style: style});

        // Add an instance of the card Element into the `card-element` <div>.
        card.mount('#card-element');

        // Handle real-time validation errors from the card Element.
        card.addEventListener('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        // Handle form submission.
        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            stripe.createToken(card).then(function(result) {
                if (result.error) {
                    // Inform the user if there was an error.
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                } else {
                    // Send the token to your server.
                    stripeTokenHandler(result.token);
                }
            });
        });

        // Submit the form with the token ID.
        function stripeTokenHandler(token) {
            // Insert the token ID into the form so it gets submitted to the server
            var form = document.getElementById('payment-form');
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            form.appendChild(hiddenInput);

            // Submit the form
            form.submit();
        }
    </script>
    
</body>
