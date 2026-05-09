# SYSTÈME DE PAIEMENT - ACHAT RÉGIME SPORTIF

## 📋 Contexte
- L'ID de l'utilisateur est stocké dans la **session**
- On reçoit l'**ID du client** et l'**ID du régime-sport** via requête
- L'action se déclenche quand le client clique sur le bouton **"ACHETER"**

---

## 🔄 FLUX D'ACHAT - Conditions à valider

### **ÉTAPE 1 : Vérifications préalables**
- [ ] Vérifier que l'**utilisateur est un CLIENT** (contrôler le rôle)
- [ ] Récupérer le **prix du régime-sport**
- [ ] Récupérer le **solde actuel du portefeuille** du client

### **ÉTAPE 2 : Vérification du solde**

#### **CAS 1 : SOLDE SUFFISANT** ✅
Exécuter la **transaction DB** (atomicité cruciale) :

```
BEGIN TRANSACTION
  ├─ INSERT regime_sports :
  │  - regime_id
  │  - sport_id
  │  - client_id (utilisateur courant)
  │  - objectif_id
  │  - date_choix (date du jour)
  │  - duree
  │  
  │  ├─ Si succès → continuer
  │  └─ Si erreur → ROLLBACK + message erreur
  │
  ├─ INSERT mvt_compte :
  │  - client_id
  │  - type_transaction = "debit"
  │  - date_mouvement (date du jour)
  │  - montant = prix_regime_sport
  │  - raison = "achat_regime_sport"
  │  
  │  ├─ Si succès → COMMIT + message succès
  │  └─ Si erreur → ROLLBACK tout + message erreur
END TRANSACTION
```

**Fonction helper à créer :**
```php
public function soldeSuffisant($clientId, $montant): bool
{
    $solde = $this->getSoldeClient($clientId);
    return $solde >= $montant;
}
```

#### **CAS 2 : SOLDE INSUFFISANT** ❌
- Afficher message d'erreur : "Solde insuffisant"
- Proposer les options disponibles :
  - Recharger le portefeuille
  - S'abonner à une option Premium (Gold, etc.) pour bénéficier d'une remise
- Afficher le solde actuel vs montant manquant
- Rediriger vers page portefeuille/recharge

---

## 💾 FONCTIONS À IMPLÉMENTER

### **Model : ClientModel (ou PaymentModel)**

```php
// Vérifier le rôle de l'utilisateur
public function getUserRole($userId): string

// Récupérer le solde actuel
public function getSoldeClient($clientId): float

// Vérifier solde suffisant
public function soldeSuffisant($clientId, $montant): bool

// Insérer souscription régime-sport
public function insertRegimeSport($regimeId, $sportId, $clientId, $objectifId, $duree)

// Insérer mouvement compte
public function insertMvtCompte($clientId, $montant, $raison = "achat_regime_sport")

// Récupérer le prix d'un régime
public function getRegimePrice($regimeId): float
```

### **Controller : RegimeSportController**

```php
public function buy()
{
    // Action d'achat avec gestion d'erreurs et transactions
}
```

---

## ⚠️ POINTS CRITIQUES

1. **ATOMICITÉ** : Les deux INSERTs doivent réussir ensemble ou échouer ensemble
2. **SÉCURITÉ** : Vérifier que le client n'achète que pour lui-même
3. **LOGS** : Enregistrer chaque transaction pour audit
4. **ERREURS** : Bien différencier les cas d'erreur :
   - Utilisateur n'est pas client → Forbidden
   - Solde insuffisant → Message clair avec montant manquant
   - Erreur DB → Rollback + message erreur interne

---

## 📌 VARIABLES DISPONIBLES

| Variable | Source | Type |
|----------|--------|------|
| `userId` | Session | int |
| `regimeSportId` / `regimeId`, `sportId` | Requête | int |
| `objectifId` | Requête ou default | int |
| `duree` | Requête | int |
| `prixRegime` | DB (regimes) | float |
| `soldeClient` | DB (mvt_compte SUM) | float | 

# Fonctionnalite : On peut rajouter de l’argent dans son porte monnaie en rentrant un 
code

- Etape creer un bouton comme dans un jeu en haut a droite : le solde 
- En cliquant dessus on arrive dans une page ou on peut voir tout les historique de mouvement de compte 
- afficher un boutton + qui  fait apparitre grace a ajax un input de code avec label " Nous vous avons envoyer un CODE Veuillez le saisir  ici : "
- on verifie si le CODE  existe
- si le code existe on verifie s'il n'as pas ete deja utiliser
- si tout se passe bien on commence la transaction
  - inserer une ligne dans mvt_compte 
- il faut maintenant creer un repertoire dans vue : 
  - portefeuille ( c'est la qu.on fait les vues)


# Amelioration de l'homogeneite du projet 

## Comportement : 
- Comportement le "/" doit conduire a un landing page 
- Qu'est ce que qu'on doit trouver dans un landign page
  - Navbar fixe : logo , Accueil,regime ,Mon compte,Mon regime  Connexion/Inscription
    
  - hero section 
    - Titre accrocheur 
    - Sous titre
    - des inputs pour entrer les champ necessaire pour calculer un IMC
    - Bouton "Calculer mon IMc" en Ajax
  - section categories : X regimes +poids et Y regimes -poids
  - tu peux ajouter d'autre chose
- Footer 

## Filtre 
- Utilisateur connecte : 
  - navbar : 
    - connexion et inscription
    - Mon compte doit demander de se connecter 
    - Mon Regime doit demander de se connecter
- Utilisateur  non connecte : 
    - remplace connexion et inscription par solde
    - Mon  Compte doit afficher le profil de l'utilisateur
    - Mon Regime doit afficher une page de son regime vie regime_sport

- Pour les deux : Regime doit lister tout les regime et sport existant dans la base 

## Architecture 
- utiliser des layouts commun pour l'homogeneite du code
- decomposer les layouts et pour le minimum de redendonce
- pour les pages des admin , oon verrai tout a l'heure mais il faut anticipe quand 

## Design Important 
- Theme accueillant comme le site "Comme j'aime" 
- Choix la palette de couleur et declare les en tant que variables pour pouvoir les utiliser
- 
- utiliser scss pour minimaliser la redeondonce
- utiliser des variables fidele a l'identite de la marque , taille fonts , couleur, gapping , etc ...

