<?php

namespace App\Controllers;


class Home extends BaseController
{

    protected $session;
    protected $selectedStyle;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Initialize the session
        $this->session = \Config\Services::session();

        // Set session variable for style if not set already
        if (!$this->session->has('selectedStyle')) {
            $styles = ['indian', 'us', 'south-african', 'colombian', 'uk-hk'];
            $preferredStyle = $styles[array_rand($styles)];
            $this->session->set('selectedStyle', 'style-' . $preferredStyle . '.css');
        }
        $this->selectedStyle = $this->session->get('selectedStyle');
    }

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
