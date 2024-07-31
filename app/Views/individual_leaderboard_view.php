<!DOCTYPE html>
<html>

<head>
    <title>100 Days Fitness Challenge</title>
    <link rel="stylesheet" href="<?= base_url('css/style-individual.css?v=' . time()); ?>">
    <link rel="icon" href="<?= base_url('favicon.ico'); ?>" type="image/x-icon">
</head>

<body>

    <?= $this->include('_header') ?>
    <h1>Individual Statistics</h1>
    <main>
        <div class="filter-bar">
            <input type="text" id="filterInput" onkeyup="filterTable()" placeholder="Search...">
        </div>
        <table class="leaderboard-table consolidated-leaderboard-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <?php
                    if (!empty($leaderboardData)) {
                        foreach (array_keys($leaderboardData[0]) as $columnName) {
                            if ($columnName != 'profile_medium' && $columnName != 'name') {
                                echo "<th>" . ucwords(str_replace('_', ' ', $columnName)) . "</th>";
                            }
                        }
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($leaderboardData as $entry) : ?>
                    <tr>
                        <td data-label="Name">
                            <?php if (!empty($entry['profile_medium']) && strpos($entry['profile_medium'], 'avatar/athlete/medium.png') !== false) : ?>
                                <img src="<?= base_url('images/replacement_image.png'); ?>" alt="Athlete Profile" class="profile-pic">
                            <?php else : ?>
                                <img src="<?= $entry['profile_medium']; ?>" alt="Athlete Profile" class="profile-pic">
                            <?php endif; ?>
                            <?= $entry['name']; ?>
                        </td>

                        <?php foreach ($entry as $key => $value) : ?>
                            <?php if ($key != 'profile_medium' && $key != 'name') : ?>
                                <td data-label="<?= ucwords(str_replace('_', ' ', $key)); ?>">
                                    <?php
                                    if (is_numeric($value) && strpos($value, '.') !== false) {
                                        echo number_format($value, 2);  // Format to 2 decimal places
                                    } else {
                                        echo $value;
                                    }
                                    ?>
                                </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>




        </table>
    </main>
    <?= $this->include('_footer') ?>
    <script>
        function filterTable() {
            // Get filter value and table rows
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("filterInput");
            filter = input.value.toUpperCase();
            table = document.querySelector(".leaderboard-table");

            tr = table.getElementsByTagName("tr");

            // Loop through all table rows, and hide those who don't match the search query
            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0];
                // Check only the first column (Name)
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>
</body>

</html>