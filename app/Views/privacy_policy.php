<!DOCTYPE html>
<html>
<head>
    <title>100 Days Fitness Challenge</title>
    <link rel="stylesheet" href="<?= base_url('css/' . session()->get('selectedStyle')); ?>"> 
    <link rel="icon" href="<?= base_url('favicon.ico'); ?>" type="image/x-icon">
</head>
<body>
<?= $this->include('_header') ?>

    <main>
        <h2>100 Days Challenge Privacy Policy</h2>

        <p>
            This Privacy Policy describes how we collect, use, and share your personal information when you participate in the Wellness Challenge.
        </p>

        <h3>Information We Collect</h3>

        <p>
            When you connect your Strava account to our challenge platform, we collect the following information:
        </p>
        <ul>
            <li>Your Strava athlete ID</li>
            <li>Your full name (as displayed on Strava)</li>
            <li>Your Strava profile picture URLs (medium and large sizes)</li>
            <li>Data about your activities, including distance, time, and date</li>
        </ul>

        <h3>How We Use Your Information</h3>
        <p>
            We use the collected information for the following purposes:
        </p>
        <ul>
            <li>To calculate your total distance and points.</li>
            <li>To rank you on the challenge leaderboard.</li>
            <li>To display your name and profile picture (if available) on the leaderboard.</li>
        </ul>

        <h3>Data Sharing</h3>
        <p>
            We do not share your personal information with any third parties, except for the data displayed on the public leaderboard (your name, profile picture, total distance, and points). However, in case of any disputes, this data might be shared with relevant authorities.
        </p>

        <h3>Data Retention</h3>

        <p>
            We will retain your information for the duration of the Wellness Challenge. After the challenge concludes, we will delete all data associated with your participation.
        </p>

        <h3>Data Usage Disclaimer</h3>
        <p>
            Any analytics derived from the Wellness Challenge and the data acquired will be used for marketing within the organization that the participants belong to.
        </p>
        
        <h3>Disclaimer</h3>
        <p>
            This wellness challenge is an independent initiative and is not affiliated with or endorsed by Strava. We are not responsible for the privacy practices or content of Strava.
        </p>

        <h3>Your Rights</h3>

        <p>
            You have the right to request access to, correction of, or deletion of your personal data. To exercise these rights, please contact us at sudarshankushal@gmail.com.
        </p>

        <h3>Changes to This Privacy Policy</h3>

        <p>
            We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page.
        </p>
        
        <h3>Contact Us</h3>

        <p>
            If you have any questions or concerns about this Privacy Policy, please contact us at sudarshankushal@gmail.com.
        </p>
    </main>
    
    <?= $this->include('_footer') ?>
</body>
</html>
