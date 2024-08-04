<?php

namespace App\Controllers;

class Privacy extends BaseController
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
        $data['selectedStyle'] = $this->session->get('selectedStyle');

        return view('privacy_policy', $data); // Load your new view file
    }

    public function faqs()
    {
        return view('faqs_view');
    }
}
