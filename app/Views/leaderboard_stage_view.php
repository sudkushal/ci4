<!DOCTYPE html>
<html>

<head>
    <title>Stage Leaderboard - Fitness Challenge</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css'); ?>">
</head>

<body>
    <header>
        <h1>Stage Leaderboard</h1>
        <?= $this->include('_menu') ?>
    </header>

    <main>
    <h2>Overall Leaderboard (All Stages Upto <?= $currentStage ?>)</h2>

    <table class="leaderboard-table">
        <thead>
            <tr>
                <th>Rank</th>
                <th>Name</th>
                <th>Total Points</th>
                <th>Points Breakdown (Stage Wise)</th>
                <th>Total Distance (km)</th>
                <th>Total Activities</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $rank = 1;
            foreach ($leaderboardData as $entry): 
            ?>
            <tr data-details-row="<?= $rank; ?>">
                <td><?= $rank++; ?></td>
                <td>
                    <?php if (!empty($entry['profile_medium'])) : ?>
                        <img src="<?= $entry['profile_medium']; ?>" alt="Athlete Profile" class="profile-pic">
                    <?php endif; ?>
                    <?= $entry['name']; ?>
                </td>
                <td><?= $entry['total_points']; ?></td>
                <td>
                    <button class="show-more">Show More</button>
                    <div class="points-breakdown" id="details-<?= $rank; ?>" style="display:none;">
                        <ul>
                            <?php foreach ($entry['stage_points'] as $stage => $stageData): ?>
                                <li><strong>Stage <?= $stage ?>:</strong> <?= $stageData['points']; ?> points</li>
                                <ul>
                                    <?php if (!empty($stageData['breakdown'])) : ?>
                                        <?php foreach ($stageData['breakdown'] as $criterion => $points): ?>
                                            <li><?= $criterion . ': ' . $points; ?> points</li>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <li>No points earned</li> 
                                    <?php endif; ?>
                                </ul>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </td>
                <td>
                    <?php 
                        $totalDistance = 0; 
                        foreach ($entry['stage_points'] as $stage => $stageData) {
                            $totalDistance += $stageData['total_distance']; // Access total_distance from stageData
                        }
                        echo number_format($totalDistance, 2); 
                    ?> km
                </td>
                <td>
                    <?php
                        $totalActivities = 0;
                        foreach ($entry['stage_points'] as $stage => $stageData) {
                            $totalActivities += $stageData['total_activities'];
                        }
                        echo $totalActivities;
                    ?>
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