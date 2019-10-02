<?php

namespace Techquity\ProductUpsells;

use Closure;
use Aero\Cart\CartItem;
use Aero\Responses\ResponseStep;
use Aero\Catalog\Models\Variant;
use Aero\Responses\ResponseBuilder;
use Illuminate\Validation\ValidationException;
use Aero\Cart\Exceptions\CartModificationException;

class AddProductUpsells implements ResponseStep
{
    public function handle(ResponseBuilder $builder, Closure $next)
    {
        foreach ($builder->request->input('upsells', []) as $id => $quantity) {
            if ($quantity < 1) {
                continue;
            }

            $variant = Variant::find($id);

            if (! $variant) {
                continue;
            }

            $item = new CartItem($variant, $quantity);

            /* @var $cart \Aero\Cart\Cart */
            $cart = $builder->cart;

            try {
                $cart->add($item);
            } catch (CartModificationException $e) {
                throw ValidationException::withMessages([
                    "upsells.{$id}" => [$e->getMessage()],
                ]);
            }
        }

        return $next($builder);
    }
}
