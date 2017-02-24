<?php
/*
Template Name: Host Account
*/

get_header();
?>
<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/custom_support/checkip.php');

global $user_ID, $wpdb;

$first_name_arr = get_user_meta($user_ID, "first_name", false);
$first_name = (count($first_name_arr) > 0) ? $first_name_arr[0] : "";
$last_name_arr = get_user_meta($user_ID, "last_name", false);
$last_name = (count($last_name_arr) > 0) ? $last_name_arr[0] : "";
$dob_arr = get_user_meta($user_ID, "dob", false);
$dob = (count($dob_arr) > 0) ? $dob_arr[0] : "";
$country_arr = get_user_meta($user_ID, "country", false);
$country = (count($country_arr) > 0) ? $country_arr[0] : "";
$street_arr = get_user_meta($user_ID, "street", false);
$street = (count($street_arr) > 0) ? $street_arr[0] : "";
$city_arr = get_user_meta($user_ID, "city", false);
$city = (count($city_arr) > 0) ? $city_arr[0] : "";
$state_arr = get_user_meta($user_ID, "state", false);
$state = (count($state_arr) > 0) ? $state_arr[0] : "";
$postcode_arr = get_user_meta($user_ID, "postcode", false);
$postcode = (count($postcode_arr) > 0) ? $postcode_arr[0] : "";
$isBankAccCreated_arr = get_user_meta($user_ID, "isBankAccCreated", false);
$isBankAccCreated = (count($isBankAccCreated_arr) > 0) ? $isBankAccCreated_arr[0] : "";

$user_info = get_userdata($user_ID);
$email = $user_info->user_email;

$is_tenant_arr = get_user_meta($user_ID, "is_tenant", false);
$is_tenant = (count($is_tenant_arr) > 0) ? $is_tenant_arr[0] : 0;
$is_host_arr = get_user_meta($user_ID, "is_host", false);
$is_host = (count($is_host_arr) > 0) ? $is_host_arr[0] : 0;

$ip = getClientIP();

function update_account()
{
    $user = $_POST["userID"];

    $first_name = $_POST["firstName"];
    $last_name = $_POST["lastName"];
    $dob = $_POST["dateOfBirth"];
    $country = $_POST["country"];
    $street = $_POST["street"];
    $city = $_POST["city"];
    $state = $_POST["state"];
    $postcode = $_POST["postcode"];

    update_user_meta( $user, 'first_name', $first_name );
    update_user_meta( $user, 'last_name', $last_name );
    update_user_meta( $user, 'dob', $dob );
    update_user_meta( $user, 'country', $country );
    update_user_meta( $user, 'street', $street );
    update_user_meta( $user, 'city', $city );
    update_user_meta( $user, 'state', $state );
    update_user_meta( $user, 'postcode', $postcode );
    update_user_meta( $user, 'isBankAccCreated', true );
}

