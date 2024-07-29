<?php

namespace App\Controllers;


class Home extends BaseController
{
    
    
    public function index()
    {
        $data = []; // Initialize an empty data array for the view

        $data['selectedStyle'] = $this->session->get('selectedStyle');

        // Check for any flash messages (success or error)
        if (session()->has('success')) {
            $data['success'] = session()->getFlashdata('success');
        }
        if (session()->has('error')) {
            $data['error'] = session()->getFlashdata('error');
        }
        // Load the landing page view, passing the data array to it
        return view('landing_page', $data); 
    }
}
