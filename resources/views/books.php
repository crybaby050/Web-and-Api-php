<?php
/**
 * books.php
 *
 * Coquille HTML de la page catalogue : ne contient AUCUN livre en dur.
 * Toute la liste est chargée, ajoutée et supprimée dynamiquement par
 * public/js/books.js, qui appelle les routes /api/books.
 *
 * Mise en page : liste des livres à gauche (grille de 3 colonnes),
 * formulaire d'ajout compact à droite — la liste est visible
 * immédiatement à l'ouverture de la page.
 *
 * @var string $titre
 */
?>
<h1 class="text-2xl font-bold mb-6"><?= htmlspecialchars($titre) ?></h1>

<div class="flex flex-col lg:flex-row gap-8 items-start">

    <!-- Colonne principale : liste des livres (grille de 3) -->
    <div class="w-full lg:w-2/3 min-w-0 order-2 lg:order-1">

        <p id="message" class="hidden mb-4 text-sm font-semibold"></p>

        <div id="liste-livres" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <p class="text-slate-400 text-center py-6 col-span-full">Chargement du catalogue…</p>
        </div>

    </div>

    <!-- Colonne latérale : formulaire d'ajout -->
    <div class="w-full lg:w-1/3 min-w-0 order-1 lg:order-2 lg:sticky lg:top-10">
        <form id="form-ajout" class="bg-clay-surface rounded-clay shadow-clay p-6 flex flex-col gap-4">
            <h2 class="font-semibold text-lg mb-1">Ajouter un livre</h2>

            <div class="flex flex-col gap-1">
                <label for="titre" class="text-sm font-semibold text-slate-500">Titre</label>
                <input type="text" id="titre" name="titre" required
                       class="bg-clay-bg rounded-2xl shadow-clay-inset px-4 py-3 outline-none
                              focus:ring-2 focus:ring-primary/40 transition">
            </div>

            <div class="flex flex-col gap-1">
                <label for="auteur" class="text-sm font-semibold text-slate-500">Auteur</label>
                <input type="text" id="auteur" name="auteur" required
                       class="bg-clay-bg rounded-2xl shadow-clay-inset px-4 py-3 outline-none
                              focus:ring-2 focus:ring-primary/40 transition">
            </div>

            <button type="submit"
                    class="bg-primary text-white font-semibold px-6 py-3 rounded-2xl
                           shadow-clay-sm hover:bg-primary-dark transition">
                Ajouter le livre
            </button>
        </form>
    </div>

</div>

<script src="/js/books.js"></script>