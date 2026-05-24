<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Commune - {{ $commune->nom }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #0056b3; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #0056b3; font-size: 16px; text-transform: uppercase; }
        .header h2 { margin: 5px 0 0; font-size: 14px; color: #333; }
        .header p { margin: 2px 0 0; font-size: 10px; color: #666; }
        .info-box { border: 1px solid #ddd; padding: 10px; margin-bottom: 15px; border-radius: 4px; page-break-inside: avoid; }
        .info-title { background: #f0f4f8; padding: 5px; font-weight: bold; color: #0056b3; border-bottom: 1px solid #ddd; margin: -10px -10px 10px -10px; border-radius: 4px 4px 0 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10px; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        th { background-color: #0056b3; color: white; text-transform: uppercase; font-size: 9px; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .footer { position: fixed; bottom: -20px; left: 0; right: 0; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
        .text-right { text-align: right; }
        .status-badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; color: white; }
        .bg-success { background-color: #28a745; }
        .bg-warning { background-color: #ffc107; color: #000; }
        .bg-danger { background-color: #dc3545; }
    </style>
</head>
<body>

    <div class="header">
        <h1>République du Burkina Faso</h1>
        <p>ONEA - Direction Provinciale du Yadéga</p>
        <h2>RAPPORT DÉTAILLÉ PAR COMMUNE</h2>
        <p>Commune : {{ strtoupper($commune->nom) }} | Généré le : {{ date('d/m/Y à H:i') }}</p>
    </div>

    <!-- Statistiques Globales Commune -->
    <div class="info-box">
        <div class="info-title">1. STATISTIQUES GLOBALES</div>
        <table style="border:none;">
            <tr>
                <td style="border:none; width: 25%;"><strong>Total Sites :</strong></td>
                <td style="border:none; width: 25%;">{{ $stats['total_sites'] }}</td>
                <td style="border:none; width: 25%;"><strong>Total Forages :</strong></td>
                <td style="border:none; width: 25%;">{{ $stats['total_forages'] }}</td>
            </tr>
            <tr>
                <td style="border:none;"><strong>Sites Fonctionnels :</strong></td>
                <td style="border:none;">{{ $stats['sites_fonctionnels'] }}</td>
                <td style="border:none;"><strong>Sites en Panne :</strong></td>
                <td style="border:none;">{{ $stats['sites_en_panne'] }}</td>
            </tr>
            <tr>
                <td style="border:none;"><strong>Interventions Totales :</strong></td>
                <td style="border:none;">{{ $stats['total_interventions'] }}</td>
                <td style="border:none;"><strong>Coût Global :</strong></td>
                <td style="border:none;">{{ number_format($stats['cout_global'], 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>
    </div>

    <!-- Liste des Sites -->
    <div class="info-box">
        <div class="info-title">2. INVENTAIRE DES SITES DE LA COMMUNE</div>
        @if($commune->sites->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Nom Site</th>
                        <th>Village</th>
                        <th>Type</th>
                        <th>Nb Forages</th>
                        <th>Statut</th>
                        <th>Dernière Intervention</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($commune->sites as $site)
                    <tr>
                        <td><strong>{{ $site->nom }}</strong></td>
                        <td>{{ $site->village->nom }}</td>
                        <td>{{ ucfirst($site->type) }}</td>
                        <td>{{ $site->forages->count() }}</td>
                        <td>
                            <span class="status-badge {{ $site->statut == 'fonctionnel' ? 'bg-success' : ($site->statut == 'en_panne' ? 'bg-danger' : 'bg-warning') }}">
                                {{ ucfirst(str_replace('_', ' ', $site->statut)) }}
                            </span>
                        </td>
                        <td>
                            @php $lastInt = $site->interventions()->latest()->first(); @endphp
                            {{ $lastInt ? $lastInt->date_intervention->format('d/m/Y') : 'Jamais' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align:center; color:#999; font-style:italic;">Aucun site enregistré dans cette commune.</p>
        @endif
    </div>

    <!-- Historique Interventions Commune -->
    <div class="info-box">
        <div class="info-title">3. HISTORIQUE DES INTERVENTIONS (Tous sites confondus)</div>
        @if($stats['interventions_recentes']->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Site</th>
                        <th>Type</th>
                        <th>Technicien</th>
                        <th>Statut</th>
                        <th>Coût (FCFA)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['interventions_recentes'] as $intervention)
                    <tr>
                        <td>{{ $intervention->date_intervention->format('d/m/Y') }}</td>
                        <td>{{ $intervention->site->nom }}</td>
                        <td>{{ ucfirst($intervention->type) }}</td>
                        <td>{{ $intervention->technicien_nom }}</td>
                        <td>
                            <span class="status-badge {{ $intervention->statut == 'terminee' ? 'bg-success' : ($intervention->statut == 'en_cours' ? 'bg-warning' : 'bg-danger') }}">
                                {{ ucfirst(str_replace('_', ' ', $intervention->statut)) }}
                            </span>
                        </td>
                        <td class="text-right">{{ number_format($intervention->cout_total, 0, ',', ' ') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align:center; color:#999; font-style:italic;">Aucune intervention enregistrée pour cette commune.</p>
        @endif
    </div>

    <div class="footer">
        Document généré automatiquement par AEPS-Maint Pro Ouahigouya - Page 1/1
    </div>

</body>
</html>
