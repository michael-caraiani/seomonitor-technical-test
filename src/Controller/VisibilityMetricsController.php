<?php declare(strict_types=1);

namespace App\Controller;

use App\Dto\VisibilityMetricsRequestDto;
use App\Service\VisibilityMetricsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('visibility-metrics', name: 'visibility_metrics', methods: ['POST'])]
class VisibilityMetricsController extends AbstractController
{
    public function __construct(
        private readonly VisibilityMetricsService $visibilityMetricsService,
    ) {
    }

    public function __invoke(
        #[MapRequestPayload] VisibilityMetricsRequestDto $requestDto,
    ): JsonResponse {
        $response = $this->visibilityMetricsService->calculate($requestDto->domain, $requestDto->keywords);

        return $this->json($response);
    }
}
