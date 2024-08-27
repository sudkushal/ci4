<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>100 Days Fitness Challenge</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/style-india.css?v=' . time()); ?>">
    <link rel="icon" href="<?= base_url('favicon.ico'); ?>" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            transform: rotate(<?php ?>deg);
            /* Rotate based on percentage */
        }

        .progress {
            height: 20px;
            /* Adjust the height as needed */
            background-color: #e9ecef;
            /* Light gray background */
            border-radius: 5px;
            margin-bottom: 20px;
            /* Add some spacing below the progress bar */
        }

        .progress-bar {
            background-color: #4CAF50;
            /* Green */
            width: <?php echo $progress_percentage; ?>%;
            height: 100%;
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
                <h2>100 Days Challenge - Analytics</h2>
                <h3>As on <?= date('F j, Y, g:i A', time() + 5.5 * 3600) ?></h3>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $progress_percentage; ?>%;" aria-valuenow="<?php echo $progress_percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="d-flex justify-content-between">
                    <small><?= date('F j, Y', strtotime($startDate)); ?></small>
                    <small><?= date('F j, Y', strtotime($endDate)); ?></small>
                </div>
            </div>
        </div>

        <div class="row mt-4 dashboard-stats">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h5 class="card-title">Leaderboard</h5>
                        <p class="card-text"><?php if (!empty($top5Ranks)) : ?>
                        <ol>
                            <?php foreach ($top5Ranks as $rank) : ?>
                                <li><?= $rank['participant_name'] ?> (Rank: <?= $rank['rank_order'] ?>)</li>
                            <?php endforeach; ?>
                        </ol>
                    <?php else : ?>
                        <p>No ranks found yet.</p>
                    <?php endif; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h5 class="card-title">Engagement Champions</h5>
                        <p class="card-text">
                        <table>
                            <thead>
                                <tr>
                                    <th>Stage</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($participantsCompletingMoreThan3 as $stage => $count) : ?>
                                    <tr>
                                        <td><?= $stage ?></td>
                                        <td><?= $count ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        </p>
                    </div>
                </div>
            </div>
        </div>


        <div class="row mt-4 dashboard-stats">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h5 class="card-title">Registered Participants</h5>
                        <p class="card-text"><?php echo $total_participants; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h5 class="card-title">Active Participants</h5>
                        <p class="card-text"><?php echo $active_participants; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h5 class="card-title">Distance Completed</h5>
                        <p class="card-text"><?php echo number_format($totalDistance / 1000, 2) . " km"; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4 dashboard-stats">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h5 class="card-title">Avg Activities per Participant</h5>
                        <p class="card-text"><?php echo $averageActivitiesPerParticipant; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h5 class="card-title">Avg Distance per Activity</h5>
                        <p class="card-text"><?= number_format($averageDistance / 1000, 2); ?> km</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h5 class="card-title">Average Time per Activity</h5>
                        <p class="card-text"><?= gmdate('i:s', $averageMovingTime); ?> (MM:SS)</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4 dashboard-stats">
            <div class="col-12 col-md-8">
                <div class="card">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h5 class="card-title">Longest Activity</h5>
                        <p class="card-text">
                            <?php if ($longestActivity) : ?>
                                <?= number_format($longestActivity['distance'] / 1000, 2); ?> km by <?= $longestActivity['user_name']; ?> on <?= date('F j, Y', strtotime($longestActivity['start_date_local'])); ?>
                            <?php else : ?>
                                No activities recorded yet.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h5 class="card-title">Most Active Day</h5>
                        <p class="card-text">
                            <?= $mostActiveDay; ?> </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">KMs per Stage</h5>
                        <canvas id="kmsPerStageChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Participants per Stage</h5>
                        <canvas id="participantsPerStageChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Challenges Completed per Stage</h5>
                        <canvas id="challengesCompletedChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"></h5>
                        <canvas id="distanceDistributionChart1"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?= $this->include('_footer') ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-trendline"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
    <script>
        const stageStats = <?php echo json_encode($stageStats); ?>;
        const kmsPerStageData = {
            labels: Object.keys(stageStats),
            datasets: [{
                label: 'KMs',
                data: Object.values(stageStats).map(stats => stats.total_distance), // Extract total distances
                backgroundColor: 'rgba(75, 192, 192, 0.2)', // Example color
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        };

        const kmsPerStageConfig = {
            type: 'bar',
            data: kmsPerStageData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    trendline: {
                        style: 'rgba(255, 0, 0, .8)', // Red color for the trendline
                        width: 2
                    }
                }
            }
        };

        const kmsPerStageChart = new Chart(
            document.getElementById('kmsPerStageChart'),
            kmsPerStageConfig
        );
    </script>
    <script>
        const participantsPerStageChartData = {
            labels: Object.keys(stageStats),
            datasets: [{
                    label: 'Count',
                    data: Object.values(stageStats).map(stats => stats.participant_count), // Extract total distances
                    backgroundColor: 'rgba(75, 192, 192, 0.2)', // Example color
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                { // Add the trendline dataset
                    type: 'line',
                    label: 'Trendline',
                    data: Object.values(stageStats).map(stats => stats.participant_count),
                    borderColor: 'rgba(255, 0, 0, .8)',
                    borderWidth: 2
                }
            ]
        };

        const participantsPerStageChartConfig = {
            type: 'bar',
            data: participantsPerStageChartData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    trendline: {
                        style: 'rgba(255, 0, 0, .8)', // Red color for the trendline
                        width: 2
                    }
                }
            }
        };

        const participantsPerStageChart = new Chart(
            document.getElementById('participantsPerStageChart'),
            participantsPerStageChartConfig
        );
    </script>
    <script>
        <?php
        // Extract labels (stage names)
        $labels = array_keys($challengesCompleted);

        // Extract datasets (one for each number of challenges completed)
        $datasets = [];
        $maxChallengesCompleted = max(array_map(function ($stageData) {
            return max(array_keys($stageData));
        }, $challengesCompleted));

        for ($challenges = 1; $challenges <= $maxChallengesCompleted; $challenges++) {
            $dataset = [
                'label' => $challenges . ' Challenge' . ($challenges == 1 ? '' : 's') . ' Completed',
                'data' => [],
                'backgroundColor' => sprintf('rgba(%d, %d, %d, 0.2)', rand(0, 255), rand(0, 255), rand(0, 255)), // Random color for each dataset
                'borderColor' => sprintf('rgba(%d, %d, %d, 1)', rand(0, 255), rand(0, 255), rand(0, 255)),
                'borderWidth' => 1
            ];

            foreach ($labels as $stage) {
                $dataset['data'][] = $challengesCompleted[$stage][$challenges] ?? 0; // Default to 0 if no one completed that many challenges in a stage
            }

            $datasets[] = $dataset;
        }
        ?>
        const challengesCompletedChartData = {
            labels: <?php echo json_encode($labels); ?>,
            datasets: <?php echo json_encode($datasets); ?>
        };

        const challengesCompletedChartConfig = {
            type: 'bar',
            data: challengesCompletedChartData,
            options: {
                scales: {
                    x: {
                        stacked: true
                    },
                    y: {
                        beginAtZero: true,
                        stacked: true
                    }
                },

                plugins: {
                    datalabels: {
                        formatter: (value, context) => {
                            // Only display labels if the value is greater than 0
                            return value > 0;
                        },
                        color: 'black',
                        font: {
                            weight: 'bold'
                        }
                    }
                }
            }
        };

        const challengesCompletedChart = new Chart(
            document.getElementById('challengesCompletedChart'),
            challengesCompletedChartConfig
        );
    </script>
    <script>
        <?php
        $labels = array_keys($distanceDistribution);
        $datasets = [];

        foreach ($labels as $stage) {
            $dataset = [
                'label' => $stage,
                'data' => $distanceDistribution[$stage],
                'backgroundColor' => sprintf('rgba(%d, %d, %d, 0.2)', rand(0, 255), rand(0, 255), rand(0, 255)),
                'borderColor' => sprintf('rgba(%d, %d, %d, 1)', rand(0, 255), rand(0, 255), rand(0, 255)),
                'borderWidth' => 1
            ];
            $datasets[] = $dataset;
        }
        ?>
        const distanceDistributionData = {
            labels: <?php echo json_encode($labels); ?>,
            datasets: <?php echo json_encode($datasets); ?>
        };

        const distanceDistributionConfig = {
            type: 'bar', // You can change this to 'histogram' if Chart.js supports it directly
            data: distanceDistributionData,
            options: {
                scales: {
                    x: {
                        type: 'linear', // Important for histograms
                        beginAtZero: true,
                        binning: {
                            bins: [60, 70, 80, 90, 100, 110, 120] // Example buckets: 0-10 km, 10-20 km, ...
                        }
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        const distanceDistributionChart = new Chart(
            document.getElementById('distanceDistributionChart'),
            distanceDistributionConfig
        );
    </script>

</body>

</html>