<?php
/**
 * Quick Pricing System Test Script
 * 
 * Run this from command line: php test-pricing.php
 * Or access via browser if placed in public folder
 * 
 * This script tests the pricing calculations without needing to create actual jobs
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Contract;
use App\Models\Role;

echo "========================================\n";
echo "PRICING SYSTEM TEST SCRIPT\n";
echo "========================================\n\n";

// Test 1: Carer Job - Above Minimum
echo "TEST 1: Carer Job - £30/hour (Above Minimum)\n";
echo "----------------------------------------\n";
$contract = new Contract();
$contract->role_id = 3; // Carer
$contract->hourly_rate = 30.00;

$clientRate = $contract->hourly_rate;
$providerRate = $contract->getProviderHourlyRate();
$platformFee = $contract->getPlatformFeeHourly();
$minimumRate = $contract->getMinimumProviderRate();

echo "Client Rate:        £" . number_format($clientRate, 2) . "/hr\n";
echo "Provider Rate:      £" . number_format($providerRate, 2) . "/hr (66.6%)\n";
echo "Platform Fee:       £" . number_format($platformFee, 2) . "/hr (33.3333%)\n";
echo "Minimum Rate:       £" . number_format($minimumRate, 2) . "/hr\n";
echo "Total Check:         £" . number_format($providerRate + $platformFee, 2) . " (should equal £" . number_format($clientRate, 2) . ")\n";
echo "Status: " . (abs(($providerRate + $platformFee) - $clientRate) < 0.01 ? "✅ PASS" : "❌ FAIL") . "\n";
echo "Minimum Enforced:   " . ($providerRate >= $minimumRate ? "No ✅" : "Yes ⚠️") . "\n\n";

// Test 2: Carer Job - Below Minimum
echo "TEST 2: Carer Job - £25/hour (Below Minimum)\n";
echo "----------------------------------------\n";
$contract2 = new Contract();
$contract2->role_id = 3; // Carer
$contract2->hourly_rate = 25.00;

$clientRate2 = $contract2->hourly_rate;
$providerRate2 = $contract2->getProviderHourlyRate();
$platformFee2 = $contract2->getPlatformFeeHourly();
$minimumRate2 = $contract2->getMinimumProviderRate();
$calculatedRate = ($clientRate2 * 66.6667) / 100;

echo "Client Rate:        £" . number_format($clientRate2, 2) . "/hr\n";
echo "Calculated (66.6%):  £" . number_format($calculatedRate, 2) . "/hr\n";
echo "Provider Rate:      £" . number_format($providerRate2, 2) . "/hr (minimum enforced)\n";
echo "Platform Fee:       £" . number_format($platformFee2, 2) . "/hr (adjusted)\n";
echo "Minimum Rate:       £" . number_format($minimumRate2, 2) . "/hr\n";
echo "Total Check:         £" . number_format($providerRate2 + $platformFee2, 2) . " (should equal £" . number_format($clientRate2, 2) . ")\n";
echo "Status: " . (abs(($providerRate2 + $platformFee2) - $clientRate2) < 0.01 ? "✅ PASS" : "❌ FAIL") . "\n";
echo "Minimum Enforced:   " . ($providerRate2 >= $minimumRate2 && $calculatedRate < $minimumRate2 ? "Yes ✅" : "No ⚠️") . "\n\n";

// Test 3: Housekeeper Job - Below Minimum
echo "TEST 3: Housekeeper Job - £20/hour (Below Minimum)\n";
echo "----------------------------------------\n";
$contract3 = new Contract();
$contract3->role_id = 5; // Housekeeper
$contract3->hourly_rate = 20.00;

$clientRate3 = $contract3->hourly_rate;
$providerRate3 = $contract3->getProviderHourlyRate();
$platformFee3 = $contract3->getPlatformFeeHourly();
$minimumRate3 = $contract3->getMinimumProviderRate();
$calculatedRate3 = ($clientRate3 * 66.6667) / 100;

echo "Client Rate:        £" . number_format($clientRate3, 2) . "/hr\n";
echo "Calculated (66.6%):  £" . number_format($calculatedRate3, 2) . "/hr\n";
echo "Provider Rate:      £" . number_format($providerRate3, 2) . "/hr (minimum enforced)\n";
echo "Platform Fee:       £" . number_format($platformFee3, 2) . "/hr (adjusted)\n";
echo "Minimum Rate:       £" . number_format($minimumRate3, 2) . "/hr\n";
echo "Total Check:         £" . number_format($providerRate3 + $platformFee3, 2) . " (should equal £" . number_format($clientRate3, 2) . ")\n";
echo "Status: " . (abs(($providerRate3 + $platformFee3) - $clientRate3) < 0.01 ? "✅ PASS" : "❌ FAIL") . "\n";
echo "Minimum Enforced:   " . ($providerRate3 >= $minimumRate3 && $calculatedRate3 < $minimumRate3 ? "Yes ✅" : "No ⚠️") . "\n\n";

// Test 4: Pricing Breakdown
echo "TEST 4: Pricing Breakdown Method\n";
echo "----------------------------------------\n";
$breakdown = $contract->getPricingBreakdown();
echo "Client Rate:        £" . number_format($breakdown['client_rate'], 2) . "/" . $breakdown['type'] . "\n";
echo "Provider Rate:       £" . number_format($breakdown['provider_rate'], 2) . "/" . $breakdown['type'] . "\n";
echo "Platform Fee:       £" . number_format($breakdown['platform_fee'], 2) . "/" . $breakdown['type'] . "\n";
echo "Total Check:         £" . number_format($breakdown['provider_rate'] + $breakdown['platform_fee'], 2) . " (should equal £" . number_format($breakdown['client_rate'], 2) . ")\n";
echo "Status: " . (abs(($breakdown['provider_rate'] + $breakdown['platform_fee']) - $breakdown['client_rate']) < 0.01 ? "✅ PASS" : "❌ FAIL") . "\n\n";

// Test 5: Percentage Verification
echo "TEST 5: Percentage Verification\n";
echo "----------------------------------------\n";
$testRates = [30, 25, 20, 50, 15];
foreach ($testRates as $rate) {
    $testContract = new Contract();
    $testContract->role_id = 3; // Carer
    $testContract->hourly_rate = $rate;
    
    $provider = $testContract->getProviderHourlyRate();
    $platform = $testContract->getPlatformFeeHourly();
    $client = $testContract->hourly_rate;
    
    $providerPercent = ($provider / $client) * 100;
    $platformPercent = ($platform / $client) * 100;
    
    echo "Client: £" . number_format($client, 2) . " → Provider: £" . number_format($provider, 2) . " (" . number_format($providerPercent, 1) . "%) | Platform: £" . number_format($platform, 2) . " (" . number_format($platformPercent, 1) . "%)\n";
}

echo "\n========================================\n";
echo "TEST COMPLETE\n";
echo "========================================\n";
echo "\nNext Steps:\n";
echo "1. Test in browser with actual jobs\n";
echo "2. Test timesheet payment generation\n";
echo "3. Verify email notifications\n";
echo "4. Check admin views show correct breakdown\n";


