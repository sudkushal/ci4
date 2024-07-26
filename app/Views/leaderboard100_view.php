<!DOCTYPE html>
<html>
<head>
    <title>100 Days Leaderboard - Fitness Challenge</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css'); ?>"> 
</head>
<body>
<header>
    <h1>100 Days Leaderboard</h1>
    <?= $this->include('_menu') ?> 
</header>

<main>
    <h2>Top Athletes for 100 Days Challenge </h2>
    
    <table class="leaderboard-table">
        <thead>
            <tr>
                <th>Rank</th>
                <th>Name</th>
                <th>Points</th>
                <th>Points Breakdown</th>
                <th>Total Distance (km)</th>
                <th>Qualifying Activities</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $rank = 1;
            foreach ($leaderboardData as $entry): 
            ?>
            <tr>
                <td><?= $rank++; ?></td>
                <td>
                    <?php if (!empty($entry['profile_medium'])) : ?>
                        <img src="<?= $entry['profile_medium']; ?>" alt="Athlete Profile" class="profile-pic">
                    <?php endif; ?>
                    <?= $entry['name']; ?>
                </td>
                <td><?= $entry['points']; ?></td>
                <td>
                    <ul>
                        <?php foreach ($entry['points_breakdown'] as $criterion => $points): ?>
                            <li><?= $criterion . ': ' . $points; ?> points</li>
                        <?php endforeach; ?>
                    </ul>
                </td>
                <td><?= number_format($entry['total_distance'], 2); ?></td>
                <td>
                    <?php if (!empty($entry['qualifying_activities'])) : ?>
                        <ul>
                            <?php foreach ($entry['qualifying_activities'] as $activity) : ?>
                                <li>
                                    <a href="https://www.strava.com/activities/<?= $activity['activity_id']; ?>" target="_blank">
                                        <?= $activity['name']; ?> (<?= number_format($activity['distance'] / 1000, 2); ?> km)
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else : ?>
                        No qualifying activities
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<footer>
</footer>
</body>
</html>
