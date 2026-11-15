<?php

namespace Tests\Feature\Purchases;

use App\DTOs\PurchaseData;
use App\Enums\PurchaseStatus;
use App\Exceptions\PurchaseException;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\User;
use App\Services\FinanceTransactionService;
use App\Services\PurchaseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PurchaseServiceTest extends TestCase
{
    use RefreshDatabase;

    private PurchaseService $service;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PurchaseService::class);
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    // ---------------------------------------------------------------- helpers

    private function makeProduct(array $attrs = []): Product
    {
        return Product::factory()->create(array_merge([
            'quantity' => 10,
            'purchase_price' => 1000,
            'selling_price' => 1500,
            'notes' => null,
        ], $attrs));
    }

    /**
     * @param array<int, array{product_id:int, quantity:int, unit_price:float, selling_price?:?float}> $items
     */
    private function makePurchase(PurchaseStatus $status = PurchaseStatus::DRAFT, array $attrs = [], array $items = []): Purchase
    {
        $purchase = Purchase::create(array_merge([
            'supplier_id' => Supplier::factory()->create()->id,
            'purchase_date' => now()->toDateString(),
            'status' => $status,
            'created_by' => $this->user->id,
            'total' => 0,
        ], $attrs));

        foreach ($items as $item) {
            PurchaseItem::create(array_merge([
                'purchase_id' => $purchase->id,
                'subtotal' => $item['quantity'] * $item['unit_price'],
                'selling_price' => null,
            ], $item));
        }

        return $purchase->fresh();
    }

    private function purchaseData(array $overrides = []): PurchaseData
    {
        $product = $this->makeProduct();

        return PurchaseData::fromArray(array_merge([
            'supplier_id' => Supplier::factory()->create()->id,
            'purchase_date' => '2026-11-01',
            'items' => [['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 1000, 'selling_price' => null]],
        ], $overrides));
    }

    public static function statuses(array $only): array
    {
        $out = [];
        foreach ($only as $status) {
            $out[$status->value] = [$status];
        }
        return $out;
    }

    // ---------------------------------------------------------------- T1 create

    public function test_create_purchase_stores_items_and_computes_total(): void
    {
        $p1 = $this->makeProduct();
        $p2 = $this->makeProduct();

        $purchase = $this->service->createPurchase(PurchaseData::fromArray([
            'supplier_id' => Supplier::factory()->create()->id,
            'purchase_date' => '2026-11-01',
            'items' => [
                ['product_id' => $p1->id, 'quantity' => 2, 'unit_price' => 1000, 'selling_price' => null],
                ['product_id' => $p2->id, 'quantity' => 3, 'unit_price' => 500, 'selling_price' => 800],
            ],
        ]), $this->user->id);

        $purchase = $purchase->fresh();
        $this->assertSame(PurchaseStatus::DRAFT, $purchase->status);
        $this->assertEquals(3500, $purchase->total);
        $this->assertCount(2, $purchase->items);
        $this->assertSame($this->user->id, $purchase->created_by);
    }

    // ---------------------------------------------------------------- T2 update

    public function test_update_replaces_items_for_editable_purchase(): void
    {
        $purchase = $this->makePurchase(PurchaseStatus::ORDERED);
        $product = $this->makeProduct();

        $this->service->updatePurchase($purchase, PurchaseData::fromArray([
            'supplier_id' => $purchase->supplier_id,
            'purchase_date' => '2026-11-02',
            'invoice_number' => 'INV-UPD',
            'items' => [['product_id' => $product->id, 'quantity' => 4, 'unit_price' => 250, 'selling_price' => null]],
        ]));

        $purchase->refresh();
        $this->assertSame('INV-UPD', $purchase->invoice_number);
        $this->assertEquals(1000, $purchase->total);
        $this->assertSame(PurchaseStatus::ORDERED, $purchase->status);
    }

    public static function nonEditableStatuses(): array
    {
        return self::statuses([PurchaseStatus::RECEIVED, PurchaseStatus::PAID, PurchaseStatus::CANCELLED]);
    }

    #[DataProvider('nonEditableStatuses')]
    public function test_update_rejected_for_non_editable_status(PurchaseStatus $status): void
    {
        $purchase = $this->makePurchase($status);

        $this->expectException(PurchaseException::class);
        $this->expectExceptionMessage("Cannot update purchase with status '{$status->label()}'.");

        $this->service->updatePurchase($purchase, $this->purchaseData());
    }

    // ---------------------------------------------------------------- T3 delete

    public static function deletableStatuses(): array
    {
        return self::statuses([PurchaseStatus::DRAFT, PurchaseStatus::CANCELLED]);
    }

    #[DataProvider('deletableStatuses')]
    public function test_delete_allowed(PurchaseStatus $status): void
    {
        $product = $this->makeProduct();
        $purchase = $this->makePurchase($status, [], [['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 100]]);

        $this->service->deletePurchase($purchase);

        $this->assertDatabaseMissing('purchases', ['id' => $purchase->id]);
        $this->assertDatabaseMissing('purchase_items', ['purchase_id' => $purchase->id]);
    }

    public static function nonDeletableStatuses(): array
    {
        return self::statuses([PurchaseStatus::ORDERED, PurchaseStatus::RECEIVED, PurchaseStatus::PAID]);
    }

    #[DataProvider('nonDeletableStatuses')]
    public function test_delete_rejected(PurchaseStatus $status): void
    {
        $purchase = $this->makePurchase($status);

        $this->expectException(PurchaseException::class);
        $this->expectExceptionMessage(
            "Failed to delete purchase. Cannot delete purchase with status [{$status->label()}]. Only Draft or Cancelled purchases can be deleted."
        );

        $this->service->deletePurchase($purchase);
    }

    // ---------------------------------------------------------------- T4 order

    public function test_mark_as_ordered_from_draft_with_items(): void
    {
        $product = $this->makeProduct();
        $purchase = $this->makePurchase(PurchaseStatus::DRAFT, [], [['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 100]]);

        $this->service->markAsOrdered($purchase);

        $this->assertSame(PurchaseStatus::ORDERED, $purchase->fresh()->status);
    }

    public function test_mark_as_ordered_rejected_without_items(): void
    {
        $purchase = $this->makePurchase(PurchaseStatus::DRAFT);

        $this->expectException(PurchaseException::class);
        $this->expectExceptionMessage('Failed to update purchase: Cannot order a purchase with no items.');

        $this->service->markAsOrdered($purchase);
    }

    public static function nonOrderableStatuses(): array
    {
        return self::statuses([PurchaseStatus::ORDERED, PurchaseStatus::RECEIVED, PurchaseStatus::PAID, PurchaseStatus::CANCELLED]);
    }

    #[DataProvider('nonOrderableStatuses')]
    public function test_mark_as_ordered_rejected_for_status(PurchaseStatus $status): void
    {
        $purchase = $this->makePurchase($status);

        $this->expectException(PurchaseException::class);
        $this->expectExceptionMessage("Cannot order purchase with status '{$status->label()}'.");

        $this->service->markAsOrdered($purchase);
    }

    // ---------------------------------------------------------------- T5 receive: guards

    public static function nonReceivableStatuses(): array
    {
        return self::statuses([PurchaseStatus::RECEIVED, PurchaseStatus::PAID, PurchaseStatus::CANCELLED]);
    }

    #[DataProvider('nonReceivableStatuses')]
    public function test_receive_rejected_for_status(PurchaseStatus $status): void
    {
        $purchase = $this->makePurchase($status, ['invoice_number' => 'INV-1', 'proof_image' => 'proofs/a.jpg']);

        $this->expectException(PurchaseException::class);
        $this->expectExceptionMessage("Cannot receive purchase with status '{$status->label()}'.");

        $this->service->markAsReceived($purchase);
    }

    public function test_receive_requires_invoice_first(): void
    {
        // keduanya kosong -> pesan invoice yang muncul (urutan pengecekan)
        $purchase = $this->makePurchase(PurchaseStatus::ORDERED);

        $this->expectException(PurchaseException::class);
        $this->expectExceptionMessage('Missing required reference: Invoice Number.');

        $this->service->markAsReceived($purchase);
    }

    public function test_receive_requires_proof_image(): void
    {
        $purchase = $this->makePurchase(PurchaseStatus::ORDERED, ['invoice_number' => 'INV-1']);

        $this->expectException(PurchaseException::class);
        $this->expectExceptionMessage('Missing required reference: Proof Image.');

        $this->service->markAsReceived($purchase);
    }

    // ---------------------------------------------------------------- T6-T8 receive: effects

    public function test_receive_from_draft_is_allowed(): void
    {
        $product = $this->makeProduct(['quantity' => 0]);
        $purchase = $this->makePurchase(PurchaseStatus::DRAFT,
            ['invoice_number' => 'INV-D', 'proof_image' => 'proofs/a.jpg'],
            [['product_id' => $product->id, 'quantity' => 2, 'unit_price' => 1000]]);

        $this->service->markAsReceived($purchase);

        $this->assertSame(PurchaseStatus::RECEIVED, $purchase->fresh()->status);
        $this->assertSame(2, $product->fresh()->quantity);
    }

    public function test_receive_updates_stock_prices_and_appends_price_log(): void
    {
        $this->travelTo(Carbon::parse('2026-11-15 10:30:00'));

        $product = $this->makeProduct([
            'quantity' => 10, 'purchase_price' => 1000, 'selling_price' => 1500, 'notes' => 'Catatan awal',
        ]);
        $purchase = $this->makePurchase(PurchaseStatus::ORDERED,
            ['invoice_number' => 'INV-001', 'proof_image' => 'proofs/a.jpg'],
            [['product_id' => $product->id, 'quantity' => 5, 'unit_price' => 1200, 'selling_price' => 1800]]);

        $this->service->markAsReceived($purchase);

        $product->refresh();
        $this->assertSame(15, $product->quantity);
        $this->assertEquals(1200, $product->purchase_price);
        $this->assertEquals(1800, $product->selling_price);
        $this->assertSame(
            "Catatan awal\n\n[System Log - 2026-11-15 10:30] Price update via Invoice #INV-001:"
            . "\n- Buying Price: Rp 1.000 -> Rp 1.200"
            . "\n- Selling Price: Rp 1.500 -> Rp 1.800",
            $product->notes
        );
        $this->assertSame(PurchaseStatus::RECEIVED, $purchase->fresh()->status);
    }

    public function test_receive_price_log_is_trimmed_when_notes_empty(): void
    {
        $this->travelTo(Carbon::parse('2026-11-15 08:00:00'));

        $product = $this->makeProduct(['purchase_price' => 1000, 'notes' => null]);
        $purchase = $this->makePurchase(PurchaseStatus::ORDERED,
            ['invoice_number' => 'INV-002', 'proof_image' => 'proofs/a.jpg'],
            [['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 900]]);

        $this->service->markAsReceived($purchase);

        $this->assertSame(
            "[System Log - 2026-11-15 08:00] Price update via Invoice #INV-002:\n- Buying Price: Rp 1.000 -> Rp 900",
            $product->fresh()->notes
        );
    }

    public function test_receive_without_price_change_keeps_notes(): void
    {
        $product = $this->makeProduct(['purchase_price' => 1000, 'selling_price' => 1500, 'notes' => 'Tetap']);
        $purchase = $this->makePurchase(PurchaseStatus::ORDERED,
            ['invoice_number' => 'INV-003', 'proof_image' => 'proofs/a.jpg'],
            [['product_id' => $product->id, 'quantity' => 3, 'unit_price' => 1000, 'selling_price' => 1500]]);

        $this->service->markAsReceived($purchase);

        $product->refresh();
        $this->assertSame('Tetap', $product->notes);
        $this->assertSame(13, $product->quantity);
    }

    public function test_receive_with_null_selling_price_keeps_selling_price(): void
    {
        $product = $this->makeProduct(['purchase_price' => 1000, 'selling_price' => 1500]);
        $purchase = $this->makePurchase(PurchaseStatus::ORDERED,
            ['invoice_number' => 'INV-004', 'proof_image' => 'proofs/a.jpg'],
            [['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 1000, 'selling_price' => null]]);

        $this->service->markAsReceived($purchase);

        $this->assertEquals(1500, $product->fresh()->selling_price);
        $this->assertNull($product->fresh()->notes);
    }

    // ---------------------------------------------------------------- T9 pay

    public static function payableStatuses(): array
    {
        return self::statuses([PurchaseStatus::ORDERED, PurchaseStatus::RECEIVED]);
    }

    #[DataProvider('payableStatuses')]
    public function test_pay_records_finance_expense(PurchaseStatus $status): void
    {
        $purchase = $this->makePurchase($status, ['invoice_number' => 'INV-P', 'proof_image' => 'proofs/a.jpg', 'total' => 5000]);

        $this->service->markAsPaid($purchase);

        $this->assertSame(PurchaseStatus::PAID, $purchase->fresh()->status);
        $this->assertDatabaseHas('finance_transactions', [
            'reference_type' => Purchase::class,
            'reference_id' => $purchase->id,
            'external_reference' => 'INV-P',
        ]);
        $this->assertDatabaseHas('finance_categories', ['name' => 'Product Purchases']);
    }

    public static function nonPayableStatuses(): array
    {
        return self::statuses([PurchaseStatus::DRAFT, PurchaseStatus::PAID, PurchaseStatus::CANCELLED]);
    }

    #[DataProvider('nonPayableStatuses')]
    public function test_pay_rejected_for_status(PurchaseStatus $status): void
    {
        $purchase = $this->makePurchase($status, ['invoice_number' => 'INV-P', 'proof_image' => 'proofs/a.jpg']);

        $this->expectException(PurchaseException::class);
        $this->expectExceptionMessage("Cannot pay purchase with status '{$status->label()}'.");

        $this->service->markAsPaid($purchase);
    }

    public function test_pay_requires_references(): void
    {
        $purchase = $this->makePurchase(PurchaseStatus::RECEIVED, ['invoice_number' => 'INV-P']);

        $this->expectException(PurchaseException::class);
        $this->expectExceptionMessage('Missing required reference: Proof Image.');

        $this->service->markAsPaid($purchase);
    }

    // ---------------------------------------------------------------- T10 cancel

    public static function cancellableStatuses(): array
    {
        // CATATAN: CANCELLED -> CANCELLED diizinkan oleh kode awal (perilaku dipertahankan)
        return self::statuses([PurchaseStatus::DRAFT, PurchaseStatus::ORDERED, PurchaseStatus::CANCELLED]);
    }

    #[DataProvider('cancellableStatuses')]
    public function test_cancel_allowed_and_voids_finance(PurchaseStatus $status): void
    {
        $purchase = $this->makePurchase($status, ['invoice_number' => 'INV-C']);
        app(FinanceTransactionService::class)->recordExpenseFromPurchase($purchase);

        $this->service->cancelPurchase($purchase);

        $this->assertSame(PurchaseStatus::CANCELLED, $purchase->fresh()->status);
        $this->assertDatabaseMissing('finance_transactions', [
            'reference_type' => Purchase::class,
            'reference_id' => $purchase->id,
        ]);
    }

    public static function nonCancellableStatuses(): array
    {
        return self::statuses([PurchaseStatus::RECEIVED, PurchaseStatus::PAID]);
    }

    #[DataProvider('nonCancellableStatuses')]
    public function test_cancel_rejected(PurchaseStatus $status): void
    {
        $purchase = $this->makePurchase($status);

        $this->expectException(PurchaseException::class);
        $this->expectExceptionMessage("Cannot cancel purchase with status '{$status->label()}'.");

        $this->service->cancelPurchase($purchase);
    }

    // ---------------------------------------------------------------- T11 restore

    public function test_restore_from_cancelled_to_draft(): void
    {
        $purchase = $this->makePurchase(PurchaseStatus::CANCELLED);

        $this->service->restoreToDraft($purchase);

        $this->assertSame(PurchaseStatus::DRAFT, $purchase->fresh()->status);
    }

    public static function nonRestorableStatuses(): array
    {
        return self::statuses([PurchaseStatus::DRAFT, PurchaseStatus::ORDERED, PurchaseStatus::RECEIVED, PurchaseStatus::PAID]);
    }

    #[DataProvider('nonRestorableStatuses')]
    public function test_restore_rejected(PurchaseStatus $status): void
    {
        $purchase = $this->makePurchase($status);

        $this->expectException(PurchaseException::class);
        $this->expectExceptionMessage("Cannot restore purchase with status '{$status->label()}'.");

        $this->service->restoreToDraft($purchase);
    }
}
