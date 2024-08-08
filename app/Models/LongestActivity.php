<?php

namespace App\Models;

use CodeIgniter\Model;

class LongestActivity extends Model
{
    protected $table = 'longest_activities'; // The name of the view

    public function getActivitiesWithUsers()
    {
        $builder = $this->db->table($this->table); // Select from 'longest_activities' view
        $builder->select('longest_activities.*, users.name as user_name'); // Specify the columns you want
        $builder->join('users', 'users.strava_athlete_id = longest_activities.strava_athlete_id'); // Adjust the ON condition as per your schema
        //$builder->orderBy('some_column', 'ASC');

        $query = $builder->get(); // Execute the query

        return $query->getResultArray(); // Return the results as an array
    }
}
