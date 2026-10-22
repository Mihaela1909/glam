<?php get_header() ?>
    <div class="product-breadcrumb">
        <?php woocommerce_breadcrumb( array(
            'delimiter' => '<span class="crumb-sep">/</span>',
            'home'      => '',
        ) ); ?>
    </div>
    <h1 class="cart-title">Your cart</h1>
    <?php echo do_shortcode("[woocommerce_cart]") ?>
<?php get_footer() ?>