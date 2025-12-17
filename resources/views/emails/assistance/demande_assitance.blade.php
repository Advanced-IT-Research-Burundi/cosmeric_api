<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demande d'Assistance</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">

    <h2>Nouvelle Demande d'Assistance</h2>

    <p>Bonjour,</p>

    <p>
        Nous vous informons qu’un membre du groupement <strong>CASOMIREC</strong> 
        a soumis une nouvelle demande d'assistance.
    </p>

    <h3>Informations du Membre</h3>
    <p>
        <strong>Nom :</strong> {{ $assistance->membre->nom }} <br>
        <strong>Prénom :</strong> {{ $assistance->membre->prenom }}
    </p>

    <h3>Détails de l'Assistance</h3>
    <p>
        <strong>Type d'assistance :</strong> {{ $assistance->typeAssistance->nom ?? 'N/A' }} <br>
        <strong>Montant demandé :</strong> {{ number_format($assistance->montant_demande, 0, ',', ' ') }} FBu <br>
        <strong>Motif de la demande :</strong> {{ $assistance->motif }} <br>
        <strong>Date de la demande :</strong> {{ \Carbon\Carbon::parse($assistance->date_demande)->format('d/m/Y') }} <br>
        <strong>Statut :</strong> {{ ucfirst($assistance->statut) }} <br>
        <strong>ID de la demande :</strong> {{ $assistance->id }}
    </p>

    <p>
        Merci de procéder à l’analyse de cette demande.<br>
        Cordialement.
    </p>

</body>
</html>
