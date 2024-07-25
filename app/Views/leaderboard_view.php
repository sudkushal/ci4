<!DOCTYPE html>
<html>
<head>
    <title>Leaderboard - 100 Days Fitness Challenge</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css'); ?>">
</head>
<body>
    <header>
        <h1>Leaderboard</h1>
        <nav>
            <ul>
                <li><a href="<?= site_url('/'); ?>">Home</a></li>
                <li><a href="<?= site_url('leaderboard'); ?>">Leaderboard</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Top Athletes (<?= date('F j', strtotime($startDate)); ?> - <?= date('F j, Y', strtotime($endDate)); ?>)</h2> 
        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Name</th>
                    <th>Points</th>
                    <th>Total Distance (km)</th>
                    <th>Total Walks</th>
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

