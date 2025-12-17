<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Votre demande de crédit a été approuvée</title>
</head>
<body style="margin:0;padding:0;background:#f7f7f9;font-family:Arial, Helvetica, sans-serif;color:#222;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#f7f7f9;padding:24px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;border-radius:8px;overflow:hidden;border:1px solid #e6e6ee;">
                    <tr>
                        <td style="background:#0f172a;color:#ffffff;padding:18px 24px;font-size:18px;font-weight:bold;">
                            CASOMIREC
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 12px 0;font-size:16px;">
                                Bonjour
                                <strong>
                                    {{ $credit->membre->full_name ?? ($credit->membre->prenom . ' ' . $credit->membre->nom) }}
                                </strong>,
                            </p>

                            <p style="margin:0 0 12px 0;line-height:1.6;">
                                Nous avons le plaisir de vous informer que votre demande de crédit
                                <strong>#{{ $credit->id }}</strong> a été <strong>approuvée</strong>
                                le
                                <strong>{{ \Carbon\Carbon::parse($credit->date_approbation)->format('d/m/Y') }}</strong>.
                            </p>

                            <div style="margin:18px 0;padding:16px;border:1px solid #e6e6ee;border-radius:6px;background:#fafafe;">
                                <p style="margin:0 0 8px 0;"><strong>Détails de votre crédit</strong></p>
                                <ul style="margin:8px 0 0 18px;padding:0;line-height:1.6;">
                                    <li>Montant demandé :
                                        <strong>{{ number_format((float) $credit->montant_demande, 2, ',', ' ') }} FBU</strong>
                                    </li>
                                    <li>Montant accordé :
                                        <strong>{{ number_format((float) $credit->montant_accorde, 2, ',', ' ') }} FBU</strong>
                                    </li>
                                    <li>Taux d’intérêt :
                                        <strong>{{ number_format((float) $credit->taux_interet, 2, ',', ' ') }} %</strong>
                                    </li>
                                    <li>Durée :
                                        <strong>{{ $credit->duree_mois }} mois</strong>
                                    </li>
                                    <li>Mensualité estimée :
                                        <strong>{{ number_format((float) $credit->montant_mensualite, 2, ',', ' ') }} FBU</strong>
                                    </li>
                                </ul>
                            </div>

                            <p style="margin:0 0 12px 0;line-height:1.6;">
                                Les prochaines étapes et l’échéancier de remboursement seront disponibles dans votre espace client.
                                Pour toute question, n’hésitez pas à répondre à ce message.
                            </p>

                            <p style="margin:16px 0 0 0;">
                                Cordialement,<br/>
                                <strong>Équipe CASOMIREC</strong>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="background:#f1f5f9;color:#475569;font-size:12px;padding:12px 24px;">
                            Cet email vous est adressé suite à votre demande de crédit. Merci de ne pas partager ces informations avec des tiers.
                        </td>
                    </tr>
                </table>

                <p style="color:#64748b;font-size:12px;margin:12px 0 0 0;">
                    © {{ date('Y') }} CASOMIREC. Tous droits réservés.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
