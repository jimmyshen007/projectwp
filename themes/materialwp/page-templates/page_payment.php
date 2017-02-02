<?php
/*
Template Name: Payment
*/
get_header();

global $wpdb, $user_ID;
$query = 'SELECT user_email FROM wp_users WHERE ID = ' . $user_ID;
$results = $wpdb->get_results( $query, ARRAY_A );
$email = $results[0]['user_email'];
?>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript">
    Stripe.setPublishableKey('pk_test_JJCG8Qu51sXzY3sIRLhfU2sf'); // TODO: update to live publishable key

    $(function() {
        var $form = $('#payment-form');
        $form.submit(function(event) {
            // Disable the submit button to prevent repeated clicks:
            $form.find('.submit').prop('disabled', true);
            document.getElementById("BtnPay").disabled = true;

            // Request a token from Stripe:
            Stripe.card.createToken($form, stripeResponseHandler);

            // Prevent the form from being submitted:
            return false;
        });
    });

    function policyCheck()
    {
        if (document.getElementById('policyCheckbox').checked)
        {
            document.getElementById("BtnPay").disabled = false;
            document.getElementById("TxtPolicyReminder").style.display = 'none';
        } else {
            document.getElementById("BtnPay").disabled = true;
            document.getElementById("TxtPolicyReminder").style.display = 'block';
        }
    }

    function stripeResponseHandler(status, response) {
        // Grab the form:
        var $form = $('#payment-form');

        if (response.error) { // Problem!

            // Show the errors on the form:
            $form.find('.payment-errors').text(response.error.message);
            $form.find('.submit').prop('disabled', false); // Re-enable submission
            document.getElementById("BtnPay").disabled = false;

        } else { // Token was created!

            $form.find('.payment-errors').text("");

            // Get the token ID:
            var token = response.id;

            // Insert the token ID into the form so it gets submitted to the server:
            //$form.append($('<input type="hidden" name="stripeToken">').val(token));

            var urlstr = '/api/0/orders/pay/stripe/';
            jQuery.ajax({
                url: urlstr.concat('<?php echo $_POST["orderStripeId"]; ?>'),
                dataType: "json",
                method: "post",
                data: {
                    "source": token,
                    "email": "<?php echo $email; ?>"
                },
                success: function (result) {
                    var orderID = '<?php echo $_POST["orderId"]; ?>';
                    var urlstr2 = '/api/0/worders/';
                    jQuery.ajax({
                        url: urlstr2.concat(orderID),
                        dataType: "json",
                        method: "post",
                        data: {
                            "appStatus": "Completed"
                        },
                        success: function (result) {
                            window.location = "/your-profile/users-orders/";
                        },
                        error: function () {
                            $form.find('.payment-errors').text("Opps, The payment didn't go through. Please make sure your payment info is correct.");
                            document.getElementById("BtnPay").disabled = false;
                        }
                    });
                }
            });

            // Submit the form:
            //$form.get(0).submit();
        }
    }
</script>
<div class="container">
    <div class="row">
        <div id="primary" class="col-md-12 col-lg-12">
            <main id="main" class="site-main" role="main">
                <div class="card">
                    <div class="entry-img"></div>
                    <div class="entry-container">
                        <p></p>
                        <div class="panel panel-default">
                            <div class="panel-heading">Payment</div>
                            <table>
                                <tbody>
                                <tr>
                                    <td style="width: 8%"></td>
                                    <td style="width: 84%">
                                        <div class="panel-body">
                                            <form action="" method="POST" id="payment-form">
                                                <span class="payment-errors text-danger"></span>
                                                <p>Payment description: explain how our payment works, how much tenants need to pay</p>
                                                <table style="width: 50%">
                                                    <tbody>
                                                    <tr style="height: 50px">
                                                        <td style="width: 30%">
                                                            <label class="control-label" for="name" style="margin-bottom:0">Cardholder Name</label>
                                                        </td>
                                                        <td style="width: 70%">
                                                            <input class="form-control input-lg" width="80%" type="text" id="name" size="20" data-stripe="name" placeholder="cardholder name">
                                                        </td>
                                                    </tr>
                                                    <tr style="height: 50px">
                                                        <td style="width: 30%">
                                                            <label class="control-label" for="cardnum" style="margin-bottom:0">Card Number<span>&nbsp;*</span></label>
                                                        </td>
                                                        <td style="width: 70%">
                                                            <input class="form-control input-lg" width="80%" type="text" id="cardnum" size="20" data-stripe="number" placeholder="credit card number">
                                                        </td>
                                                    </tr>
                                                    <tr style="height: 50px">
                                                        <td style="width: 30%">
                                                            <label class="control-label" for="expire" style="margin-bottom: 0">Expiry (MM/YY)<span>&nbsp;*</span></label>
                                                        </td>
                                                        <td style="width: 70%">
                                                            <table style="width: 28%; margin-bottom: 0">
                                                                <tr>
                                                                    <td >
                                                                        <input class="form-control input-lg" style="width: 30px" type="text" size="2" data-stripe="exp_month" placeholder="MM">
                                                                    </td>
                                                                    <td><span> / </span></td>
                                                                    <td>
                                                                        <input class="form-control input-lg" style="width: 30px" type="text" size="2" data-stripe="exp_year" placeholder="YY">
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr style="height: 50px">
                                                        <td style="width: 30%">
                                                            <label class="control-label" for="cvc" style="margin-bottom: 0">CVC<span>&nbsp;*</span></label>
                                                        </td>
                                                        <td style="width: 70%">
                                                            <input class="form-control input-lg" style="width: 70px" type="text" id="cvc" size="4" data-stripe="cvc" placeholder="CVC">
                                                        </td>
                                                    </tr>
                                                </table>
                                                <div style="margin-bottom: 10px"><span>*&nbsp;Required fields</span></div>
                                                <div id="TxtPolicyReminder"><label><strong style="color: #ffb400">Before booking, agree to the House Rules and Terms.</strong></label></div>
                                                <table>
                                                    <tr>
                                                        <td>
                                                            <div class="checkbox" style="margin-top: 7.5px; margin-right: 10px">
                                                                <label><input id="policyCheckbox" type="checkbox" onclick="policyCheck()"></label>
                                                            </div>
                                                        </td>
                                                        <td><span>I agree to the House Rules, Cancellation Policy, and to the Guest Refund Policy. I also agree to pay the total amount shown.</span></td>
                                                    </tr>
                                                </table>
                                                <div><input id="BtnPay" type="submit" disabled="disabled" class="btn btn-raised btn-info" value="Submit Payment"></div>
                                            </form>
                                        </div>
                                    </td>
                                    <td style="width: 8%"></td>
                                </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </main><!-- #main -->
        </div><!-- #primary -->

    </div> <!-- .row -->
</div> <!-- .container -->

<?php get_footer(); ?>
