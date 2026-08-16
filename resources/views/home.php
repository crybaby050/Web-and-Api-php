<?php
/**
 * home.php
 *
 * Page d'accueil : simple vitrine, pas de logique métier ni d'appel API.
 * @var string $titre
 */
?>
<div class="bg-clay-surface rounded-clay shadow-clay p-8 text-center">
    <h1 class="text-2xl font-bold mb-3"><?= htmlspecialchars($titre) ?></h1>
    <p class="text-slate-500 mb-6">
        Mini-application PHP + JS : routes web pour l'humain, routes API pour le programme.
    </p>
    <a href="/books"
       class="inline-block bg-primary text-white font-semibold px-6 py-3 rounded-2xl
              shadow-clay-sm hover:bg-primary-dark transition">
        Voir le catalogue
    </a>
</div>