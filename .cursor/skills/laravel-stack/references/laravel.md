# Laravel 11 — Referencia rápida

## Estructura de bootstrap

```php
// bootstrap/app.php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__.'/../routes/web.php')
    ->withMiddleware(function (Middleware $middleware) { })
    ->withExceptions(function (Exceptions $exceptions) { })
    ->create();
```

## Action classes

```php
// app/Actions/Cart/AddItemToCart.php
class AddItemToCart
{
    public function handle(Cart $cart, ProductVariant $variant, int $qty = 1): Cart
    {
        // lógica aquí
    }
}
```

## Form Request

```php
class StoreOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return ['address' => 'required|string|max:255'];
    }
}
```

## Service Provider (Laravel 11, sin register/boot separados en Kernel)

Registrá providers en `bootstrap/providers.php`.

## Eloquent tips

- `$table->foreignIdFor(User::class)->constrained()->cascadeOnDelete()`
- Scopes: `scopeActive`, `scopePublished`
- Observers en `AppServiceProvider::boot()` con `Model::observe(Observer::class)`
