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

        $totalActivities = $activityModel->whereIn('type', $activityTypes)
            ->where('start_date >=', $startDate)->where('start_date <', $endDate)->countAllResults();

        $totalDistance = $activityModel->select('SUM(distance)')->whereIn('type', $activityTypes)
            ->where('distance >=', 2000)->where('start_date >=', $startDate)->where('start_date <', $endDate)->first()['SUM(distance)'] ?? 0;

        $participants = $activityModel->select('strava_athlete_id')->distinct()
            ->whereIn('type', $activityTypes)
            ->where('start_date >=', $startDate)->where('start_date <', $endDate)->countAllResults();
        $totalDays = floor((strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24));
        $averageActivitiesPerParticipant = ($participants > 0) ? floor($totalActivities / $participants) : 0;
        $averageDistance = ($totalActivities > 0) ? $totalDistance / $totalActivities : 0;
        $totalMovingTime = $activityModel->select('SUM(moving_time)')
        ->whereIn('type', $activityTypes)
        ->where('start_date >=', $startDate)
        ->where('start_date <', $endDate)
        ->first()['SUM(moving_time)'] ?? 0;
        $averageMovingTime = ($totalActivities > 0) ? $totalMovingTime / $totalActivities : 0;

        $longestActivity = $activityModel->select('strava_activities.*, users.name AS user_name')
        ->join('users', 'strava_activities.strava_athlete_id = users.strava_athlete_id', 'left') // Replace 'user_id' and 'id' if necessary
        ->whereIn('strava_activities.type', $activityTypes)
        ->where('strava_activities.distance >=', 2000)
        ->where('strava_activities.start_date >=', $startDate)
        ->where('strava_activities.start_date <', $endDate)
        ->orderBy('strava_activities.distance', 'DESC')
        ->first();
        $mostActiveDay = $activityModel->select("DATE_FORMAT(start_date_local, '%Y-%m-%d') AS day, COUNT(*) AS count")
        ->whereIn('type', $activityTypes)
        ->where('start_date >=', $startDate)->where('start_date <', $endDate)->where('distance >=', 2000)
        ->groupBy('day')
        ->orderBy('count', 'DESC')
        ->first();
        $mostActiveHour = $activityModel->select("HOUR(start_date_local) AS hour, COUNT(*) AS count")
            ->whereIn('type', $activityTypes)
            ->where('start_date >=', $startDate)->where('start_date <', $endDate)->where('distance >=', 2000)
            ->groupBy('hour')
            ->orderBy('count', 'DESC')
            ->first();

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
        ];

        return view('dashboard', $data);
    }
}
