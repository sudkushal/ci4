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
            <div class="col-12 text-center">
                <h2>Consolidated Leaderboard - 100 Days Challenge (Starts 15 August, 2024)</h2>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Rank</th>
                                <th scope="col">Name</th>
                                <th scope="col">Stage 1</th>
                                <th scope="col">Stage 2</th>
                                <th scope="col">Stage 3</th>
                                <th scope="col">Stage 4</th>
                                <th scope="col">Stage 5</th>
                                <th scope="col">Total Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leaderboard_data as $row) : ?>
                                <tr>
                                    <td><?php echo $row['rank_order']; ?></td>
                                    <td>
                                        <?php if (!empty($row['profile_medium'])) : ?>
                                            <img src="<?= $row['profile_medium']; ?>" alt="Athlete Profile" class="profile-pic rounded-circle mr-2" style="width: 40px; height: 40px;">
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
</body>

</html>
