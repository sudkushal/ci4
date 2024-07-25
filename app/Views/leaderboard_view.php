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
                <li><a href="<?= site_url('analytics'); ?>">Analytics</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?php
            // Determine the filter type from the query parameter
            $filterType = 'all';
            $filterLabel = ($filterType === 'Walk') ? 'Walk' : (($filterType === 'Run') ? 'Run' : 'All Activities');
        ?>

        <h2>Top Athletes (<?= $filterLabel; ?>) (<?= date('F j', strtotime($startDate)); ?> - <?= date('F j, Y', strtotime($endDate)); ?>)</h2>

        

        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Name</th>
                    <th>Points</th>
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
                    <td><?= number_format($entry['total_distance'], 2); ?></td>
                    <td>
                        <?php if (!empty($entry['qualifying_activities'])) : ?>
                            <ul>
                                <?php foreach ($entry['qualifying_activities'] as $activity) : ?>
                                    <li>
                                        <a href="https://www.strava.com/activities/<?= $activity['activity_id']; ?>" target="_blank">
                                            <?= $activity['name']; ?> (<?= number_format($activity['distance'], 2); ?> km)
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
