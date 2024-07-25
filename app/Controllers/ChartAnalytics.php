<?php namespace App\Controllers;

use App\Models\StravaActivityModel;
use App\Config\AppConstants;

class ChartAnalytics extends BaseController
{
    public function index()
    {
        $activityModel = new StravaActivityModel();
        $startDate = AppConstants::CHALLENGE_START_DATE;
        $endDate = AppConstants::CHALLENGE_END_DATE;
        $activityTypes = ['Walk', 'Run']; // Always fetch both types for analytics

        try {
            // Data for Charts

            // Distance by Day (Bar Chart)
            $distanceByDay = $activityModel
                ->select("DATE_FORMAT(start_date_local, '%Y-%m-%d') AS day, SUM(distance) AS total_distance", false)
                ->whereIn('type', $activityTypes)
                ->where('start_date >=', $startDate)
                ->where('start_date <', $endDate)
                ->groupBy('day')
                ->orderBy('day')
                ->findAll();

            // Activities by Type (Pie Chart)
            $activitiesByType = $activityModel
                ->select('type, COUNT(*) AS count', false)
                ->whereIn('type', $activityTypes)
                ->where('start_date >=', $startDate)
                ->where('start_date <', $endDate)
                ->groupBy('type')
                ->findAll();

            // Cumulative Distance (Line Chart)
            $cumulativeDistance = [];
            $runningTotal = 0;
            foreach ($distanceByDay as $data) {
                $runningTotal += $data['total_distance'];
                $cumulativeDistance[] = [
                    'day' => $data['day'],
                    'total_distance' => $runningTotal
                ];
            }

            // Distance vs. Time (Scatter Plot)
            $distanceVsTime = $activityModel
                ->select('distance, moving_time')
                ->whereIn('type', $activityTypes)
                ->where('start_date >=', $startDate)
                ->where('start_date <', $endDate)
                ->findAll();
            
            // Activity by Time of Day (Heatmap) 
            $activityByHour = $activityModel
                ->select("HOUR(start_date_local) AS hour, DAYNAME(start_date_local) AS day, COUNT(*) AS count", false)
                ->whereIn('type', $activityTypes)
                ->where('start_date >=', $startDate)
                ->where('start_date <', $endDate)
                ->groupBy('hour, day')
                ->orderBy('hour, day')
                ->findAll();

            // Prepare data for JSON response
            $data = [
                'success' => true,
                'distanceByDayData' => $distanceByDay,
                'cumulativeDistanceData' => $cumulativeDistance,
                'activitiesByTypeData' => $activitiesByType,
                'distanceVsTimeData' => $distanceVsTime,
                'activityByHourData' => $activityByHour
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error fetching chart data: ' . $e->getMessage());
            $data = ['success' => false, 'error' => 'Failed to fetch data for charts.'];
        }

        return $this->response->setJSON($data);
    }
}
