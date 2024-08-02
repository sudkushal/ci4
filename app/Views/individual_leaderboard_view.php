<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>100 Days Fitness Challenge</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tabulator CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tabulator/5.4.3/css/tabulator.min.css" rel="stylesheet">
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
                <div id="individual-stats-table" class="mt-4"></div> <!-- This div will hold the Tabulator table -->
            </div>
        </div>
    </div>

    <?= $this->include('_footer') ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Tabulator JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tabulator/5.4.3/js/tabulator.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Use PHP to output the table data directly into the JavaScript
            const tableData = <?= $tableData; ?>;

            // Initialize Tabulator with the data
            const table = new Tabulator("#individual-stats-table", {
                data: tableData, // Assign data to table
                layout: "fitColumns", // Fit columns to width of table
                responsiveLayout: "collapse", // Collapse columns that don't fit on the screen
                tooltips: true, // Show tooltips on cells
                addRowPos: "top", // When adding a new row, place it at the top of the table
                history: true, // Allow undo and redo actions on the table
                pagination: "local", // Paginate the data
                paginationSize: 15, // Number of rows per page
                movableColumns: true, // Allow column order to be changed
                resizableRows: true, // Allow row order to be changed
                initialSort: [ // Define the initial sorting order
                    { column: "name", dir: "asc" },
                ],
                columns: [
                    { title: "Name", field: "name", headerFilter: "input" },
                    { title: "Max Distance", field: "max_distance", sorter: "number", hozAlign: "center" },
                    { title: "Total Distance", field: "total_distance", sorter: "number", hozAlign: "center" },
                    { title: "Total Time", field: "total_time", sorter: "number", hozAlign: "center" },
                    { title: "Max Speed", field: "max_speed", sorter: "number", hozAlign: "center" },
                    { title: "Activities Per Week", field: "activities_per_week", sorter: "number", hozAlign: "center" },
                    { title: "Avg Dist Per Week", field: "avg_distance_per_week", sorter: "number", hozAlign: "center" },
                    { title: "Avg Time Per Week", field: "avg_time_per_week", sorter: "number", hozAlign: "center" }
                ]
            });
        });
    </script>
</body>

</html>
