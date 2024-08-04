<?php
namespace App\Models;

use CodeIgniter\Model;

class LeaderboardModel extends Model {

    protected $table = 'consolidated_leaderboard'; // Replace with the actual view name

    public function get_leaderboard_data()
    {
        return $this->findAll(); // Fetches all rows from the view
    }

    // Function to retrieve data from stage 1
    public function getStage1Data()
    {
        return $this->db->table('stage1_combined')->get()->getResultArray();
    }

    // Function to retrieve data from stage 2
    public function getStage2Data()
    {
        return $this->db->table('stage2_combined')->get()->getResultArray();
    }

    // Function to retrieve data from stage 3
    public function getStage3Data()
    {
        return $this->db->table('stage3_combined')->get()->getResultArray();
    }

    // Function to retrieve data from stage 4
    public function getStage4Data()
    {
        return $this->db->table('stage4_combined')->get()->getResultArray();
    }

    // Function to retrieve data from stage 5
    public function getStage5Data()
    {
        return $this->db->table('stage5_combined')->get()->getResultArray();
    }

    // Function to retrieve consolidated data
    public function getConsolidatedData()
    {
        return $this->db->table('consolidated_leaderboard')->get()->getResultArray();
    }
}
