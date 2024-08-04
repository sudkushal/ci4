<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Fitness Portal</title>
    <link rel="stylesheet" href="<?= base_url('css/style-india.css?v=' . time()); ?>">
    <link rel="icon" href="<?= base_url('favicon.ico'); ?>" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            /* Removing padding-top for the sticky menu */
            margin: 0;
            padding: 0;
        }
        .faq-section {
            padding: 80px 0;
            background: linear-gradient(to right, #f7f7f7, #e0e0e0);
            color: #333;
        }
        .faq-header {
            margin-bottom: 50px;
            font-size: 2rem;
            color: #FF8C00; /* Orange color to match the theme */
        }
        .faq-item {
            margin-bottom: 30px;
        }
        .faq-question {
            font-weight: bold;
            font-size: 1.2rem;
            color: #006400; /* Dark green color for questions */
        }
        .faq-answer {
            font-size: 1rem;
            color: #333;
            line-height: 1.6;
        }
        .faq-answer ol {
            padding-left: 20px;
        }
        .faq-answer ol li {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<?= $this->include('_menu') ?>

    <!-- FAQ Section -->
    <div class="container faq-section">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <h2 class="text-center faq-header">Frequently Asked Questions</h2>

                <!-- FAQ Item 1 -->
                <div class="faq-item">
                    <p class="faq-question">Q1: How do I sync my Strava data with this portal?</p>
                    <div class="faq-answer">
                        <ol>
                            <li>Connect Strava Account: Navigate to the 'Connect Strava' section in the portal. You will see an option to link your Strava account.</li>
                            <li>Authorize: Click the 'Authorize' button, which will redirect you to Strava’s website. Login to Strava, and authorize the portal to access your data.</li>
                            <li>Sync Data: Once authorization is complete, your Strava activities will automatically sync with the portal.</li>
                        </ol>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="faq-item">
                    <p class="faq-question">Q2: How can I view the leaderboard for each stage?</p>
                    <div class="faq-answer">
                        <ol>
                            <li>Navigate to Leaderboards: On the main dashboard, select the ‘Leaderboards’ tab.</li>
                            <li>Select Stage: You will see options for different stages. Click on the stage you are interested in.</li>
                            <li>View Rankings: The leaderboard for the selected stage will display participants ranked by their performance metrics, such as points or distance.</li>
                        </ol>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="faq-item">
                    <p class="faq-question">Q3: Where can I find the consolidated leaderboard?</p>
                    <div class="faq-answer">
                        <ol>
                            <li>Go to Consolidated Leaderboard: In the Leaderboards section, look for the ‘Consolidated Leaderboard’ tab.</li>
                            <li>View Overall Rankings: This leaderboard provides an aggregated view of all stages, showing overall performance across the entire challenge period.</li>
                        </ol>
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="faq-item">
                    <p class="faq-question">Q4: How can I access analytics and my individual statistics?</p>
                    <div class="faq-answer">
                        <ol>
                            <li>Access Analytics: Go to the ‘Analytics’ section from the main menu.</li>
                            <li>View Detailed Stats: Here, you can view detailed statistics on your performance, such as total distance covered, active days, and performance trends over time.</li>
                            <li>Compare with Peers: Some analytics sections may also allow you to compare your performance with others in your group or the overall challenge.</li>
                        </ol>
                    </div>
                </div>

                <!-- FAQ Item 5 -->
                <div class="faq-item">
                    <p class="faq-question">Q5: What should I do if my Strava data is not syncing?</p>
                    <div class="faq-answer">
                        <ol>
                            <li>Check Connection: Ensure your Strava account is properly connected by revisiting the ‘Connect Strava’ section.</li>
                            <li>Reauthorize: If needed, reauthorize the connection by following the steps to connect Strava again.</li>
                            <li>Contact Support: If issues persist, contact your engagement mania team captain.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= $this->include('_footer') ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
