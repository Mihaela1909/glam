# glam — WordPress E-commerce Theme

A custom WordPress theme built for **glam**, a curated cruelty-free beauty
and skincare reseller based in Denmark. Built as a semester CMS project,
hand-coded without any page builder.

**Live site:** https://glamweb.dk

## Team
- Mihaela
- Orinta
- Aneta

## Tech stack
- WordPress (custom theme, no page builder)
- Secure Custom Fields (SCF) — custom field management
- Vanilla PHP, CSS, JavaScript
- AJAX (via `admin-ajax.php`) for filtering, load more, and contact form
- Hosted on Simply.com

## Project structure

wp-content/themes/mytheme/
├── assets/ # Theme images (logo, decorative icons)
├── css/ # Page-specific stylesheets
│ ├── base.css # Global: header, footer, shared components
│ ├── front-page.css # Homepage only
│ ├── contact.css # Contact page only
│ ├── blog-listing.css # Blog listing page only
│ └── single-post.css # Single blog post only
├── template-parts/
│ └── blog-card.php # Reusable blog post card (homepage + listing)
├── functions.php # Theme setup, custom post types, AJAX, SEO/schema
├── header.php / footer.php
├── front-page.php # Homepage
├── page-contact-us.php # Contact page (AJAX form submission)
├── page-front-blog.php # Blog listing (AJAX filter + load more)
└── single.php # Single blog post template


## Features

- **Custom post types:** Testimonials, Products, Homepage Content, Contact Messages
- **AJAX:** blog category filtering, "Load more" pagination, contact form submission — all without full page reloads
- **Accessibility:** WCAG A/AA compliant (audited with SiteImprove + WAVE) — semantic heading hierarchy, keyboard navigation, visible focus states, proper ARIA landmarks
- **SEO:** per-page meta descriptions, custom title tags, Organization/Article/Product schema (JSON-LD), clean permalink structure
- **Performance:** WebP images with correct sizing, lazy loading, per-page CSS splitting (instead of one global stylesheet), Autoptimize minification

## Sustainability

A companion static site documenting glam's sustainability initiatives is
hosted separately at https://sustainability.glamweb.dk
(repo: [SustainabilityRepo](https://github.com/Mihaela1909/SustainabilityRepo))

## Local development

Built and tested using [Local by WP Engine](https://localwp.com/).

1. Clone this repo
2. Set up a new site in Local, pointing to this theme folder
3. Activate Secure Custom Fields plugin
4. Activate "mytheme" under Appearance → Themes

## Required plugins
- Secure Custom Fields
- Wordfence
- UpdraftPlus (or WPvivid)
- Autoptimize