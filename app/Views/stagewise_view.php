<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>100 Days Fitness Challenge</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tabulator/5.4.3/css/tabulator.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/style-individual.css?v=' . time()); ?>">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <link rel="icon" href="<?= base_url('favicon.ico'); ?>" type="image/x-icon">
    <style>
        .dataTables_wrapper .dataTables_filter input {
            background-color: white;
        }

        .dt-buttons {
            display: inline-block !important;
        }

        #goToTopBtn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 99;
        }

        @media (max-width: 768px) {
            #goToTopBtn {
                bottom: 10px;
                right: 10px;
                font-size: 12px;
            }
        }

        /* Default styles for all screen sizes */
        .table-responsive-sm {
            overflow-x: auto;
        }

        .table-responsive-sm table {
            display: table;
            width: 100%;
            background-color: #fff;
            /* White background */
            border-collapse: collapse;
            /* Remove space between borders */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            /* Subtle shadow */
        }

        .table-responsive-sm th,
        .table-responsive-sm td {
            border: 1px solid #ddd;
            /* Light gray border */
            padding: 10px 15px;
            /* More padding for better readability */
            vertical-align: middle;
        }

        .table-responsive-sm th {
            background-color: #f0f0f0;
            /* Light gray header background */
            font-weight: bold;
        }

        .table-responsive-sm tbody tr {
            display: table-row;
        }

        .table-responsive-sm tbody td {
            text-align: left;
            padding: 15px;
            background-color: #f8f8f8;
            /* Very light gray background */
            border-bottom: 1px solid #ddd;
            /* Add a bottom border */
        }

        /* Media query for smaller screens (adjust the breakpoint if needed) */
        @media (max-width: 768px) {
            .table-responsive-sm table thead {
                display: none !important;
            }

            .table-responsive-sm table {
                display: block;
                overflow-x: auto;
            }

            .table-responsive-sm tbody td {
                display: block;
                width: 100%;
                text-align: right;
            }

            .table-responsive-sm tbody td:before {
                content: attr(data-label) ": ";
                float: left;
                font-weight: bold;
            }
        }

        /* Tab Navigation Styling */
        .nav-tabs .nav-link {
            color: #fff !important;
            /* Set the text color to white */
            background-color: #007bff;
            /* Set the background color to blue */
            border: 1px solid #007bff;
            /* Add a border to the tabs */
            margin-right: 5px;
            /* Space between tabs */
            border-radius: 5px;
            /* Rounded corners */
        }

        .nav-tabs .nav-link.active {
            background-color: #0056b3;
            /* Darker blue for the active tab */
            color: #fff !important;
            /* Keep the text color white */
            border-color: #004085;
            /* Darker border for active tab */
        }

        .nav-tabs {
            border-bottom: none;
            /* Remove the default bottom border */
        }

        /* Hover effect for tabs */
        .nav-tabs .nav-link:hover {
            background-color: #0056b3;
            /* Darker blue on hover */
            border-color: #004085;
            /* Darker border on hover */
        }

        /* Adjust the spacing and padding for the tabs */
        .nav-tabs .nav-item {
            padding: 0.5rem 1rem;
        }
    </style>
</head>

