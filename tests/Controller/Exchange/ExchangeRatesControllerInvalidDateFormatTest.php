<?php

declare(strict_types=1);

namespace Controller\Exchange;

use App\Controller\Exchange\ExchangeRatesController;
use App\Services\Exchange\ExchangeRateService;
use App\Services\Exchange\NbpApiService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ExchangeRatesControllerInvalidDateFormatTest extends TestCase
{
    public function testIndexWithInvalidDateFormat()
    {
        $nbpApiService = $this->createMock(NbpApiService::class);
        $exchangeRateService = $this->createMock(ExchangeRateService::class);

        $controller = new ExchangeRatesController($nbpApiService, $exchangeRateService);

        $request = new Request();

        $invalidDate = '01-01-2024';

        $response = $controller->index($request, $invalidDate);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['error' => 'Invalid date format or date out of range']),
            $response->getContent()
        );
    }
}
