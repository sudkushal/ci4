<?php namespace App\Views; ?>


<nav class="navbar navbar-expand-lg navbar-light bg-success sticky-top">
        <a class="navbar-brand text-white" href="<?= site_url('/'); ?>">100 Days Fitness Challenge</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link text-white" href="<?= site_url('/'); ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?= site_url('stage'); ?>">Leaderboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?= site_url('individualboard'); ?>">Individual Statistics</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?= site_url('activities'); ?>">Longest Activity</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?= site_url('config'); ?>">Challenge Config</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?= site_url('faqs'); ?>">FAQs</a>
                </li>
            </ul>
        </div>
    </nav>