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
        <h2>Top Athletes</h2>
        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Name</th>
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
                        <?php 
                            // Display athlete's profile picture if available
                            if (!empty($entry['profile_medium'])) : ?>
                                <img src="<?= $entry['profile_medium']; ?>" alt="Athlete Profile" class="profile-pic">
                        <?php endif; ?>
                    </td>
                    <td><?= number_format($entry['total_distance'] / 1000, 2); ?></td>  
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
