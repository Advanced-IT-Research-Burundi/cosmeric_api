<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demande de Crédit</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">

    <h2>Nouvelle Demande de Crédit</h2>

    <p>Bonjour,</p>

    <p>
        Nous vous informons qu’un membre du groupement <strong>COSEMERIC</strong> 
        a soumis une nouvelle demande de crédit.
    </p>

    <h3>Informations du Membre</h3>
    <p>
        <strong>Nom :</strong> {{ $credit->membre->nom }} <br>
        <strong>Prénom :</strong> {{ $credit->membre->prenom }}
    </p>

    <h3>Détails du Crédit</h3>
    <p>
        <strong>Montant demandé :</strong> {{ number_format($credit->montant_demande, 0, ',', ' ') }} FBu <br>
        <strong>Taux d’intérêt :</strong> {{ $credit->taux_interet }} % <br>
        <strong>Durée :</strong> {{ $credit->duree_mois }} mois <br>
        <strong>Mensualité :</strong> {{ number_format($credit->montant_mensualite, 0, ',', ' ') }} FBu <br>
        <strong>Montant total à rembourser :</strong> {{ number_format($credit->montant_total_rembourser, 0, ',', ' ') }} FBu <br>
        <strong>Motif :</strong> {{ $credit->motif }} <br>
        <strong>Date de la demande :</strong> {{ \Carbon\Carbon::parse($credit->date_demande)->format('d/m/Y') }} <br>
        <strong>Statut :</strong> {{ ucfirst($credit->statut) }} <br>
        <strong>ID de la demande :</strong> {{ $credit->id }}
    </p>

    <p>
        Merci de procéder à l’analyse de cette demande.<br>
        Cordialement.
    </p>

</body>
</html>
