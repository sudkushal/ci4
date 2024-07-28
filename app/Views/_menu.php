<?php namespace App\Views; ?>

<nav>
    <style>
        nav ul {
            list-style: none;
            padding: 0;
            margin: 1rem auto 0;
            text-align: center;
        }

        nav li {
            display: inline;
            margin-right: 20px;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        nav a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        /* Mobile Responsive Styles for Menu */
        @media (max-width: 768px) { 
            nav ul {
                flex-direction: column; 
                align-items: center;
            }
        
            nav li {
                margin: 5px 0; 
            }
        }
    </style>

    <ul>
        <li><a href="<?= site_url('/'); ?>" class="<?php if (current_url() == site_url('/')) echo 'active'; ?>">Home</a></li>
        <li><a href="<?= site_url('leaderboard'); ?>" class="<?php if (current_url() == site_url('leaderboard')) echo 'active'; ?>">Leaderboard</a></li>
        <li><a href="<?= site_url('leaderboardstage'); ?>" class="<?php if (current_url() == site_url('leaderboardstage')) echo 'active'; ?>">Leaderboard Stagewise</a></li>
        <li><a href="<?= site_url('analytics'); ?>" class="<?php if (current_url() == site_url('analytics')) echo 'active'; ?>">Analytics</a></li>
    </ul>
</nav>
