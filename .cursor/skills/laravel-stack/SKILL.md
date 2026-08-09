---
name: laravel-stack
description: >
  Define el stack tecnológico del proyecto actual: Laravel 11.9, Livewire 3,
  LunarPHP, FilamentPHP y Tailwind CSS. Usa esta skill SIEMPRE que el usuario
  pida escribir, modificar, revisar o explicar cualquier código del proyecto,
  incluso si no menciona explícitamente el stack. Aplica también cuando se habla
  de modelos, controladores, componentes, vistas, migraciones, rutas, paneles de
  administración, catálogos de productos, carrito de compras, o cualquier
  funcionalidad de la aplicación.
---

# Stack del Proyecto

Este proyecto usa el siguiente stack. Seguí estas convenciones en **todo** el
código que generes o modifiques.

## Versiones

| Tecnología   | Versión |
|--------------|---------|
| PHP          | 8.2+    |
| Laravel      | 11.9    |
| Livewire     | 3.x     |
| LunarPHP     | latest  |
| FilamentPHP  | 3.x     |
| Tailwind CSS | 3.x     |

---

## Laravel 11.9

- Usá la estructura de directorios de Laravel 11 (sin `app/Http/Kernel.php`,
  bootstrap con `bootstrap/app.php`).
- Preferí **Action classes** sobre lógica pesada en controladores.
- Usá **Form Requests** para validación.
- Nombrá rutas con `->name()` y accedelas con `route()`.
- Migraciones con tipos modernos: `$table->foreignIdFor(Model::class)`.
- Usá `php artisan make:` para scaffolding; respetá las convenciones de
  nomenclatura (PascalCase modelos, snake_case tablas).
- Consultá `references/laravel.md` para patrones avanzados.

## Livewire 3

- Componentes en `app/Livewire/` con `#[Layout]`, `#[Title]` attributes cuando
  corresponda.
- Usá **wire:model.live** para binding en tiempo real y **wire:model.blur**
  cuando no se necesite reactividad inmediata.
- Preferí `#[Computed]` properties sobre métodos en la vista.
- Usá **Alpine.js** para interacciones JS pequeñas dentro de componentes
  Livewire; evitá JS puro salvo necesidad.
- Para formularios complejos usá `WithFileUploads`, `WithPagination` de Livewire.
- Consultá `references/livewire.md` para patrones de componentes.

## LunarPHP

- LunarPHP es el motor de e-commerce. Usá sus modelos nativos:
  `Product`, `ProductVariant`, `Order`, `Cart`, `Customer`, `Collection`.
- Extendé modelos con **Lunar Extenders**, no los modifiques directamente.
- Precios siempre en centavos (int); usá `\Lunar\Models\Price` y los helpers
  de Lunar para formatear.
- Para catálogo: usá `ProductType`, `Attribute`, `AttributeGroup`.
- Consultá `references/lunar.md` para el modelo de datos y pipelines.

## FilamentPHP 3

- Paneles en `app/Filament/`; recursos con `php artisan make:filament-resource`.
- Usá **Filament Forms**, **Tables** y **Infolists** con sus field builders
  nativos; evitá HTML crudo en paneles.
- Para relaciones usá `RelationManagers`.
- Notificaciones con `Filament\Notifications\Notification::make()`.
- Plugins de Filament para Lunar (`lunar-php/lunar-filament`) si están
  instalados.
- Consultá `references/filament.md` para componentes y patrones.

## Tailwind CSS 3

- Usá clases utilitarias directamente en Blade/Livewire; no escribas CSS
  custom salvo que sea inevitable.
- Seguí la paleta de colores definida en `tailwind.config.js` del proyecto.
- Responsive-first: `sm:`, `md:`, `lg:`, `xl:`.
- Para dark mode usá el prefijo `dark:`.
- Componentes UI reutilizables como Blade components en
  `resources/views/components/`.

---

## Services

Cuando el usuario pida crear un Service, seguí **siempre** estas reglas:

### Estructura obligatoria

- Una clase por caso de uso (no servicios enormes con múltiples responsabilidades).
- Ubicación: `app/Services/{Dominio}/{NombreService}.php`
- Un único método público llamado `handle()`.
- El nombre de la clase describe la acción: `CreateOrderService`, `ApplyCouponService`.

```php
<?php

namespace App\Services\Order;

use App\Models\Order;

class CreateOrderService
{
    public function handle(array $data): Order
    {
        return Order::create($data);
    }
}
```

### Simplicidad primero

- Si `handle()` cabe en pocas líneas claras → sin comentarios, el código se explica solo.
- Si `handle()` es extenso o complejo (múltiples pasos, condiciones no obvias, lógica de negocio importante) → documentá con un bloque PHPDoc que explique **qué hace**, **parámetros** y **qué retorna**.

```php
/**
 * Crea una orden a partir del carrito activo, aplica descuentos
 * y dispara los eventos de notificación al cliente.
 *
 * @param  Cart   $cart     Carrito con ítems validados
 * @param  string $coupon   Código de cupón opcional
 * @return Order            Orden creada con estado 'pending'
 */
public function handle(Cart $cart, string $coupon = ''): Order
{
    // ...lógica extensa...
}
```

### Reglas adicionales

- Sin lógica en el constructor (usá inyección de dependencias solo si es estrictamente necesario).
- No llames a otros Services desde un Service; delegá a Actions si necesitás componer pasos.
- Consultá `references/services.md` para ejemplos con LunarPHP y Filament.

---

## Convenciones generales

1. **Idioma del código**: inglés (variables, métodos, clases). Comentarios y
   strings de UI en el idioma del proyecto.
2. **Blade + Livewire**: preferí componentes Livewire sobre controladores
   clásicos para interactividad.
3. **No mezcles** lógica de negocio en vistas ni en componentes Livewire;
   delegá a Services o Actions.
4. Cuando generes código, indicá el **path completo** del archivo
   (`app/Livewire/Cart/CartSummary.php`).
5. Si una funcionalidad toca LunarPHP y FilamentPHP a la vez, consultá ambos
   archivos de referencia.

---

## Referencias adicionales

Lee estos archivos cuando necesites más detalle:

- `references/laravel.md` — Patrones avanzados de Laravel 11
- `references/livewire.md` — Patrones de componentes Livewire 3
- `references/lunar.md` — Modelo de datos y pipelines de LunarPHP
- `references/filament.md` — Componentes y recursos de FilamentPHP 3
- `references/services.md` — Ejemplos de Services con LunarPHP y Filament
