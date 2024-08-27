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
        $stageData = [];
        foreach ($result as $row) {
            $stage = "Stage {$row['stage']}";
            $value = $row[$key];

            if ($conversionFactor) {
                $value /= $conversionFactor; // Apply conversion if needed
            }

            $stageData[$stage] = $value;
        }
        return $stageData;
    }

    public function getStageStats()
    {
        $builder = $this->db->table('stage_combined');
        $builder->select('stage, SUM(total_distance) as total_distance, COUNT(DISTINCT strava_athlete_id) as participant_count');
        $builder->where('stage IS NOT NULL');
        $builder->groupBy('stage');

        $result = $builder->get()->getResultArray();

        $stageStats = [];
        foreach ($result as $row) {
            $stageStats["Stage {$row['stage']}"] = [
                'total_distance' => $row['total_distance'] / 1000,
                'participant_count' => $row['participant_count']
            ];
        }

        return $stageStats;
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
        $builder->where('challenges_completed > 3'); 
        $builder->groupBy('stage');

        $result = $builder->get()->getResultArray();

        return $this->formatStageData($result, 'participant_count', null); // No conversion needed
    }
}