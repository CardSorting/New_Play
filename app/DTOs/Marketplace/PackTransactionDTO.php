<?php

namespace App\DTOs\Marketplace;

use App\Models\Pack;
use App\Models\User;

class PackTransactionDTO
{
    public function __construct(
        public readonly int $packId,
        public readonly int $price,
        public readonly int $sellerId,
        public readonly int $buyerId,
        public readonly string $transactionType,
        public readonly string $description
    ) {}

    public static function fromPurchase(Pack $pack, User $buyer): self
    {
        return new self(
            packId: $pack->id,
            price: $pack->price,
            sellerId: $pack->user_id,
            buyerId: $buyer->id,
            transactionType: 'purchase',
            description: 'Purchase pack #' . $pack->id
        );
    }

    public static function fromSale(Pack $pack, User $buyer): self
    {
        return new self(
            packId: $pack->id,
            price: $pack->price,
            sellerId: $pack->user_id,
            buyerId: $buyer->id,
            transactionType: 'sale',
            description: 'Sold pack #' . $pack->id
        );
    }

    public function isPurchase(): bool
    {
        return $this->transactionType === 'purchase';
    }

    public function isSale(): bool
    {
        return $this->transactionType === 'sale';
    }
}
