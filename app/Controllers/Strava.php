<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\StravaCredentialModel;
use App\Models\StravaActivityModel; // Model to store Strava activities
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Config\Services;
use App\Config\AppConstants;
use DateTime;  

class Strava extends BaseController
{
    // Initiate Strava Connection
    public function connectToStrava(): RedirectResponse
    {
        $credentials = (new StravaCredentialModel())->first();

        $authorizeUrl = 'https://www.strava.com/oauth/authorize';
        $params = [
            'client_id' => $credentials['client_id'],
            'response_type' => 'code',
            'redirect_uri' => site_url('strava/callback'), // Your callback URL
            'approval_prompt' => 'auto',
            'scope' => 'read,activity:read_all'
        ];
        $fullUrl = $authorizeUrl . '?' . http_build_query($params);
        return redirect()->to($fullUrl);
    }

    public function callback()
    {
        // Load Strava credentials (use getCredentials() method)
        $credentials = (new StravaCredentialModel())->getCredentials();

        // Error handling: missing credentials
        if (!$credentials) {
            return redirect()->to('/')->with('error', 'Missing Strava credentials.');
        }

        // Error handling: missing authorization code
        $code = $this->request->getVar('code');
        if (!$code) {
            return redirect()->to('/')->with('error', 'Invalid authorization code.');
        }

        // Exchange authorization code for tokens
        $client = Services::curlrequest();

        try {
            $response = $client->request('POST', 'https://www.strava.com/oauth/token', [
                'form_params' => [
                    'client_id'     => $credentials['client_id'],
                    'client_secret' => $credentials['client_secret'],
                    'code'          => $code,
                    'grant_type'    => 'authorization_code'
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            // Error Handling: Check if tokens were received 
            if (!isset($data['access_token']) || !isset($data['refresh_token'])) {
                log_message('error', 'Error getting Strava tokens: Invalid response format.'); // Log the error
                return redirect()->to('/')->with('error', 'Failed to connect to Strava. Please try again.');
            }

            $accessToken = $data['access_token'];
            $refreshToken = $data['refresh_token'];
            $expiresAt = $data['expires_at'];

            // Get athlete information
            $athlete = $data['athlete'];
            $athleteId = $athlete['id'];
            $athleteName = $athlete['firstname'] . ' ' . $athlete['lastname'];
            $athleteProfileMedium = $athlete['profile_medium'];
            $athleteProfile = $athlete['profile'];

            // Store or update user data in the database
            $userModel = new UserModel();
            $userData = [
                'strava_athlete_id' => $athleteId,
                'name' => $athleteName,
                'strava_access_token' => $accessToken,
                'strava_refresh_token' => $refreshToken,
                'strava_expires_at' => $expiresAt,
                'profile_medium' => $athleteProfileMedium,
                'profile' => $athleteProfile
            ];

            $userModel->updateOrCreate($userData);

            // Fetch and sync activities
            $page = 1;
            $perPage = 200;
            $activities = [];
            $stravaStartDate = new DateTime('2024-08-01'); 

            // Convert the start date to a Unix timestamp
            $afterTimestamp = $stravaStartDate->getTimestamp(); 

            $startDate = strtotime(AppConstants::CHALLENGE_START_DATE);
            $endDate = strtotime(AppConstants::CHALLENGE_END_DATE);

            do {
                $activitiesResponse = $client->request('GET', "https://www.strava.com/api/v3/athlete/activities?page={$page}&per_page={$perPage}&after={$afterTimestamp}", [
                    'headers' => ['Authorization' => 'Bearer ' . $accessToken]
                ]);
                $pageActivities = json_decode($activitiesResponse->getBody(), true);
                $filteredActivities = array_filter($pageActivities, function ($activity) use ($startDate, $endDate) {
                    $activityStartTime = strtotime($activity['start_date_local']); // Assuming the date is in the correct format
                    return $activityStartTime >= $startDate && $activityStartTime < $endDate;
                });

                //echo "<pre>"; print_r($filteredActivities); echo "</pre>"; exit;

                $activities = array_merge($activities, $filteredActivities);
                $page++;
            } while (count($pageActivities) === $perPage);

            $stravaActivityModel = new StravaActivityModel();

            foreach ($activities as $activity) {
                $activityData = [
                    'strava_athlete_id' => $athleteId,
                    'activity_id' => $activity['id'],
                    'name' => $activity['name'],
                    'distance' => $activity['distance'],
                    'moving_time' => $activity['moving_time'],
                    'elapsed_time' => $activity['elapsed_time'],
                    'type' => $activity['type'],
                    'start_date' => $activity['start_date'],
                    'start_date_local' => $activity['start_date_local'],
                    'timezone' => $activity['timezone'],
                    'utc_offset' => $activity['utc_offset'],
                    'total_elevation_gain' => $activity['total_elevation_gain'],
                    'average_speed' => $activity['average_speed'],
                    'max_speed' => $activity['max_speed'],
                ];

                $stravaActivityModel->updateOrCreate($activityData, $athleteId);
            }

            return redirect()->to(site_url('activities'))->with('success', 'Strava connected and data synced successfully!');
        } catch (\Exception $e) {
            log_message('error', 'Error in Strava callback: ' . $e->getMessage());
            return redirect()->to('/')->with('error', 'Failed to connect to Strava. Please try again.');
        }
    }
}
