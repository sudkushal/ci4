<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>100 Days Fitness Challenge</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/style-india.css?v=' . time()); ?>">
    <link rel="icon" href="<?= base_url('favicon.ico'); ?>" type="image/x-icon">
    <style>
        /* Add custom styles for progress circle and layout adjustments as needed */
        .progress-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: #eee;
            position: relative;
            margin: 0 auto;
            /* Center the circle */
        }

        .progress-circle-fill {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background-color: #4CAF50;
            /* Green */
            clip: rect(0, 100px, 100px, 50px);
            /* Initial half-circle */
            transform: rotate(<?php  ?>deg);
            /* Rotate based on percentage */
        }

        .progress-circle-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 18px;
        }

        /* Adjust layout for 3 columns */
        .dashboard-stats .col-md-6 {
            flex: 0 0 33.333333%;
            /* Equal width columns */
            max-width: 33.333333%;
        }
    </style>
</head>

<body>

    <?= $this->include('_menu') ?>

    <main class="container mt-5">
        <div class="row">
            <div class="col-12 text-center">
                <h2>100 Days Fitness Challenge - Analytics</h2>
                <h3>As on <?= date('F j, Y, g:i A', time() + 5.5 * 3600) ?></h3>
            </div>
        </div>

        <div class="row mt-4 dashboard-stats">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h5 class="card-title">Registered Participants</h5>
                        <p class="card-text"><?php echo $total_participants; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h5 class="card-title">Active Participants</h5>
                        <p class="card-text"><?php echo $active_participants; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h5 class="card-title">Distance Completed</h5>
                        <p class="card-text"><?php echo number_format($totalDistance / 1000, 2) . " km"; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4 dashboard-stats">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h5 class="card-title">Avg Activities per Participant</h5>
                        <p class="card-text"><?php echo $averageActivitiesPerParticipant; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h5 class="card-title">Avg Distance per Activity</h5>
                        <p class="card-text"><?= number_format($averageDistance / 1000, 2); ?> km</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h5 class="card-title">Average Time per Activity</h5>
                        <p class="card-text"><?= gmdate('i:s', $averageMovingTime); ?> (MM:SS)</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4 dashboard-stats">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h5 class="card-title">Longest Activity</h5>
                        <p class="card-text">
                            <?php if ($longestActivity) : ?>
                                <?= number_format($longestActivity['distance'] / 1000, 2); ?> km by <?= $longestActivity['user_name']; ?> on <?= date('F j, Y', strtotime($longestActivity['start_date_local'])); ?> <?php else : ?>
                                No activities recorded yet.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">  
                         
                        <h5 class="card-title">Most Active Day</h5>
                        <p class="card-text">
                            <?= $mostActiveDay; ?> </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">  
                         
                        <h5 class="card-title">Most Active Hour</h5>
                        <p class="card-text">
                            <?= $mostActiveHour; ?>:00 </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?= $this->include('_footer') ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>