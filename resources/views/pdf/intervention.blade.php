<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Intervention #{{ $intervention->reference }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #0056b3; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #0056b3; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 10px; color: #666; }
        .info-box { border: 1px solid #ddd; padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .info-title { background: #f0f4f8; padding: 5px; font-weight: bold; color: #0056b3; border-bottom: 1px solid #ddd; margin: -10px -10px 10px -10px; border-radius: 4px 4px 0 0; }
        .row { display: table; width: 100%; margin-bottom: 5px; }
        .col { display: table-cell; width: 50%; padding: 2px 0; }
        .col-label { font-weight: bold; width: 40%; color: #555; }
        .col-value { color: #000; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; font-size: 11px; }
        th { background-color: #0056b3; color: white; text-transform: uppercase; font-size: 10px; }
        .footer { position: fixed; bottom: -20px; left: 0; right: 0; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
        .status-badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 10px; font-weight: bold; color: white; }
        .bg-success { background-color: #28a745; }
        .bg-warning { background-color: #ffc107; color: #000; }
        .bg-danger { background-color: #dc3545; }
        .signature-box { margin-top: 40px; display: table; width: 100%; }
        .sign-col { display: table-cell; width: 33%; text-align: center; vertical-align: top; }
        .sign-line { border-top: 1px solid #000; width: 80%; margin: 40px auto 5px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>République du Burkina Faso</h1>
        <p>ONEA - Direction Provinciale du Yadéga</p>
        <h2>Rapport d'Intervention N° {{ $intervention->reference }}</h2>
        <p>Date : {{ $intervention->date_intervention->format('d/m/Y') }} | Site : {{ $intervention->site->nom }}</p>
    </div>

    <!-- Détails Intervention -->
    <div class="info-box">
        <div class="info-title">1. INFORMATIONS GÉNÉRALES</div>
        <div class="row"><div class="col"><span class="col-label">Référence :</span> <span class="col-value">{{ $intervention->reference }}</span></div><div class="col"><span class="col-label">Type :</span> <span class="col-value">{{ ucfirst($intervention->type) }}</span></div></div>
        <div class="row"><div class="col"><span class="col-label">Site Concerné :</span> <span class="col-value">{{ $intervention->site->nom }} ({{ $intervention->site->commune->nom }})</span></div><div class="col"><span class="col-label">Forage Concerné :</span> <span class="col-value">{{ $intervention->forage ? $intervention->forage->numero_forage : 'Global' }}</span></div></div>
        <div class="row"><div class="col"><span class="col-label">Technicien :</span> <span class="col-value">{{ $intervention->technicien_nom }}</span></div><div class="col"><span class="col-label">Statut :</span> 
            <span class="status-badge {{ $intervention->statut == 'terminee' ? 'bg-success' : ($intervention->statut == 'en_cours' ? 'bg-warning' : 'bg-danger') }}">
                {{ ucfirst(str_replace('_', ' ', $intervention->statut)) }}
            </span>
        </div></div>
    </div>

    <!-- Description & Travaux -->
    <div class="info-box">
        <div class="info-title">2. DESCRIPTION & TRAVAUX EFFECTUÉS</div>
        <p><strong>Problème Signalé :</strong></p>
        <p style="margin-bottom: 15px; text-align: justify;">{{ $intervention->probleme_signale }}</p>
        
        <p><strong>Travaux Effectués :</strong></p>
        <p style="margin-bottom: 15px; text-align: justify;">{{ $intervention->travaux_effectues }}</p>
        
        <p><strong>Observations / Recommandations :</strong></p>
        <p style="text-align: justify; font-style: italic;">{{ $intervention->observations ?? 'Aucune observation particulière.' }}</p>
    </div>

    <!-- Pièces Utilisées -->
    <div class="info-box">
        <div class="info-title">3. PIÈCES DE RECHANGE UTILISÉES</div>
        @if($intervention->pieces->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Désignation</th>
                        <th>Référence</th>
                        <th>Quantité</th>
                        <th>Prix Unitaire (FCFA)</th>
                        <th>Total (FCFA)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalPieces = 0; @endphp
                    @foreach($intervention->pieces as $piece)
                        @php $subtotal = $piece->pivot->quantite * $piece->prix_unitaire; $totalPieces += $subtotal; @endphp
                        <tr>
                            <td>{{ $piece->nom }}</td>
                            <td>{{ $piece->reference }}</td>
                            <td>{{ $piece->pivot->quantite }}</td>
                            <td>{{ number_format($piece->prix_unitaire, 0, ',', ' ') }}</td>
                            <td>{{ number_format($subtotal, 0, ',', ' ') }}</td>
                        </tr>
                    @endforeach
                    <tr style="background:#f9f9f9; font-weight:bold;">
                        <td colspan="4" style="text-align:right;">SOUS-TOTAL PIÈCES :</td>
                        <td>{{ number_format($totalPieces, 0, ',', ' ') }}</td>
                    </tr>
                </tbody>
            </table>
        @else
            <p style="text-align:center; color:#999; font-style:italic;">Aucune pièce utilisée pour cette intervention.</p>
        @endif
    </div>

    <!-- Coûts Totaux -->
    <div class="info-box">
        <div class="info-title">4. RÉCAPITULATIF FINANCIER</div>
        <div class="row"><div class="col"><span class="col-label">Coût Pièces :</span> <span class="col-value">{{ number_format($totalPieces, 0, ',', ' ') }} FCFA</span></div><div class="col"><span class="col-label">Main d'œuvre / Divers :</span> <span class="col-value">{{ number_format($intervention->cout_main_oeuvre ?? 0, 0, ',', ' ') }} FCFA</span></div></div>
        <div class="row" style="border-top: 2px solid #0056b3; padding-top: 10px; margin-top: 10px;">
            <div class="col" style="font-size: 14px;"><strong>TOTAL GÉNÉRAL :</strong></div>
            <div class="col" style="font-size: 14px; color: #0056b3;"><strong>{{ number_format($intervention->cout_total, 0, ',', ' ') }} FCFA</strong></div>
        </div>
    </div>

    <!-- Signatures -->
    <div class="signature-box">
        <div class="sign-col>
            <p>Le Technicien</p>
            <div class="sign-line"></div>
        </div>
        <div class="sign-col">
            <p>Le Chef d'Équipe</p>
            <div class="sign-line"></div>
        </div>
        <div class="sign-col">
            <p>Le Responsable ONEA</p>
            <div class="sign-line"></div>
        </div>
    </div>

    <div class="footer">
        Document généré automatiquement par AEPS-Maint Pro Ouahigouya - Page 1/1
    </div>

</body>
</html>
