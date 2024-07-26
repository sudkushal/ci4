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
    <h2>Top Athletes for 100 Days Challenge</h2>
    <p>
        <strong>Challenge Rules:</strong><br>
        <?= $challengeConfig['minQualifyingDays'] ?> days of <?= $challengeConfig['minDailyDistance'] ?>+ km: <?= $challengeConfig['pointsPerDay'] * $challengeConfig['minQualifyingDays'] ?> points<br>
        <?= $challengeConfig['maxBonusDays'] ?> days of <?= $challengeConfig['bonusDistance'] ?>+ km: <?= $challengeConfig['bonusPoints'] * $challengeConfig['maxBonusDays'] ?> points<br>
        Overall <?= $overallMinDistance; ?>+ km: <?= $challengeConfig['overallPoints'] ?> points
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
                            <?php if (!empty($entry['points_breakdown'])) : 
                                foreach ($entry['points_breakdown'] as $criterion => $points): ?>
                                    <li><?= $criterion . ': ' . $points; ?> points</li>
                                <?php endforeach; 
                            else : ?>
                                No points earned yet
                            <?php endif; ?>
                        </ul>
                    </td>
                    <td><?= number_format($entry['total_distance'], 2); ?></td> 
                    <td><?= $entry['total_activities']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<footer>
</footer>
</body>
</html>
