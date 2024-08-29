<?php

namespace App\Models;

use CodeIgniter\Model;

class ParticipantModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['distance'];

    public function getTotalParticipants()
    {
        return $this->countAllResults();
    }

    private function formatStageData($result, $key = 'total_distance', $conversionFactor = 1000)
    {
        return array_column($result, $key, 'stage'); 
    }

    public function getStageStats()
    {
        $builder = $this->db->table('stage_combined');
        $builder->select('stage, SUM(total_distance) as total_distance, COUNT(DISTINCT strava_athlete_id) as participant_count');
        $builder->where('stage IS NOT NULL');
        $builder->groupBy('stage');

        $result = $builder->get()->getResultArray();

        return array_reduce($result, function ($carry, $row) {
            $stage = "Stage {$row['stage']}";
            $carry[$stage] = [
                'total_distance' => number_format(($row['total_distance'] / 1000), 2),
                'participant_count' => $row['participant_count']
            ];
            return $carry;
        }, []);
    }

    public function getActiveParticipants()
{
    $builder = $this->db->table('longest_activities'); 
    $builder->select('COUNT(DISTINCT strava_athlete_id) AS active_participants'); // Move the alias outside the function
    $query = $builder->get();

    $result = $query->getRow();

    if ($result) {
        return $result->active_participants; 
    } else {
        return 0; 
    }
}

    public function getTotalDistance()
    {
        return $this->db->table('stage_combined')
            ->selectSum('total_distance')
            ->get()
            ->getRow()
            ->total_distance ?? 0;
    }

    public function getChallengesCompletedDistributionPerStage()
    {
        $builder = $this->db->table('stage_combined');
        $builder->select('stage, challenges_completed, COUNT(*) as participant_count');
        $builder->where('stage IS NOT NULL');
        $builder->where('challenges_completed > 0');
        $builder->groupBy('stage, challenges_completed');

        $result = $builder->get()->getResultArray();

        return array_reduce($result, function ($carry, $row) {
            $stage = "Stage {$row['stage']}";
            $challengesCompleted = $row['challenges_completed'];

            $carry[$stage][$challengesCompleted] = $row['participant_count'];
            return $carry;
        }, []);
    }

    public function getDistanceDistributionPerStage()
    {
        $builder = $this->db->table('stage_combined');
        $builder->select('stage, total_distance');
        $builder->where('stage IS NOT NULL');

        $result = $builder->get()->getResultArray();

        return $this->formatStageData($result); 
    }

    public function getTop5Ranks()
    {
        $builder = $this->db->table('consolidated_challenge_leaderboard');
        $builder->select('participant_name, rank_order');
        $builder->orderBy('rank_order', 'ASC'); 
        $builder->limit(5);

        return $builder->get()->getResultArray();
    }

    public function getAverageActivitiesPerParticipant()
    {
        $builder = $this->db->table('longest_activities');
        $builder->select('COUNT(*) as total_activities, COUNT(DISTINCT strava_athlete_id) as participant_count');
        $query = $builder->get();

        $result = $query->getRow();

        if ($result && $result->participant_count > 0) {
            return floor($result->total_activities / $result->participant_count); 
        } else {
            return 0; 
        }
    }

    public function getParticipantsCompletingMoreThan3ChallengesPerStage()
    {
        $builder = $this->db->table('stage_combined');
        $builder->select('stage, COUNT(DISTINCT strava_athlete_id) as participant_count');
        $builder->where('challenges_completed > 2'); 
        $builder->groupBy('stage');

        $result = $builder->get()->getResultArray();

        return $this->formatStageData($result, 'participant_count', null); 
    }

    public function getActivitiesWithUsers()
    {
        $builder = $this->db->table($this->table); 
        $builder->select('longest_activities.*, users.name as user_name');
        $builder->join('users', 'users.strava_athlete_id = longest_activities.strava_athlete_id'); 

        $query = $builder->get(); 

        $result = $query->getResultArray(); 

        $userNames = array_column($result, 'user_name');
        $userNames = array_unique($userNames); 

        sort($userNames); 

        $stages = array_column($result, 'stage');
        $stages = array_unique($stages); 

        return [
            'activities' => $result, 
            'userNames' => $userNames,
            'stages' => $stages
        ];
    }
}