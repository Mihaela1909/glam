<?php get_header() ?>
    <div class="product-breadcrumb">
        <?php woocommerce_breadcrumb( array(
            'delimiter' => '<span class="crumb-sep">/</span>',
            'home'      => '',
        ) ); ?>
    </div>
    <h1 class="checkout-title"><?php echo is_wc_endpoint_url( 'order-received' ) ? 'Order received' : 'Checkout'; ?></h1>
    <?php echo do_shortcode("[woocommerce_checkout]") ?>
<?php get_footer() ?>