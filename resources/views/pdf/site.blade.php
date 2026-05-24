<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche Site - {{ $site->nom }}</title>
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
    </style>
</head>
<body>

    <div class="header">
        <h1>République du Burkina Faso</h1>
        <p>ONEA - Direction Provinciale du Yadéga</p>
        <h2>Fiche Technique du Site : {{ strtoupper($site->nom) }}</h2>
        <p>Généré le : {{ date('d/m/Y à H:i') }}</p>
    </div>

    <!-- Informations Générales -->
    <div class="info-box">
        <div class="info-title">1. LOCALISATION & IDENTIFICATION</div>
        <div class="row"><div class="col"><span class="col-label">Commune :</span> <span class="col-value">{{ $site->commune->nom }}</span></div><div class="col"><span class="col-label">Village :</span> <span class="col-value">{{ $site->village->nom }}</span></div></div>
        <div class="row"><div class="col"><span class="col-label">Code Site :</span> <span class="col-value">{{ $site->code }}</span></div><div class="col"><span class="col-label">Type :</span> <span class="col-value">{{ ucfirst($site->type) }}</span></div></div>
        <div class="row"><div class="col"><span class="col-label">Coordonnées GPS :</span> <span class="col-value">{{ $site->latitude }}, {{ $site->longitude }}</span></div><div class="col"><span class="col-label">Année Mise en Service :</span> <span class="col-value">{{ $site->annee_mise_service ?? 'N/A' }}</span></div></div>
        <div class="row"><div class="col"><span class="col-label">Statut Actuel :</span> <span class="col-value">{{ ucfirst($site->statut) }}</span></div></div>
    </div>

    <!-- Forages -->
    <div class="info-box">
        <div class="info-title">2. INFRASTRUCTURES DE FORAGE ({{ $site->forages->count() }})</div>
        @if($site->forages->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>N° Forage</th>
                        <th>Profondeur (m)</th>
                        <th>Débit (m³/h)</th>
                        <th>État Pompage</th>
                        <th>Date Installation</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($site->forages as $forage)
                    <tr>
                        <td>{{ $forage->numero_forage }}</td>
                        <td>{{ $forage->profondeur }}</td>
                        <td>{{ $forage->debit }}</td>
                        <td>{{ ucfirst($forage->etat_pompe) }}</td>
                        <td>{{ $forage->date_installation ? \Carbon\Carbon::parse($forage->date_installation)->format('d/m/Y') : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align:center; color:#999; font-style:italic;">Aucun forage enregistré sur ce site.</p>
        @endif
    </div>

    <!-- Énergie -->
    <div class="info-box">
        <div class="info-title">3. SOURCES D'ÉNERGIE</div>
        @if($site->energySources->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Marque/Modèle</th>
                        <th>Puissance/Capacité</th>
                        <th>État</th>
                        <th>Date Installation</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($site->energySources as $energy)
                    <tr>
                        <td>{{ ucfirst($energy->type) }}</td>
                        <td>{{ $energy->marque_modele }}</td>
                        <td>{{ $energy->puissance_capacite }}</td>
                        <td>{{ ucfirst($energy->etat) }}</td>
                        <td>{{ $energy->date_installation ? \Carbon\Carbon::parse($energy->date_installation)->format('d/m/Y') : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align:center; color:#999; font-style:italic;">Aucune source d'énergie enregistrée.</p>
        @endif
    </div>

    <!-- Dernières Interventions -->
    <div class="info-box">
        <div class="info-title">4. HISTORIQUE RÉCENT DES INTERVENTIONS (5 dernières)</div>
        @if($site->interventions->take(5)->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Technicien</th>
                        <th>Statut</th>
                        <th>Coût (FCFA)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($site->interventions->take(5) as $intervention)
                    <tr>
                        <td>{{ $intervention->date_intervention->format('d/m/Y') }}</td>
                        <td>{{ ucfirst($intervention->type) }}</td>
                        <td>{{ $intervention->technicien_nom }}</td>
                        <td>
                            <span class="status-badge {{ $intervention->statut == 'terminee' ? 'bg-success' : ($intervention->statut == 'en_cours' ? 'bg-warning' : 'bg-danger') }}">
                                {{ ucfirst(str_replace('_', ' ', $intervention->statut)) }}
                            </span>
                        </td>
                        <td>{{ number_format($intervention->cout_total, 0, ',', ' ') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align:center; color:#999; font-style:italic;">Aucune intervention récente.</p>
        @endif
    </div>

    <div class="footer">
        Document généré automatiquement par AEPS-Maint Pro Ouahigouya - Page 1/1
    </div>

</body>
</html>
