<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container my-5">
    <div class="row">
        <div class="col-12 text-center">
            <h1>Individual Statistics</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table id="individual-stats-table" class="table table-striped table-bordered table-responsive-sm">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Total Activities</th>
                            <th>Total Distance (km)</th>
                            <th>Total Time (hours)</th>
                            <th>Activities/Week</th>
                            <th>Avg Dist/Week (km)</th>
                            <th>Avg Time/Week (hours)</th>
                            <th>Personal Best Distance (km)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($analytics as $athlete_data) : ?>
                            <tr>
                                <td><?= esc($athlete_data['athlete_name']); ?></td>
                                <td><?= esc($athlete_data['total_activities']); ?></td>
                                <td><?= number_format($athlete_data['total_distance_km'], 2); ?></td>
                                <td><?= esc($athlete_data['total_moving_time']); ?></td>
                                <td><?= number_format($athlete_data['avg_activities_per_week'], 2); ?></td>
                                <td><?= number_format($athlete_data['avg_distance_per_week_km'], 2); ?></td>
                                <td><?= number_format($athlete_data['avg_time_per_week_hours'], 2); ?></td>
                                <td><?= number_format($athlete_data['personal_best_distance_km'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#individual-stats-table').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true,
            pageLength: 15,
            columnDefs: [{
                    targets: [1, 2, 3, 4, 5, 6],
                    className: 'dt-center'
                } // Center align certain columns
            ],
            order: [
                [0, 'asc']
            ], // Default sort by the first column (Name)
            language: {
                emptyTable: "No data available" // Message displayed when no data is available
            }
        });
    });
</script>
<?= $this->endSection() ?>