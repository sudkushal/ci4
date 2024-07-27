<!DOCTYPE html>
<html>
<head>
    <title>100 Days Fitness Challenge</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css?v=' . time()); ?>">
</head>
<body>
<?= $this->include('_header') ?>


    <main>
        <section id="strava-connect">
            <h2>Connect with Strava</h2>
            <p>Track your progress, compete with friends, and stay motivated!</p>

            <label>
                <input type="checkbox" id="privacyCheckbox">
                I have read and agree to the <a href="<?= site_url('privacy'); ?>">Privacy Policy</a>.
            </label>
            <button id="connectStravaBtn" disabled class="btn btn-primary">Connect with Strava</button>
        </section>
    </main>

    <?= $this->include('_footer') ?>


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
