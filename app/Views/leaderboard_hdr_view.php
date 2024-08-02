<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>100 Days Fitness Challenge</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/style-india.css?v=' . time()); ?>">
    <link rel="icon" href="<?= base_url('favicon.ico'); ?>" type="image/x-icon">
</head>

<body>
    <?= $this->include('_menu') ?>

    <main class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center">Top Athletes for Step It Up Challenge</h2>
                <p class="text-center">
                    <strong>Challenge Period:</strong> <?= date('F j, Y', strtotime($startDate)); ?> - <?= date('F j, Y', strtotime($endDate . '-1 day')); ?><br>
                    <strong>Challenge Rules:</strong><br>
                    <?= $challengeConfig['minDistance']['km'] ?>+ km for <?= $challengeConfig['minDistance']['minDays'] ?> days: <?= $challengeConfig['minDistance']['pointsPerDay'] ?> points per day<br>
                    <?= $challengeConfig['activeDay']['km'] ?>+ km for <?= $challengeConfig['activeDay']['minDays'] ?>-<?= $challengeConfig['activeDay']['maxDays'] ?> days: <?= $challengeConfig['activeDay']['pointsPerDay'] ?> points per day<br>
                    <?= $challengeConfig['bonusDay']['km'] ?>+ km on one day (max 1 time): <?= $challengeConfig['bonusDay']['points'] ?> points<br>
                    Overall <?= $challengeConfig['overallMinDistance']['km'] ?>+ km: <?= $challengeConfig['overallMinDistance']['points'] ?> points
                </p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="thead-dark">
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
                                            <?php if (strpos($entry['profile_medium'], 'avatar/athlete/medium.png') !== false) : ?>
                                                <img src="<?= base_url('images/replacement_image.png'); ?>" alt="Athlete Profile" class="profile-pic">
                                            <?php else : ?>
                                                <img src="<?= $entry['profile_medium']; ?>" alt="Athlete Profile" class="profile-pic">
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?= $entry['name']; ?>
                                    </td>
                                    <td><?= $entry['points']; ?></td>
                                    <td><?= number_format($entry['total_distance'], 2); ?> km</td>
                                    <td><?= $entry['total_activities']; ?></td>
                                    <td><button class="btn btn-primary show-more btn-sm">Show More</button></td>
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
                </div>
            </div>
        </div>
    </main>

    <?= $this->include('_footer') ?>

    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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
