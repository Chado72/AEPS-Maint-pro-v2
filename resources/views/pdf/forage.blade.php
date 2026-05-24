<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche Forage - {{ $forage->numero_forage }}</title>
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
        <h2>Fiche Technique Forage : {{ $forage->numero_forage }}</h2>
        <p>Site : {{ $forage->site->nom }} | Généré le : {{ date('d/m/Y à H:i') }}</p>
    </div>

    <!-- Informations Techniques -->
    <div class="info-box">
        <div class="info-title">1. CARACTÉRISTIQUES TECHNIQUES</div>
        <div class="row"><div class="col"><span class="col-label">Numéro Forage :</span> <span class="col-value">{{ $forage->numero_forage }}</span></div><div class="col"><span class="col-label">Type :</span> <span class="col-value">{{ ucfirst($forage->type) }}</span></div></div>
        <div class="row"><div class="col"><span class="col-label">Profondeur Totale :</span> <span class="col-value">{{ $forage->profondeur }} m</span></div><div class="col"><span class="col-label">Diamètre :</span> <span class="col-value">{{ $forage->diametre ?? 'N/A' }} mm</span></div></div>
        <div class="row"><div class="col"><span class="col-label">Débit d'essai :</span> <span class="col-value">{{ $forage->debit }} m³/h</span></div><div class="col"><span class="col-label">Niveau Statique :</span> <span class="col-value">{{ $forage->niveau_statique ?? 'N/A' }} m</span></div></div>
        <div class="row"><div class="col"><span class="col-label">Date Installation :</span> <span class="col-value">{{ $forage->date_installation ? \Carbon\Carbon::parse($forage->date_installation)->format('d/m/Y') : 'N/A' }}</span></div><div class="col"><span class="col-label">Entreprise :</span> <span class="col-value">{{ $forage->entreprise_installateur ?? 'N/A' }}</span></div></div>
    </div>

    <!-- Équipement de Pompage -->
    <div class="info-box">
        <div class="info-title">2. ÉQUIPEMENT DE POMPAGE</div>
        <div class="row"><div class="col"><span class="col-label">Type Pompe :</span> <span class="col-value">{{ ucfirst($forage->type_pompe ?? 'Non défini') }}</span></div><div class="col"><span class="col-label">Marque :</span> <span class="col-value">{{ $forage->marque_pompe ?? 'N/A' }}</span></div></div>
        <div class="row"><div class="col"><span class="col-label">Puissance :</span> <span class="col-value">{{ $forage->puissance_pompe ?? 'N/A' }} kW</span></div><div class="col"><span class="col-label">Hauteur Manométrique :</span> <span class="col-value">{{ $forage->hmt ?? 'N/A' }} m</span></div></div>
        <div class="row"><div class="col"><span class="col-label">État Actuel :</span> <span class="col-value">{{ ucfirst($forage->etat_pompe) }}</span></div><div class="col"><span class="col-label">Dernière Révision :</span> <span class="col-value">{{ $forage->derniere_revision_pompe ? \Carbon\Carbon::parse($forage->derniere_revision_pompe)->format('d/m/Y') : 'Jamais' }}</span></div></div>
    </div>

    <!-- Historique Interventions Spécifiques -->
    <div class="info-box">
        <div class="info-title">3. HISTORIQUE DES INTERVENTIONS SUR CE FORAGE</div>
        @if($forage->interventions->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Technicien</th>
                        <th>Coût (FCFA)</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($forage->interventions as $intervention)
                    <tr>
                        <td>{{ $intervention->date_intervention->format('d/m/Y') }}</td>
                        <td>{{ ucfirst($intervention->type) }}</td>
                        <td>{{ Str::limit($intervention->description, 40) }}</td>
                        <td>{{ $intervention->technicien_nom }}</td>
                        <td>{{ number_format($intervention->cout_total, 0, ',', ' ') }}</td>
                        <td>
                            <span class="status-badge {{ $intervention->statut == 'terminee' ? 'bg-success' : ($intervention->statut == 'en_cours' ? 'bg-warning' : 'bg-danger') }}">
                                {{ ucfirst(str_replace('_', ' ', $intervention->statut)) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align:center; color:#999; font-style:italic;">Aucune intervention spécifique enregistrée pour ce forage.</p>
        @endif
    </div>

    <div class="footer">
        Document généré automatiquement par AEPS-Maint Pro Ouahigouya - Page 1/1
    </div>

</body>
</html>
