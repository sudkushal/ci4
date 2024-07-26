<?php namespace App\Views; ?>

<nav>
    <ul>
        <li><a href="<?= site_url('/'); ?>" class="<?php if (current_url() == site_url('/')) echo 'active'; ?>">Home</a></li>
        <li><a href="<?= site_url('leaderboard'); ?>" class="<?php if (current_url() == site_url('leaderboard')) echo 'active'; ?>">Leaderboard</a></li>
        <li><a href="<?= site_url('leaderboard100'); ?>" class="<?php if (current_url() == site_url('leaderboard100')) echo 'active'; ?>">100 Days Leaderboard</a></li>
        <li><a href="<?= site_url('analytics'); ?>" class="<?php if (current_url() == site_url('analytics')) echo 'active'; ?>">Analytics</a></li>
    </ul>
</nav>