<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class SellerRecommendationService
{
    /**
     * Calculate match score for a seller based on experience, location, and other factors
     * 
     * @param User $seller The seller to score
     * @param float|null $clientLat Client's latitude
     * @param float|null $clientLng Client's longitude
     * @return array ['score' => float, 'breakdown' => array]
     */
    public function calculateMatchScore(User $seller, $clientLat = null, $clientLng = null): array
    {
        $score = 0;
        $maxScore = 100;
        $breakdown = [];

        // 1. Experience Score (30 points max)
        $experienceScore = $this->calculateExperienceScore($seller);
        $score += $experienceScore;
        $breakdown['experience'] = [
            'score' => round($experienceScore, 2),
            'max' => 30,
            'percentage' => round(($experienceScore / 30) * 100, 1)
        ];

        // 2. Location/Distance Score (25 points max)
        $locationScore = $this->calculateLocationScore($seller, $clientLat, $clientLng);
        $score += $locationScore;
        $breakdown['location'] = [
            'score' => round($locationScore, 2),
            'max' => 25,
            'percentage' => round(($locationScore / 25) * 100, 1),
            'distance' => $this->calculateDistance($seller, $clientLat, $clientLng)
        ];

        // 3. Ratings/Reviews Score (20 points max)
        $ratingsScore = $this->calculateRatingsScore($seller);
        $score += $ratingsScore;
        $breakdown['ratings'] = [
            'score' => round($ratingsScore, 2),
            'max' => 20,
            'percentage' => round(($ratingsScore / 20) * 100, 1)
        ];

        // 4. Verification Status Score (10 points max - removed insured/vaccinated)
        $verificationScore = $this->calculateVerificationScore($seller);
        $score += $verificationScore;
        $breakdown['verification'] = [
            'score' => round($verificationScore, 2),
            'max' => 10,
            'percentage' => round(($verificationScore / 10) * 100, 1)
        ];

        // 5. Profile Completeness Score (15 points max - increased to compensate)
        $profileScore = $this->calculateProfileCompletenessScore($seller);
        $score += $profileScore;
        $breakdown['profile'] = [
            'score' => round($profileScore, 2),
            'max' => 15,
            'percentage' => round(($profileScore / 15) * 100, 1)
        ];

        // Calculate final percentage
        $finalPercentage = min(100, round(($score / $maxScore) * 100, 1));

        return [
            'score' => round($score, 2),
            'max_score' => $maxScore,
            'percentage' => $finalPercentage,
            'breakdown' => $breakdown
        ];
    }

    /**
     * Calculate experience score (0-30 points)
     */
    private function calculateExperienceScore(User $seller): float
    {
        $score = 0;

        // Years of experience (0-15 points)
        $yearsExperience = $seller->years_experience ?? 0;
        if ($yearsExperience > 0) {
            // Cap at 15 points for 10+ years
            $score += min(15, ($yearsExperience / 10) * 15);
        }

        // Experience types/qualifications (0-10 points)
        $experienceCount = $seller->experiences()->count();
        $score += min(10, ($experienceCount / 5) * 10); // Max 10 points for 5+ experiences

        // Education (0-5 points)
        $educationCount = $seller->educations()->count();
        $score += min(5, ($educationCount / 3) * 5); // Max 5 points for 3+ educations

        return min(30, $score);
    }

    /**
     * Calculate location/distance score (0-25 points)
     */
    private function calculateLocationScore(User $seller, $clientLat, $clientLng): float
    {
        if (!$clientLat || !$clientLng || !$seller->lat || !$seller->long) {
            // If no location data, give base score
            return 10;
        }

        $distance = $this->calculateDistance($seller, $clientLat, $clientLng);

        if ($distance === null) {
            return 10;
        }

        // Score based on distance (in miles)
        // 0-3 miles: 25 points
        // 3-6 miles: 20 points
        // 6-12 miles: 15 points
        // 12-19 miles: 10 points
        // 19-31 miles: 5 points
        // 31+ miles: 2 points

        if ($distance <= 3) {
            return 25;
        } elseif ($distance <= 6) {
            return 20;
        } elseif ($distance <= 12) {
            return 15;
        } elseif ($distance <= 19) {
            return 10;
        } elseif ($distance <= 31) {
            return 5;
        } else {
            return 2;
        }
    }

    /**
     * Calculate distance between seller and client in miles
     */
    private function calculateDistance(User $seller, $clientLat, $clientLng): ?float
    {
        if (!$seller->lat || !$seller->long || !$clientLat || !$clientLng) {
            return null;
        }

        $earthRadius = 3959; // Earth's radius in miles

        $latFrom = deg2rad($seller->lat);
        $lonFrom = deg2rad($seller->long);
        $latTo = deg2rad($clientLat);
        $lonTo = deg2rad($clientLng);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return round($distance, 2);
    }

    /**
     * Calculate ratings/reviews score (0-20 points)
     */
    private function calculateRatingsScore(User $seller): float
    {
        $avgRating = $seller->reviews_avg ?? 0;
        $reviewCount = $seller->reviews_count ?? 0;

        if ($reviewCount == 0) {
            return 5; // Base score for no reviews
        }

        // Rating score (0-15 points based on 5-star rating)
        $ratingScore = ($avgRating / 5) * 15;

        // Review count bonus (0-5 points)
        // More reviews = more reliable
        $countScore = min(5, ($reviewCount / 10) * 5); // Max 5 points for 10+ reviews

        return min(20, $ratingScore + $countScore);
    }

    /**
     * Calculate verification status score (0-10 points - removed insured/vaccinated)
     */
    private function calculateVerificationScore(User $seller): float
    {
        $score = 0;

        // Approved status (7 points)
        if ($seller->approved ?? false) {
            $score += 7;
        }

        // DBS check (3 points)
        if ($seller->dbs ?? false) {
            $score += 3;
        }

        return min(10, $score);
    }

    /**
     * Calculate profile completeness score (0-15 points - increased)
     */
    private function calculateProfileCompletenessScore(User $seller): float
    {
        $score = 0;
        $checks = 0;
        $maxChecks = 10;

        // Profile photo
        if ($seller->profile) {
            $score += 1;
        }
        $checks++;

        // Bio/Info
        if ($seller->info && strlen($seller->info) > 50) {
            $score += 1;
        }
        $checks++;

        // Phone
        if ($seller->phone) {
            $score += 1;
        }
        $checks++;

        // Address
        if ($seller->address) {
            $score += 1;
        }
        $checks++;

        // Postcode
        if ($seller->postcode) {
            $score += 1;
        }
        $checks++;

        // Languages
        if ($seller->languages()->count() > 0) {
            $score += 1;
        }
        $checks++;

        // Skills
        if ($seller->skills()->count() > 0) {
            $score += 1;
        }
        $checks++;

        // Availability/Days
        if ($seller->days()->count() > 0) {
            $score += 1;
        }
        $checks++;

        // References
        if ($seller->referee1_name || $seller->referee2_name) {
            $score += 1;
        }
        $checks++;

        // Documents
        if ($seller->documents()->count() > 0) {
            $score += 1;
        }
        $checks++;

        return ($score / $maxChecks) * 15;
    }

    /**
     * Rank and sort sellers by match score
     * 
     * @param Collection $sellers
     * @param float|null $clientLat
     * @param float|null $clientLng
     * @return Collection
     */
    public function rankSellers(Collection $sellers, $clientLat = null, $clientLng = null): Collection
    {
        return $sellers->map(function ($seller) use ($clientLat, $clientLng) {
            $matchData = $this->calculateMatchScore($seller, $clientLat, $clientLng);
            $seller->match_score = $matchData['score'];
            $seller->match_percentage = $matchData['percentage'];
            $seller->match_breakdown = $matchData['breakdown'];
            return $seller;
        })->sortByDesc('match_score')->values();
    }
}

