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

        $totalDistances = $this->formatStageData($result);
        $participantCounts = $this->formatStageData($result, 'participant_count');

        $stageStats = [];
        foreach ($totalDistances as $stage => $totalDistance) {
            $stageStats["Stage {$stage}"] = [
                'total_distance' => $totalDistance / 1000, 
                'participant_count' => $participantCounts[$stage] ?? 0 
            ];
        }

        return $stageStats;
    }

    public function getActiveParticipants()
    {
        $builder = $this->db->table('longest_activities'); 
        $builder->select('COUNT(DISTINCT strava_athlete_id) as active_participants'); 
        $result = $builder->get()->getRow();

        return $result ? $result->active_participants : 0; 
    }

    public function getTotalDistance()
    {
        $builder = $this->db->table('stage_combined');
        $result = $builder->selectSum('total_distance')->get()->getRow();

        return $result ? $result->total_distance : 0; 
    }

    public function getChallengesCompletedDistributionPerStage()
    {
        $builder = $this->db->table('stage_combined');
        $builder->select('stage, challenges_completed, COUNT(*) as participant_count');
        $builder->where('stage IS NOT NULL');
        $builder->where('challenges_completed > 0');
        $builder->groupBy('stage, challenges_completed');

        $result = $builder->get()->getResultArray();

        $challengesCompletedDistribution = [];
        foreach ($result as $row) {
            $stage = "Stage {$row['stage']}";
            $challengesCompleted = $row['challenges_completed'];

            if (!isset($challengesCompletedDistribution[$stage])) {
                $challengesCompletedDistribution[$stage] = [];
            }

            $challengesCompletedDistribution[$stage][$challengesCompleted] = $row['participant_count'];
        }

        return $challengesCompletedDistribution;
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

        // Separate usernames (still get unique usernames)
        $userNames = array_column($result, 'user_name');
        $userNames = array_unique($userNames); 

        // Sort usernames alphabetically
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
}