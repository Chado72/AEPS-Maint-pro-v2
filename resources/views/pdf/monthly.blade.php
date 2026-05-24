<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Mensuel - {{ $mois }} {{ $annee }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #0056b3; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #0056b3; font-size: 16px; text-transform: uppercase; }
        .header h2 { margin: 5px 0 0; font-size: 14px; color: #333; }
        .header p { margin: 2px 0 0; font-size: 10px; color: #666; }
        .summary-box { display: table; width: 100%; margin-bottom: 20px; border: 1px solid #ddd; }
        .sum-row { display: table-row; }
        .sum-cell { display: table-cell; width: 25%; padding: 10px; text-align: center; border-right: 1px solid #ddd; background: #f8f9fa; }
        .sum-cell:last-child { border-right: none; }
        .sum-value { font-size: 18px; font-weight: bold; color: #0056b3; display: block; }
        .sum-label { font-size: 10px; text-transform: uppercase; color: #666; }
        .info-box { border: 1px solid #ddd; padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .info-title { background: #f0f4f8; padding: 5px; font-weight: bold; color: #0056b3; border-bottom: 1px solid #ddd; margin: -10px -10px 10px -10px; border-radius: 4px 4px 0 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10px; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        th { background-color: #0056b3; color: white; text-transform: uppercase; font-size: 9px; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .footer { position: fixed; bottom: -20px; left: 0; right: 0; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h1>République du Burkina Faso</h1>
        <p>ONEA - Direction Provinciale du Yadéga</p>
        <h2>RAPPORT MENSUEL D'ACTIVITÉ MAINTENANCE</h2>
        <p>Période : {{ ucfirst($mois) }} {{ $annee }}</p>
        <p>Date d'émission : {{ date('d/m/Y') }}</p>
    </div>

    <!-- Résumé Statistiques -->
    <div class="summary-box">
        <div class="sum-row">
            <div class="sum-cell">
                <span class="sum-value">{{ $stats['total_interventions'] }}</span>
                <span class="sum-label">Interventions</span>
            </div>
            <div class="sum-cell">
                <span class="sum-value">{{ $stats['interventions_terminees'] }}</span>
                <span class="sum-label">Terminées</span>
            </div>
            <div class="sum-cell">
                <span class="sum-value">{{ number_format($stats['cout_total'], 0, ',', ' ') }}</span>
                <span class="sum-label">Coût Total (FCFA)</span>
            </div>
            <div class="sum-cell">
                <span class="sum-value">{{ $stats['sites_impactes'] }}</span>
                <span class="sum-label">Sites Impactés</span>
            </div>
        </div>
    </div>

    <!-- Interventions par Type -->
    <div class="info-box">
        <div class="info-title">1. RÉPARTITION PAR TYPE D'INTERVENTION</div>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Nombre</th>
                    <th>Coût Associé (FCFA)</th>
                    <th>% du Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['par_type'] as $type => $data)
                <tr>
                    <td>{{ ucfirst($type) }}</td>
                    <td class="text-right">{{ $data['count'] }}</td>
                    <td class="text-right">{{ number_format($data['cost'], 0, ',', ' ') }}</td>
                    <td class="text-right">{{ $data['percentage'] }}%</td>
                </tr>
                @endforeach
                <tr class="font-bold" style="background:#e9ecef;">
                    <td>TOTAL</td>
                    <td class="text-right">{{ $stats['total_interventions'] }}</td>
                    <td class="text-right">{{ number_format($stats['cout_total'], 0, ',', ' ') }}</td>
                    <td class="text-right">100%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Interventions par Commune -->
    <div class="info-box">
        <div class="info-title">2. ACTIVITÉ PAR COMMUNE</div>
        <table>
            <thead>
                <tr>
                    <th>Commune</th>
                    <th>Interventions</th>
                    <th>Sites Touchés</th>
                    <th>Coût Total (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['par_commune'] as $commune => $data)
                <tr>
                    <td>{{ $commune }}</td>
                    <td class="text-right">{{ $data['count'] }}</td>
                    <td class="text-right">{{ $data['sites'] }}</td>
                    <td class="text-right">{{ number_format($data['cost'], 0, ',', ' ') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Top 5 Sites les plus intervenus -->
    <div class="info-box">
        <div class="info-title">3. TOP 5 DES SITES LES PLUS SOLLICITÉS</div>
        <table>
            <thead>
                <tr>
                    <th>Site</th>
                    <th>Commune</th>
                    <th>Nombre d'interventions</th>
                    <th>Coût Cumulé (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['top_sites'] as $siteData)
                <tr>
                    <td>{{ $siteData['nom'] }}</td>
                    <td>{{ $siteData['commune'] }}</td>
                    <td class="text-right">{{ $siteData['count'] }}</td>
                    <td class="text-right">{{ number_format($siteData['cost'], 0, ',', ' ') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Consommation Pièces -->
    <div class="info-box">
        <div class="info-title">4. CONSOMMATION DES PIÈCES DE RECHANGE</div>
        <table>
            <thead>
                <tr>
                    <th>Pièce</th>
                    <th>Référence</th>
                    <th>Quantité Utilisée</th>
                    <th>Coût Total (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['pieces_utilisees'] as $pieceData)
                <tr>
                    <td>{{ $pieceData['nom'] }}</td>
                    <td>{{ $pieceData['reference'] }}</td>
                    <td class="text-right">{{ $pieceData['quantite'] }}</td>
                    <td class="text-right">{{ number_format($pieceData['cout'], 0, ',', ' ') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        Document généré automatiquement par AEPS-Maint Pro Ouahigouya - Page 1/1
    </div>

</body>
</html>
