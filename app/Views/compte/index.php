<?= $this->extend('layout/site') ?>

<?= $this->section('content') ?>

<?php
$displayName = (string) ($user['nom'] ?? 'Utilisateur');
$displayEmail = (string) ($user['email'] ?? '—');
$displayRole = (string) ($user['role'] ?? 'user');
$displayTaille = isset($user['taille']) ? number_format((float) $user['taille'], 2) . ' cm' : '—';
$displayPoids = isset($user['poids']) ? number_format((float) $user['poids'], 2) . ' kg' : '—';
$displayAge = $age !== null ? $age . ' ans' : '—';
$displayObjetif = $latestObjectif ?: 'Aucun objectif défini';
$displayOption = !empty($latestOption['libelle']) ? ucfirst((string) $latestOption['libelle']) : 'Aucune option';
$displayOptionDate = !empty($latestOption['date_option']) ? date('d/m/Y', strtotime((string) $latestOption['date_option'])) : null;
$displayInitials = $userInitials ?: 'FR';
?>

<div class="profile-page">
    <section class="profile-hero card">
        <div class="profile-hero-main">
            <div class="profile-avatar" aria-hidden="true"><?= esc($displayInitials) ?></div>
            <div class="profile-hero-copy">
                <span class="eyebrow">Mon compte</span>
                <h1><?= esc($displayName) ?></h1>
                <p><?= esc($displayEmail) ?></p>
                <div class="profile-badges">
                    <span class="profile-badge">Âge : <?= esc($displayAge) ?></span>
                    <span class="profile-badge">Rôle : <?= esc(ucfirst($displayRole)) ?></span>
                    <span class="profile-badge">Membre depuis : <?= esc($memberSince ?? '—') ?></span>
                </div>
            </div>
        </div>

        <div class="profile-cta-panel">
            <a class="btn btn-primary" href="<?= site_url('mon-regime') ?>">Voir mon régime</a>
            <a class="btn btn-ghost" href="<?= site_url('portefeuille') ?>">Voir mon solde</a>
        </div>
    </section>

    <section class="profile-metrics">
        <article class="profile-metric card">
            <span>Objectif actif</span>
            <strong><?= esc($displayObjetif) ?></strong>
            <p>Dernier objectif enregistré sur votre compte.</p>
        </article>
        <article class="profile-metric card">
            <span>Option actuelle</span>
            <strong><?= esc($displayOption) ?></strong>
            <p><?= $displayOptionDate ? 'Depuis le ' . esc($displayOptionDate) : 'Aucune option active pour le moment.' ?></p>
        </article>
        <article class="profile-metric card">
            <span>Régimes achetés</span>
            <strong><?= esc((string) $purchasedCount) ?></strong>
            <p><a href="<?= site_url('mon-regime') ?>">Consulter les résultats achetés</a></p>
        </article>
        <article class="profile-metric card">
            <span>Compte</span>
            <strong><?= esc($displayRole) ?></strong>
            <p>Profil utilisateur et données personnelles.</p>
        </article>
    </section>

    <section class="profile-details card">
        <div class="section-head">
            <h2>Profil utilisateur</h2>
            <p>Les informations principales de votre compte.</p>
        </div>

        <div class="profile-info-grid">
            <div class="profile-info-item">
                <span>Nom</span>
                <strong><?= esc($displayName) ?></strong>
            </div>
            <div class="profile-info-item">
                <span>Email</span>
                <strong><?= esc($displayEmail) ?></strong>
            </div>
            <div class="profile-info-item">
                <span>Date de naissance</span>
                <strong><?= esc($birthDate ?? '—') ?></strong>
            </div>
            <div class="profile-info-item">
                <span>Âge</span>
                <strong><?= esc($displayAge) ?></strong>
            </div>
            <div class="profile-info-item">
                <span>Taille</span>
                <strong><?= esc($displayTaille) ?></strong>
            </div>
            <div class="profile-info-item">
                <span>Poids</span>
                <strong><?= esc($displayPoids) ?></strong>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>
