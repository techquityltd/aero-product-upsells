### Install

Add to your Aero project using the following composer command:

```
composer require techquity/aero-product-upsells --prefer-dist
```

### Usage

To access the collection in your Twig files:

```twig
{% for upsell in product_upsells(product) %}
    {% set variant = upsell.variants | first %}
    <div class="product-upsell">
        <input type="number"
               name="upsells[{{ variant.id }}]"
               autocomplete="off"
               value="0"
               min="0">
        <div class="product-upsell__name">{{ upsell.name }}</div>
    </div>
{% endfor %}
```

You can also specify a limit on the number of products, and also eager load product relationships:

```twig
{% for upsell in product_upsells(product, 6, ["images", "manufacturer", "variants.prices"]) %}
...
```
