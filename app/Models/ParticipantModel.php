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

    public function getTotalDistance()
    {
        return $this->selectSum('distance')->first()['distance'] ?? 0;
    }
}