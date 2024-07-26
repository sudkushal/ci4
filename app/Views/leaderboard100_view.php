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
                        <?php foreach ($entry['points_breakdown'] as $breakdownItem): ?>
                            <li><?= $breakdownItem; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </td>
                <td><?= number_format($entry['total_distance'], 2); ?></td>
                <td>
                    <ul>
                    </ul>
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
