<!DOCTYPE html>
<html>
<head>
    <title>Analytics - 100 Days Fitness Challenge</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css'); ?>"> </head>
<body>
    <header>
    </header>
    <main>
        <h2>100 Days Fitness Challenge - Analytics</h2>
        <h3>Challenge Period: <?= date('F j', strtotime($startDate)); ?> - <?= date('F j, Y', strtotime($endDate)); ?></h3>

        <h3>Overall Statistics</h3>
        <ul>
            <li>Total Distance: <?= number_format($totalDistance / 1000, 2); ?> km</li>
            <li>Total Activities: <?= $totalActivities; ?></li>
            <li>Average Distance per Activity: <?= number_format($averageDistance / 1000, 2); ?> km</li>
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
            <li>Total Moving Time: <?= gmdate('H:i:s', $totalMovingTime); ?> </li>
            <li>Average Moving Time per Activity: <?= gmdate('i:s', $averageMovingTime); ?> </li>
            <li>Most Active Day: <?= $mostActiveDay; ?></li>
            <li>Most Active Hour of Day: <?= $mostActiveHour; ?>:00</li> 
        </ul>

    </main>

    <footer>
    </footer>
</body>
</html>

