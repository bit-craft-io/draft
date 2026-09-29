<?php

declare(strict_types=1);

namespace App\Core\Domains\ValueProxy;

/**
 * @method void credit_free(integer $value)
 * @method void credit_paid(integer $value)
 * @property-read integer $credit_free
 * @property-read integer $credit_paid
 * @property-read integer $credit_max_stock
 */
class VpCredit extends BaseVp
{
    public function canCharge(int $value): bool
    {
        $credit_sum = $this->credit_free + $this->credit_paid + $value;
        return $this->credit_max_stock >= $credit_sum;
    }

    public function chargeFree(int $value): void
    {
        $this->credit_free($value);
    }

    public function chargePaid(int $value): void
    {
        $this->credit_paid($value);
    }

    public function consumeFree(int $value): void
    {
        $sub = max(($this->credit_free - $value), 0);
        $this->credit_free($sub);
    }

    public function consumePaid(int $value): void
    {
        $sub = max(($this->credit_paid - $value), 0);
        $this->credit_paid($sub);
    }
}
