<?php

namespace App\Controllers;

use App\Models\LongestActivity;

class ActivitiesController extends BaseController
{
    public function index()
    {
        $model = new LongestActivity();

        // Fetch data from the model
        $dataFromModel = $model->getActivitiesWithUsers();

        $data = [
            // ... other data for the view
            'activities' => $dataFromModel['activities'],
            'userNames' => $dataFromModel['userNames'],
            'stages' => $dataFromModel['stages'] // Add this line
        ];
        
        return view('activities_view', $data);
    }

    public function fullActivities()
    {
        $model = new LongestActivity();

        $data['fullActivities'] = $model->getFullActivitiesWithUsers();

        return view('full_activities_view', $data); // Assuming you have a separate view for this
    }
}
