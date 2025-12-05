<?php
// student_dashboard.php
declare(strict_types=1);

require __DIR__ . '/backend/config.php';

if (!isset($_SESSION['user_id'], $_SESSION['user_role'])) {
    header('Location: public/login.html');
    exit;
}

if ($_SESSION['user_role'] !== 'student') {
    http_response_code(403);
    echo 'Access denied.';
    exit;
}

$userName = $_SESSION['user_name'] ?? 'Student';
$userEmail = $_SESSION['user_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Student Dashboard · BCS Placement Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="public/css/main.css" />
    <script src="public/js/main.js" defer></script>
</head>
<body class="page page--dashboard">
<header class="site-header">
    <div class="site-header__inner">
        <div class="site-header__brand">
            <span class="site-header__logo-circle">BCS</span>
            <div class="site-header__text">
                <span class="site-header__title">BCS Placement Portal</span>
                <span class="site-header__subtitle">Student dashboard</span>
            </div>
        </div>
        <div class="site-header__user">
            <div class="site-header__user-info">
                <span class="site-header__user-name">
                    <?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?>
                </span>
                <?php if ($userEmail): ?>
                    <span class="site-header__user-email">
                        <?php echo htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                <?php endif; ?>
            </div>
            <a class="button button--ghost" href="backend/logout.php">Logout</a>
        </div>
    </div>
</header>

<div class="layout">
    <aside class="layout__sidebar">
        <nav class="sidebar-nav" aria-label="Student dashboard navigation">
            <p class="sidebar-nav__title">Overview</p>
            <ul class="sidebar-nav__list">
                <li class="sidebar-nav__item sidebar-nav__item--active">
                    <a href="#overview" class="sidebar-nav__link">Dashboard</a>
                </li>
                <li class="sidebar-nav__item">
                    <a href="#profile" class="sidebar-nav__link">My profile</a>
                </li>
                <li class="sidebar-nav__item">
                    <a href="#cv" class="sidebar-nav__link">My CV</a>
                </li>
                <li class="sidebar-nav__item">
                    <a href="#skills" class="sidebar-nav__link">Skills (SFIA)</a>
                </li>
                <li class="sidebar-nav__item">
                    <a href="#matches" class="sidebar-nav__link">Matched placements</a>
                </li>
            </ul>
            <p class="sidebar-nav__section-label">Support</p>
            <ul class="sidebar-nav__list sidebar-nav__list--secondary">
                <li class="sidebar-nav__item">
                    <span class="sidebar-nav__link sidebar-nav__link--muted">
                        Careers advisor contact (placeholder)
                    </span>
                </li>
            </ul>
        </nav>
    </aside>

    <main class="layout__main">
        <section id="overview" class="dashboard-section">
            <header class="dashboard-section__header">
                <h1 class="dashboard-section__title">Welcome back,
                    <?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?> 👋
                </h1>
                <p class="dashboard-section__subtitle">
                    Use this space to manage your profile, upload your CV and keep track of
                    placements that match your SFIA skills.
                </p>
            </header>

            <div class="dashboard-grid dashboard-grid--3">
                <article class="stat-card">
                    <h2 class="stat-card__label">Profile completeness</h2>
                    <p class="stat-card__value">40%</p>
                    <p class="stat-card__hint">Add more details to help employers understand your strengths.</p>
                </article>

                <article class="stat-card">
                    <h2 class="stat-card__label">Uploaded CV</h2>
                    <p class="stat-card__value">Not uploaded</p>
                    <p class="stat-card__hint">You can upload a PDF CV in the “My CV” section.</p>
                </article>

                <article class="stat-card">
                    <h2 class="stat-card__label">Matched placements</h2>
                    <p class="stat-card__value">0</p>
                    <p class="stat-card__hint">Once your skills are set up, matches will appear here.</p>
                </article>
            </div>
        </section>

        <section id="profile" class="dashboard-section">
            <header class="dashboard-section__header">
                <h2 class="dashboard-section__title">My profile</h2>
                <p class="dashboard-section__subtitle">
                    This is where you’ll manage personal details like your course, year of study
                    and preferred placement locations. For now, this is a static placeholder layout.
                </p>
            </header>
            <div class="card">
                <div class="card__body">
                    <div class="profile-row">
                        <div>
                            <p class="profile-row__label">Full name</p>
                            <p class="profile-row__value">
                                <?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                        </div>
                        <div>
                            <p class="profile-row__label">Email</p>
                            <p class="profile-row__value">
                                <?php echo htmlspecialchars($userEmail ?: '—', ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                        </div>
                    </div>
                    <div class="profile-row">
                        <div>
                            <p class="profile-row__label">Course</p>
                            <p class="profile-row__value">BSc Computer Science (placeholder)</p>
                        </div>
                        <div>
                            <p class="profile-row__label">Year of study</p>
                            <p class="profile-row__value">Year 2 (placement year target)</p>
                        </div>
                    </div>
                    <p class="profile-row__help">
                        Later, this section can be wired to a real "students" table in the database.
                    </p>
                </div>
            </div>
        </section>

        <section id="cv" class="dashboard-section">
            <header class="dashboard-section__header">
                <h2 class="dashboard-section__title">My CV</h2>
                <p class="dashboard-section__subtitle">
                    You will be able to upload a PDF version of your CV here (as required by the
                    project brief). The upload form and storage wiring will be added later.
                </p>
            </header>
            <div class="card">
                <div class="card__body">
                    <p class="profile-row__value">
                        CV upload is not yet implemented. This placeholder explains the feature for
                        your assignment:
                    </p>
                    <ul class="card__list">
                        <li>Accepts PDF files only.</li>
                        <li>Stores the file path against the logged-in student in the database.</li>
                        <li>Allows students to replace the CV with an updated version.</li>
                    </ul>
                    <p class="profile-row__help">
                        In a future step, this section will include a file input, server-side validation
                        and secure storage.
                    </p>
                </div>
            </div>
        </section>

        <section id="skills" class="dashboard-section">
            <header class="dashboard-section__header">
                <h2 class="dashboard-section__title">Skills (SFIA)</h2>
                <p class="dashboard-section__subtitle">
                    The portal will use the SFIA framework to describe your skills and the levels
                    employers are looking for.
                </p>
            </header>
            <div class="card">
                <div class="card__body">
                    <p class="profile-row__value">
                        Example skill categories (static placeholders):
                    </p>
                    <div class="chip-row">
                        <span class="chip">Programming / Software Development</span>
                        <span class="chip">Systems Design</span>
                        <span class="chip">Testing</span>
                        <span class="chip">Data Analysis</span>
                    </div>
                    <p class="profile-row__help">
                        In a later phase, you’ll store selected skills and SFIA levels in the database
                        and use them for matching placements.
                    </p>
                </div>
            </div>
        </section>

        <section id="matches" class="dashboard-section">
            <header class="dashboard-section__header">
                <h2 class="dashboard-section__title">Matched placements</h2>
                <p class="dashboard-section__subtitle">
                    Once the matching engine is implemented, placements that match your SFIA
                    skills will be listed here.
                </p>
            </header>
            <div class="card card--empty">
                <div class="card__body card__body--center">
                    <p class="card__empty-title">No matches yet</p>
                    <p class="card__empty-subtitle">
                        Complete your profile, add your skills and upload your CV to start
                        receiving placement matches.
                    </p>
                </div>
            </div>
        </section>

        <footer class="dashboard-footer">
            &copy; <span id="year"></span> BCS Manchester · Year-long placement matching
        </footer>
    </main>
</div>
</body>
</html>