if(!empty($_POST) && $_POST["action"] == "updateAccount") {
    update_account();
    header("Location: http://".$_SERVER[HTTP_HOST].$_POST['prevURL']);
    die();
}
?>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript">
    Stripe.setPublishableKey('pk_test_JJCG8Qu51sXzY3sIRLhfU2sf'); // TODO: update to live publishable key

    var userID = <?php echo $user_ID;?>;
    var stripeAccID = "";
    var stripeExtaccountID = "";

    function policyCheck()
    {
        if (document.getElementById('policyCheckbox').checked)
        {
            document.getElementById("BtnSubmit").disabled = false;
        } else {
            document.getElementById("BtnSubmit").disabled = true;
        }
    }

    // Expect input as y-m-d
    function isValidDate(s) {
        var bits = s.split('-');
        var d = new Date(bits[1], bits[1] - 1, bits[2]);
        return d && (d.getMonth() + 1) == bits[1];
    }

    $( function() {
        var dateFormat = "yy-mm-dd";
        $( "#inputDOB" ).datepicker({
                dateFormat: dateFormat,
                changeMonth: true,
                changeYear: true,
                yearRange: "-100:+0"
            })
            .on( "change", function() {
                var pp_expiry = document.getElementById("inputDOB").value;
                if (pp_expiry != "") {
                    if (!isValidDate(pp_expiry)) {
                        document.getElementById("inputDOB").value = "";
                    }
                }
            });

        var country= "<?php echo $country;?>";
        $("#selectCoun").val(country);

        var $form = $('#account-form');
        $form.submit(function(event) {
            // Disable the submit button to prevent repeated clicks:
            $form.find('.submit').prop('disabled', true);
            document.getElementById("BtnSubmit").disabled = true;

            /* Check fields validation */
            var valid = true;
            var error_txt = '';
            if (document.getElementById('inputFN').value == "") {
                error_txt += 'Error: Please enter your first name.<br>';
            }
            if (document.getElementById('inputLN').value == "") {
                error_txt += 'Error: Please enter your last name.<br>';
            }
            if (document.getElementById('inputDOB').value == "") {
                error_txt += 'Error: Please enter your date of birth.<br>';
            }
            if (document.getElementById('inputAccNo').value == "") {
                error_txt += 'Error: Please enter your bank account number.<br>';
            } else if(!/^[0-9]+$/.test(document.getElementById('inputAccNo').value)){
                error_txt += 'Error: Please enter a valid account number.<br>';
            }
            if (document.getElementById('inputRoute').value == "") {
                if(document.getElementById('selectCoun').value == 'AU')
                    error_txt += 'Error: Please enter your bank BSB number.<br>';
                else if(document.getElementById('selectCoun').value == 'US')
                    error_txt += 'Error: Please enter your bank IBAN number.<br>';
            } else if(!/^[0-9]+$/.test(document.getElementById('inputRoute').value)){
                if(document.getElementById('selectCoun').value == 'AU')
                    error_txt += 'Error: Please enter a valid BSB number.<br>';
                else if(document.getElementById('selectCoun').value == 'US')
                    error_txt += 'Error: Please enter a valid IBAN number.<br>';

            }

            document.getElementById("errorTxt").innerHTML= error_txt;

            /* If no error, go ahead and get stripe account id*/
            if(error_txt == '')
            {
                jQuery.ajax({
                    url: "/api/0/waccounts/user/" + userID,
                    dataType: "json",
                    method: "Get",
                    success: function (result) {
                        if (result.data.length != 0 && (stripeAccID = result.data[0].stripeAccID) != "") {
                            var account_no = document.getElementById('inputAccNo').value;
                            var routing_no = document.getElementById('inputRoute').value;
                            var country = document.getElementById('selectCoun').value;
                            var currency;
                            switch (country) {
                                case "AU":
                                    currency = "aud";
                                    break;
                                case "US":
                                    currency = "usd";
                                    break;
                                default:
                                    currency = "aud";
                                    break;
                            }
                            Stripe.bankAccount.createToken({
                                country: country,
                                currency: currency,
                                routing_number: routing_no,
                                account_number: account_no,
                            }, stripeResponseHandler);
                        }
                        else {  // No account existed yet, create a new one
                            var country = document.getElementById('selectCoun').value;
                            jQuery.ajax({
                                url: '/api/0/accounts',
                                dataType: "json",
                                method: "POST",
                                data: {"country": country, "managed": true, "email": "<?php echo $email;?>", "metadata": {"userID": userID}},
                                success: function (result) {
                                    stripeAccID = result.data.stripeAccID;
                                    var account_no = document.getElementById('inputAccNo').value;
                                    var routing_no = document.getElementById('inputRoute').value;
                                    var currency;
                                    switch (country) {
                                        case "AU":
                                            currency = "aud";
                                            break;
                                        case "US":
                                            currency = "usd";
                                            break;
                                        default:
                                            currency = "aud";
                                            break;
                                    }
                                    Stripe.bankAccount.createToken({
                                        country: country,
                                        currency: currency,
                                        routing_number: routing_no,
                                        account_number: account_no,
                                    }, stripeResponseHandler);
                                }
                            });
                        }
                    }
                });
            } else {
                document.getElementById("BtnSubmit").disabled = false;
            }
            // Prevent the form from being submitted:
            return false;
        });
    });

    function stripeResponseHandler(status, response) {

        if (response.error) { // Problem!
            // Show the errors on the form:
            document.getElementById("errorTxt").innerHTML= 'Error: ' + response.error.message + '<br>';
            document.getElementById("BtnSubmit").disabled = false; // Re-enable submission
        } else { // Token created!
            document.getElementById("errorTxt").innerHTML = "";
            // Get the token ID:
            var token = response.id;

            //Create external account
            jQuery.ajax({
                url: "/api/0/extaccounts",
                dataType: "json",
                method: "post",
                data: {
                    "external_account": token,
                    "default_for_currency" : true,
                    "metadata": {"stripeAccID": stripeAccID, "userID": userID}
                },
                success: function (result) {
                    var error_txt = "";
                    document.getElementById("errorTxt").innerHTML= error_txt;
                    stripeExtaccountID = result.data.stripeExtaccountID;
                    //Update account
                    var first_name = document.getElementById('inputFN').value;
                    var last_name = document.getElementById('inputLN').value;
                    var street = document.getElementById('inputStreet').value;
                    var city = document.getElementById('inputCity').value;
                    var state = document.getElementById('inputState').value;
                    var post_code = document.getElementById('inputPostCode').value;
                    var dob = document.getElementById("inputDOB").value;
                    var bits = dob.split('-');
                    var dob_date = new Date(bits[0], bits[1], bits[2]);
                    var dob_day = bits[2];
                    var dob_month = bits[1];
                    var dob_year = bits[0];
                    var now = Math.round(new Date().getTime() / 1000);
                    var ip = "<?php echo $ip;?>";
                    jQuery.ajax({
                        url: "/api/0/accounts/stripe/" + stripeAccID,
                        dataType: "json",
                        method: "post",
                        data: {
                            "legal_entity": {"dob": {"day": dob_day, "month": dob_month, "year": dob_year},
                                "first_name": first_name, "last_name": last_name,  "type": "individual",
                                "address": {"line1": street, "city": city,
                                    "postal_code": post_code, "state": state}},
                            "tos_acceptance": {"date": now, "ip": ip}
                        },
                        success: function (result) {
                            // Submit the form:
                            var $form = $('#account-form');
                            $form.get(0).submit();
                        },
                        error: function () {
                            var error_txt = "Opps, The submission didn't go through. Please make sure your bank info is correct or try it later.";
                            document.getElementById("errorTxt").innerHTML= error_txt;
                            document.getElementById("BtnSubmit").disabled = false;
                        }
                    });
                },
                error: function () {
                    var error_txt = "Opps, The submission didn't go through. Please make sure your bank info is correct or try it later.";
                    document.getElementById("errorTxt").innerHTML= error_txt;
                    document.getElementById("BtnSubmit").disabled = false;
                }
            });


        }
    }

    function changeCountry()
    {
        var country = document.getElementById('selectCoun').value;
        switch (country) {
            case 'AU':
                document.getElementById('labelRoute').innerHTML = "BSB";
                document.getElementsByName('routeNo')[0].placeholder = "BSB";
                break;
            case 'US':
                document.getElementById('labelRoute').innerHTML = "IBAN";
                document.getElementsByName('routeNo')[0].placeholder = "IBAN";
                break;
            default:
                document.getElementById('labelRoute').innerHTML = "BSB";
                document.getElementsByName('routeNo')[0].placeholder = "BSB";
                break;
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
                        <div>
                            <ul class="nav nav-pills" style="margin-bottom: 35px; margin-left: 0px;margin-top: -15px;">
                                <li><a href="/your-profile/">Profile</a></li>
                                <li><a href="/your-profile/wish-list/">Wish List</a></li>
                                <?php if($is_host == 1) {?>
                                    <li><a href="/your-profile/users-listings/">Your Listings</a></li>
                                <?php }?>
                                <?php if($is_tenant == 1) {?>
                                    <li><a href="/your-profile/users-orders/">Orders</a></li>
                                <?php }?>
                                <?php if($is_host == 1) {?>
                                    <li class="active"><a href="/your-profile/account/">Account</a></li>
                                <?php }?>
                            </ul>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">Host Account Settings</div>
                            <div class="panel-body">
                                <table>
                                    <tbody>
                                    <tr>
                                        <td style="width: 8%"></td>
                                        <td style="width: 77%">
                                            <?php if($isBankAccCreated) {?>
                                                <p class="text-muted" style="margin-bottom: 5px;">Thanks for providing your bank details. We will transfer tenant's bond money to your bank account once the tenant moves</p>
                                                <p class="text-muted" style="margin-bottom: 5px;">into your accommodation.</p>
                                                <button type="button" id="updateppBtn" class="btn btn-raised btn-default" data-toggle="collapse" data-target="#accountColl1" style="width: 230px;border: 1px solid #009587; border-radius: 3px"><span style="color: #009587">Update bank account</span></button>
                                            <?php } else {?>
                                                <p class="text-muted" style="margin-bottom: 5px;">As a host, you need to provide your personal information for verification as well as your bank details so that we can transfer</p>
                                                <p class="text-muted" style="margin-bottom: 5px;">applicant's bond money to your bank account. Lack of bank details results in your listings not being able to be applied by</p>
                                                <p class="text-muted" style="margin-bottom: 5px;">visitors.</p>
                                                <button type="button" id="updateppBtn" class="btn btn-raised btn-default" data-toggle="collapse" data-target="#accountColl1" style="width: 230px;border: 1px solid #009587; border-radius: 3px"><span style="color: #009587">Add bank account</span></button>
                                            <?php }?>
                                            <div id="accountColl1" class="collapse">
                                                <form action="/your-profile/account/" method="POST" id="account-form">
                                                    <p></p>
                                                    <?php if($isBankAccCreated) {?>
                                                    <p class="text-muted" style="margin-bottom: 5px;">Please provide the following information to update your bank account.</p>
                                                    <?php } else {?>
                                                    <p class="text-muted" style="margin-bottom: 5px;">Please provide the following information to add your bank account.</p>
                                                    <?php }?>
                                                    <p></p>
                                                    <span class="text-danger" id="errorTxt"></span>
                                                    <table>
                                                        <tbody>
                                                        <tr>
                                                            <td style="width: 80%">
                                                                <div class="form-group">
                                                                    <h4 style="color: #009587">Account Holder</h4>
                                                                    <table>
                                                                        <tr><td style="width: 45%">
                                                                                <label class="control-label" for="inputFN">First Name</label>
                                                                                <input class="form-control input-lg" type="text" id="inputFN" name="firstName" placeholder="First Name" <?php if($first_name != "") echo "value='".$first_name."'";?>>
                                                                            </td>
                                                                            <td style="width: 10%">
                                                                            </td>
                                                                            <td style="width: 45%">
                                                                                <label class="control-label" for="inputLN">Last Name</label>
                                                                                <input class="form-control input-lg" type="text" id="inputLN" name="lastName" placeholder="Last Name" <?php if($last_name != "") echo "value='".$last_name."'";?>>
                                                                            </td>
                                                                        </tr>
                                                                        <tr><td style="width: 45%">
                                                                                <label class="control-label" for="inputDOB">Date of Birth</label>
                                                                                <input class="form-control input-lg" type="text" id="inputDOB" name="dateOfBirth" placeholder="yyyy-mm-dd" value="<?php echo $dob;?>">
                                                                            </td>
                                                                            <td style="width: 10%">
                                                                            </td>
                                                                            <td style="width: 45%">
                                                                                <label for="selectCoun" class="control-label">Country</label>
                                                                                <select id="selectCoun" class="form-control input-lg" name="country" onchange="changeCountry()">
                                                                                    <option value="AU">Australia</option>
                                                                                    <option value="US">United States</option>
                                                                                </select>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                                <div class="form-group">
                                                                    <h4 style="color: #009587">Address</h4>
                                                                    <table>
                                                                        <tr><td style="width: 45%">
                                                                                <label class="control-label" for="inputStreet">Street</label>
                                                                                <input class="form-control input-lg" type="text" id="inputStreet" name="street" placeholder="Street" value="<?php echo $street;?>">
                                                                            </td>
                                                                            <td style="width: 10%">
                                                                            </td>
                                                                            <td style="width: 45%">
                                                                                <label class="control-label" for="inputCity">City</label>
                                                                                <input class="form-control input-lg" type="text" id="inputCity" name="city" placeholder="City" value="<?php echo $city;?>">
                                                                            </td>
                                                                        </tr>
                                                                        <tr><td style="width: 45%">
                                                                                <label class="control-label" for="inputState">State</label>
                                                                                <input class="form-control input-lg" type="text" id="inputState" name="state" placeholder="State" value="<?php echo $state;?>">
                                                                            </td>
                                                                            <td style="width: 10%">
                                                                            </td>
                                                                            <td style="width: 45%">
                                                                                <label class="control-label" for="inputPostCode">Postal Code</label>
                                                                                <input class="form-control input-lg" type="text" id="inputPostCode" name="postcode" placeholder="Postal Code" value="<?php echo $postcode;?>">
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                                <div class="form-group">
                                                                    <h4 style="color: #009587">Bank Details</h4>
                                                                    <table>
                                                                        <tr><td style="width: 45%">
                                                                                <label class="control-label" for="inputAccNo">Account No.</label>
                                                                                <input class="form-control input-lg" type="text" id="inputAccNo" name="AccountNo" placeholder="Account Number">
                                                                            </td>
                                                                            <td style="width: 10%">
                                                                            </td>
                                                                            <td style="width: 45%">
                                                                                <label class="control-label" for="inputRoute" id="labelRoute">BSB</label>
                                                                                <input class="form-control input-lg" type="text" id="inputRoute" name="routeNo" placeholder="BSB">
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                                <div class="form-group">
                                                                    <table>
                                                                        <tr>
                                                                            <td>
                                                                                <div class="checkbox" style="margin-top: 7px; margin-right: 10px">
                                                                                    <label><input id="policyCheckbox" type="checkbox" onclick="policyCheck()"></label>
                                                                                </div>
                                                                            </td>
                                                                            <td><span>I agree to the House Rules, Cancellation Policy, and to the Guest Refund Policy.</span></td>
                                                                        </tr>
                                                                    </table>
                                                                    <input type="hidden" name="action" value="updateAccount"/>
                                                                    <input type="hidden" name="userID" value="<?php echo $user_ID;?>"/>
                                                                    <input type="hidden" name="prevURL" value="<?php echo $_SERVER['REQUEST_URI']; ?>"/>
                                                                    <input id="BtnSubmit" style="margin-top: -10px;width: 230px" disabled="disabled" type="submit" class="btn btn-raised btn-primary" value="Submit">
                                                                </div>

                                                            </td>
                                                            <td style="width: 40%">
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </form>
                                            </div>
                                        </td>
                                        <td style="width: 15%"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main><!-- #main -->
        </div><!-- #primary -->

    </div> <!-- .row -->
</div> <!-- .container -->

<?php get_footer(); ?>
