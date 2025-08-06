import requests
import json
import csv
import time
import argparse
import os

# --- Configuration ---
API_URL = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent"
TEMPERATURE = 0
DEBUG = False

# --- Output Columns ---
ADDITIONAL_COLUMNS = [
    'consolidated_risk_level', 'consolidated_risk_score',
    'risk_explanation', 'confidence_score'
]

# --- Utility Functions ---
def log(msg):
    if DEBUG:
        print(msg)

def safe_int(value):
    try:
        return int(value)
    except (ValueError, TypeError):
        return None

def build_prompt(input_json):
    try:
        with open("prompt_template.txt", encoding="utf-8") as f:
            template = f.read()
        return template.replace("{{INPUT_JSON_PLACEHOLDER}}", input_json)
    except FileNotFoundError:
        print("[ERROR] 'prompt_template.txt' not found. Please ensure it exists.")
        exit(1)

def parse_row(row):
    return json.dumps({
        "email": {
            "riskoutcome": row.get('email_riskoutcome'),
            "riskdatefirstseen": row.get('email_riskdatefirstseen'),
            "riskbasiccheckstatus": row.get('email_riskbasiccheckstatus'),
            "verifybreachdata": row.get('email_verifybreachdata'),
            "verifycreditheadercheck": row.get('email_verifycreditheadercheck')
        },
        "phone": {
            "riskoutcome": row.get('phone_riskoutcome'),
            "riskdatefirstseen": row.get('phone_riskdatefirstseen'),
            "riskscore": safe_int(row.get('phone_riskscore')),
            "verifybreachdata": row.get('phone_verifybreachdata'),
            "verifycreditheadercheck": row.get('phone_verifycreditheadercheck')
        },
        "document": {
            "riskoutcome": row.get('document_riskoutcome')
        },
        "selfie": {
            "riskoutcome": row.get('selfie_riskoutcome')
        },
        "device": {
            "riskoutcome": row.get('device_riskoutcome'),
            "riskdatefirstseen": row.get('device_riskdatefirstseen')
        }
    }, ensure_ascii=False)

def call_api(prompt, api_key):
    try:
        response = requests.post(
            f"{API_URL}?key={api_key}",
            headers={"Content-Type": "application/json"},
            json={
                "contents": [{"parts": [{"text": prompt}]}],
                "generationConfig": {
                    "temperature": TEMPERATURE,
                    "responseMimeType": "application/json"
                }
            }
        )
        response.raise_for_status()
        data = response.json()
        candidate = data.get("candidates", [{}])[0]
        text = candidate.get("content", {}).get("parts", [{}])[0].get("text", "")
        if text.startswith("```json"):
            text = text.strip("```json").strip("```")
        return json.loads(text)
    except (requests.RequestException, json.JSONDecodeError, KeyError) as e:
        log(f"[ERROR] API or JSON failure: {e}")
        return {"consolidated_risk_level": "ERROR", "risk_explanation": str(e)}

def process_file(api_key, input_csv, output_csv):
    if not os.path.isfile(input_csv):
        print(f"[ERROR] Input file not found: {input_csv}")
        return

    try:
        with open(input_csv, newline='', encoding='utf-8') as infile:
            reader = csv.DictReader(infile)

            if not reader.fieldnames:
                print(f"[ERROR] Input file '{input_csv}' has no headers or is empty.")
                return

            print(f"[INFO] Detected CSV headers: {reader.fieldnames}")

            fieldnames = reader.fieldnames + [col for col in ADDITIONAL_COLUMNS if col not in reader.fieldnames]

            with open(output_csv, mode='w', newline='', encoding='utf-8') as outfile:
                writer = csv.DictWriter(outfile, fieldnames=fieldnames)
                writer.writeheader()

                for i, row in enumerate(reader, start=1):
                    record_id = row.get("record_id", f"Record #{i}")
                    print(f"\n→ Processing {record_id}...")

                    input_json = parse_row(row)
                    prompt = build_prompt(input_json)
                    result = call_api(prompt, api_key)

                    for col in ADDITIONAL_COLUMNS:
                        row[col] = result.get(col, '')

                    writer.writerow(row)
                    print(f"✓ Finished {record_id}")
                    time.sleep(1)

    except Exception as e:
        print(f"[FATAL ERROR] {e}")

# --- CLI Entry Point ---
if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Consolidate identity risk data using Gemini API.")
    parser.add_argument('--api_key', required=True, help="Your Google AI Studio API key.")
    parser.add_argument('--input', default="input_risks.csv", help="Path to input CSV file.")
    parser.add_argument('--output', default="output_consolidated_risks.csv", help="Path to output CSV file.")
    args = parser.parse_args()

    print("\n[STARTING] Risk consolidation process...")
    print(f"[DEBUG] Working directory: {os.getcwd()}")
    process_file(args.api_key, args.input, args.output)
    print("\n[COMPLETED] All records processed.")
