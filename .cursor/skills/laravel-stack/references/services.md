# Services — Ejemplos del stack

## Servicio simple (sin documentar, se explica solo)

```php
<?php
// app/Services/User/CreateUserService.php
namespace App\Services\User;

use App\Models\User;

class CreateUserService
{
    public function handle(array $data): User
    {
        return User::create($data);
    }
}
```

---

## Servicio con LunarPHP (documentado por ser extenso)

```php
<?php
// app/Services/Order/CreateOrderFromCartService.php
namespace App\Services\Order;

use Lunar\Facades\CartSession;
use Lunar\Models\{Cart, Order};

class CreateOrderFromCartService
{
    /**
     * Convierte el carrito activo en una orden confirmada.
     * Recalcula totales, valida stock y actualiza el estado del carrito.
     *
     * @param  Cart   $cart    Carrito con ítems y dirección de envío cargados
     * @param  string $notes   Notas opcionales del cliente para el pedido
     * @return Order           Orden creada con estado 'pending'
     *
     * @throws \Lunar\Exceptions\CartException  Si el carrito está vacío o tiene ítems sin stock
     */
    public function handle(Cart $cart, string $notes = ''): Order
    {
        $cart->calculate();

        $order = $cart->createOrder();

        $order->update([
            'status'            => 'pending',
            'customer_note'     => $notes,
        ]);

        CartSession::forget();

        return $order;
    }
}
```

---

## Servicio con Filament (documentado)

```php
<?php
// app/Services/Product/PublishProductService.php
namespace App\Services\Product;

use Lunar\Models\Product;
use Filament\Notifications\Notification;

class PublishProductService
{
    /**
     * Publica un producto y notifica al usuario de Filament activo.
     * Valida que el producto tenga al menos una variante con precio antes de publicar.
     *
     * @param  Product $product  Producto a publicar
     * @return bool              True si se publicó, false si faltaban datos
     */
    public function handle(Product $product): bool
    {
        if ($product->variants()->whereHas('prices')->doesntExist()) {
            Notification::make()
                ->title('El producto no tiene variantes con precio')
                ->warning()
                ->send();

            return false;
        }

        $product->update(['status' => 'published']);

        Notification::make()
            ->title('Producto publicado correctamente')
            ->success()
            ->send();

        return true;
    }
}
```

---

## Cómo invocar un Service

**Desde Livewire:**
```php
public function submit(CreateOrderFromCartService $service): void
{
    $order = $service->handle(CartSession::current(), $this->notes);
    $this->redirect(route('orders.confirmation', $order));
}
```

**Desde una Action de Filament:**
```php
Tables\Actions\Action::make('publish')
    ->action(function (Product $record, PublishProductService $service) {
        $service->handle($record);
    }),
```
