<?php
/**
 * My Account dashboard — overrides woocommerce/templates/myaccount/dashboard.php (v4.4.0).
 * Shows the customer's name and overview cards from the Figma design.
 */

if (! defined('ABSPATH')) {
	exit;
}

$customer    = new WC_Customer(get_current_user_id());
$order_count = wc_get_customer_order_count($customer->get_id());
$last_order  = wc_get_customer_last_order($customer->get_id());

$saved_addresses = 0;
if ($customer->get_billing_address_1()) {
	$saved_addresses++;
}
if ($customer->get_shipping_address_1()) {
	$saved_addresses++;
}

$name = trim($customer->get_first_name() . ' ' . $customer->get_last_name());
if (! $name) {
	$name = $current_user->display_name;
}
?>

<div class="account-dashboard">
	<header class="account-dashboard__header">
		<div class="account-dashboard__avatar" aria-hidden="true"></div>
		<h1 class="account-dashboard__name"><?php echo esc_html($name); ?></h1>
	</header>

	<div class="account-dashboard__box">
		<p>
			From your account dashboard you can view your
			<a href="<?php echo esc_url(wc_get_endpoint_url('orders')); ?>">recent orders</a>,
			manage your
			<a href="<?php echo esc_url(wc_get_endpoint_url('edit-address')); ?>">shipping and billing addresses</a>,
			and <a href="<?php echo esc_url(wc_get_endpoint_url('edit-account')); ?>">edit your password and account details</a>.
		</p>

		<ul class="account-dashboard__cards">
			<li class="account-dashboard__card">
				<span class="account-dashboard__value"><?php echo esc_html($order_count); ?></span>
				<span class="account-dashboard__label">Orders placed</span>
			</li>
			<li class="account-dashboard__card">
				<span class="account-dashboard__value">
					<?php echo $last_order ? wp_kses_post(wc_price($last_order->get_total(), array('currency' => $last_order->get_currency()))) : '–'; ?>
				</span>
				<span class="account-dashboard__label">Most recent order</span>
			</li>
			<li class="account-dashboard__card">
				<span class="account-dashboard__value"><?php echo esc_html($saved_addresses); ?></span>
				<span class="account-dashboard__label">Saved addresses</span>
			</li>
		</ul>
	</div>
</div>

<?php
do_action('woocommerce_account_dashboard');
do_action('woocommerce_before_my_account');
do_action('woocommerce_after_my_account');