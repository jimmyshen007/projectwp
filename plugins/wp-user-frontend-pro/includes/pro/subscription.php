<?php

class WPUF_subscription_element {

    public static function add_subscription_element( $sub_meta, $hidden_recurring_class, $hidden_trial_class, $obj ) {

        ?>
        <tr valign="top">
            <th><label><?php _e( 'Recurring', 'wpuf' ); ?></label></th>
            <td>
                <label for="wpuf-recuring-pay">
                    <input type="checkbox" <?php checked( $sub_meta['recurring_pay'], 'yes' ); ?> size="20" style="" id="wpuf-recuring-pay" value="yes" name="recurring_pay" />
                    <?php _e( 'Enable Recurring Payment', 'wpuf' ); ?>
                </label>
            </td>
        </tr>

        <tr valign="top" class="wpuf-recurring-child" style="display: <?php echo $hidden_recurring_class; ?>;">
            <th><label for="wpuf-billing-cycle-number"><?php _e( 'Billing cycle:', 'wpuf' ); ?></label></th>
            <td>
                <select id="wpuf-billing-cycle-number" name="billing_cycle_number">
                    <?php echo $obj->lenght_type_option( $sub_meta['billing_cycle_number'] ); ?>
                </select>

                <select id="cycle_period" name="cycle_period">
                    <?php echo $obj->option_field( $sub_meta['cycle_period'] ); ?>
                </select>
                <div><span class="description"></span></div>
            </td>
        </tr>

        <tr valign="top" class="wpuf-recurring-child" style="display: <?php echo $hidden_recurring_class; ?>;">
            <th><label for="wpuf-billing-limit"><?php _e( 'Billing cycle stop', 'wpuf' ); ?></label></td>
                <td>
                    <select id="wpuf-billing-limit" name="billing_limit">
                        <option value=""><?php _e( 'Never', 'wpuf' ); ?></option>
                        <?php echo $obj->lenght_type_option( $sub_meta['billing_limit'] ); ?>
                    </select>
                    <div><span class="description"><?php _e( 'After how many cycles should billing stop?', 'wpuf' ); ?></span></div>
                </td>
            </th>
        </tr>

        <tr valign="top" class="wpuf-recurring-child" style="display: <?php echo $hidden_recurring_class; ?>;">
            <th><label for="wpuf-trial-status"><?php _e( 'Trial', 'wpuf' ); ?></label></th>
            <td>
                <label for="wpuf-trial-status">
                    <input type="checkbox" size="20" style="" id="wpuf-trial-status" <?php checked( $sub_meta['trial_status'], 'yes' ); ?> value="yes" name="trial_status" />
                    <?php _e( 'Enable trial period', 'wpuf' ); ?>
                </label>
            </td>
        </tr>

        <tr class="wpuf-trial-child" style="display: <?php echo $hidden_trial_class; ?>;">
            <th><label for="wpuf-trial-cost"><?php _e( 'Trial amount', 'wpuf' ); ?></label></th>
            <td>
                <?php echo wpuf_get_option( 'currency_symbol', 'wpuf_payment', '$' ); ?><input type="text" size="20" class="small-text" id="wpuf-trial-cost" value="<?php echo esc_attr( $sub_meta['trial_cost'] ); ?>" name="trial_cost" />
                <span class="description"><?php _e( 'Amount to bill for the trial period', 'wpuf' ); ?></span>
            </td>
        </tr>

        <tr class="wpuf-trial-child" style="display: <?php echo $hidden_trial_class; ?>;">
            <th><label for="wpuf-trial-duration"><?php _e( 'Trial period', 'wpuf' ); ?></label></th>
            <td>
                <select id="wpuf-trial-duration" name="trial_duration">
                    <?php echo $obj->lenght_type_option( $sub_meta['trial_duration'] ); ?>
                </select>
                <select id="trial-duration-type" name="trial_duration_type">
                    <?php echo $obj->option_field( $sub_meta['trial_duration_type'] ); ?>
                </select>
                <span class="description"><?php _e( 'Define the trial period', 'wpuf' ); ?></span>
            </td>
        </tr>
    <?php

    }

}