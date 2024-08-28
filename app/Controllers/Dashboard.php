<?php

namespace App\Controllers;

use App\Models\ParticipantModel;
use App\Models\StravaActivityModel;
use App\Config\AppConstants;

class Dashboard extends BaseController
{
    public function index()
    {
        $activityModel = new StravaActivityModel();
        $participantModel = new ParticipantModel();

        $startDate = AppConstants::CHALLENGE_START_DATE;
        $endDate = AppConstants::CHALLENGE_END_DATE;
        $activityTypes = ['Walk', 'Run'];

        // Calculate total activities, distance, and participants
        $totalActivities = $this->getTotalActivities($activityModel, $startDate, $endDate, $activityTypes);
        $totalDistance = $participantModel->getTotalDistance();
        $participants = $participantModel->getActiveParticipants();

        // Calculate derived metrics
        $totalDays = $this->calculateTotalDays($startDate, $endDate);
        $averageActivitiesPerParticipant = $this->calculateAverageActivities($totalActivities, $participants);
        $averageDistance = $this->calculateAverageDistance($totalDistance, $totalActivities);
        $averageMovingTime = $this->calculateAverageMovingTime($activityModel, $startDate, $endDate, $activityTypes, $totalActivities);

        // Fetch additional data
        $longestActivity = $this->getLongestActivity($activityModel, $startDate, $endDate, $activityTypes);
        $mostActiveDay = $this->getMostActiveDay($activityModel, $startDate, $endDate, $activityTypes);
        $mostActiveHour = $this->getMostActiveHour($activityModel, $startDate, $endDate, $activityTypes);

        // Calculate progress percentage
        $progressPercentage = $this->calculateProgressPercentage($startDate, $endDate);

        // Fetch data from ParticipantModel
        $stageStats = $participantModel->getStageStats();
        $challengesCompleted = $participantModel->getChallengesCompletedDistributionPerStage();
        $distanceDistribution = $participantModel->getDistanceDistributionPerStage();
        $top5Ranks = $participantModel->getTop5Ranks();
        $participantsCompletingMoreThan3 = $participantModel->getParticipantsCompletingMoreThan3ChallengesPerStage();

        $data = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'averageActivitiesPerParticipant' => $averageActivitiesPerParticipant,
            'total_participants' => $participantModel->getTotalParticipants(),
            'active_participants' => $participants,
            'totalDistance' => $totalDistance,
            'averageDistance' => $averageDistance,
            'averageMovingTime' => $averageMovingTime,
            'longestActivity' => $longestActivity,
            'mostActiveDay' => $mostActiveDay ? $mostActiveDay['day'] : 'N/A',
            'mostActiveHour' => $mostActiveHour ? $mostActiveHour['hour'] : 'N/A',
            'progress_percentage' => $progressPercentage,
            'total_days' => $totalDays, // Removed 'elapsed_days'
            'stageStats' => $stageStats,
            'challengesCompleted' => $challengesCompleted,
            'distanceDistribution' => $distanceDistribution,
            'top5Ranks' => $top5Ranks,
            'participantsCompletingMoreThan3' => $participantsCompletingMoreThan3,
        ];

        return view('dashboard', $data);
    }

    // Helper functions

    private function getTotalActivities($activityModel, $startDate, $endDate, $activityTypes)
    {
        return $activityModel->whereIn('type', $activityTypes)
            ->where('start_date >=', $startDate)
            ->where('start_date <', $endDate)
            ->countAllResults();
    }

    private function getTotalDistance($activityModel, $startDate, $endDate, $activityTypes)
    {
        return $activityModel->select('SUM(distance)')
            ->whereIn('type', $activityTypes)
            ->where('distance >=', 2000)
            ->where('start_date >=', $startDate)
            ->where('start_date <', $endDate)
            ->first()['SUM(distance)'] ?? 0;
    }

    private function getParticipantsCount($activityModel, $startDate, $endDate, $activityTypes)
    {
        return $activityModel->select('strava_athlete_id')
            ->distinct()
            ->whereIn('type', $activityTypes)
            ->where('start_date >=', $startDate)
            ->where('start_date <', $endDate)
            ->countAllResults();
    }

    private function calculateTotalDays($startDate, $endDate)
    {
        return floor((strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24)) + 1; 
    }

    private function calculateAverageActivities($totalActivities, $participants)
    {
        return ($participants > 0) ? floor($totalActivities / $participants) : 0;
    }

    private function calculateAverageDistance($totalDistance, $totalActivities)
    {
        return ($totalActivities > 0) ? $totalDistance / $totalActivities : 0;
    }

    private function calculateAverageMovingTime($activityModel, $startDate, $endDate, $activityTypes, $totalActivities)
    {
        $totalMovingTime = $activityModel->select('SUM(moving_time)')
            ->whereIn('type', $activityTypes)
            ->where('start_date >=', $startDate)
            ->where('start_date <', $endDate)
            ->first()['SUM(moving_time)'] ?? 0;

        return ($totalActivities > 0) ? $totalMovingTime / $totalActivities : 0;
    }

    private function getLongestActivity($activityModel, $startDate, $endDate, $activityTypes)
    {
        return $activityModel->select('strava_activities.*, users.name AS user_name')
            ->join('users', 'strava_activities.strava_athlete_id = users.strava_athlete_id', 'left')
            ->whereIn('strava_activities.type', $activityTypes)
            ->where('strava_activities.distance >=', 2000)
            ->where('strava_activities.start_date >=', $startDate)
            ->where('strava_activities.start_date <', $endDate)
            ->orderBy('strava_activities.distance', 'DESC')
            ->first();
    }

    private function getMostActiveDay($activityModel, $startDate, $endDate, $activityTypes)
    {
        return $activityModel->select("DATE_FORMAT(start_date_local, '%Y-%m-%d') AS day, COUNT(*) AS count")
            ->whereIn('type', $activityTypes)
            ->where('start_date >=', $startDate)
            ->where('start_date <', $endDate)
            ->where('distance >=', 2000)
            ->groupBy('day')
            ->orderBy('count', 'DESC')
            ->first();
    }

    private function getMostActiveHour($activityModel, $startDate, $endDate, $activityTypes)
    {
        return $activityModel->select("HOUR(start_date_local) AS hour, COUNT(*) AS count")
            ->whereIn('type', $activityTypes)
            ->where('start_date >=', $startDate)
            ->where('start_date <', $endDate)
            ->where('distance >=', 2000)
            ->groupBy('hour')
            ->orderBy('count', 'DESC')
            ->first();
    }

    private function calculateProgressPercentage($startDate, $endDate)
    {
        $start_date = strtotime($startDate);
        $end_date = strtotime($endDate);
        $today = time() + 5.5 * 3600; 
        $total_days = ($end_date - $start_date) / 86400 + 1; 
        $elapsed_days = ($today - $start_date) / 86400 + 1;

        return round(($elapsed_days / $total_days) * 100, 2);
    }
}