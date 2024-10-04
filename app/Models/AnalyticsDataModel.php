<?php

namespace App\Models;

use CodeIgniter\Model;

class AnalyticsDataModel extends Model
{
    // ... other functions for analytics data ... 

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