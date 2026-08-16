# Atelier 22 — Routes web + routes API (PHP + JS)

**Objectif** : comprendre la différence entre une **route web** (HTML servi par PHP)
et une **route API** (JSON consommé par JavaScript), puis les faire travailler ensemble
dans une mini-application catalogue.

---

## 1. Le problème

Jusqu’ici, une action utilisateur (ajouter un livre, supprimer…) provoquait souvent
un **rechargement complet** de la page (formulaire HTML → PHP → nouvelle page).

Avec une API JSON + `fetch` côté navigateur :

1. PHP sert **une fois** la coquille HTML (route web) ;
2. JavaScript appelle les **endpoints API** ;
3. le DOM est mis à jour **sans recharger** toute la page.

```
Navigateur
   │
   │  GET /books          ← route WEB → HTML (PageController)
   ▼
Page HTML + books.js
   │
   │  GET  /api/books     ← route API → JSON
   │  POST /api/books     ← route API → JSON
   │  DELETE /api/books/2 ← route API → JSON
   ▼
Mise à jour du DOM (liste, messages…)
```

## 2. Deux fichiers de routes

Comme dans Laravel, on sépare clairement :

| Fichier            | Rôle                         | Réponse      |
|--------------------|------------------------------|--------------|
| `routes/web.php`   | Pages affichées à l’humain   | HTML         |
| `routes/api.php`   | Données pour le programme JS | JSON         |

```php
// routes/web.php
$router->get('/', [PageController::class, 'home']);
$router->get('/books', [PageController::class, 'books']);

// routes/api.php
$router->get('/api/books', [BookApiController::class, 'index']);
$router->post('/api/books', [BookApiController::class, 'store']);
$router->delete('/api/books/{id}', [BookApiController::class, 'destroy']);
```

Le **même** `Router` et le **même** `public/index.php` gèrent les deux.

## 3. Contrôleur web vs contrôleur API

```php
// WEB → string HTML
class PageController {
    public function books(): string {
        return View::render('books', ['titre' => 'Catalogue']);
    }
}

// API → Response JSON
class BookApiController {
    public function index(): Response {
        return Response::json($this->books->all());
    }
}
```

Le router détecte le type de retour :

- `string` → `echo` (HTML) ;
- `Response` → en-tête `Content-Type: application/json` + corps JSON.

Format de succès :

```json
{ "data": [ { "id": 1, "titre": "1984", "auteur": "Orwell", "disponible": true } ] }
```

Format d’erreur :

```json
{ "error": "Livre introuvable", "code": 404 }
```

## 4. JavaScript et `fetch`

La page `/books` ne contient **pas** la liste des livres en PHP.
Elle charge un script qui parle à l’API :

```js
const res = await fetch(base + '/api/books');
const json = await res.json();
rendu(json.data); // injecte le HTML dans #liste-livres
```

Création :

```js
await fetch(base + '/api/books', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ titre, auteur }),
});
```

## 5. Lancer les exemples

Depuis `exemples/public/` :

```bash
php -S localhost:8080 router.php
```

Puis ouvrez http://localhost:8080/

- Accueil = route web ;
- Livres = coquille HTML + JS qui appelle `/api/books` ;
- Ajout / suppression = appels API, liste rafraîchie sans rechargement.

Sous Apache (XAMPP), pointez vers `…/exemples/public/` (le `.htaccess` redirige vers `index.php`).

## 6. Exercice guidé

Travaillez dans `exercice/` (même structure, avec des TODO).

1. **TODO 1a** — Dans `routes/web.php`, ajoutez `GET /books` → `PageController@books`.
2. **TODO 1b / 1c** — Dans `routes/api.php`, ajoutez :
   - `POST /api/books/{id}/toggle` → `toggle`
   - `DELETE /api/books/{id}` → `destroy`
3. **TODO 2 / 3** — Complétez `toggle()` et `destroy()` dans `BookApiController`.
4. **TODO 4 / 5 / 6** — Dans `public/js/books.js`, implémentez `chargerLivres`, `creerLivre`, `toggleLivre` avec `fetch`.
5. Testez : `php -S localhost:8081 router.php` dans `exercice/public/`.

### Solution (extraits)

```php
// routes/web.php
$router->get('/books', [PageController::class, 'books']);

// routes/api.php
$router->post('/api/books/{id}/toggle', [BookApiController::class, 'toggle']);
$router->delete('/api/books/{id}', [BookApiController::class, 'destroy']);

// BookApiController
public function toggle(string $id): Response
{
    $livre = $this->books->toggleDisponible((int) $id);
    if ($livre === null) {
        return Response::error('Livre introuvable', 404);
    }
    return Response::json($livre);
}

public function destroy(string $id): Response
{
    if (!$this->books->delete((int) $id)) {
        return Response::error('Livre introuvable', 404);
    }
    return Response::json(['supprime' => true]);
}
```

```js
async function chargerLivres() {
    const res = await fetch(base + '/api/books');
    const json = await res.json();
    if (!res.ok) throw new Error(json.error || 'Erreur API');
    rendu(json.data);
}

async function creerLivre(titre, auteur) {
    const res = await fetch(base + '/api/books', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ titre, auteur }),
    });
    const json = await res.json();
    if (!res.ok) throw new Error(json.error || 'Création impossible');
}

async function toggleLivre(id) {
    const res = await fetch(base + '/api/books/' + id + '/toggle', { method: 'POST' });
    const json = await res.json();
    if (!res.ok) throw new Error(json.error || 'Toggle impossible');
    await chargerLivres();
}
```

## Ce qu'il faut retenir

- **Route web** = HTML pour l’humain (navigation, coquille de page).
- **Route API** = JSON pour le programme (JS, mobile, autre serveur).
- Un front-controller unique peut servir les deux (`web.php` + `api.php`).
- `fetch` + mise à jour du DOM = interactions fluides sans rechargement complet.
- C’est le modèle « hybride » : PHP pour les pages, API pour les données dynamiques
  (même idée que le projet `bibliotheque/` + `api/` du cours).
