<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;


class ProcessOrders extends Command
{

    protected $signature = 'update:order:status';
    protected $description = 'Process the pending orders';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pendingOrders= Order::where('status' ,'pending')->get();

        foreach($pendingOrders as $order) {
            $this->info('Order id ' . $order->id . ' processing');
            $order->update(['status' => 'processing']);

            sleep(5);

            $order->update(['status' => 'completed']);
        }
        $this->info('All orders status updated');

        return Command::SUCCESS;


    }
}
