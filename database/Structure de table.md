# Fichier guide de la structure de table

**NB : les noms de table et de champ a faire en ASCII , pas de nom de table avec des caractères en UTF-8**

## Clients

+ id
+ nom
+ email
+ date_naissance
+ taille
+ poids

## Objectif

 perte de poids , gain , IMC idéal

+ id
+ libelle

## Client-objectif

+ client
+ objectif
+ date_choix

## Régime

**Description :** composition alimentaire

+ id
+ pourcentage_viande
+ pourcentage_volaille
+ pourcentage_poisson
+ prix_par_jour
+ impact_journalier

## Sport

+ libelle

## Régime-Sport

+ régime
+ sport
+ client_objectif
+ durée

## Option

 Gold et tout

+ libelle
+ remise

## Client-option

Sert d'historique pour les options des clients

+ client
+ option
+ date

## Porte-monnaie ( mvt_compte )

Historique des manœuvres financière

+ client
+ type transaction (débit - crédit )
+ date
+ montant
+ faire table raison ?

## Code

Code pour mettre de l'argent dans son compte , carte cadeau ou un peu comme une carte de crédit on insère pour créditer le compte

+ id
+ valeur
+ gain (gain d'argent)

## Users

Table pour les admin

+ username
+ password
