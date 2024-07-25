<?php namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id'; // Assuming your users table has an auto-incrementing 'id' column
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'strava_athlete_id',
        'name',
        'strava_access_token', 
        'strava_refresh_token', 
        'profile_medium',
        'profile'
    ];

    // ... (other model methods) ...

    public function updateOrCreate(array $userData)
    {
        // Find user by Strava athlete ID
        $user = $this->where('strava_athlete_id', $userData['strava_athlete_id'])->first();

        if ($user) {
            // Update existing user
            $this->update($user['id'], $userData); 
            return $user['id'];
        } else {
            // Create new user
            return $this->insert($userData); 
        }
    }
}
