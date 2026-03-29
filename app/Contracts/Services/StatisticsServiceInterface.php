<?php

declare(strict_types=1);

namespace App\Contracts\Services;

interface StatisticsServiceInterface
{
    public function getOrderStatistics();

    public function getProductStatistics();
}
