<?php

namespace App\ValueObjects;

use App\ValueObjects\CartItem;
use Illuminate\Support\Collection;

class Cart
{
    private Collection $items;

    /**
     * Create a new Cart instance
     *
     * @param array<int, array<string, mixed>> $items
     */
    public function __construct(array $items)
    {
        $this->items = collect($items)->map(fn (array $item) => new CartItem(
            id: $item['id'],
            quantity: $item['quantity'],
            price: $item['price']
        ));
    }

    /**
     * Get the total amount for the cart
     */
    public function getTotalAmount(): float
    {
        return $this->items->sum(fn (CartItem $item) => $item->getSubtotal());
    }

    /**
     * Get the cart items
     *
     * @return Collection<int, CartItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * Convert cart to array for API
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'items' => $this->items->map->toArray()->all(),
            'total' => $this->getTotalAmount()
        ];
    }

    /**
     * Create a Cart from a request array
     *
     * @param array<string, mixed> $data
     */
    public static function fromRequest(array $data): self
    {
        if (!isset($data['cart']) || !is_array($data['cart'])) {
            throw new \InvalidArgumentException('Invalid cart data');
        }

        return new self($data['cart']);
    }
}