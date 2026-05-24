<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Global AEPS-Maint Pro</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #0056b3; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #0056b3; font-size: 18px; text-transform: uppercase; }
        .header h2 { margin: 5px 0 0; font-size: 14px; color: #333; }
        .header p { margin: 2px 0 0; font-size: 10px; color: #666; }
        .summary-grid { display: table; width: 100%; margin-bottom: 20px; }
        .grid-row { display: table-row; }
        .grid-cell { display: table-cell; width: 20%; padding: 10px; text-align: center; border: 1px solid #ddd; background: #f8f9fa; }
        .sum-value { font-size: 20px; font-weight: bold; color: #0056b3; display: block; }
        .sum-label { font-size: 10px; text-transform: uppercase; color: #666; }
        .section-box { border: 1px solid #ddd; padding: 10px; margin-bottom: 15px; border-radius: 4px; page-break-inside: avoid; }
        .section-title { background: #0056b3; color: white; padding: 6px; font-weight: bold; margin: -10px -10px 10px -10px; border-radius: 4px 4px 0 0; text-transform: uppercase; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10px; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        th { background-color: #e9ecef; color: #333; text-transform: uppercase; font-size: 9px; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .footer { position: fixed; bottom: -20px; left: 0; right: 0; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .chart-placeholder { height: 150px; background: #f0f4f8; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; color: #999; font-style: italic; margin: 10px 0; }
    </style>
</head>
<body>

    <div class="header">
        <h1>République du Burkina Faso</h1>
        <p>ONEA - Direction Provinciale du Yadéga</p>
        <h2>RAPPORT GLOBAL D'ACTIVITÉ ET DE PATRIMOINE</h2>
        <p>Période : Du {{ $date_debut->format('d/m/Y') }} au {{ $date_fin->format('d/m/Y') }}</p>
        <p>Date d'émission : {{ date('d/m/Y à H:i') }}</p>
    </div>

    <!-- Indicateurs Clés -->
    <div class="summary-grid">
        <div class="grid-row">
            <div class="grid-cell">
                <span class="sum-value">{{ $stats['total_sites'] }}</span>
                <span class="sum-label">Sites AEPS/PEA</span>
            </div>
            <div class="grid-cell">
                <span class="sum-value">{{ $stats['total_forages'] }}</span>
                <span class="sum-label">Forages Actifs</span>
            </div>
            <div class="grid-cell">
                <span class="sum-value">{{ $stats['taux_fonctionnement'] }}%</span>
                <span class="sum-label">Taux Fonctionnement</span>
            </div>
            <div class="grid-cell">
                <span class="sum-value">{{ $stats['total_interventions'] }}</span>
                <span class="sum-label">Interventions</span>
            </div>
            <div class="grid-cell">
                <span class="sum-value">{{ number_format($stats['cout_total'], 0, ',', ' ') }}</span>
                <span class="sum-label">Coût Total (FCFA)</span>
            </div>
        </div>
    </div>

    <!-- Répartition par Commune -->
    <div class="section-box">
        <div class="section-title">1. RÉPARTITION DU PATRIMOINE PAR COMMUNE</div>
        <table>
            <thead>
                <tr>
                    <th>Commune</th>
                    <th>Sites</th>
                    <th>Forages</th>
                    <th>Fonctionnels</th>
                    <th>En Panne</th>
                    <th>% Fonctionnement</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['par_commune'] as $communeData)
                <tr>
                    <td><strong>{{ $communeData['nom'] }}</strong></td>
                    <td class="text-center">{{ $communeData['sites'] }}</td>
                    <td class="text-center">{{ $communeData['forages'] }}</td>
                    <td class="text-center" style="color:green;">{{ $communeData['fonctionnels'] }}</td>
                    <td class="text-center" style="color:red;">{{ $communeData['pannes'] }}</td>
                    <td class="text-center"><strong>{{ $communeData['pourcentage'] }}%</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Analyse des Interventions -->
    <div class="section-box">
        <div class="section-title">2. ANALYSE DES INTERVENTIONS (PÉRIODE SÉLECTIONNÉE)</div>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Nombre</th>
                    <th>% du Total</th>
                    <th>Coût Moyen (FCFA)</th>
                    <th>Coût Total (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['interventions_par_type'] as $type => $data)
                <tr>
                    <td>{{ ucfirst($type) }}</td>
                    <td class="text-center">{{ $data['count'] }}</td>
                    <td class="text-center">{{ $data['percentage'] }}%</td>
                    <td class="text-right">{{ number_format($data['avg_cost'], 0, ',', ' ') }}</td>
                    <td class="text-right"><strong>{{ number_format($data['total_cost'], 0, ',', ' ') }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Consommation Pièces Détachées -->
    <div class="section-box">
        <div class="section-title">3. CONSOMMATION GLOBALE DE PIÈCES DÉTACHÉES</div>
        <table>
            <thead>
                <tr>
                    <th>Désignation</th>
                    <th>Catégorie</th>
                    <th>Quantité Totale</th>
                    <th>Stock Actuel</th>
                    <th>Coût Total Consommé (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['top_pieces'] as $pieceData)
                <tr>
                    <td>{{ $pieceData['nom'] }}</td>
                    <td>{{ $pieceData['categorie'] }}</td>
                    <td class="text-center">{{ $pieceData['quantite_utilisee'] }}</td>
                    <td class="text-center">{{ $pieceData['stock_actuel'] }}</td>
                    <td class="text-right">{{ number_format($pieceData['cout_total'], 0, ',', ' ') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Top 10 Sites Critiques -->
    <div class="section-box">
        <div class="section-title">4. TOP 10 DES SITES LES PLUS CRITIQUES (Plus d'interventions)</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Site</th>
                    <th>Commune</th>
                    <th>Statut Actuel</th>
                    <th>Nb Interventions</th>
                    <th>Coût Cumulé (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['sites_critiques'] as $index => $siteData)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $siteData['nom'] }}</strong></td>
                    <td>{{ $siteData['commune'] }}</td>
                    <td class="text-center">{{ ucfirst($siteData['statut']) }}</td>
                    <td class="text-center">{{ $siteData['nb_interventions'] }}</td>
                    <td class="text-right">{{ number_format($siteData['cout_cumule'], 0, ',', ' ') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Recommandations IA (si disponible) -->
    @if(isset($stats['recommandations_ia']) && !empty($stats['recommandations_ia']))
    <div class="section-box">
        <div class="section-title">5. RECOMMANDATIONS DE L'ASSISTANT IA</div>
        <div style="padding: 10px; background: #e8f4fd; border-left: 4px solid #0056b3; font-style: italic;">
            {!! nl2br(e($stats['recommandations_ia'])) !!}
        </div>
    </div>
    @endif

    <div class="footer">
        Document généré automatiquement par AEPS-Maint Pro Ouahigouya - Rapport Global - Page 1/1
    </div>

</body>
</html>
