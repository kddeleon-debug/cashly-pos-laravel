<?php

namespace App\Exceptions;

use RuntimeException;

class InsufficientStockException extends RuntimeException
{
    public function __construct(public readonly string $productName, public readonly int $available, public readonly int $requested)
    {
        $message = $available <= 0
            ? "“{$productName}” is out of stock, so the sale was not recorded."
            : "Not enough stock for “{$productName}”: only {$available} available but {$requested} requested. The sale was not recorded.";

        parent::__construct($message);
    }
}
