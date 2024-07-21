<?php

declare(strict_types=1);

namespace App\Controller\Exchange;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Service\Exchange\NbpApiService;
use App\Service\Exchange\ExchangeRateService;

class ExchangeRatesController extends AbstractController
{
    private $nbpApiService;
    private $exchangeRateService;

    public function __construct(NbpApiService $nbpApiService, ExchangeRateService $exchangeRateService)
    {
        $this->nbpApiService = $nbpApiService;
        $this->exchangeRateService = $exchangeRateService;
    }

    public function index(Request $request, $date): JsonResponse
    {
        $currencies = ['EUR', 'USD', 'CZK', 'IDR', 'BRL'];
        $nbpRates = $this->nbpApiService->fetchNbpRates($currencies, $date);

        $currentDay = (new DateTime())->format('Y-m-d');
        $currentNbpRates = $this->nbpApiService->fetchNbpRates($currencies, $currentDay);

        $rates = $this->exchangeRateService->calculateRates($nbpRates);
        $currentRates = $this->exchangeRateService->calculateRates($currentNbpRates);

        return new JsonResponse([
            'date' => $date,
            'rates' => $rates,
            'current_rates' => $currentRates
        ]);
    }
}
