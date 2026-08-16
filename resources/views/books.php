<?php
/**
 * books.php
 *
 * Coquille HTML de la page catalogue : ne contient AUCUN livre en dur.
 * Toute la liste est chargée, ajoutée et supprimée dynamiquement par
 * public/js/books.js, qui appelle les routes /api/books.
 * @var string $titre
 */
?>
<h1 class="text-2xl font-bold mb-6"><?= htmlspecialchars($titre) ?></h1>

<!-- Formulaire d'ajout -->
<form id="form-ajout" class="bg-clay-surface rounded-clay shadow-clay p-6 mb-8 flex flex-col gap-4">
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
            class="self-start bg-primary text-white font-semibold px-6 py-3 rounded-2xl
                   shadow-clay-sm hover:bg-primary-dark transition">
        Ajouter le livre
    </button>
</form>

<!-- Message d'erreur / info, masqué par défaut -->
<p id="message" class="hidden mb-4 text-sm font-semibold"></p>

<!-- Liste des livres, injectée par books.js -->
<div id="liste-livres" class="flex flex-col gap-4">
    <p class="text-slate-400 text-center py-6">Chargement du catalogue…</p>
</div>

<script src="/js/books.js"></script>