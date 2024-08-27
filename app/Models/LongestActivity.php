<?php

namespace App\Models;

use CodeIgniter\Model;

class LongestActivity extends Model
{
    protected $table = 'longest_activities'; // The name of the view

    public function getActivitiesWithUsers()
    {
        $builder = $this->db->table($this->table);
        $builder->select('longest_activities.*, users.name as user_name');
        $builder->join('users', 'users.strava_athlete_id = longest_activities.strava_athlete_id');

        $query = $builder->get();

        $result = $query->getResultArray();

        // Separate usernames (still get unique usernames)
        $userNames = array_column($result, 'user_name');
        $userNames = array_unique($userNames);
        sort($userNames); 

        // Separate stages
        $stages = array_column($result, 'stage');
        $stages = array_unique($stages);

        return [
            'activities' => $result,
            'userNames' => $userNames,
            'stages' => $stages
        ];
    }

    public function getFullActivitiesWithUsers()
    {
        $builder = $this->db->table('longest_activities_full'); // Use the new view name
        $builder->select('longest_activities_full.*, users.name as user_name');
        $builder->join('users', 'users.strava_athlete_id = longest_activities_full.strava_athlete_id');
        $builder->orderBy('activity_date', 'DESC');

        $query = $builder->get();

        return $query->getResultArray();
    }
}