<body>
    <?= $this->include('_menu') ?>

    <div class="container my-4">
        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs" id="fitnessChallengeTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="stage1-tab" data-toggle="tab" href="#stage1" role="tab" aria-controls="stage1" aria-selected="true">Stage 1</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="stage2-tab" data-toggle="tab" href="#stage2" role="tab" aria-controls="stage2" aria-selected="false">Stage 2</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="stage3-tab" data-toggle="tab" href="#stage3" role="tab" aria-controls="stage3" aria-selected="false">Stage 3</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="stage4-tab" data-toggle="tab" href="#stage4" role="tab" aria-controls="stage4" aria-selected="false">Stage 4</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="stage5-tab" data-toggle="tab" href="#stage5" role="tab" aria-controls="stage5" aria-selected="false">Stage 5</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="consolidated-tab" data-toggle="tab" href="#consolidated" role="tab" aria-controls="consolidated" aria-selected="false">Consolidated</a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="fitnessChallengeTabContent">
            <!-- Stage 1 Content -->
            <div class="tab-pane fade show active" id="stage1" role="tabpanel" aria-labelledby="stage1-tab">
                <div class="container table-responsive-sm py-4">
                    <h2 class="text-center">Stage 1</h2>
                    <table class="table table-striped table-bordered" id="stage1Table">
                        <thead>
                            <tr>
                                <th>Participant Name</th>
                                <th>Daily Points</th>
                                <th>Bonus Points</th>
                                <th>Active Day Points</th>
                                <th>Total Distance</th>
                                <th>Min Distance Points</th>
                                <th>Stage Total Points</th>
                                <th>Challenges Completed (x/4)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stage1_data as $row) : ?>
                                <tr>
                                    <td data-label="Participant Name"><?= $row['participant_name']; ?></td>
                                    <td data-label="Daily Points"><?= $row['daily_points']; ?></td>
                                    <td data-label="Bonus Points"><?= $row['bonus_points']; ?></td>
                                    <td data-label="Active Day Points"><?= $row['active_day_points']; ?></td>
                                    <td data-label="Total Distance"><?= number_format($row['total_distance'], 2); ?></td>
                                    <td data-label="Min Distance Points"><?= $row['min_distance_points']; ?></td>
                                    <td data-label="Stage Total Points"><?= $row['stage_total_points']; ?></td>
                                    <td data-label="Challenge Completed (x/4)"><?= $row['challenges_completed']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Repeat similar structure for Stage 2 to Stage 5 and Consolidated -->

            <div class="tab-pane fade" id="stage2" role="tabpanel" aria-labelledby="stage2-tab">
                <div class="container table-responsive-sm py-4">
                    <h2 class="text-center">Stage 2</h2>
                    <table class="table table-striped table-bordered" id="stage2Table">
                        <thead>
                            <tr>
                                <th>Participant Name</th>
                                <th>Daily Points</th>
                                <th>Bonus Points</th>
                                <th>Active Day Points</th>
                                <th>Total Distance</th>
                                <th>Min Distance Points</th>
                                <th>Stage Total Points</th>
                                <th>Challenges Completed (x/4)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stage2_data as $row) : ?>
                                <tr>
                                    <td data-label="Participant Name"><?= $row['participant_name']; ?></td>
                                    <td data-label="Daily Points"><?= $row['daily_points']; ?></td>
                                    <td data-label="Bonus Points"><?= $row['bonus_points']; ?></td>
                                    <td data-label="Active Day Points"><?= $row['active_day_points']; ?></td>
                                    <td data-label="Total Distance"><?= number_format($row['total_distance'], 2); ?></td>
                                    <td data-label="Min Distance Points"><?= $row['min_distance_points']; ?></td>
                                    <td data-label="Stage Total Points"><?= $row['stage_total_points']; ?></td>
                                    <td data-label="Challenge Completed (x/4)"><?= $row['challenges_completed']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="stage3" role="tabpanel" aria-labelledby="stage3-tab">
                <div class="container table-responsive-sm py-4">
                    <h2 class="text-center">Stage 3</h2>
                    <table class="table table-striped table-bordered" id="stage3Table">
                        <thead>
                            <tr>
                                <th>Participant Name</th>
                                <th>Daily Points</th>
                                <th>Bonus Points</th>
                                <th>Active Day Points</th>
                                <th>Total Distance</th>
                                <th>Min Distance Points</th>
                                <th>Stage Total Points</th>
                                <th>Challenges Completed (x/4)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stage3_data as $row) : ?>
                                <tr>
                                    <td data-label="Participant Name"><?= $row['participant_name']; ?></td>
                                    <td data-label="Daily Points"><?= $row['daily_points']; ?></td>
                                    <td data-label="Bonus Points"><?= $row['bonus_points']; ?></td>
                                    <td data-label="Active Day Points"><?= $row['active_day_points']; ?></td>
                                    <td data-label="Total Distance"><?= number_format($row['total_distance'], 2); ?></td>
                                    <td data-label="Min Distance Points"><?= $row['min_distance_points']; ?></td>
                                    <td data-label="Stage Total Points"><?= $row['stage_total_points']; ?></td>
                                    <td data-label="Challenge Completed (x/4)"><?= $row['challenges_completed']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="stage4" role="tabpanel" aria-labelledby="stage4-tab">
                <div class="container table-responsive-sm py-4">
                    <h2 class="text-center">Stage 4</h2>
                    <table class="table table-striped table-bordered" id="stage4Table">
                        <thead>
                            <tr>
                                <th>Participant Name</th>
                                <th>Daily Points</th>
                                <th>Bonus Points</th>
                                <th>Active Day Points</th>
                                <th>Total Distance</th>
                                <th>Min Distance Points</th>
                                <th>Stage Total Points</th>
                                <th>Challenges Completed (x/4)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stage4_data as $row) : ?>
                                <tr>
                                    <td data-label="Participant Name"><?= $row['participant_name']; ?></td>
                                    <td data-label="Daily Points"><?= $row['daily_points']; ?></td>
                                    <td data-label="Bonus Points"><?= $row['bonus_points']; ?></td>
                                    <td data-label="Active Day Points"><?= $row['active_day_points']; ?></td>
                                    <td data-label="Total Distance"><?= number_format($row['total_distance'], 2); ?></td>
                                    <td data-label="Min Distance Points"><?= $row['min_distance_points']; ?></td>
                                    <td data-label="Stage Total Points"><?= $row['stage_total_points']; ?></td>
                                    <td data-label="Challenge Completed (x/4)"><?= $row['challenges_completed']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="stage5" role="tabpanel" aria-labelledby="stage5-tab">
                <div class="container table-responsive-sm py-4">
                    <h2 class="text-center">Stage 5</h2>
                    <table class="table table-striped table-bordered" id="stage5Table">
                        <thead>
                            <tr>
                                <th>Participant Name</th>
                                <th>Daily Points</th>
                                <th>Bonus Points</th>
                                <th>Active Day Points</th>
                                <th>Total Distance</th>
                                <th>Min Distance Points</th>
                                <th>Stage Total Points</th>
                                <th>Challenges Completed (x/4)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stage5_data as $row) : ?>
                                <tr>
                                    <td data-label="Participant Name"><?= $row['participant_name']; ?></td>
                                    <td data-label="Daily Points"><?= $row['daily_points']; ?></td>
                                    <td data-label="Bonus Points"><?= $row['bonus_points']; ?></td>
                                    <td data-label="Active Day Points"><?= $row['active_day_points']; ?></td>
                                    <td data-label="Total Distance"><?= number_format($row['total_distance'], 2); ?></td>
                                    <td data-label="Min Distance Points"><?= $row['min_distance_points']; ?></td>
                                    <td data-label="Stage Total Points"><?= $row['stage_total_points']; ?></td>
                                    <td data-label="Challenge Completed (x/4)"><?= $row['challenges_completed']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="consolidated" role="tabpanel" aria-labelledby="consolidated-tab">
                <div class="container table-responsive-sm py-4">
                    <h2 class="text-center">Consolidated</h2>
                    <table class="table table-striped table-bordered" id="consolidatedTable">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Participant Name</th>
                                <th>Total Stage 1 Points</th>
                                <th>Total Stage 2 Points</th>
                                <th>Total Stage 3 Points</th>
                                <th>Total Stage 4 Points</th>
                                <th>Total Stage 5 Points</th>
                                <th>Overall Total Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($consolidated_data as $row) : ?>
                                <tr>
                                    <td data-label="Rank"><?= $row['rank_order']; ?></td>
                                    <td>
                                        <?php
                                        // Check if the image URL is exactly "avatar/athlete/medium.png"
                                        if ($row['profile_medium'] === 'avatar/athlete/medium.png') {
                                            $imageSrc = base_url('images/replacement_image.png'); // Use backup image
                                        } else {
                                            $imageSrc = $row['profile_medium']; // Use original image
                                        }
                                        ?>

                                        <img src="<?= $imageSrc; ?>" alt="Athlete Profile" class="profile-pic">
                                        <?= $row['name']; ?>
                                    </td>
                                    <td data-label="Total Stage 1 Points"><?= $row['stage1_points']; ?></td>
                                    <td data-label="Total Stage 2 Points"><?= $row['stage2_points']; ?></td>
                                    <td data-label="Total Stage 3 Points"><?= $row['stage3_points']; ?></td>
                                    <td data-label="Total Stage 4 Points"><?= $row['stage4_points']; ?></td>
                                    <td data-label="Total Stage 5 Points"><?= $row['stage5_points']; ?></td>
                                    <td data-label="Overall Total Points"><?= $row['total_points']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Go to Top Button -->
    <button id="goToTopBtn" class="btn btn-primary" title="Go to top">Top</button>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tabulator/5.4.3/js/tabulator.min.js"></script>
    <script>
        $(document).ready(function() {
            // DataTable initialization
            $('#stage1Table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel', 'pdf', 'print'
                ]
            });

            // Initialize for each additional table
            $('#stage2Table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel', 'pdf', 'print'
                ]
            });
            $('#stage3Table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel', 'pdf', 'print'
                ]
            });
            $('#stage4Table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel', 'pdf', 'print'
                ]
            });
            $('#stage5Table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel', 'pdf', 'print'
                ]
            });
            $('#consolidatedTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel', 'pdf', 'print'
                ]
            });

            // Smooth scroll to top
            $('#goToTopBtn').click(function() {
                $('html, body').animate({
                    scrollTop: 0
                }, '300');
            });
        });
    </script>
</body>

</html>