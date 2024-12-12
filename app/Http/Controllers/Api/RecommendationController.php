<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RecommendationService;

class RecommendationController extends Controller
{
    protected $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    public function getRecommendations(Request $request)
    {
        $user = $request->user();
        $recommendations = $this->recommendationService->getRecommendationsForUser($user);
        return response()->json($recommendations);
    }
}
