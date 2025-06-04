
#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import csv
import unicodedata
from pathlib import Path

BASE_DIR = Path(__file__).parent
COMMUNES_CSV = BASE_DIR / "communes-france-2024-limite.csv"
DATA_CSV = BASE_DIR / "data.csv"
OUTPUT_SQL = BASE_DIR / "data.sql"
UNMATCH_LOG = BASE_DIR / "unmatched_localities.log"

def fix_mojibake(s: str) -> str:
    if not s:
        return ""
    if "Ã" in s:
        try:
            return s.encode("latin-1").decode("utf-8")
        except Exception:
            return s
    return s

def normalize(s: str) -> str:
    if s is None:
        return ""
    s = fix_mojibake(s).strip()
    s = unicodedata.normalize("NFKD", s)
    s = s.encode("ascii", "ignore").decode("ascii")
    return " ".join(s.lower().split())

def safe_float(val, default=0.0):
    try:
        return float(str(val).replace(",", "."))
    except Exception:
        return default

def escape_sql(s: str) -> str:
    return s.replace("'", "''")

insee_mapping = {}
with COMMUNES_CSV.open(newline="", encoding="utf-8") as f_comm:
    reader = csv.DictReader(f_comm, delimiter=";")
    for row in reader:
        key = normalize(row["nom_standard"])
        insee_mapping[key] = {
            "code_insee": row["code_insee"].strip(),
            "reg_code": int(row["reg_code"]),
            "reg_nom": fix_mojibake(row["reg_nom"].strip()),
            "dep_code": row["dep_code"].strip(),
            "dep_nom": fix_mojibake(row["dep_nom"].strip()),
        }

sql_lines = [
    "INSERT INTO pays (nom_pays) VALUES ('France');"
]

regions = set()
departements = set()
communes = set()
installateurs = {}
marques_panneau = set()
modeles_panneau = set()
marques_onduleur = set()
modeles_onduleur = set()

orientation_dict = {
    'Nord': 0, 'Nord-Est': 45, 'Est': 90, 'Sud-Est': 135,
    'Sud': 180, 'Sud-Ouest': 225, 'Ouest': 270, 'Nord-Ouest': 315
}

unknown = set()
localisation_id = 1

