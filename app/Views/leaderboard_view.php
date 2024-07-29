<!DOCTYPE html>
<html>

<head>
    <title>100 Days Fitness Challenge</title>
    <link rel="stylesheet" href="<?= base_url('css/style-india.css?v=' . time()); ?>">
    <link rel="icon" href="<?= base_url('favicon.ico'); ?>" type="image/x-icon">
</head>

<body>
    <?= $this->include('_header') ?>

    <main>
    <h2>Consolidated Leaderboard - 100 Days Challenge (Starts 15 August, 2024) </h2>

        <table class='leaderboard-table'>
            <tr>
                <th>Rank</th>
                <th>Name</th>
                <th>Stage 1</th>
                <th>Stage 2</th>
                <th>Stage 3</th>
                <th>Stage 4</th>
                <th>Stage 5</th>
                <th>Total Points</th>
            </tr>

            <?php
            $rank = 1; // Initialize rank
            foreach ($leaderboard_data as $row) :
            ?>
                <tr>
                    <td><?php echo $row['rank_order']; ?></td>
                    <td>
                        <?php if (!empty($row['profile_medium'])) : ?>
                            <img src="<?= $row['profile_medium']; ?>" alt="Athlete Profile" class="profile-pic">
                        <?php endif; ?>
                        <?= $row['name']; ?>
                    </td>
                    <td><?php echo $row['stage1_points']; ?></td>
                    <td><?php echo $row['stage2_points']; ?></td>
                    <td><?php echo $row['stage3_points']; ?></td>
                    <td><?php echo $row['stage4_points']; ?></td>
                    <td><?php echo $row['stage5_points']; ?></td>
                    <td><?php echo $row['total_points']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </main>
    <?= $this->include('_footer') ?>
</body>

</html>