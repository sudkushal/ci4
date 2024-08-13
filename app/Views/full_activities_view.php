<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
    <?php
    $userNames = [];

    foreach ($fullActivities as $activity) {
        if (!in_array($activity['user_name'], $userNames)) {
            $userNames[] = $activity['user_name'];
        }
    }
    ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Full Activities (from August 1st)</h1>

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
        </div> 

        <div class="table-responsive">
            <table id="activities-table" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Strava Athlete ID</th>
                        <th>User Name</th>
                        <th>Activity Date</th>
                        <th>Distance (km)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fullActivities as $activity) : ?>
                        <tr>
                            <td><?= esc($activity['strava_athlete_id']); ?></td>
                            <td><?= esc($activity['user_name']); ?></td>
                            <td><?= esc($activity['activity_date']); ?></td>
                            <td><?= esc(number_format($activity['distance'] / 1000, 2)); ?></td> 
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
            var table = $('#activities-table').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                responsive: true,
                pageLength: 15,
                order: [
                    [2, 'desc'] 
                ],
                columnDefs: [
                    { targets: [3], className: 'text-center' }, 
                    { targets: [1], className: 'text-center' } 
                ]
            });

            $('#filter-username').on('change', function() {
                var searchTerm = $(this).val();
                table.column(1).search(searchTerm).draw();
            });

        });
    </script>
<?= $this->endSection() ?>

