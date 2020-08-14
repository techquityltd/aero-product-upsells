# Install

Add to your Aero project using the following composer command:
```
composer require techquity/aero-product-upsells
```

## Add it your project by running: 
```php artisan migrate```

```php artisan vendor:publish --tag=aero-product-upsells```

# Usage
To access the collection in your Twig files:

```twig
{% for upsell in cross_products(product, 'COLLECTION_NAME') %}
    {% set variant = upsell.variants | first %}
    
    //PRODUCT DETAILS HERE
    
{% endfor %}
```

You can also specify a limit on the number of products
{% for upsell in product_upsells(product, 6) %}
...


# Adding collections
1. To add a collection, go to Admin > Modules > Cross-sell products
2. Select a product you'd like to link other products to
3. Click create a collection
4. Add a collection

-- This collection can then be used to link products together via the COLLECTION_NAME in the front-end component above

# Linking products
1. To add a collection, go to Admin > Modules > Cross-sell products
2. Select a product you'd like to link other products to
3. Select a collection to link within
4. Select the products you'd like to link
5. Click 'Link products'
