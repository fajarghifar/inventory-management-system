<?php

namespace App\Console\Commands;

use App\Models\Sale;
use App\Models\Purchase;
use App\Enums\SaleStatus;
use App\Enums\PurchaseStatus;
use Illuminate\Console\Command;
use App\Services\FinanceTransactionService;
use Illuminate\Support\Facades\DB;

class SyncFinanceTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finance:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync existing Sales and Purchases to Finance Transactions (Ledger)';

    public function __construct(
        protected FinanceTransactionService $financeService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Finance Ledger Sync...');

        $sales = Sale::where('status', SaleStatus::COMPLETED)->get();
        $purchases = Purchase::where('status', PurchaseStatus::PAID)->get();

        $total = $sales->count() + $purchases->count();
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        DB::transaction(function () use ($sales, $purchases, $bar) {
            foreach ($sales as $sale) {
                try {
                    $this->financeService->recordIncomeFromSale($sale);
                } catch (\Exception $e) {
                    $this->error("Failed to sync Sale ID {$sale->id}: " . $e->getMessage());
                    throw $e; // Stop transaction
                }
                $bar->advance();
            }

            foreach ($purchases as $purchase) {
                try {
                    $this->financeService->recordExpenseFromPurchase($purchase);
                } catch (\Exception $e) {
                    $this->error("Failed to sync Purchase ID {$purchase->id}: " . $e->getMessage());
                    throw $e;
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info('Finance Ledger Sync Completed Successfully!');
    }
}
