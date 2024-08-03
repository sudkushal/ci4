<?php namespace App\Models;

use CodeIgniter\Model;

class ChallengeConfigModel extends Model
{
    protected $table            = 'challenge_config';
    protected $primaryKey       = 'id'; // Assuming your table has an 'id' primary key
    protected $returnType       = 'array'; 
    protected $allowedFields    = ['stage', 'config_key', 'config_value'];

    // Optional: If you need more complex validation
    // protected $validationRules = [
    //     'stage'        => 'required|integer',
    //     'config_key'   => 'required|string',
    //     'config_value' => 'required|string',
    // ];
}
