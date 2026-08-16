<?php
/**
 * base.layout.php
 *
 * Layout commun à toutes les vues. Reçoit :
 * - $titre   : titre de la page (onglet + <h1> si utilisé par la vue)
 * - $content : HTML déjà rendu de la vue courante (injecté par Core\View)
 *
 * Tailwind est chargé via CDN — suffisant pour un projet pédagogique
 * (pas de build step à configurer).
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titre ?? 'Atelier 22') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Palette et ombres personnalisées pour l'effet "claymorphisme"
        // (fond pastel, ombre claire + ombre sombre pour un relief doux).
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        clay: {
                            bg: '#e8ecf3',
                            surface: '#eef1f7',
                        },
                        primary: {
                            DEFAULT: '#6c7ee1',
                            dark: '#5566c9',
                        },
                    },
                    boxShadow: {
                        'clay': '8px 8px 16px #b8bfcc, -8px -8px 16px #ffffff',
                        'clay-sm': '5px 5px 10px #b8bfcc, -5px -5px 10px #ffffff',
                        'clay-inset': 'inset 4px 4px 8px #b8bfcc, inset -4px -4px 8px #ffffff',
                    },
                    borderRadius: {
                        'clay': '24px',
                    },
                },
            },
        };
    </script>
</head>
<body class="bg-clay-bg min-h-screen text-slate-700 font-sans">
    <div class="max-w-6xl mx-auto px-5 py-10">

        <!-- Navigation claymorphiste -->
        <nav class="flex gap-3 bg-clay-surface rounded-clay shadow-clay px-5 py-4 mb-8">
            <a href="/" class="font-semibold px-4 py-2 rounded-2xl hover:shadow-clay-inset transition">
                Accueil
            </a>
            <a href="/books" class="font-semibold px-4 py-2 rounded-2xl hover:shadow-clay-inset transition">
                Catalogue
            </a>
        </nav>

        <?= $content ?>

    </div>
</body>
</html>