<?php

namespace Tests\Unit;

use App\Enums\PurchaseStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PurchaseStatusTest extends TestCase
{
    /** [status, isEditable, isDeletable, canBeOrdered, canBeReceived, canBePaid, canBeCancelled, canBeRestored] */
    public static function matrix(): array
    {
        return [
            'draft'     => [PurchaseStatus::DRAFT,     true,  true,  true,  true,  false, true,  false],
            'ordered'   => [PurchaseStatus::ORDERED,   true,  false, false, true,  true,  true,  false],
            'received'  => [PurchaseStatus::RECEIVED,  false, false, false, false, true,  false, false],
            'paid'      => [PurchaseStatus::PAID,      false, false, false, false, false, false, false],
            'cancelled' => [PurchaseStatus::CANCELLED, false, true,  false, false, false, true,  true],
        ];
    }

    #[DataProvider('matrix')]
    public function test_transition_rules(
        PurchaseStatus $status,
        bool $editable, bool $deletable, bool $orderable, bool $receivable,
        bool $payable, bool $cancellable, bool $restorable,
    ): void {
        $this->assertSame($editable, $status->isEditable());
        $this->assertSame($deletable, $status->isDeletable());
        $this->assertSame($orderable, $status->canBeOrdered());
        $this->assertSame($receivable, $status->canBeReceived());
        $this->assertSame($payable, $status->canBePaid());
        $this->assertSame($cancellable, $status->canBeCancelled());
        $this->assertSame($restorable, $status->canBeRestored());
    }
}
