<?php

namespace App\Models;

use CodeIgniter\Model;

class StravaActivityModel extends Model
{
    protected $table            = 'strava_activities';
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'strava_athlete_id',
        'activity_id',
        'name',
        'distance',
        'moving_time',
        'elapsed_time',
        'type',
        'start_date',
        'start_date_local',
        'timezone',
        'utc_offset',
        'total_elevation_gain',
        'average_speed',
        'max_speed'
    ];

    public function updateOrCreate(array $data)
    {
        // Check if the activity already exists for this athlete
        $existingActivity = $this
            ->where('strava_athlete_id', $data['strava_athlete_id'])
            ->where('activity_id', $data['activity_id'])
            ->first();

        if ($existingActivity) {
            // Update the existing activity
            $this->update($existingActivity['id'], $data);
        } else {
            // Create a new activity record
            $this->insert($data);
        }
    }

    public function getActivitiesForLeaderboard($stravaAthleteId, $startDate, $endDate, $activityTypes, $minDistance)
    {
        $minDistance = $minDistance * 1000;
        print_r("startDate :".$startDate); 
        print_r("endDate :".$endDate); exit;

        return $this->where('strava_athlete_id', $stravaAthleteId)
            ->where('start_date >=', $startDate)
            ->where('start_date <', $endDate)
            ->where('distance >=', $minDistance)
            ->whereIn('type', $activityTypes)
            ->findAll();
    }
}
