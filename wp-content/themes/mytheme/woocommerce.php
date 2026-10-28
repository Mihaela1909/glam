<?php get_header() ?>
<?php if (!is_product()): ?>
    <section class="shop-banner">
        <div class="shop-banner-text">
            <h1 class="shop-title">Shop all products</h1>
            <p class="shop-description">Choose from a curated range of makeup, skincare and hair care.</p>
        </div>
        <img src="<?php echo esc_url(get_theme_file_uri('assets/shop-hero.webp')); ?>" alt="shop banner">
    </section>
<?php else: ?>
    <div class="product-breadcrumb">
        <?php woocommerce_breadcrumb(array(
            'delimiter' => '<span class="crumb-sep">/</span>',
            'home' => '',
        )); ?>
    </div>
<?php endif; ?>
<!-- Add a filter to modify the WooCommerce content output and display categories -->
<div class="shop-layout">
    <?php if (!is_product()): ?>
        <aside class="shop-categories">
            <h2>Categories</h2>
            <ul>
                <?php wp_list_categories(array(
                    'taxonomy' => 'product_cat',
                    'title_li' => '',
                    'hide_empty' => false,
                    'show_count' => true,
                )); ?>
            </ul>
            <div class="quiz-promo">
                <p class="quiz-promo-title">Not sure what to pick?</p>
                <p class="quiz-promo-text">5 quick questions, 3 perfect matches.</p>
                <button type="button" class="btn quiz-promo-btn" data-quiz-open aria-haspopup="dialog" aria-controls="quiz-dialog">Find my match</button>
            </div>
            <h2>GOOD TO KNOW</h2>
            <ul>
                <li>
                    Can choose from
                    100% cruelty-free brands
                </li>
                <li>
                    Recyclable packaging
                </li>
                <li>
                    Ships from Denmark
                </li>
            </ul>
        </aside>

        <script>
            document.querySelectorAll('.shop-categories li').forEach(function (item) {
                // Check if the main category has children
                var children = item.querySelector(':scope > .children');
                if (!children) {
                    return;
                }

                // Create a button to toggle the visibility of the subcategories
                var button = document.createElement('button');
                button.type = 'button';
                button.className = 'cat-toggle';
                button.textContent = '>';
                button.setAttribute('aria-label', 'Show subcategories');

                // Keep the list open when you're on one of its subcategories.
                var open = item.classList.contains('current-cat-parent') || item.classList.contains('current-cat-ancestor');
                item.classList.toggle('is-open', open);
                button.setAttribute('aria-expanded', open);

                button.addEventListener('click', function () {
                    var isOpen = item.classList.toggle('is-open');
                    button.setAttribute('aria-expanded', isOpen);
                });

                children.before(button);
            });
        </script>
    <?php endif; ?>
    <div class="shop-products">
        <?php woocommerce_content() ?>
    </div>
</div>
<?php if (!is_product()) {
    get_template_part('template-parts/product-quiz');
} ?>
<?php get_footer() ?>