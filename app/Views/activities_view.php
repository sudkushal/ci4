<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Longest Activity</h1>
    <h5 class="text-center mb-4">Only activity from this list is considered for score/ranks.</h5>

    <div class="row mb-4">
        <div class="col-md-4">
            <label for="filter-username">Filter by User Name:</label>
            <select id="filter-username" class="form-control" data-column="1">
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
                        <td>
                            <a href="https://www.strava.com/activities/<?= esc($activity['activity_id']); ?>" target="_blank">
                                <?= esc($activity['activity_id']); ?>
                            </a>
                        </td>
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
        var table = $('#activities-table').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            responsive: true,
            pageLength: 15,
            order: [[2, 'desc']], 
            columnDefs: [
                { targets: [3], className: 'text-center' }, 
                { targets: [4], className: 'text-center' }, 
                { targets: [1], className: 'text-center' } 
            ]
        });

        $('#filter-username, #filter-stage').on('change', function() {
            table.column($(this).data('column')).search($(this).val()).draw();
        });
    });
</script>

<?= $this->endSection() ?>