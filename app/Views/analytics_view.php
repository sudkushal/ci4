<!DOCTYPE html>
<html>
<head>
    <title>Analytics - 100 Days Fitness Challenge</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css?v=' . time()); ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .graphs-container {
            display: flex; /* Use flexbox for layout */
            flex-wrap: wrap; /* Allow charts to wrap to the next line on smaller screens */
            justify-content: space-around; /* Evenly distribute space between charts */
            gap: 20px;
        }

        .chart-container {
            width: 45%; /* Make each chart container take roughly half the width */
            max-width: 500px;  /* Limit maximum width for larger screens */
        }

        /* Responsive adjustments for smaller screens */
        @media (max-width: 768px) {
            .chart-container {
                width: 95%; /* Take up almost full width on smaller screens */
                margin: 10px auto; /* Center the charts */
            }
        }
    </style>
</head>
<body>

<?= $this->include('_header') ?>

<main>
    <h2>100 Days Fitness Challenge - Analytics</h2>
    <h3>Challenge Period: <?= date('F j', strtotime($startDate)); ?> - <?= date('F j, Y', strtotime($endDate)); ?></h3>

    <h3>Overall Statistics</h3>
    <ul>
        <li>Total Distance: <?= number_format($totalDistance / 1000, 2); ?> km</li>
        <li>Total Activities: <?= $totalActivities; ?></li>
        <li>Average Distance per Activity: <?= number_format($averageDistance / 1000, 2); ?> km</li>
        <li>Total Elevation Gain: <?= number_format($totalElevationGain, 2); ?> m</li>
        <li>Average Speed: <?= number_format($averageSpeed, 2); ?> km/h</li>
        <li>Max Speed: <?= number_format($maxSpeed, 2); ?> km/h</li>
    </ul>

    <h3>Participation</h3>
    <ul>
        <li>Number of Participants: <?= $participants; ?></li>
        <li>Average Activities per Participant: <?= number_format($averageActivitiesPerParticipant, 2); ?></li>
    </ul>

    <h3>Distance-Based Insights</h3>
    <ul>
        <?php if ($longestActivity): ?>
            <li>Longest Activity: <?= $longestActivity['name']; ?> (<?= number_format($longestActivity['distance'] / 1000, 2); ?> km)</li>
        <?php endif; ?>
        <?php if ($shortestActivity): ?>
            <li>Shortest Activity: <?= $shortestActivity['name']; ?> (<?= number_format($shortestActivity['distance'] / 1000, 2); ?> km)</li>
        <?php endif; ?>
    </ul>

    <h3>Time-Based Insights</h3>
    <ul>
        <li>Total Moving Time: <?= gmdate('H:i:s', $totalMovingTime); ?> (HH:MM:SS)</li>
        <li>Average Moving Time per Activity: <?= gmdate('i:s', $averageMovingTime); ?> (MM:SS)</li>
        <li>Most Active Day: <?= $mostActiveDay; ?></li>
        <li>Most Active Hour of Day: <?= $mostActiveHour; ?>:00</li>
    </ul>

    <div class="graphs-container">
        <div class="chart-container">
            <canvas id="distanceByDayChart"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="activitiesByTypeChart"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="cumulativeDistanceChart"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="distanceVsTimeChart"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="activityByHourChart"></canvas>
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

                /* // Activities By Type Chart (Pie Chart)
                const typeCtx = document.getElementById('activitiesByTypeChart').getContext('2d');
                new Chart(typeCtx, {
                    type: 'pie',
                    data: {
                        labels: data.activitiesByTypeData.map(item => item.type),
                        datasets: [{
                            label: 'Activities by Type',
                            data: data.activitiesByTypeData.map(item => item.count),
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.7)',    // Red
                                'rgba(54, 162, 235, 0.7)',   // Blue
                                // ... Add more colors if you have more activity types
                            ],
                            hoverOffset: 4
                        }]
                    }
                }); */

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

                // Activity By Hour (Heatmap - Requires additional Chart.js plugin)
                // You'll need to include and configure the Chart.js heatmap plugin here.
            })
            .catch(error => {
                console.error('Error fetching or processing chart data:', error);
                const graphsContainer = document.querySelector('.graphs-container');
                graphsContainer.innerHTML = '<p class="error">Error loading charts. Please try again later.</p>';
            });
    </script>
</main>

<?= $this->include('_footer') ?>
</body>
</html>
