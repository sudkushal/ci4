<?php

namespace App\Controllers;

class Privacy extends BaseController
{
    public function index()
    {
        $data['selectedStyle'] = $this->session->get('selectedStyle');

        return view('privacy_policy', $data); // Load your new view file
    }
}
