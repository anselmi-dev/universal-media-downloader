# LunarPHP — Referencia rápida

## Modelos principales

| Modelo           | Descripción                        |
|------------------|------------------------------------|
| `Product`        | Producto con atributos y variantes |
| `ProductVariant` | SKU con precio y stock             |
| `ProductType`    | Define atributos del producto      |
| `Collection`     | Categoría/colección de productos   |
| `Cart`           | Carrito activo                     |
| `Order`          | Pedido confirmado                  |
| `Customer`       | Cliente registrado                 |

## Precios

```php
// Siempre en centavos
$variant->prices()->create([
    'price'        => 1999,   // $19.99
    'currency_id'  => $currency->id,
    'quantity_break' => 1,
]);

// Formatear para mostrar
$variant->price->formatted; // "$19.99"
```

## Carrito

```php
use Lunar\Facades\CartSession;

// Agregar al carrito
CartSession::add($variant, quantity: 2);

// Obtener carrito actual
$cart = CartSession::current();
$cart->calculate(); // recalcula totales

// Totales
$cart->total->formatted;
$cart->subTotal->formatted;
```

## Crear pedido desde carrito

```php
$order = $cart->createOrder();
$order->update(['status' => 'payment-received']);
```

## Atributos de producto

```php
// Leer atributo
$product->attr('description');
$product->attr('seo_title');

// En Blade
{{ $product->translateAttribute('name') }}
```

## Pipelines de precio

Lunar usa pipelines para calcular precios. Para extender:

```php
// En AppServiceProvider
\Lunar\Facades\Pricing::extend(function ($pipeline) {
    return $pipeline->pipe(MyPricingPipe::class);
});
```

## Extender modelos

```php
// No modificar modelos de Lunar directamente
// Usar morphMap o macros en ServiceProvider
Product::macro('isOnSale', function () {
    return $this->sale_price !== null;
});
```
