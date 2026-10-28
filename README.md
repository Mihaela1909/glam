# glam — WordPress Webshop Theme

A custom WordPress + WooCommerce theme for **glam**, a curated cruelty-free beauty
reseller based in Denmark. Built as a semester CMS project, hand-coded without a page builder.

**Live site:** https://glamweb.dk

> glam is a student project, not a real webshop. Nothing is sold, payments run in
> Stripe test mode, and all brand names, product names and images belong to their owners.

## Team
- Mihaela
- Orinta
- Aneta

## Tech stack
- WordPress with a custom theme (`wp-content/themes/mytheme`), no page builder
- WooCommerce — shop, product pages, cart, checkout and account, all restyled in the theme
- Stripe for WooCommerce (test mode)
- Secure Custom Fields (SCF) — custom fields for blog posts, homepage content and testimonials
- Vanilla PHP, CSS and JavaScript; AJAX through `admin-ajax.php`
- Hosted on Simply.com

## Features

**Webshop**
- 17 products with categories, tags (cruelty-free, vegan, budget) and shade/colour variations
- Custom shop layout: category sidebar, search + sort toolbar, product badges, accordion on product pages
- Shipping zones for Denmark (incl. Bornholm and free shipping in Esbjerg) and Germany; prices in DKK
- Coupon codes: `GLAM10` (newsletter), `GLOW10`, `AUTUMN26` (autumn campaign)

**Marketing**
- Newsletter pop-up with a 10% code (`GLAM10`), shown once per visitor, reopened from a floating star button
- Product quiz on the shop page: 5 chat-style questions → a 3-product starter kit with a suggested
  shade, a matching tutorial and an "Email me my results" option (adds the email to the newsletter list)

**Content**
- Blog with AJAX category filter and "Load more"
- Contact form with AJAX submission, saved in WP Admin

**Custom post types**

| Post type | Used for |
|---|---|
| `testimonial` | "What users think" on the homepage |
| `site_content` | Editable homepage content (hero slides, buttons, brand message) |
| `product` | Shop products (WooCommerce) |
| `contact_message` | Contact form submissions (admin only) |
| `newsletter_signup` | Newsletter and quiz email sign-ups (admin only) |

**Roles (CRUD by role)**

| Role | Can do |
|---|---|
| Administrator | Everything, including users, plugins and settings |
| Communication Officer | Create, edit, publish and delete blog posts, site content, testimonials and products |
| Intern | Write and edit their own drafts; cannot publish or delete |
| Visitor | Only the public forms: contact, newsletter, quiz email, blog comments |

The two custom roles are created in `mytheme_setup_roles()` in `functions.php`.

**Security**
- Nonces on every form and AJAX handler; all output escaped
- Wordfence firewall (enabled and protecting), rate limiting and login protection
- UpdraftPlus weekly backups to Google Drive

**Accessibility**
- WCAG 2.2 AA: no A/AA issues in SiteImprove; Lighthouse Accessibility 100 on all main pages
- Skip link, visible focus states, ARIA labels, 44px tap targets, reduced-motion support
- Pop-up and quiz use native `<dialog>`: Esc closes them, focus returns to the button that opened them

**SEO**
- Custom title tags and meta descriptions per page type
- JSON-LD schema: Organization (site-wide), Article (blog posts), Product (product pages)
- WordPress XML sitemap at `/wp-sitemap.xml`

**Performance**
- Per-page CSS files instead of one global stylesheet
- Homepage: hero image preloaded, all images below the hero lazy-loaded
- Google Fonts loaded with preconnect (Stack Sans Text)
- WebP for theme images and most uploads

## Project structure

```
wp-content/themes/mytheme/
├── assets/                     # Logo, hero images, sparkle icon, newsletter background
├── css/
│   ├── base.css                # Global: variables, header, footer, buttons, cards
│   ├── front-page.css          # Homepage
│   ├── about.css               # About page
│   ├── contact.css             # Contact page
│   ├── blog-listing.css        # Blog listing
│   ├── single-post.css         # Single blog post
│   ├── shop.css                # Shop, product pages, product cards
│   ├── cart.css / checkout.css / account.css
│   ├── newsletter.css          # Newsletter pop-up + star button
│   └── quiz.css                # Product quiz
├── js/
│   └── quiz.js                 # Product quiz chat flow
├── template-parts/
│   ├── blog-card.php           # Blog card (homepage + blog listing)
│   ├── newsletter-popup.php    # Newsletter pop-up markup
│   └── product-quiz.php        # Product quiz dialog markup
├── woocommerce/
│   └── myaccount/dashboard.php # Custom My Account dashboard
├── functions.php               # Setup, post types, roles, AJAX, SEO/schema, quiz logic, performance
├── header.php / footer.php
├── front-page.php              # Homepage
├── page-about.php              # About page
├── page-contact-us.php         # Contact page
├── page-front-blog.php         # Blog listing
├── page-cart.php / page-checkout.php / page-my-account.php
├── woocommerce.php             # Shop and product page wrapper (sidebar, quiz)
├── single.php                  # Single blog post
├── comments.php                # Blog comments
└── index.php                   # Fallback template
```

## Sustainability

A companion static site about glam's sustainability work is hosted at
https://sustainability.glamweb.dk
(repo: [SustainabilityRepo](https://github.com/Mihaela1909/SustainabilityRepo)).

## Local development

Built and tested with [Local by WP Engine](https://localwp.com/). The repository is the site's
`public` folder; `.gitignore` keeps only `wp-content/` (theme, plugins and uploads) and this README.
`wp-config.php` is not in the repository.

1. Create a new site in Local.
2. Clone this repository into the site's `app/public` folder (replace the default `wp-content`).
3. Set the database details in `wp-config.php` (ask the team for the shared database credentials).
4. In WP Admin, activate the required plugins and the **My Theme** theme under Appearance → Themes.

## Required plugins
- WooCommerce
- WooCommerce Stripe Gateway
- Secure Custom Fields
- Wordfence Security
- UpdraftPlus
