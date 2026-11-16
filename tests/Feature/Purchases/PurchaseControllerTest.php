<?php

namespace Tests\Feature\Purchases;

use App\Enums\PurchaseStatus;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PurchaseControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Storage::fake('public');
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    private function makePurchase(PurchaseStatus $status = PurchaseStatus::DRAFT, array $attrs = [], bool $withItem = true): Purchase
    {
        $purchase = Purchase::create(array_merge([
            'supplier_id' => Supplier::factory()->create()->id,
            'purchase_date' => '2026-11-01',
            'status' => $status,
            'created_by' => $this->user->id,
            'total' => 0,
        ], $attrs));

        if ($withItem) {
            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id' => Product::factory()->create()->id,
                'quantity' => 1,
                'unit_price' => 100,
                'subtotal' => 100,
            ]);
        }

        return $purchase->fresh();
    }

    private function formPayload(array $overrides = []): array
    {
        return array_merge([
            'supplier_id' => Supplier::factory()->create()->id,
            'purchase_date' => '2026-11-01',
            'items' => [[
                'product_id' => Product::factory()->create()->id,
                'quantity' => 2,
                'unit_price' => 1000,
                'selling_price' => null,
            ]],
        ], $overrides);
    }

    // T12
    public function test_edit_forbidden_for_received(): void
    {
        $purchase = $this->makePurchase(PurchaseStatus::RECEIVED);

        $this->get(route('purchases.edit', $purchase))->assertForbidden();
    }

    public function test_edit_page_opens_for_ordered(): void
    {
        $purchase = $this->makePurchase(PurchaseStatus::ORDERED);

        $this->get(route('purchases.edit', $purchase))->assertOk();
    }

    // T13
    public function test_rejected_action_redirects_back_with_exact_error(): void
    {
        $purchase = $this->makePurchase(PurchaseStatus::ORDERED);

        $this->from(route('purchases.show', $purchase))
            ->patch(route('purchases.mark-ordered', $purchase))
            ->assertRedirect(route('purchases.show', $purchase))
            ->assertSessionHas('error', "Cannot order purchase with status 'Ordered'.");
    }

    public function test_success_messages_for_status_actions(): void
    {
        $draft = $this->makePurchase(PurchaseStatus::DRAFT);
        $this->from(route('purchases.show', $draft))
            ->patch(route('purchases.mark-ordered', $draft))
            ->assertSessionHas('success', 'Purchase marked as ordered.');

        $this->from(route('purchases.show', $draft))
            ->patch(route('purchases.cancel', $draft))
            ->assertSessionHas('success', 'Purchase order cancelled.');

        $this->from(route('purchases.show', $draft))
            ->patch(route('purchases.restore-draft', $draft))
            ->assertSessionHas('success', 'Purchase restored to draft.');

        $received = $this->makePurchase(PurchaseStatus::RECEIVED, ['invoice_number' => 'INV-R', 'proof_image' => 'proofs/x.jpg']);
        $this->from(route('purchases.show', $received))
            ->patch(route('purchases.mark-paid', $received))
            ->assertSessionHas('success', 'Purchase marked as paid.');
    }

    // T14
    public function test_mark_received_requires_invoice_and_proof(): void
    {
        $purchase = $this->makePurchase(PurchaseStatus::ORDERED);

        $this->from(route('purchases.show', $purchase))
            ->patch(route('purchases.mark-received', $purchase), [])
            ->assertSessionHasErrors(['invoice_number', 'proof_image']);

        $this->assertSame(PurchaseStatus::ORDERED, $purchase->fresh()->status);
    }

    public function test_mark_received_with_upload_stores_proof(): void
    {
        $purchase = $this->makePurchase(PurchaseStatus::ORDERED);

        $this->from(route('purchases.show', $purchase))
            ->patch(route('purchases.mark-received', $purchase), [
                'invoice_number' => 'INV-9',
                'proof_image' => UploadedFile::fake()->create('bukti.jpg', 100, 'image/jpeg'),
            ])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Purchase received and stock updated.');

        $purchase->refresh();
        $this->assertSame('INV-9', $purchase->invoice_number);
        $this->assertStringStartsWith('proofs/', $purchase->proof_image);
        Storage::disk('public')->assertExists($purchase->proof_image);
        $this->assertSame(PurchaseStatus::RECEIVED, $purchase->status);
    }

    // T15
    public function test_store_without_items_fails_validation(): void
    {
        $this->post(route('purchases.store'), $this->formPayload(['items' => []]))
            ->assertSessionHasErrors(['items' => 'Please add at least one item.']);
    }

    public function test_store_forces_draft_and_redirects_to_show(): void
    {
        $response = $this->post(route('purchases.store'), $this->formPayload(['status' => 'paid']));

        $purchase = Purchase::latest('id')->first();
        $response->assertRedirect(route('purchases.show', $purchase))
            ->assertSessionHas('success', 'Purchase created successfully.');
        $this->assertSame(PurchaseStatus::DRAFT, $purchase->status);
        $this->assertEquals(2000, $purchase->total);
    }

    public function test_store_with_proof_image_saves_file(): void
    {
        $this->post(route('purchases.store'), $this->formPayload([
            'proof_image' => UploadedFile::fake()->create('bukti.jpg', 50, 'image/jpeg'),
        ]));

        $purchase = Purchase::latest('id')->first();
        $this->assertStringStartsWith('proofs/', $purchase->proof_image);
    }

    // T16
    public function test_update_accepts_own_invoice_number(): void
    {
        $purchase = $this->makePurchase(PurchaseStatus::DRAFT, ['invoice_number' => 'INV-1', 'proof_image' => 'proofs/old.jpg']);

        $this->put(route('purchases.update', $purchase), $this->formPayload(['invoice_number' => 'INV-1']))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('purchases.show', $purchase))
            ->assertSessionHas('success', 'Purchase updated successfully.');

        // tanpa upload baru, bukti lama dipertahankan
        $this->assertSame('proofs/old.jpg', $purchase->fresh()->proof_image);
    }

    public function test_update_rejects_invoice_of_other_purchase(): void
    {
        $this->makePurchase(PurchaseStatus::DRAFT, ['invoice_number' => 'INV-TAKEN']);
        $purchase = $this->makePurchase(PurchaseStatus::DRAFT);

        $this->put(route('purchases.update', $purchase), $this->formPayload(['invoice_number' => 'INV-TAKEN']))
            ->assertSessionHasErrors('invoice_number');
    }

    public function test_update_received_returns_error_with_input(): void
    {
        $purchase = $this->makePurchase(PurchaseStatus::RECEIVED);

        $this->from(route('purchases.edit', $purchase))
            ->put(route('purchases.update', $purchase), $this->formPayload(['invoice_number' => 'INV-X']))
            ->assertRedirect(route('purchases.edit', $purchase))
            ->assertSessionHas('error', "Cannot update purchase with status 'Received'.")
            ->assertSessionHasInput('invoice_number', 'INV-X');
    }

    public function test_destroy_draft_redirects_to_index(): void
    {
        $purchase = $this->makePurchase(PurchaseStatus::DRAFT);

        $this->delete(route('purchases.destroy', $purchase))
            ->assertRedirect(route('purchases.index'))
            ->assertSessionHas('success', 'Purchase deleted successfully.');
    }

    public function test_destroy_paid_returns_error(): void
    {
        $purchase = $this->makePurchase(PurchaseStatus::PAID);

        $this->from(route('purchases.show', $purchase))
            ->delete(route('purchases.destroy', $purchase))
            ->assertRedirect(route('purchases.show', $purchase))
            ->assertSessionHas('error', 'Failed to delete purchase. Cannot delete purchase with status [Paid]. Only Draft or Cancelled purchases can be deleted.');
    }
}
