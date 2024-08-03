<?php namespace App\Controllers;

use App\Models\ChallengeConfigModel;

class ChallengeConfig extends BaseController
{
    public function index()
    {
        $model = new ChallengeConfigModel();

        $data = [
            'configData' => $model->orderBy('stage', 'ASC')->findAll() 
        ];

        return view('challenge_config_view', $data); 
    }
}
