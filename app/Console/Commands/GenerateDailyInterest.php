<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Investment;
use App\Models\DailyInterest;
use Carbon\Carbon;

class GenerateDailyInterest extends Command
{
    protected $signature = 'interest:generate-daily';
    protected $description = 'Generate daily interest for all active investments';

    public function handle()
    {
        $today = Carbon::now()->format('Y-m-d');

        $investments = Investment::with('select_plan')
            ->where('status', 'active')
            ->get();

        if ($investments->count() == 0) {
            $this->info("No active investments found.");
            return Command::SUCCESS;
        }

        foreach ($investments as $inv) {

            $plan = $inv->select_plan;

            if (!$plan) {
                $this->error("Plan not found for Investment ID: {$inv->id}");
                continue;
            }

            // Prevent duplicate entries
            $already = DailyInterest::where('investment_id', $inv->id)
                ->where('interest_date', $today)
                ->exists();

            if ($already) {
                $this->info("Interest already saved for Investment ID: {$inv->id}");
                continue;
            }

            $secure = floatval($plan->secure_interest_percent);
            $market = floatval($plan->market_interest_percent);

            $totalPercent = $secure + $market;

            $dailyInterest = ($inv->principal_amount * $totalPercent / 100) / 30;

            DailyInterest::create([
                'investment_id'         => $inv->id,
                'investor_id'           => $inv->select_investor_id,
                'plan_id'               => $inv->select_plan_id,
                'principal_amount'      => $inv->principal_amount,
                'daily_interest_amount' => round($dailyInterest, 2),
                'interest_date'         => $today,
            ]);

            $this->info("Interest saved for Investment ID: {$inv->id}");
        }

        return Command::SUCCESS;
    }
}
