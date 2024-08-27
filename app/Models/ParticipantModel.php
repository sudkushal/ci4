<?php

namespace App\Models; // Adjust namespace if needed

use CodeIgniter\Model;

class ParticipantModel extends Model
{
    protected $table = 'users'; // Assuming your table name is 'users'
    protected $primaryKey = 'id'; // Adjust if your primary key is different
    protected $allowedFields = ['distance']; // Add other fields as needed

    public function getTotalParticipants()
    {
        return $this->countAllResults();
    }

    public function getStageStats()
    {
        $builder = $this->db->table('stage_combined');
        $builder->select('stage, SUM(total_distance) as total_distance, COUNT(DISTINCT strava_athlete_id) as participant_count');
        $builder->where('stage IS NOT NULL');
        $builder->groupBy('stage');
        $query = $builder->get();

        $result = $query->getResultArray();

        $stageStats = [];
        foreach ($result as $row) {
            $distanceInKms = $row['total_distance'] / 1000;
            $stageStats["Stage {$row['stage']}"] = [
                'total_distance' => $distanceInKms,
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
        $query = $builder->get();

        $result = $query->getResultArray();

        // Initialize an array to store the distribution for each stage
        $challengesCompletedDistribution = [];

        foreach ($result as $row) {
            $stage = "Stage {$row['stage']}";
            $challengesCompleted = $row['challenges_completed'];
            $participantCount = $row['participant_count'];

            // If the stage doesn't exist in the result array, initialize it
            if (!isset($challengesCompletedDistribution[$stage])) {
                $challengesCompletedDistribution[$stage] = [];
            }

            // Store the participant count for the specific number of challenges completed
            $challengesCompletedDistribution[$stage][$challengesCompleted] = $participantCount;
        }
        return $challengesCompletedDistribution;
    }

    public function getDistanceDistributionPerStage()
    {
        $builder = $this->db->table('stage_combined');
        $builder->select('stage, total_distance'); // Select stage and total_distance
        $builder->where('stage IS NOT NULL');
        $query = $builder->get();

        $result = $query->getResultArray();

        $distanceDistribution = [];
        foreach ($result as $row) {
            $stage = "Stage {$row['stage']}";
            $distanceInKms = $row['total_distance'] / 1000;

            // If the stage doesn't exist in the result array, initialize it
            if (!isset($distanceDistribution[$stage])) {
                $distanceDistribution[$stage] = [];
            }

            $distanceDistribution[$stage][] = $distanceInKms;
        }
        return $distanceDistribution;
    }

    public function getTop5Ranks()
    {
        $builder = $this->db->table('consolidated_challenge_leaderboard');
        $builder->select('participant_name, rank_order');
        $builder->orderBy('rank_order', 'ASC'); // Assuming lower rank is better
        $builder->limit(5);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getParticipantsCompletingMoreThan3ChallengesPerStage()
    {
        $builder = $this->db->table('stage_combined');
        $builder->select('stage, COUNT(DISTINCT strava_athlete_id) as participant_count');
        $builder->where('challenges_completed > 3'); // Filter for challenges_completed > 3
        $builder->groupBy('stage');
        $query = $builder->get();

        $result = $query->getResultArray();

        $participantCounts = [];
        foreach ($result as $row) {
            $participantCounts["Stage {$row['stage']}"] = $row['participant_count'];
        }
        return $participantCounts;
    }
}
