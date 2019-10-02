<?php

namespace Techquity\ProductUpsells;

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
    var upsells = {}, elements = document.querySelectorAll('input[name^="upsells["],select[name^="upsells["]');

    for (var i = 0; i < elements.length; i++) {
        var el = elements[i], type = el.nodeName.toLowerCase(), value = parseInt(el.value),
            id = el.name.replace(']', '').replace('upsells[', '');
        
        if (type === 'checkbox' && !el.checked) {
            continue;
        }
        
        if ((type === 'select' || type === 'input') && value < 1) {
            continue;
        } 

        upsells[id] = value;
    }

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
