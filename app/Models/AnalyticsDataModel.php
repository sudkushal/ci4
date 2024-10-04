<?php

namespace App\Models;

use CodeIgniter\Model;

class AnalyticsDataModel extends Model
{
    public function getTotalDistance($startDate, $endDate, $activityTypes)
    {
        $db = \Config\Database::connect();

        return $db->table('longest_activities AS la')
            ->select('SUM(la.distance)') // Select distance from longest_activities
            ->join('strava_activities AS sa', 'la.activity_id = sa.activity_id')
            ->whereIn('sa.type', $activityTypes)
            ->where('la.activity_date >=', $startDate)
            ->where('la.activity_date <', $endDate)
            ->get()
            ->getRow()
            ->{'SUM(la.distance)'} ?? 0;
    }
    public function getTotalActivities($startDate, $endDate, $activityTypes)
    {
        $db = \Config\Database::connect();

        return $db->table('longest_activities AS la')
            ->join('strava_activities AS sa', 'la.activity_id = sa.activity_id') // Join to get the type
            ->whereIn('sa.type', $activityTypes)
            ->where('la.activity_date >=', $startDate)
            ->where('la.activity_date <', $endDate)
            ->countAllResults();
    }
    public function calculateAverageMovingTime($startDate, $endDate, $activityTypes, $totalActivities)
    {
        $db = \Config\Database::connect();

        $totalMovingTime = $db->table('longest_activities AS la')
            ->select('SUM(sa.moving_time)')
            ->join('strava_activities AS sa', 'la.activity_id = sa.activity_id')
            ->whereIn('sa.type', $activityTypes)
            ->where('la.activity_date >=', $startDate)
            ->where('la.activity_date <', $endDate)
            ->get()
            ->getRow()
            ->{'SUM(sa.moving_time)'} ?? 0;

        return ($totalActivities > 0) ? $totalMovingTime / $totalActivities : 0;
    }

    // ... other functions for analytics data ... 
}
