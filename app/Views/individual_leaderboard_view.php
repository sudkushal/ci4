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
                        <?php $tableData = json_decode($tableData, true); ?>
                        <?php foreach ($tableData as $row) : ?>
                            <tr>
                                <td><?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($row['total_activities'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($row['total_distance_km'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($row['total_time_hours'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($row['activities_per_week'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($row['avg_distance_per_week_km'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($row['avg_time_per_week_hours'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($row['personal_best_distance_km'], ENT_QUOTES, 'UTF-8'); ?></td>
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