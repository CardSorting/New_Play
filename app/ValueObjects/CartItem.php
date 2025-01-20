<?php

namespace App\ValueObjects;

class CartItem
{
    public function __construct(
        private readonly string $id,
        private readonly int $quantity,
        private readonly float $price
    ) {
        if ($quantity < 1) {
            throw new \InvalidArgumentException('Quantity must be at least 1');
        }

        if ($price < 0.50) {
            throw new \InvalidArgumentException('Price must be at least $0.50');
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getSubtotal(): float
    {
        return $this->price * $this->quantity;
    }

    /**
     * Convert to array for API
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'subtotal' => $this->getSubtotal()
        ];
    }

    /**
     * Convert to Stripe format
     *
     * @return array<string, mixed>
     */
    public function toStripeFormat(): array
    {
        return [
            'amount' => (int) ($this->price * 100), // Convert to cents
            'currency' => 'usd',
            'metadata' => [
                'item_id' => $this->id,
                'quantity' => $this->quantity
            ]
        ];
    }
}