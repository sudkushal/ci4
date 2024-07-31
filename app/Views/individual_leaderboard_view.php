<!DOCTYPE html>
<html>

<head>
    <title>100 Days Fitness Challenge</title>
    <link rel="stylesheet" href="<?= base_url('css/style-individual.css?v=' . time()); ?>">
    <link rel="icon" href="<?= base_url('favicon.ico'); ?>" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tabulator/5.4.3/css/tabulator.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tabulator/5.4.3/js/tabulator.min.js"></script>

</head>

<body>

    <?= $this->include('_header') ?>
    <h1>Individual Statistics</h1>
    <main>
        <div id="individual-stats-table"></div> <!-- This div will hold the Tabulator table -->
    </main>
    <?= $this->include('_footer') ?>
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
                    { title: "Total Activities", field: "total_activities", sorter: "number", hozAlign: "center" },
                    { title: "Max Distance", field: "max_distance", sorter: "number", hozAlign: "center" },
                    { title: "Total Distance", field: "total_distance", sorter: "number", hozAlign: "center" },
                    { title: "Total Time", field: "total_time", sorter: "number", hozAlign: "center" },
                    { title: "Total Elevation", field: "total_elevation", sorter: "number", hozAlign: "center" },
                    { title: "Max Speed", field: "max_speed", sorter: "number", hozAlign: "center" },
                    { title: "Max Average Speed", field: "max_avg_speed", sorter: "number", hozAlign: "center" },
                    { title: "Activities Per Week", field: "activities_per_week", sorter: "number", hozAlign: "center" }
                ]
            });
        });
    </script>
</body>

</html>