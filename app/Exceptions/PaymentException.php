<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class PaymentException extends Exception
{
    private array $context;

    public function __construct(
        string $message = '',
        array $context = [],
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public static function invalidCart(string $message = 'Invalid cart data'): self
    {
        return new self($message, [], 422);
    }

    public static function paymentFailed(
        string $message = 'Payment failed',
        array $context = [],
        ?Throwable $previous = null
    ): self {
        return new self($message, $context, 400, $previous);
    }

    public static function stripeError(
        string $message,
        array $context = [],
        ?Throwable $previous = null
    ): self {
        return new self($message, $context, 500, $previous);
    }

    public static function webhookError(
        string $message = 'Webhook processing failed',
        array $context = [],
        ?Throwable $previous = null
    ): self {
        return new self($message, $context, 500, $previous);
    }
}