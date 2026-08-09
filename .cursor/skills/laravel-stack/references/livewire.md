# Livewire 3 — Referencia rápida

## Componente básico

```php
<?php
namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\{Layout, Title, Computed};

#[Layout('layouts.app')]
#[Title('Mi Componente')]
class MyComponent extends Component
{
    public string $search = '';

    #[Computed]
    public function results()
    {
        return Product::where('name', 'like', "%{$this->search}%")->get();
    }

    public function render()
    {
        return view('livewire.my-component');
    }
}
```

## Binding

```html
<!-- Reactivo en tiempo real -->
<input wire:model.live="search">

<!-- Solo al perder foco -->
<input wire:model.blur="email">
```

## Paginación

```php
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.product-list', [
            'products' => Product::paginate(12),
        ]);
    }
}
```

## Upload de archivos

```php
use Livewire\WithFileUploads;

class UploadForm extends Component
{
    use WithFileUploads;
    public $photo;

    public function save()
    {
        $this->validate(['photo' => 'image|max:1024']);
        $this->photo->store('photos');
    }
}
```

## Eventos entre componentes

```php
// Emitir
$this->dispatch('product-added', productId: $product->id);

// Escuchar
#[On('product-added')]
public function handleProductAdded(int $productId) { }
```

## Alpine.js + Livewire

```html
<div x-data="{ open: false }">
    <button x-on:click="open = !open">Toggle</button>
    <div x-show="open" wire:ignore>...</div>
</div>
```
