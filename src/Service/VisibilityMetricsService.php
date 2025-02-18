<?php declare(strict_types=1);

namespace App\Service;

class VisibilityMetricsService
{
    public function __construct(
        private readonly SearchVolumeService $searchVolumeService,
        private readonly SerpService $serpService,
    ) {
    }

    public function calculate(string $domain, array $keywords): array
    {
        $searchVolume = $this->searchVolumeService->getSearchVolumeForKeywords($keywords);
        $serpData = $this->serpService->getPositionDomainForKeywords($domain, $keywords);

        $seoData = $this->addDomainPositionToSearchVolume($serpData, $searchVolume);

        return $this->visibilityMetricsCalculation($seoData);
    }

    private function addDomainPositionToSearchVolume(array $serpData, array $searchVolumeData): array
    {
        $data = [];
        foreach ($serpData as $serp) {
            foreach ($searchVolumeData as $volume) {
                if ($serp['keyword'] === $volume['keyword']) {
                    $data[] = [
                        'keyword' => $volume['keyword'],
                        'search_volume' => $volume['search_volume'],
                        'position' => $serp['position'],
                    ];
                }
            }
        }

        return $data;
    }

    private function visibilityMetricsCalculation(array $data): array
    {
        $list = [];
        $totalSearchVolume = 0;
        $totalVScore = 0;
        $totalCount = 0;
        foreach ($data as $item) {
            $searchVolume = $item['search_volume'];
            $vIndex = $searchVolume * $this->ctr($item['position']);
            $vScore = round($vIndex / $searchVolume, 2);

            $list[] = [
                'name' => $item['keyword'],
                'position' => strval($item['position']),
                'vscore' => $vScore,
                'search_volume' => $searchVolume,
            ];

            $totalSearchVolume += $searchVolume;
            $totalVScore += $vScore;
            ++$totalCount;
        }

        return [
            'totals' => [
                'total_vscore' => round($totalVScore, 2),
                'count' => $totalCount,
                'total_search_volume' => $totalSearchVolume,
            ],
            'list' => $list,
        ];
    }

    private function ctr(int $position): float|int
    {
        $ctrValues = [
            1 => 1,
            2 => 0.95,
            3 => 0.90,
            4 => 0.75,
            5 => 0.70,
            6 => 0.65,
            7 => 0.60,
            8 => 0.55,
            9 => 0.50,
            10 => 0.45,
            11 => 0.28,
            12 => 0.26,
            13 => 0.24,
            14 => 0.22,
            15 => 0.20,
            16 => 0.18,
            17 => 0.16,
            18 => 0.14,
            19 => 0.12,
            20 => 0.10,
        ];

        return $ctrValues[$position] ?? 0;
    }
}
