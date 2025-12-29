<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle Demande d'Assistance</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">

    <h2>Nouvelle Demande d'Assistance</h2>

    <p>Bonjour,</p>

    <p>
        Une nouvelle demande d'assistance a été créée.
    </p>

    <h3>Détails de l'Assistance</h3>
    <p>
        <strong>Membre :</strong> {{ $assistance->membre->nom }} {{ $assistance->membre->prenom }} <br>
        <strong>Type d'assistance :</strong> {{ $assistance->typeAssistance->nom ?? 'N/A' }} <br>
        <strong>Montant :</strong> {{ number_format($assistance->montant, 0, ',', ' ') }} FBu <br>
        <strong>Date de la demande :</strong> {{ \Carbon\Carbon::parse($assistance->date_demande)->format('d/m/Y') }} <br>
        <strong>Statut :</strong> {{ ucfirst($assistance->statut) }}
    </p>

    <p>
        Merci de traiter cette demande dans les plus brefs délais.<br>
        Cordialement.
    </p>

</body>
</html>
