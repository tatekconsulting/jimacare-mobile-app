<?php

namespace App\Services;

use App\Models\User;
use App\Models\Contract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CarerMatchingService
{
    protected $weights = [
        'job_type' => 0.25,
        'skills' => 0.20,
        'experience' => 0.15,
        'availability_days' => 0.15,
        'availability_time' => 0.10,
        'location' => 0.10,
        'reviews' => 0.05,
    ];

    /**
     * Find best matching carers for a job
     */
    public function findMatches(array $jobRequirements, int $limit = 10): Collection
    {
        $roleId = $jobRequirements['role_id'] ?? null;
        
        // Get carers of the required type
        $carers = User::where('role_id', $roleId)
            ->where('status', 'active')
            ->where('approved', true)
            ->with(['skills', 'experiences', 'days', 'time_availables', 'reviews'])
            ->get();

        $scoredCarers = $carers->map(function ($carer) use ($jobRequirements) {
            $scores = $this->calculateScores($carer, $jobRequirements);
            $totalScore = $this->calculateTotalScore($scores);
            
            return [
                'carer' => $carer,
                'total_score' => round($totalScore, 1),
                'scores' => $scores,
                'match_reasons' => $this->getMatchReasons($scores, $carer),
            ];
        });

        return $scoredCarers
            ->sortByDesc('total_score')
            ->take($limit)
            ->values();
    }

    /**
     * Calculate individual scores for each factor
     */
    protected function calculateScores(User $carer, array $job): array
    {
        return [
            'job_type' => $this->scoreJobType($carer, $job),
            'skills' => $this->scoreSkills($carer, $job),
            'experience' => $this->scoreExperience($carer, $job),
            'availability_days' => $this->scoreAvailabilityDays($carer, $job),
            'availability_time' => $this->scoreAvailabilityTime($carer, $job),
            'location' => $this->scoreLocation($carer, $job),
            'reviews' => $this->scoreReviews($carer),
        ];
    }

    /**
     * Score job type match (100 if exact match)
     */
    protected function scoreJobType(User $carer, array $job): float
    {
        return ($carer->role_id == ($job['role_id'] ?? 0)) ? 100 : 0;
    }

    /**
     * Score skills using TF-IDF like matching
     */
    protected function scoreSkills(User $carer, array $job): float
    {
        $requiredSkills = $job['skills'] ?? [];
        if (empty($requiredSkills)) {
            return 100; // No requirements = full match
        }

        $carerSkills = $carer->skills->pluck('title')->map(fn($s) => strtolower($s))->toArray();
        $requiredSkills = array_map('strtolower', $requiredSkills);

        $matches = count(array_intersect($carerSkills, $requiredSkills));
        $total = count($requiredSkills);

        return $total > 0 ? ($matches / $total) * 100 : 100;
    }

    /**
     * Score experience using text similarity
     */
    protected function scoreExperience(User $carer, array $job): float
    {
        $jobDescription = strtolower($job['description'] ?? '');
        if (empty($jobDescription)) {
            return 100;
        }

        $carerExperiences = $carer->experiences->pluck('title')->map(fn($e) => strtolower($e))->toArray();
        $carerInfo = strtolower($carer->info ?? '');
        
        // Combine carer text
        $carerText = implode(' ', $carerExperiences) . ' ' . $carerInfo;
        
        // Calculate similarity using keyword matching
        $score = $this->textSimilarity($jobDescription, $carerText);
        
        return $score * 100;
    }

    /**
     * Score availability days
     */
    protected function scoreAvailabilityDays(User $carer, array $job): float
    {
        $requiredDays = $job['days'] ?? [];
        if (empty($requiredDays)) {
            return 100;
        }

        $carerDays = $carer->days->pluck('id')->toArray();
        $matches = count(array_intersect($carerDays, $requiredDays));
        $total = count($requiredDays);

        return $total > 0 ? ($matches / $total) * 100 : 100;
    }

    /**
     * Score availability time
     */
    protected function scoreAvailabilityTime(User $carer, array $job): float
    {
        // Simplified time matching
        $carerTimes = $carer->time_availables->count();
        return $carerTimes > 0 ? 100 : 50;
    }

    /**
     * Score location proximity
     */
    protected function scoreLocation(User $carer, array $job): float
    {
        $jobLat = $job['lat'] ?? null;
        $jobLong = $job['long'] ?? null;
        
        if (!$jobLat || !$jobLong || !$carer->lat || !$carer->long) {
            return 50; // Default score if no location
        }

        $distance = $this->calculateDistance($jobLat, $jobLong, $carer->lat, $carer->long);
        
        // Score based on distance (closer = better)
        if ($distance <= 1) return 100;
        if ($distance <= 3) return 90;
        if ($distance <= 5) return 80;
        if ($distance <= 10) return 60;
        if ($distance <= 20) return 40;
        return 20;
    }

    /**
     * Score reviews
     */
    protected function scoreReviews(User $carer): float
    {
        $avgRating = $carer->reviews->avg('stars') ?? 0;
        $reviewCount = $carer->reviews->count();
        
        // Bonus for more reviews
        $countBonus = min($reviewCount * 2, 20);
        
        return ($avgRating * 20) + $countBonus;
    }

    /**
     * Calculate total weighted score
     */
    protected function calculateTotalScore(array $scores): float
    {
        $total = 0;
        foreach ($scores as $key => $score) {
            $total += $score * ($this->weights[$key] ?? 0);
        }
        return $total;
    }

    /**
     * Get human-readable match reasons
     */
    protected function getMatchReasons(array $scores, User $carer): array
    {
        $reasons = [];

        if ($scores['skills'] >= 80) {
            $reasons[] = 'Skills match your requirements';
        }
        if ($scores['experience'] >= 70) {
            $reasons[] = 'Relevant experience';
        }
        if ($scores['availability_days'] >= 80) {
            $reasons[] = 'Available on your required days';
        }
        if ($scores['location'] >= 80) {
            $reasons[] = 'Located nearby';
        }
        if ($scores['reviews'] >= 80) {
            $reasons[] = 'Highly rated by others';
        }
        if ($carer->approved) {
            $reasons[] = 'Verified & Approved';
        }

        return array_slice($reasons, 0, 3); // Top 3 reasons
    }

    /**
     * Calculate text similarity (TF-IDF simplified)
     */
    protected function textSimilarity(string $text1, string $text2): float
    {
        $words1 = str_word_count(strtolower($text1), 1);
        $words2 = str_word_count(strtolower($text2), 1);

        if (empty($words1) || empty($words2)) {
            return 0.5;
        }

        $intersection = array_intersect($words1, $words2);
        $union = array_unique(array_merge($words1, $words2));

        return count($intersection) / count($union);
    }

    /**
     * Calculate distance between two points (Haversine formula)
     */
    protected function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 3959; // Miles

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }
}
