<?php

namespace App\Controllers;


class Home extends BaseController
{
    protected $session;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Load session library
        $this->session = \Config\Services::session();
    }
    
    public function index()
    {
        $data = []; // Initialize an empty data array for the view

        if (!$this->session->has('selectedStyle')) {
            $styles = ['india', 'us', 'colombia'];
            $preferredStyle = $styles[array_rand($styles)];
            $this->session->set('selectedStyle', 'style-' . $preferredStyle . '.css');
        }

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
