<?php
namespace App\Models;

use CodeIgniter\Model;

class LeaderboardModel extends Model {

    protected $table = 'consolidated_leaderboard'; // Replace with the actual view name

    public function get_leaderboard_data()
    {
        return $this->findAll(); // Fetches all rows from the view
    }
}
