<?php namespace App\Models;

use CodeIgniter\Model;

class StravaCredentialModel extends Model
{
    protected $table            = 'strava_credentials';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['client_id', 'client_secret']; // Fields to allow for mass assignment
    protected $useSoftDeletes   = false; // We won't be soft-deleting credentials
    protected $returnType       = 'array'; 

    // ... (Other model methods can go here if needed) ...

    // Optionally, add a method to fetch and decrypt credentials securely
    public function getCredentials()
    {
        $credentials = $this->first(); // Get the first (and presumably only) row
        
        return $credentials;
    }
}
