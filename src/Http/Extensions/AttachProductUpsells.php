<?php

namespace AeroCrossSelling\Http\Extensions;

use Closure;
use Aero\Responses\ResponseStep;
use Aero\Responses\ResponseBuilder;
use Aero\Store\Pipelines\ContentForBody;

class AttachProductUpsells implements ResponseStep
{
    public function handle(ResponseBuilder $builder, Closure $next)
    {
        ContentForBody::extend(static function ($content, Closure $next) {
            $content .= <<<EOD
<script>
window.AeroEvents.on('product.add-to-cart', function (data) {
    //Get all upsell inputs on the page
    var upsells = {}, elements = document.querySelectorAll('input[name^="upsells["],select[name^="upsells["]');

    for (var i = 0; i < elements.length; i++) {
        //Get the ID of the variant
        var el = elements[i], type = el.nodeName.toLowerCase(), value = parseInt(el.value),
            id = el.name.replace(']', '').replace('upsells[', '');
        
        //Only add the ones that are selected
        if (type === 'checkbox' && !el.checked) {
            continue;
        }
        
        //Only add the ones that have a quantity of above 1
        if ((type === 'select' || type === 'input') && value < 1) {
            continue;
        } 

        //Add the variant with quantity to the array if applicable
        upsells[id] = value;
    }

    //Add upsells to the data that is sent to the cart
    data.upsells = upsells;
   
    return data;
});
</script>
EOD;

            return $next($content);
        });

        return $next($builder);
    }
}
