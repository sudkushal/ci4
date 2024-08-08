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
            <!-- Bootstrap Responsive Table with DataTables -->
            <table id="individual-stats-table" class="table table-striped table-bordered table-responsive-sm">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Max Distance</th>
                        <th>Total Distance</th>
                        <th>Total Time</th>
                        <th>Avg Activities/Week</th>
                        <th>Avg Dist/Week</th>
                        <th>Avg Time/Week</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $tableData = json_decode($tableData, true); ?>
                    <?php foreach ($tableData as $row) : ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars(number_format($row['max_distance'], 2), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars(number_format($row['total_distance'], 2), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars(number_format($row['total_time'], 2), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars($row['activities_per_week'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars(number_format($row['avg_distance_per_week'], 2), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars(number_format($row['avg_time_per_week'], 2), ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
