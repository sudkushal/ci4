<!DOCTYPE html>
<html>

<head>
    <title>100 Days Fitness Challenge</title>
    <link rel="stylesheet" href="<?= base_url('css/' . session()->get('selectedStyle').'?v=' . time()); ?>">
    <link rel="icon" href="<?= base_url('favicon.ico'); ?>" type="image/x-icon">
</head>

<body>

    <?= $this->include('_header') ?>

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
                    <th>Total Distance (km)</th>
                    <th>Total Activities</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php $rank = 1;
                foreach ($leaderboardData as $entry) : ?>
                    <tr data-details-row="<?= $rank; ?>">
                        <td><?= $rank++; ?></td>
                        <td>
                            <?php if (!empty($entry['profile_medium'])) : ?>
                                <img src="<?= $entry['profile_medium']; ?>" alt="Athlete Profile" class="profile-pic">
                            <?php endif; ?>
                            <?= $entry['name']; ?>
                        </td>
                        <td><?= $entry['points']; ?></td>
                        <td><?= number_format($entry['total_distance'], 2); ?> km</td>
                        <td><?= $entry['total_activities']; ?></td>
                        <td><button class="show-more">Show More</button></td>
                    </tr>
                    <tr class="details-row" id="details-<?= $rank; ?>" style="display:none;">
                        <td colspan="6">
                            <strong>Points Breakdown:</strong>
                            <div class="points-breakdown">
                                <?php if (!empty($entry['breakdown'])) :
                                    foreach ($entry['breakdown'] as $criterion => $points) : ?>
                                        <li><?= $criterion . ': ' . $points; ?> points</li>
                                    <?php endforeach;
                                else : ?>
                                    No points earned yet
                                <?php endif; ?>
                            </div>

                            <strong>Activities Considered:</strong>
                            <div class="activities-considered">
                                <?php if (!empty($entry['considered_activities'])) : ?>
                                    <ul>
                                        <?php foreach ($entry['considered_activities'] as $activity) : ?>
                                            <li>
                                                <a href="https://www.strava.com/activities/<?= $activity['activity_id']; ?>" target="_blank">
                                                    <?= $activity['activity_name']; ?> (Activity ID: <?= $activity['activity_id']; ?>)
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else : ?>
                                    No activities considered
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <?= $this->include('_footer') ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const showMoreButtons = document.querySelectorAll('.show-more');

            showMoreButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Find the details row by looking for the next sibling with class 'details-row'
                    const detailsRow = this.closest('tr').nextElementSibling;
                    if (detailsRow && detailsRow.classList.contains('details-row')) { // Check if the next row is indeed a details row
                        detailsRow.style.display = detailsRow.style.display === 'none' ? 'table-row' : 'none';
                        this.textContent = detailsRow.style.display === 'none' ? 'Show More' : 'Show Less';
                    }
                });
            });
        });
    </script>
</body>

</html>