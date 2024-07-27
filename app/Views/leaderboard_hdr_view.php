<!DOCTYPE html>
<html>
<head>
    <title>HDR Leaderboard - Fitness Challenge</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css'); ?>">
</head>
<body>
    <header>
        <h1>HDR Leaderboard</h1>
        <?= $this->include('_menu') ?>
    </header>

    <main>
        <h2>Top Athletes for HDR Challenge</h2>
        <p>
            <strong>Challenge Period:</strong> <?= date('F j, Y', strtotime($startDate)); ?> - <?= date('F j, Y', strtotime($endDate)); ?><br>
            <strong>Challenge Rules:</strong><br>
            <?= $challengeConfig['minDistance']['km'] ?>+ km for <?= $challengeConfig['minDistance']['minDays'] ?> days: <?= $challengeConfig['minDistance']['pointsPerDay'] ?> points per day<br>
            <?= $challengeConfig['activeDay']['km'] ?>+ km for <?= $challengeConfig['activeDay']['minDays'] ?>-<?= $challengeConfig['activeDay']['maxDays'] ?> days: <?= $challengeConfig['activeDay']['pointsPerDay'] ?> points per day<br>
            <?= $challengeConfig['bonusDay']['km'] ?>+ km on one day (max 1 time): <?= $challengeConfig['bonusDay']['points'] ?> points<br>
            Overall <?= $challengeConfig['overallMinDistance']['km'] ?>+ km: <?= $challengeConfig['overallMinDistance']['points'] ?> points
        </p>

        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Name</th>
                    <th>Points</th>
                    <th>Points Breakdown</th>
                    <th>Total Distance (km)</th>
                    <th>Total Activities</th>
                    <th>Considered Activities</th>
                </tr>
            </thead>
            <tbody>
                <?php $rank = 1; foreach ($leaderboardData as $entry): ?>
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
                            <?php if (!empty($entry['breakdown'])) : 
                                foreach ($entry['breakdown'] as $criterion => $points): ?>
                                    <li><?= $criterion . ': ' . $points; ?> points</li>
                                <?php endforeach; 
                            else : ?>
                                No points earned yet
                            <?php endif; ?>
                        </ul>
                    </td>
                    <td><?= number_format($entry['total_distance'], 2); ?></td>
                    <td><?= $entry['total_activities']; ?></td>
                    <td>
                        <?php if (!empty($entry['considered_activities'])) : ?>
                            <ul>
                                <?php foreach ($entry['considered_activities'] as $activityId) : ?>
                                    <li>
                                        <a href="https://www.strava.com/activities/<?= $activityId; ?>" target="_blank">
                                            Activity <?= $activityId; ?> 
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else : ?>
                            No activities considered
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
    <footer>
        <script>
document.addEventListener('DOMContentLoaded', function () {
    const rows = document.querySelectorAll('.leaderboard-table tbody tr');
    rows.forEach(row => {
        row.addEventListener('click', () => {
            row.classList.toggle('expanded');
        });
    });
});
        </script>
    </footer>
</body>
</html>
