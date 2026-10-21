<?php get_header() ?>
<section class="shop-banner">
    <div class="shop-banner-text">
        <h1 class="shop-title">Shop all products</h1>
        <p class="shop-description">Choose from a curated range of makeup, skincare and hair care.</p>
    </div>
    <img src="<?php echo esc_url(get_theme_file_uri('assets/shop-hero.webp')); ?>" alt="shop banner">
</section>
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
<?php get_footer() ?>