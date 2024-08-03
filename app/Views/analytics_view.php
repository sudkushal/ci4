<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>100 Days Fitness Challenge</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/style-india.css?v=' . time()); ?>">
    <link rel="icon" href="<?= base_url('favicon.ico'); ?>" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    <?= $this->include('_menu') ?>

    <main class="container mt-5">
        <div class="row">
            <div class="col-12 text-center">
                <h2>100 Days Fitness Challenge - Analytics</h2>
                <h3>Challenge Period: <?= date('F j', strtotime($startDate)); ?> - <?= date('F j, Y', strtotime($endDate)); ?></h3>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <h3>Overall Statistics</h3>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Total Distance: <?= number_format($totalDistance / 1000, 2); ?> km</li>
                    <li class="list-group-item">Total Activities: <?= $totalActivities; ?></li>
                    <li class="list-group-item">Average Distance per Activity: <?= number_format($averageDistance / 1000, 2); ?> km</li>
                    <li class="list-group-item">Total Elevation Gain: <?= number_format($totalElevationGain, 2); ?> m</li>
                    <li class="list-group-item">Average Speed: <?= number_format($averageSpeed, 2); ?> km/h</li>
                    <li class="list-group-item">Max Speed: <?= number_format($maxSpeed, 2); ?> km/h</li>
                </ul>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <h3>Participation</h3>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Number of Participants: <?= $participants; ?></li>
                    <li class="list-group-item">Average Activities per Participant: <?= number_format($averageActivitiesPerParticipant); ?></li>
                </ul>
            </div>
            <div class="col-md-6">
                <h3>Distance-Based Insights</h3>
                <ul class="list-group list-group-flush">
                    <?php if ($longestActivity) : ?>
                        <li class="list-group-item">Longest Activity: <?= $longestActivity['name']; ?> (<?= number_format($longestActivity['distance'] / 1000, 2); ?> km) by <?= $longestActivity['user_name']; ?></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <h3>Time-Based Insights</h3>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Total Moving Time: <?= gmdate('H:i:s', $totalMovingTime); ?> (HH:MM:SS)</li>
                    <li class="list-group-item">Average Moving Time per Activity: <?= gmdate('i:s', $averageMovingTime); ?> (MM:SS)</li>
                    <li class="list-group-item">Most Active Day: <?= $mostActiveDay; ?></li>
                    <li class="list-group-item">Most Active Hour of Day: <?= $mostActiveHour; ?>:00</li>
                </ul>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="graphs-container row">
                    <div class="chart-container col-lg-6 col-md-12">
                        <canvas id="distanceByDayChart"></canvas>
                    </div>
                    <div class="chart-container col-lg-6 col-md-12">
                        <canvas id="activitiesByTypeChart"></canvas>
                    </div>
                    <div class="chart-container col-lg-6 col-md-12">
                        <canvas id="cumulativeDistanceChart"></canvas>
                    </div>
                    <div class="chart-container col-lg-6 col-md-12">
                        <canvas id="distanceVsTimeChart"></canvas>
                    </div>
                    <div class="chart-container col-lg-6 col-md-12">
                        <canvas id="activityByHourChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Fetch chart data using AJAX
            fetch('<?= site_url('chart_analytics?type='); ?>')
                .then(response => response.json())
                .then(data => {
                    // Distance By Day Chart (Bar Chart)
                    const distanceCtx = document.getElementById('distanceByDayChart').getContext('2d');
                    new Chart(distanceCtx, {
                        type: 'bar',
                        data: {
                            labels: data.distanceByDayData.map(item => item.day),
                            datasets: [{
                                label: 'Total Distance (km)',
                                data: data.distanceByDayData.map(item => item.total_distance / 1000), // Convert to km
                                backgroundColor: 'rgba(0, 123, 255, 0.7)'
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });

                    // Cumulative Distance Chart (Line Chart)
                    const cumulativeDistanceCtx = document.getElementById('cumulativeDistanceChart').getContext('2d');
                    new Chart(cumulativeDistanceCtx, {
                        type: 'line',
                        data: {
                            labels: data.cumulativeDistanceData.map(item => item.day),
                            datasets: [{
                                label: 'Cumulative Distance (km)',
                                data: data.cumulativeDistanceData.map(item => item.total_distance / 1000),
                                borderColor: 'rgba(75, 192, 192, 1)',
                                fill: false
                            }]
                        }
                    });

                    // Distance vs Time Chart (Scatter Plot)
                    const distanceVsTimeCtx = document.getElementById('distanceVsTimeChart').getContext('2d');
                    new Chart(distanceVsTimeCtx, {
                        type: 'scatter',
                        data: {
                            datasets: [{
                                label: 'Distance vs. Time',
                                data: data.distanceVsTimeData.map(item => ({
                                    x: item.moving_time / 60, // Convert to minutes
                                    y: item.distance / 1000 // Convert to km
                                })),
                                backgroundColor: 'rgba(255, 159, 64, 0.7)'
                            }]
                        },
                        options: {
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Moving Time (minutes)'
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'Distance (km)'
                                    }
                                }
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error('Error fetching or processing chart data:', error);
                    const graphsContainer = document.querySelector('.graphs-container');
                    graphsContainer.innerHTML = '<p class="error">Error loading charts. Please try again later.</p>';
                });
        </script>
    </main>

    <?= $this->include('_footer') ?>

    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
