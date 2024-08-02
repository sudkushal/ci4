<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>100 Days Fitness Challenge</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/style-india.css?v=' . time()); ?>">
    <link rel="icon" href="<?= base_url('favicon.ico'); ?>" type="image/x-icon">
</head>

<body>
    <!-- Bootstrap Navbar -->
    <?= $this->include('_menu') ?>


    <main class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h3 class="text-center">Please complete this process and then share the score in Microsoft Forms.</h3>
            </div>
        </div>
        <div class="row justify-content-center my-4">
            <div class="col-12 col-md-8 col-lg-6">
                <section id="strava-connect" class="text-center">
                    <h2>Connect with Strava</h2>
                    <p>Track your progress, compete with friends, and stay motivated!</p>

                    <div class="form-group text-left">
                        <div class="form-check">
                            <input type="checkbox" id="privacyCheckbox" class="form-check-input">
                            <label class="form-check-label" for="privacyCheckbox">
                                I have read and agree to the <a href="<?= site_url('privacy'); ?>">Privacy Policy</a>.
                            </label>
                        </div>
                    </div>
                    <button id="connectStravaBtn" disabled class="btn btn-primary btn-block">Connect with Strava</button>
                </section>
            </div>
        </div>
    </main>

    <?= $this->include('_footer') ?>

    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        const privacyCheckbox = document.getElementById('privacyCheckbox');
        const connectButton = document.getElementById('connectStravaBtn');

        privacyCheckbox.addEventListener('change', () => {
            connectButton.disabled = !privacyCheckbox.checked;
        });

        connectButton.addEventListener('click', () => {
            window.location.href = "<?= site_url('strava/connectToStrava'); ?>";
        });
    </script>
</body>

</html>
