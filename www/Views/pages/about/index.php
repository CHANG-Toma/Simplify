<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../../assets/dist/css/main.css" />
    <title>About</title>
</head>
<body>
<?php
$components = [
    'navbar.php',
    'hero.php',
    'content-layout-project.php',
    'trust-us.php',
    'content-layout-team.php',
    'content-layout-cta.php',
    'testimonials.php',
    'FAQ.php',
    'contact-us.php',
    'footer.php',
];

// Parcours du tableau et inclusion de chaque fichier de composant
foreach ($components as $component) {
    include __DIR__ . '/../../_partials/' . $component;
}


?> 
</body>
</html>

