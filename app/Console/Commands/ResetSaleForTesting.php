<?php

namespace App\Console\Commands;

use App\Models\Sale;
use Illuminate\Console\Command;

class ResetSaleForTesting extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:reset-sale {sale_id}';

    /**
     * The console command description.
     */
    protected $description = 'Reset a sale to deposit_paid status for testing the auto-movement';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $saleId = $this->argument('sale_id');
        $sale = Sale::find($saleId);
        
        if (!$sale) {
            $this->error("Sale with ID {$saleId} not found!");
            return 1;
        }

        $this->info("Current sale status:");
        $this->info("Payment status: {$sale->payment_status}");
        $this->info("Sale price: €" . number_format($sale->sale_price, 2));
        $this->info("Deposit: €" . number_format($sale->deposit_amount ?? 0, 2));
        
        if ($this->confirm('Reset this sale to deposit_paid status for testing?')) {
            $sale->update([
                'payment_status' => 'deposit_paid',
                'deposit_amount' => 1000, // Keep some deposit
            ]);
            
            $this->info("✅ Sale {$saleId} has been reset to 'deposit_paid' status.");
            $this->info("You can now test the auto-movement by marking it as fully paid.");
        }
        
        return 0;
    }
}
