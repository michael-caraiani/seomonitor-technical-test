<?php declare(strict_types=1);

namespace App\Controller;

use App\Dto\InsightsRequestDto;
use App\Service\SeoInsightsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('seo-insights', name: 'seo_insights', methods: ['POST'])]
class SeoInsightsController extends AbstractController
{
    public function __construct(
        private readonly SeoInsightsService $seoInsightsService,
    ) {
    }

    public function __invoke(
        #[MapRequestPayload] InsightsRequestDto $requestDto,
    ): JsonResponse {
        $response = $this->seoInsightsService->generateSeoInsights($requestDto->domain, $requestDto->keywords);

        return $this->json($response);
    }
}
