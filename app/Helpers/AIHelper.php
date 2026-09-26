<?php

namespace App\Helpers;

use App\Services\AIService;
use Illuminate\Support\Facades\App;

class AIHelper
{
    protected static $service;

    protected static function getService()
    {
        if (!self::$service) {
            self::$service = App::make(AIService::class);
        }
        return self::$service;
    }

    /**
     * Check if AI is available
     */
    public static function isAvailable()
    {
        return self::getService()->isRunning();
    }

    /**
     * Get task recommendations
     */
    public static function getTaskRecommendations($task, $project)
    {
        return self::getService()->getTaskRecommendations($task, $project);
    }

    /**
     * Analyze project health
     */
    public static function analyzeProjectHealth($project)
    {
        return self::getService()->analyzeProjectHealth($project);
    }

    /**
     * Analyze team workload
     */
    public static function analyzeWorkload($users)
    {
        return self::getService()->analyzeWorkload($users);
    }

    /**
     * Get rebalance suggestions
     */
    public static function rebalanceTeam($users)
    {
        return self::getService()->rebalanceTeam($users);
    }
}