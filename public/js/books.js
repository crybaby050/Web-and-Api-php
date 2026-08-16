/**
 * books.js
 *
 * Script consommé par la page /books (voir resources/views/books.php).
 * Ne contient aucun HTML en dur côté PHP : charge, crée, bascule et
 * supprime les livres exclusivement via les routes /api/books (fetch).
 */

// Base commune à tous les appels API (facilite un futur déploiement
// sous un sous-dossier, ex: /atelier22).
const base = '';

const listeEl = document.getElementById('liste-livres');
const formEl = document.getElementById('form-ajout');
const messageEl = document.getElementById('message');

/**
 * Affiche un message d'erreur ou d'info au-dessus de la liste.
 */
function afficherMessage(texte, type = 'erreur') {
    messageEl.textContent = texte;
    messageEl.className =
        'mb-4 text-sm font-semibold ' +
        (type === 'erreur' ? 'text-red-500' : 'text-green-600');
    messageEl.classList.remove('hidden');
}

function masquerMessage() {
    messageEl.classList.add('hidden');
}

/**
 * Construit le fragment HTML (Tailwind) d'une carte livre.
 * @param {{id:number, titre:string, auteur:string, disponible:boolean}} livre
 */
function carteLivre(livre) {
    const badgeClasses = livre.disponible
        ? 'bg-green-100 text-green-700'
        : 'bg-red-100 text-red-700';
    const badgeTexte = livre.disponible ? 'Disponible' : 'Indisponible';

    return `
        <div class="book-card flex items-center justify-between bg-clay-surface rounded-clay shadow-clay px-6 py-4" data-id="${livre.id}">
            <div>
                <h3 class="font-semibold">${escapeHtml(livre.titre)}</h3>
                <p class="text-sm text-slate-500">${escapeHtml(livre.auteur)}</p>
                <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-semibold ${badgeClasses}">
                    ${badgeTexte}
                </span>
            </div>
            <div class="flex gap-2">
                <button class="btn-toggle bg-clay-bg rounded-xl px-4 py-2 text-sm font-semibold shadow-clay-sm hover:shadow-clay-inset transition" data-id="${livre.id}">
                    ↺
                </button>
                <button class="btn-delete bg-clay-bg rounded-xl px-4 py-2 text-sm font-semibold shadow-clay-sm hover:shadow-clay-inset transition text-red-500" data-id="${livre.id}">
                    🗑
                </button>
            </div>
        </div>
    `;
}

/**
 * Échappe le HTML pour éviter toute injection via titre/auteur
 * (les données viennent de l'API, donc potentiellement de l'utilisateur).
 */
function escapeHtml(texte) {
    const div = document.createElement('div');
    div.textContent = texte;
    return div.innerHTML;
}

/**
 * Injecte la liste des livres dans le DOM. Affiche un message si
 * le catalogue est vide.
 */
function rendu(livres) {
    if (livres.length === 0) {
        listeEl.innerHTML = `<p class="text-slate-400 text-center py-6">Aucun livre pour le moment.</p>`;
        return;
    }

    listeEl.innerHTML = livres.map(carteLivre).join('');
}

/**
 * GET /api/books — charge et affiche le catalogue complet.
 */
async function chargerLivres() {
    try {
        const res = await fetch(base + '/api/books');
        const json = await res.json();

        if (!res.ok) {
            throw new Error(json.error || 'Erreur lors du chargement du catalogue.');
        }

        rendu(json.data);
    } catch (err) {
        afficherMessage(err.message);
    }
}

/**
 * POST /api/books — crée un livre à partir des champs du formulaire.
 */
async function creerLivre(titre, auteur) {
    const res = await fetch(base + '/api/books', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ titre, auteur }),
    });
    const json = await res.json();

    if (!res.ok) {
        throw new Error(json.error || 'Création impossible.');
    }
}

/**
 * POST /api/books/{id}/toggle — bascule la disponibilité d'un livre.
 */
async function toggleLivre(id) {
    const res = await fetch(base + '/api/books/' + id + '/toggle', {
        method: 'POST',
    });
    const json = await res.json();

    if (!res.ok) {
        throw new Error(json.error || 'Bascule impossible.');
    }
}

/**
 * DELETE /api/books/{id} — supprime un livre.
 */
async function supprimerLivre(id) {
    const res = await fetch(base + '/api/books/' + id, {
        method: 'DELETE',
    });
    const json = await res.json();

    if (!res.ok) {
        throw new Error(json.error || 'Suppression impossible.');
    }
}

// --- Écouteurs d'évènements ---------------------------------------------

// Soumission du formulaire d'ajout
formEl.addEventListener('submit', async (event) => {
    event.preventDefault();
    masquerMessage();

    const titre = document.getElementById('titre').value.trim();
    const auteur = document.getElementById('auteur').value.trim();

    try {
        await creerLivre(titre, auteur);
        formEl.reset();
        await chargerLivres();
    } catch (err) {
        afficherMessage(err.message);
    }
});

// Délégation d'évènements sur la liste (toggle + suppression),
// car les cartes sont générées dynamiquement après le chargement initial.
listeEl.addEventListener('click', async (event) => {
    const boutonToggle = event.target.closest('.btn-toggle');
    const boutonDelete = event.target.closest('.btn-delete');

    try {
        if (boutonToggle) {
            await toggleLivre(boutonToggle.dataset.id);
            await chargerLivres();
        }

        if (boutonDelete) {
            await supprimerLivre(boutonDelete.dataset.id);
            await chargerLivres();
        }
    } catch (err) {
        afficherMessage(err.message);
    }
});

// --- Initialisation ------------------------------------------------------

chargerLivres();