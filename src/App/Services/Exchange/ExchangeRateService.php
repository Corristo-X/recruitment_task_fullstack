<?php

declare(strict_types=1);

namespace App\Services\Exchange;

class ExchangeRateService
{
    public function calculateRates(array $nbpRates): array
    {
        $calculatedRates = [];
        foreach ($nbpRates as $currencyCode => $rateData) {
            $buyRate = null;
            if (in_array($currencyCode, ['EUR', 'USD'])) {
                $buyRate = $rateData['mid'] - 0.05;
                $sellRate = $rateData['mid'] + 0.07;
            } else {
                $sellRate = $rateData['mid'] + 0.15;
            }
            $calculatedRates[$currencyCode] = [
                'currency' => $rateData['currency'],
                'nbp' => $rateData['mid'],
                'buy' => $buyRate,
                'sell' => $sellRate
            ];
        }

        return $calculatedRates;
    }
}
