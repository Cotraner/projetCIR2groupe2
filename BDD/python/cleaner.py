import csv
import unicodedata
import html

INPUT_CSV = "../data.csv"
OUTPUT_CSV = "../data_cleaned.csv"

def normalize_text(text):
    if not text:
        return ""
    text = html.unescape(text)  # Remplace &amp;, &eacute; etc.
    text = unicodedata.normalize('NFKD', text)
    text = text.encode('ASCII', 'ignore').decode('utf-8')  # Supprime les accents
    text = text.lower()
    text = text.replace('-', ' ').replace('_', ' ').replace('/', ' ').replace('\\', ' ')
    text = ' '.join(text.split())  # Supprime les espaces multiples
    return text.strip()

def build_mapping(values):
    mapping = {}
    reverse = {}
    current_id = 1
    for v in values:
        norm = normalize_text(v)
        if norm not in reverse:
            reverse[norm] = v
            mapping[v] = v
        else:
            mapping[v] = reverse[norm]
    return mapping

def clean_csv():
    with open(INPUT_CSV, newline='', encoding='utf-8') as infile:
        reader = csv.DictReader(infile)
        rows = list(reader)

    installateurs = set(row["installateur"] for row in rows)
    panneaux_marques = set(row["panneaux_marque"] for row in rows)
    onduleurs_marques = set(row["onduleur_marque"] for row in rows)
    panneaux_modeles = set(row["panneaux_modele"] for row in rows)
    onduleurs_modeles = set(row["onduleur_modele"] for row in rows)

    mappings = {
        "installateur": build_mapping(installateurs),
        "panneaux_marque": build_mapping(panneaux_marques),
        "onduleur_marque": build_mapping(onduleurs_marques),
        "panneaux_modele": build_mapping(panneaux_modeles),
        "onduleur_modele": build_mapping(onduleurs_modeles),
    }

    cleaned_rows = []
    for row in rows:
        for key in mappings:
            row[key] = mappings[key].get(row[key], row[key])
        cleaned_rows.append(row)

    with open(OUTPUT_CSV, "w", newline='', encoding="utf-8") as outfile:
        writer = csv.DictWriter(outfile, fieldnames=reader.fieldnames)
        writer.writeheader()
        writer.writerows(cleaned_rows)

    print(f"Fichier nettoyé écrit dans : {OUTPUT_CSV}")

if __name__ == "__main__":
    clean_csv()
