<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>100 Days Fitness Challenge</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Basic Styling */
        body {
            background-color: #e2ffe2; 
        }

        h1, h2 {
            text-align: center;
        }

        /* Tab Navigation Styling */
        .nav-tabs .nav-link {
            border: 1px solid #007bff; 
            margin-right: 5px;         
        }

        .nav-tabs .nav-item.active .nav-link {
            background-color: #007bff; 
            color: white;
        }

        /* Table Styling */
        .table th, .table td {
            vertical-align: middle; 
        }

        .table-responsive .table {
            font-size: 0.9rem; 
        }

        .table-responsive th, .table-responsive td {
            padding: 8px 12px; 
        }

        /* Responsive Table Styling */
        @media (max-width: 768px) { 
            .table-responsive {
                overflow-x: auto; 
            }

            .table-responsive thead {
                display: none;     
            }
        
            .table-responsive tbody td {
                display: block;    
                width: 100%;       
                text-align: right; 
            }
        
            .table-responsive tbody td:before {
                content: attr(data-label) ": "; 
                float: left;       
                font-weight: bold; 
            }
        }
    </style>
</head>

<body>
    <?= $this->include('_menu') ?>

    <div class="container mt-4">
        <h1>Challenge Configuration</h1>

        <ul class="nav nav-tabs" id="configTabs" role="tablist">
            <?php
            $stages = array_unique(array_column($configData, 'stage')); 
            foreach ($stages as $index => $stage): 
                $activeClass = ($index === 0) ? 'active' : ''; 
            ?>
            <li class="nav-item">
                <a class="nav-link <?= $activeClass ?>" id="stage<?= $stage ?>-tab" data-toggle="tab" href="#stage<?= $stage ?>" role="tab" aria-controls="stage<?= $stage ?>">Stage <?= $stage ?></a>
            </li>
            <?php endforeach; ?>
        </ul>

        <div class="tab-content">
            <?php
            foreach ($stages as $index => $stage):
                $activeClass = ($index === 0) ? 'show active' : ''; 
            ?>
            <div class="tab-pane fade <?= $activeClass ?>" id="stage<?= $stage ?>" role="tabpanel">
                <div class="table-responsive"> 
                    <table class="table table-bordered mt-3">
                        <tbody>
                            <?php foreach ($configData as $row): 
                                if ($row['stage'] == $stage): 
                                    // Split the config_key by '.' first to handle cases like 'MinDistance.Km'
                                    $keyParts = explode('.', $row['config_key']);
                                    
                                    // Further split each part by capital letters and join with spaces
                                    $formattedKeyParts = [];
                                    foreach ($keyParts as $part) {
                                        $formattedPart = preg_replace('/([A-Z])/', ' $1', $part);
                                        $formattedKeyParts[] = trim($formattedPart);
                                    }
                                    ?>
                                    <tr>
                                        <?php foreach ($formattedKeyParts as $part): ?>
                                            <th><?= $part ?></th> 
                                        <?php endforeach; ?>
                                        <td><?= $row['config_value'] ?></td> 
                                    </tr>
                                <?php endif; 
                                endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

