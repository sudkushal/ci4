<!-- activities_view.php -->
<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
    <?php
    // Arrays to hold unique values
    $userNames = [];
    $stages = [];

    foreach ($activities as $activity) {
        if (!in_array($activity['user_name'], $userNames)) {
            $userNames[] = $activity['user_name'];
        }
        if (!in_array($activity['stage'], $stages)) {
            $stages[] = $activity['stage'];
        }
    }
    ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Longest Activity</h1>
        <h5 class="text-center mb-4">Only activity from this list is considered for score/ranks.</h5>

        <!-- Filter Section -->
        <div class="row mb-4">
            <div class="col-md-4">
                <label for="filter-username">Filter by User Name:</label>
                <select id="filter-username" class="form-control">
                    <option value="">All</option>
                    <?php foreach ($userNames as $userName) : ?>
                        <option value="<?= esc($userName); ?>"><?= esc($userName); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="filter-stage">Filter by Stage:</label>
                <select id="filter-stage" class="form-control">
                    <option value="">All</option>
                    <?php foreach ($stages as $stage) : ?>
                        <option value="<?= esc($stage); ?>"><?= esc($stage); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table id="activities-table" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Strava Athlete ID</th>
                        <th>User Name</th>
                        <th>Activity Date</th>
                        <th>Strava Activity ID</th>
                        <th>Distance (km)</th>
                        <th>Stage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activities as $activity) : ?>
                        <tr>
                            <td><?= esc($activity['strava_athlete_id']); ?></td>
                            <td><?= esc($activity['user_name']); ?></td>
                            <td><?= esc($activity['activity_date']); ?></td>
                            <td><a href="https://www.strava.com/activities/<?= esc($activity['activity_id']); ?>" target="_blank">
                                <?= esc($activity['activity_id']); ?>
                            </a></td>
                            <td><?= esc(number_format($activity['distance'] / 1000, 2)); ?></td>
                            <td><?= esc($activity['stage']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#activities-table').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                responsive: true,
                pageLength: 15,
                order: [
                    [2, 'desc']
                ], // Order by activity_date by default
                columnDefs: [{
                        targets: [3],
                        className: 'text-center'
                    }, // Center align the distance column
                    {
                        targets: [4],
                        className: 'text-center'
                    }, // Center align the stage column
                    {
                        targets: [1],
                        className: 'text-center'
                    } // Center align the user_name column
                ]
            });

            // Custom filtering function for User Name
            $('#filter-username').on('change', function() {
                var searchTerm = $(this).val();
                table.column(1).search(searchTerm).draw();
            });

            // Custom filtering function for Stage
            $('#filter-stage').on('change', function() {
                var searchTerm = $(this).val();
                table.column(4).search(searchTerm).draw();
            });
        });
    </script>
<?= $this->endSection() ?>
