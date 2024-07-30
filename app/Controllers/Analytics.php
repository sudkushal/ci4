<?php

namespace App\Controllers;

use App\Models\StravaActivityModel;
use App\Config\AppConstants;

class Analytics extends BaseController
{
    protected $session;
    protected $selectedStyle;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Initialize the session
        $this->session = \Config\Services::session();

        // Set session variable for style if not set already
        if (!$this->session->has('selectedStyle')) {
            $styles = ['indian', 'us', 'south-african', 'colombian', 'uk-hk'];
            $preferredStyle = $styles[array_rand($styles)];
            $this->session->set('selectedStyle', 'style-' . $preferredStyle . '.css');
        }
        $this->selectedStyle = $this->session->get('selectedStyle');
    }
    public function index()
    {
        $data['selectedStyle'] = $this->session->get('selectedStyle');

        $activityModel = new StravaActivityModel();

        $startDate = AppConstants::CHALLENGE_START_DATE;
        $endDate = AppConstants::CHALLENGE_END_DATE;
        $activityTypes = ['Walk', 'Run'];

        // Overall Stats
        $totalDistance = $activityModel->select('SUM(distance)')->whereIn('type', $activityTypes)
        ->where('distance >=', 2000)->where('start_date >=', $startDate)->where('start_date <', $endDate)->first()['SUM(distance)'] ?? 0;
        $totalActivities = $activityModel->whereIn('type', $activityTypes)
        ->where('distance >=', 2000)->where('start_date >=', $startDate)->where('start_date <', $endDate)->countAllResults();
        $averageDistance = ($totalActivities > 0) ? $totalDistance / $totalActivities : 0;

        // Participation
        $participants = $activityModel->select('strava_athlete_id')->distinct()
            ->whereIn('type', $activityTypes)
            ->where('start_date >=', $startDate)->where('start_date <', $endDate)->countAllResults();
        $totalDays = floor((strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24));
        $averageActivitiesPerParticipant = ($participants > 0) ? $totalActivities / $participants : 0;

        // Distance-Based Insights
        $longestActivity = $activityModel->whereIn('type', $activityTypes)
        ->where('distance >=', 2000)->where('start_date >=', $startDate)->where('start_date <', $endDate)
            ->orderBy('distance', 'DESC')->first();
        /*$shortestActivity = $activityModel->whereIn('type', $activityTypes)
            ->where('start_date >=', $startDate)->where('start_date <', $endDate)->where('distance >=', 2000)
            ->orderBy('distance', 'ASC')->first(); */

        // Time-Based Insights
        $totalMovingTime = $activityModel->select('SUM(moving_time)')->whereIn('type', $activityTypes)
        ->where('distance >=', 2000)->where('start_date >=', $startDate)->where('start_date <', $endDate)->first()['SUM(moving_time)'] ?? 0;
        $averageMovingTime = ($totalActivities > 0) ? $totalMovingTime / $totalActivities : 0;

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

        // Additional Analytics
        $totalElevationGain = $activityModel->select('SUM(total_elevation_gain)')->whereIn('type', $activityTypes)
        ->where('distance >=', 2000)->where('start_date >=', $startDate)->where('start_date <', $endDate)->first()['SUM(total_elevation_gain)'] ?? 0;

        $averageSpeed = $activityModel->select('AVG(average_speed)')->whereIn('type', $activityTypes)
        ->where('distance >=', 2000)->where('start_date >=', $startDate)->where('start_date <', $endDate)->first()['AVG(average_speed)'] ?? 0;

        $maxSpeed = $activityModel->select('MAX(max_speed)')->whereIn('type', $activityTypes)
        ->where('distance >=', 2000)->where('start_date >=', $startDate)->where('start_date <', $endDate)->first()['MAX(max_speed)'] ?? 0;

        // Pass data to the view
        $data = [
            'totalDistance' => $totalDistance,
            'totalActivities' => $totalActivities,
            'averageDistance' => $averageDistance,
            'participants' => $participants,
            'averageActivitiesPerParticipant' => $averageActivitiesPerParticipant,
            'longestActivity' => $longestActivity,
            'totalMovingTime' => $totalMovingTime,
            'averageMovingTime' => $averageMovingTime,
            'mostActiveDay' => $mostActiveDay ? $mostActiveDay['day'] : 'N/A',
            'mostActiveHour' => $mostActiveHour ? $mostActiveHour['hour'] : 'N/A',
            'totalElevationGain' => $totalElevationGain,
            'averageSpeed' => $averageSpeed,
            'maxSpeed' => $maxSpeed,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];

        return view('analytics_view', $data);
    }
}