with DATA_CSV.open(newline="", encoding="utf-8") as f_data:
    reader = csv.DictReader(f_data)
    for row in reader:
        norm_commune = normalize(row["locality"])
        commune_data = insee_mapping.get(norm_commune)

        if not commune_data:
            unknown.add(row["locality"])
            continue

        code_insee = commune_data["code_insee"]
        reg_code = commune_data["reg_code"]
        reg_nom = commune_data["reg_nom"]
        dep_code = commune_data["dep_code"]
        dep_nom = commune_data["dep_nom"]

        if reg_code not in regions:
            sql_lines.append(f"INSERT INTO region (reg_code, reg_nom, id_pays) VALUES ({reg_code}, '{escape_sql(reg_nom)}', 1);")
            regions.add(reg_code)

        if dep_code not in departements:
            sql_lines.append(f"INSERT INTO departement (dep_code, dep_nom, reg_code) VALUES ('{dep_code}', '{escape_sql(dep_nom)}', {reg_code});")
            departements.add(dep_code)

        if code_insee not in communes:
            code_pos = int(row.get("postal_code") or 0)
            sql_lines.append(f"INSERT INTO commune (code_INSEE, nom_commune, population, code_pos, dep_code) VALUES ('{code_insee}', '{escape_sql(row['locality'])}', 0, {code_pos}, '{dep_code}');")
            communes.add(code_insee)

        lat = safe_float(row["lat"])
        lon = safe_float(row["lon"])
        sql_lines.append(f"INSERT INTO localisation (latitude, longitude, code_INSEE) VALUES ({lat}, {lon}, '{code_insee}');")
        sql_lines.append("SET @loc := LAST_INSERT_ID();")

        inst_name = escape_sql(row["installateur"].strip()) or "Inconnu"
        if inst_name not in installateurs:
            sql_lines.append(f"INSERT INTO installateur (nom_installateur) VALUES ('{inst_name}');")
            installateurs[inst_name] = True
        sql_lines.append(f"SET @inst := (SELECT MIN(id_installateur) FROM installateur WHERE nom_installateur = '{inst_name}');")

        marque_ond = escape_sql(row.get("marque_onduleur", "").strip())
        modele_ond = escape_sql(row.get("modele_onduleur", "").strip())
        if marque_ond and marque_ond not in marques_onduleur:
            sql_lines.append(f"INSERT INTO marque_onduleur (marque) VALUES ('{marque_ond}');")
            marques_onduleur.add(marque_ond)
        if modele_ond and modele_ond not in modeles_onduleur:
            sql_lines.append(f"INSERT INTO modele_onduleur (modele_onduleur) VALUES ('{modele_ond}');")
            modeles_onduleur.add(modele_ond)
        sql_lines.append(f"SET @m_ond := (SELECT MIN(id_marque) FROM marque_onduleur WHERE marque = '{marque_ond}');")
        sql_lines.append(f"SET @mod_ond := (SELECT MIN(id_modele) FROM modele_onduleur WHERE modele_onduleur = '{modele_ond}');")
        sql_lines.append("INSERT INTO onduleur (id_modele, id_marque) VALUES (@mod_ond, @m_ond);")
        sql_lines.append("SET @ond := LAST_INSERT_ID();")

        mois = row['mois_installation'].zfill(2)
        annee = row['an_installation']
        date = f"{annee}-{mois}-01"

        nb_panneaux = int(row['nb_panneaux'] or 0)
        surface = int(safe_float(row['surface']))
        puissance = int(safe_float(row['puissance_crete']) * 1000)
        nb_ond = int(row['nb_onduleur'] or 1)
        pente = int(row['pente'] or 0)
        pente_opti = int(row.get('pente_optimum', 0) or 0)
        ori = orientation_dict.get(row['orientation'].strip(), 0)
        ori_opti = orientation_dict.get(row.get('orientation_optimum', '').strip(), 0)
        prod = int(safe_float(row.get('production_pvgis', 0)))

        sql_lines.append(
            "INSERT INTO installation (date_installation, nb_panneaux, surface, puissance_crete, nb_ondulateur, "
            f"pente, pente_opti, orientation, orientation_opti, prod_pvgis, id_onduleur, id_localisation, id_installateur) "
            f"VALUES ('{date}', {nb_panneaux}, {surface}, {puissance}, {nb_ond}, {pente}, {pente_opti}, {ori}, {ori_opti}, {prod}, @ond, @loc, @inst);"
        )
        sql_lines.append("SET @instal := LAST_INSERT_ID();")

        marque_pan = escape_sql(row.get("marque_panneau", "").strip())
        modele_pan = escape_sql(row.get("modele_panneau", "").strip())
        if marque_pan and marque_pan not in marques_panneau:
            sql_lines.append(f"INSERT INTO marque_panneau (marque) VALUES ('{marque_pan}');")
            marques_panneau.add(marque_pan)
        if modele_pan and modele_pan not in modeles_panneau:
            sql_lines.append(f"INSERT INTO modele_panneau (modele) VALUES ('{modele_pan}');")
            modeles_panneau.add(modele_pan)

        sql_lines.append(f"SET @m_pan := (SELECT MIN(id_marque) FROM marque_panneau WHERE marque = '{marque_pan}');")
        sql_lines.append(f"SET @mod_pan := (SELECT MIN(id_modele) FROM modele_panneau WHERE modele = '{modele_pan}');")
        sql_lines.append("INSERT INTO panneau (id_installation, id_marque, id_modele) VALUES (@instal, @m_pan, @mod_pan);")

with OUTPUT_SQL.open("w", encoding="utf-8") as out:
    out.write("\n".join(sql_lines))

with UNMATCH_LOG.open("w", encoding="utf-8") as f:
    for name in sorted(unknown):
        f.write(f"{name}\n")
