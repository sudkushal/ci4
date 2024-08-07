<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>100 Days Fitness Challenge</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('css/style-individual.css?v=' . time()); ?>">
    <link rel="icon" href="<?= base_url('favicon.ico'); ?>" type="image/x-icon">
</head>

<body>

    <?= $this->include('_menu') ?>

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
                            <th>Activities Per Week</th>
                            <th>Avg Dist Per Week</th>
                            <th>Avg Time Per Week</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $tableData = json_decode($tableData, true); ?>
                        <?php if (is_array($tableData) && !empty($tableData)) : ?>
                            <?php foreach ($tableData as $row) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($row['max_distance'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($row['total_distance'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($row['total_time'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($row['activities_per_week'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($row['avg_distance_per_week'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($row['avg_time_per_week'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="8" class="text-center">No data available</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?= $this->include('_footer') ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap4.min.js"></script>

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
                ] // Default sort by the first column (Name)
            });
        });
    </script>
</body>

</html>