import csv
import random
from datetime import datetime, timedelta
from faker import Faker
import importlib.metadata # Used for robust version check

# --- Configuration ---
NUM_RECORDS = 150000 # Increased to 150,000 records to guarantee over 1M unique nodes
OUTPUT_FILENAME = 'fraud_test_data_extended_scenarios_final.csv' # New filename for this version

# --- Global Faker Instance (for general use like generate_dob if not locale-specific) ---
global_faker = Faker() # A generic Faker instance

# --- Helper Functions for Data Generation (Moved to top) ---

def generate_random_number_string(length):
    """Generates a random string of digits."""
    return ''.join(random.choices('0123456789', k=length))

def generate_alphanumeric_id(id_type, country_iso3, faker_instance):
    """Generates an alphanumeric ID based on type and country, with varying lengths."""
    # FAKER_LOCALES is defined later, so we need to pass id_lengths or make it accessible
    # For now, will use a default length, and rely on calling context for country_data
    length = FAKER_LOCALES[country_iso3]["id_lengths"].get(id_type, 10) # FAKER_LOCALES accessible as global
    chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'

    if id_type == 'DL':
        return ''.join(random.choices('0123456789', k=length))
    elif id_type == 'PP':
        return ''.join(random.choices(chars, k=length))
    elif id_type == 'TIN':
        return ''.join(random.choices('0123456789', k=length))
    else:
        return ''.join(random.choices(chars, k=length))


def generate_dob(faker_instance, min_age=18, max_age=90):
    """Generates a random date of birth using the provided Faker instance."""
    today = datetime.now()
    max_dob = today - timedelta(days=min_age * 365)
    min_dob = today - timedelta(days=max_age * 365)
    return faker_instance.date_between(start_date=min_dob, end_date=max_dob).strftime('%Y-%m-%d')

def generate_id_issue_date(faker_instance, dob_str):
    """Generates an ID issue date after DOB but before today using the provided Faker instance."""
    dob = datetime.strptime(dob_str, '%Y-%m-%d')
    start_date = dob + timedelta(days=18 * 365)
    if start_date > datetime.now():
        start_date = datetime.now() - timedelta(days=365)
    return faker_instance.date_between(start_date=start_date, end_date='today').strftime('%Y-%m-%d')

def generate_email(faker_instance, first_name, last_name, specific_domain=None):
    """Generates a plausible email address using the provided Faker instance for domains."""
    domain = specific_domain if specific_domain else faker_instance.free_email_domain()
    return f"{first_name.lower().replace(' ', '')}.{last_name.lower().replace(' ', '')}{random.randint(1,999)}@{domain}"

# --- Fuzzy Name Mapping (Moved up) ---
# This map is designed to create phonetic or common spelling variations of names,
# simulating typos, regional spellings, or deliberate alterations.
FUZZY_NAME_MAP = {
    "Smith": ["Smyth", "Smithe", "Smith"], "Jones": ["Joens", "Jonnes", "Jones"],
    "Miller": ["Millar", "Miler", "Miller"], "Davis": ["Davies", "Davys", "Davis"],
    "Garcia": ["Garzia", "Garsia", "Garcia"], "Rodriguez": ["Rodrigues", "Rodrigz", "Rodriguez"],
    "Martinez": ["Martines", "Martinz", "Martinez"], "Hernandez": ["Hernandes", "Hernandz", "Hernandez"],
    "Wilson": ["Wilsun", "Willson", "Wilson"], "Moore": ["Moor", "More", "Moore"],
    "Taylor": ["Tayler", "Taylour", "Taylor"], "Anderson": ["Andersen", "Andersohn", "Anderson"],
    "Thomas": ["Tomas", "Thomass", "Thomas"], "Jackson": ["Jacksun", "Jaxson", "Jackson"],
    "White": ["Whyte", "Whit", "White"], "Lee": ["Li", "Le", "Lee"],
    "King": ["Kng", "Kinn", "King"], "Wright": ["Rite", "Writ", "Wright"],
    "Lopez": ["Lopes", "Lopec", "Lopez"], "Nguyen": ["Nguyeen", "Nguyan", "Nguyen"],
    "Gonzalez": ["Gonzales", "Gonzaless", "Gonzalez"],
    "Sharma": ["Sarma", "Sharrma", "Sharma"], "Singh": ["Sing", "Sngh", "Singh"],
    "Kumar": ["Kumaar", "Kummar", "Kumar"], "Gupta": ["Guptaa", "Guptha", "Gupta"],
    "Reddy": ["Reddi", "Redy", "Reddy"], "Patel": ["Patil", "Pattel", "Patel"],
    "Chan": ["Chann", "Chaan", "Chan"], "Wong": ["Wang", "Wongg", "Wong"],
    "Santos": ["Santoz", "Santoes", "Santos"], "Dela Cruz": ["De La Cruz", "Delacruz", "Dela Cruz"],
    "Müller": ["Mueller", "Muller", "Müller"], "Schmidt": ["Schmid", "Schmitt", "Schmidt"],
    "Dubois": ["Duboi", "Du Bois", "Dubois"], "Petit": ["Peti", "Petitt", "Petit"],
    
    "John": ["Jon", "Jhon", "John"], "Mary": ["Mery", "Marey", "Mary"],
    "David": ["Daveed", "Daviid", "David"], "Maria": ["Mariah", "Mariya", "Maria"],
    "Michael": ["Mikel", "Michal", "Michael"], "Sophia": ["Sofia", "Sofiya", "Sophia"],
    "Juan": ["Juann", "Jhon", "Juan"], "Carlos": ["Carloz", "Karlos", "Carlos"],
    "Pierre": ["Piare", "Peirre", "Pierre"], "Hans": ["Hanz", "Hanns", "Hans"],
    "Rahul": ["Rahool", "Raul", "Rahul"], "Priya": ["Priyaa", "Pria", "Priya"],
}

def get_fuzzy_name(base_name, is_first_name=False):
    """Returns a fuzzy variation of a given name if available."""
    return random.choice(FUZZY_NAME_MAP.get(base_name, [base_name]))


# --- Faker Instances & Country Mappings (Now after helper functions) ---
FAKER_LOCALES = {
    "IND": {"faker": Faker('en_IN'), "phone_code": "+91", "id_lengths": {"DL": 10, "PP": 9, "TIN": 10}},
    "COL": {"faker": Faker('es_CO'), "phone_code": "+57", "id_lengths": {"DL": 10, "PP": 9, "TIN": 10}},
    "MEX": {"faker": Faker('es_MX'), "phone_code": "+52", "id_lengths": {"DL": 10, "PP": 9, "TIN": 10}},
    "PHL": {"faker": Faker('en_PH'), "phone_code": "+63", "id_lengths": {"DL": 12, "PP": 9, "TIN": 10}},
    "ESP": {"faker": Faker('es_ES'), "phone_code": "+34", "id_lengths": {"DL": 9, "PP": 9, "TIN": 9}},
    "FRA": {"faker": Faker('fr_FR'), "phone_code": "+33", "id_lengths": {"DL": 12, "PP": 9, "TIN": 10}},
    "DEU": {"faker": Faker('de_DE'), "phone_code": "+49", "id_lengths": {"DL": 11, "PP": 9, "TIN": 11}},
    "USA": {"faker": Faker('en_US'), "phone_code": "+1", "id_lengths": {"DL": 10, "PP": 9, "TIN": 9}},
    "GBR": {"faker": Faker('en_GB'), "phone_code": "+44", "id_lengths": {"DL": 16, "PP": 9, "TIN": 9}},
    "CAN": {"faker": Faker('en_CA'), "phone_code": "+1", "id_lengths": {"DL": 12, "PP": 9, "TIN": 9}},
    "AUS": {"faker": Faker('en_AU'), "phone_code": "+61", "id_lengths": {"DL": 10, "PP": 9, "TIN": 9}},
}
ISO3_COUNTRIES = list(FAKER_LOCALES.keys())


# --- Shared Fraudulent Attributes (Pool for Linkages) ---
NUM_SHARED_ATTRIBUTES_POOL = 60000 

SHARED_DEVICE_IDS = [f"SHDVC{i:06d}" for i in range(1, NUM_SHARED_ATTRIBUTES_POOL + 1)]
SHARED_FACE_IDS = [f"SHFACE{i:010d}" for i in range(1, NUM_SHARED_ATTRIBUTES_POOL + 1)]
_temp_shared_phones = []
for _ in range(NUM_SHARED_ATTRIBUTES_POOL):
    random_locale_data = random.choice(list(FAKER_LOCALES.values()))
    _temp_shared_phones.append(random_locale_data["phone_code"] + "".join(random.choices('0123456789', k=10)))
SHARED_PHONE_NUMBERS = ["'" + p for p in _temp_shared_phones]

SHARED_EMAIL_DOMAINS = ["fraudnet.com", "scammail.org", "badactor.net", "darkweb.info", "phishmail.co", "anonmail.xyz"]
SHARED_EMAILS = [f"fraudster_common_{i}@{random.choice(SHARED_EMAIL_DOMAINS)}" for i in range(1, NUM_SHARED_ATTRIBUTES_POOL + 1)]


# --- Shared Attributes for Family/Related Party Fraud ---
NUM_FAMILY_CORES = int(NUM_RECORDS * 0.0001)
FAMILY_CORES = []
for _ in range(NUM_FAMILY_CORES):
    FAMILY_CORES.append({
        "last_name_base": global_faker.last_name(),
        "shared_device": "'" + generate_random_number_string(8),
        "shared_phone": "'" + "".join(random.choices('0123456789', k=10)),
        "shared_email": generate_email(global_faker, "family", "core", random.choice(SHARED_EMAIL_DOMAINS))
    })

# --- Shared Attributes for Velocity Attacks ---
NUM_VELOCITY_BURST_PROFILES = int(NUM_RECORDS * 0.0001)
VELOCITY_BURST_PROFILES = []
for _ in range(NUM_VELOCITY_BURST_PROFILES):
    VELOCITY_BURST_PROFILES.append({
        "device_id": "'" + generate_random_number_string(8),
        "face_id": "'" + generate_random_number_string(12),
        "base_time": datetime.now() - timedelta(days=random.randint(1, 365*2))
    })


# --- Main Data Generation Logic ---

def generate_fraud_test_data(num_records):
    headers = [
        "first_name", "last_name", "dob", "device_id", "face_id",
        "phone_country_code", "phone_number", "email_id", "id_type",
        "id_number", "id_issue_date", "id_issue_country", "timestamp"
    ]
    data_rows = []

    # Store "seed" legitimate users for ATO scenarios
    legitimate_users_for_ato = []
    # Store "seed" legitimate ID numbers for theft scenarios
    legitimate_ids_for_theft = []

    # Proportions of fraud scenarios (can be adjusted)
    ATO_SCENARIO_PERCENT = 0.003
    SHARED_INFRA_SCENARIO_PERCENT = 0.008
    SYNTHETIC_IDENTITY_PERCENT = 0.007
    ID_THEFT_PERCENT = 0.006

    # --- Phase 1: Generate Base Legitimate Records and Mark Some as Fraud Targets ---
    print("Phase 1: Generating base legitimate records...")
    for i in range(num_records):
        country_iso3, country_data = random.choice(list(FAKER_LOCALES.items()))
        current_faker = country_data["faker"]

        first_name = current_faker.first_name()
        last_name = current_faker.last_name()
        dob = generate_dob(current_faker)
        device_id = "'" + generate_random_number_string(8)
        face_id = "'" + generate_random_number_string(12)
        phone_country_code = country_data["phone_code"]
        phone_number_numeric_part = "".join(random.choices('0123456789', k=10))
        phone_number = "'" + phone_number_numeric_part


        email_id = generate_email(current_faker, first_name, last_name)
        id_type = random.choice(['DL', 'PP', 'TIN'])
        id_number = "'" + generate_alphanumeric_id(id_type, country_iso3, current_faker)
        id_issue_date = generate_id_issue_date(current_faker, dob)
        id_issue_country = country_iso3
        timestamp = datetime.now() - timedelta(days=random.randint(0, 365*3))
        timestamp_str = timestamp.isoformat()

        record = {
            "first_name": first_name, "last_name": last_name, "dob": dob,
            "device_id": device_id, "face_id": face_id,
            "phone_country_code": phone_country_code, "phone_number": phone_number,
            "email_id": email_id, "id_type": id_type,
            "id_number": id_number, "id_issue_date": id_issue_date, "id_issue_country": id_issue_country,
            "timestamp": timestamp_str
        }
        data_rows.append(record)

        if random.random() < ATO_SCENARIO_PERCENT / 5:
            legitimate_users_for_ato.append(record.copy())
        if random.random() < ID_THEFT_PERCENT / 5:
            legitimate_ids_for_theft.append(record['id_number'])


    # --- Phase 2: Inject Fraud Scenarios ---
    print("Phase 2: Injecting fraud scenarios...")

    # Scenario 1: Account Takeover (ATO) Chain
    print("  - Generating ATO scenarios...")
    for original_user in legitimate_users_for_ato:
        num_ato_attempts = random.randint(1, 3)
        current_ato_record = original_user.copy()
        for attempt in range(num_ato_attempts):
            fraud_country_iso3, fraud_country_data = random.choice(list(FAKER_LOCALES.items()))
            fraud_faker = fraud_country_data["faker"]

            ato_record = current_ato_record.copy()
            ato_record['first_name'] = get_fuzzy_name(ato_record['first_name'], is_first_name=True)
            ato_record['last_name'] = get_fuzzy_name(ato_record['last_name'])
            ato_record['device_id'] = "'" + random.choice(SHARED_DEVICE_IDS).lstrip("'")
            ato_record['face_id'] = "'" + random.choice(SHARED_FACE_IDS).lstrip("'")
            ato_record['phone_country_code'] = fraud_country_data["phone_code"]
            phone_number_numeric_part = "".join(random.choices('0123456789', k=10))
            ato_record['phone_number'] = random.choice(SHARED_PHONE_NUMBERS) if random.random() < 0.7 else "'" + phone_number_numeric_part

            ato_record['email_id'] = generate_email(fraud_faker, "fraud", "victim", random.choice(SHARED_EMAIL_DOMAINS))
            ato_record['timestamp'] = (datetime.now() - timedelta(days=random.randint(0, 30))).isoformat()

            if attempt > 0:
                original_dob_dt = datetime.strptime(original_user['dob'], '%Y-%m-%d')
                ato_record['dob'] = (original_dob_dt + timedelta(days=random.randint(-15,15))).strftime('%Y-%m-%d')
                original_id_clean = original_user['id_number'].lstrip("'")
                if len(original_id_clean) > 2:
                    idx = random.randint(1, len(original_id_clean) - 1)
                    new_char = random.choice('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ')
                    ato_record['id_number'] = "'" + original_id_clean[:idx] + new_char + original_id_clean[idx+1:]
                else:
                    ato_record['id_number'] = "'" + generate_alphanumeric_id(original_user['id_type'], fraud_country_iso3, fraud_faker)

            data_rows.append(ato_record)
            current_ato_record = ato_record


    # Scenario 2: Shared Fraudster Infrastructure (Device/Face Rings)
    print("  - Generating Shared Infrastructure scenarios...")
    for _ in range(int(num_records * SHARED_INFRA_SCENARIO_PERCENT / 2)):
        shared_device = random.choice(SHARED_DEVICE_IDS)
        shared_face = random.choice(SHARED_FACE_IDS)

        country_iso3_1, country_data_1 = random.choice(list(FAKER_LOCALES.items()))
        faker_1 = country_data_1["faker"]
        first_name_1 = faker_1.first_name()
        last_name_1 = faker_1.last_name()
        dob_1 = generate_dob(faker_1)
        id_type_1 = random.choice(['DL', 'PP', 'TIN'])
        id_number_1 = "'" + generate_alphanumeric_id(id_type_1, country_iso3_1, faker_1)
        phone_number_numeric_part_1 = "".join(random.choices('0123456789', k=10))
        phone_number_1 = "'" + phone_number_numeric_part_1
        timestamp_1 = (datetime.now() - timedelta(days=random.randint(0, 365))).isoformat()

        data_rows.append({
            "first_name": first_name_1, "last_name": last_name_1, "dob": dob_1,
            "device_id": "'" + shared_device.lstrip("'"), "face_id": "'" + generate_random_number_string(12),
            "phone_country_code": country_data_1["phone_code"], "phone_number": phone_number_1,
            "email_id": generate_email(faker_1, first_name_1, last_name_1, random.choice(SHARED_EMAIL_DOMAINS)),
            "id_type": id_type_1, "id_number": id_number_1,
            "id_issue_date": generate_id_issue_date(faker_1, dob_1), "id_issue_country": country_iso3_1,
            "timestamp": timestamp_1
        })

        country_iso3_2, country_data_2 = random.choice(list(FAKER_LOCALES.items()))
        faker_2 = country_data_2["faker"]
        first_name_2 = get_fuzzy_name(faker_2.first_name(), is_first_name=True)
        last_name_2 = get_fuzzy_name(faker_2.last_name())
        dob_2 = generate_dob(faker_2)
        id_type_2 = random.choice(['DL', 'PP', 'TIN'])
        id_number_2 = "'" + generate_alphanumeric_id(id_type_2, country_iso3_2, faker_2)
        phone_number_numeric_part_2 = "".join(random.choices('0123456789', k=10))
        phone_number_2 = "'" + phone_number_numeric_part_2
        timestamp_2 = (datetime.now() - timedelta(days=random.randint(0, 365))).isoformat()

        data_rows.append({
            "first_name": first_name_2, "last_name": last_name_2, "dob": dob_2,
            "device_id": "'" + generate_random_number_string(8), "face_id": "'" + shared_face.lstrip("'"),
            "phone_country_code": country_data_2["phone_code"], "phone_number": phone_number_2,
            "email_id": generate_email(faker_2, first_name_2, last_name_2, random.choice(SHARED_EMAIL_DOMAINS)),
            "id_type": id_type_2, "id_number": id_number_2,
            "id_issue_date": generate_id_issue_date(faker_2, dob_2), "id_issue_country": country_iso3_2,
            "timestamp": timestamp_2
        })

    # Scenario 3: Synthetic Identity Ring (Shared Contact Info/Fuzzy Names)
    print("  - Generating Synthetic Identity scenarios...")
    for _ in range(int(num_records * SYNTHETIC_IDENTITY_PERCENT / 2)):
        shared_phone = random.choice(SHARED_PHONE_NUMBERS)
        shared_email = random.choice(SHARED_EMAILS)

        country_iso3_base, country_data_base = random.choice(list(FAKER_LOCALES.items()))
        faker_base = country_data_base["faker"]

        first_name_1 = faker_base.first_name()
        last_name_1 = faker_base.last_name()
        dob_1 = generate_dob(faker_base)
        id_type_1 = random.choice(['DL', 'PP'])
        id_number_1 = "'" + generate_alphanumeric_id(id_type_1, country_iso3_base, faker_base)
        timestamp_1 = (datetime.now() - timedelta(days=random.randint(0, 365))).isoformat()

        data_rows.append({
            "first_name": first_name_1, "last_name": last_name_1, "dob": dob_1,
            "device_id": "'" + generate_random_number_string(8), "face_id": "'" + generate_random_number_string(12),
            "phone_country_code": country_data_base["phone_code"], "phone_number": shared_phone, "email_id": shared_email,
            "id_type": id_type_1, "id_number": id_number_1,
            "id_issue_date": generate_id_issue_date(faker_base, dob_1), "id_issue_country": country_iso3_base,
            "timestamp": timestamp_1
        })

        first_name_2 = get_fuzzy_name(first_name_1, is_first_name=True)
        last_name_2 = get_fuzzy_name(last_name_1)
        dob_2 = (datetime.strptime(dob_1, '%Y-%m-%d') + timedelta(days=random.randint(-10,10))).strftime('%Y-%m-%d')
        id_type_2 = random.choice(['DL', 'PP'])
        id_number_2 = "'" + generate_alphanumeric_id(id_type_2, country_iso3_base, faker_base)
        timestamp_2 = (datetime.now() - timedelta(days=random.randint(0, 365))).isoformat()

        data_rows.append({
            "first_name": first_name_2, "last_name": last_name_2, "dob": dob_2,
            "device_id": "'" + generate_random_number_string(8), "face_id": "'" + generate_random_number_string(12),
            "phone_country_code": country_data_base["phone_code"], "phone_number": shared_phone, "email_id": shared_email,
            "id_type": id_type_2, "id_number": id_number_2,
            "id_issue_date": generate_id_issue_date(faker_base, dob_2), "id_issue_country": country_iso3_base,
            "timestamp": timestamp_2
        })

    # Scenario 4: ID Theft with Reuse/Modification
    print("  - Generating ID Theft scenarios...")
    num_id_theft_scenarios = min(len(legitimate_ids_for_theft), int(num_records * ID_THEFT_PERCENT))
    for original_id_number in random.sample(legitimate_ids_for_theft, num_id_theft_scenarios):
        fraud_country_iso3, fraud_country_data = random.choice(list(FAKER_LOCALES.items()))
        fraud_faker = fraud_country_data["faker"]

        fraud_first_1 = fraud_faker.first_name()
        fraud_last_1 = get_fuzzy_name(fraud_faker.last_name())
        phone_number_f1_numeric = "".join(random.choices('0123456789', k=10))
        phone_number_f1 = "'" + phone_number_f1_numeric
        timestamp_f1 = (datetime.now() - timedelta(days=random.randint(0, 365))).isoformat()

        data_rows.append({
            "first_name": fraud_first_1, "last_name": fraud_last_1, "dob": generate_dob(fraud_faker),
            "device_id": "'" + random.choice(SHARED_DEVICE_IDS).lstrip("'"), "face_id": "'" + random.choice(SHARED_FACE_IDS).lstrip("'"),
            "phone_country_code": fraud_country_data["phone_code"], "phone_number": phone_number_f1,
            "email_id": generate_email(fraud_faker, fraud_first_1, fraud_last_1, random.choice(SHARED_EMAIL_DOMAINS)),
            "id_type": original_id_number.lstrip("'")[:2],
            "id_number": original_id_number,
            "id_issue_date": generate_id_issue_date(fraud_faker, generate_dob(fraud_faker)), "id_issue_country": random.choice(ISO3_COUNTRIES),
            "timestamp": timestamp_f1
        })

        fraud_first_2 = get_fuzzy_name(fraud_faker.first_name(), is_first_name=True)
        fraud_last_2 = get_fuzzy_name(fraud_faker.last_name())
        original_id_clean = original_id_number.lstrip("'")
        altered_id_number = "'" + original_id_clean[:-1] + random.choice('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ') if len(original_id_clean) > 1 else "'" + original_id_clean + "1"
        phone_number_f2_numeric = "".join(random.choices('0123456789', k=10))
        phone_number_f2 = "'" + phone_number_f2_numeric
        timestamp_f2 = (datetime.now() - timedelta(days=random.randint(0, 365))).isoformat()

        data_rows.append({
            "first_name": fraud_first_2, "last_name": fraud_last_2, "dob": generate_dob(fraud_faker),
            "device_id": "'" + random.choice(SHARED_DEVICE_IDS).lstrip("'"), "face_id": "'" + random.choice(SHARED_FACE_IDS).lstrip("'"),
            "phone_country_code": fraud_country_data["phone_code"], "phone_number": phone_number_f2,
            "email_id": generate_email(fraud_faker, fraud_first_2, fraud_last_2, random.choice(SHARED_EMAIL_DOMAINS)),
            "id_type": original_id_number.lstrip("'")[:2],
            "id_number": altered_id_number,
            "id_issue_date": generate_id_issue_date(fraud_faker, generate_dob(fraud_faker)), "id_issue_country": random.choice(ISO3_COUNTRIES),
            "timestamp": timestamp_f2
        })

    # --- NEW SCENARIO 5: Related Parties Fraud (Family/Associate Fraud) ---
    print("  - Generating Related Parties Fraud scenarios...")
    for family_core in FAMILY_CORES:
        num_family_members = random.randint(2, 5)
        for member_idx in range(num_family_members):
            country_iso3, country_data = random.choice(list(FAKER_LOCALES.items()))
            current_faker = country_data["faker"]

            first_name = current_faker.first_name()
            last_name = get_fuzzy_name(family_core["last_name_base"])
            dob = generate_dob(current_faker)
            id_type = random.choice(['DL', 'PP', 'TIN'])
            id_number = "'" + generate_alphanumeric_id(id_type, country_iso3, current_faker)
            id_issue_date = generate_id_issue_date(current_faker, dob)
            id_issue_country = country_iso3
            timestamp_member = (datetime.now() - timedelta(days=random.randint(0, 365*2))).isoformat()

            record = {
                "first_name": first_name, "last_name": last_name, "dob": dob,
                "device_id": family_core["shared_device"],
                "face_id": "'" + generate_random_number_string(12),
                "phone_country_code": country_data["phone_code"],
                "phone_number": family_core["shared_phone"],
                "email_id": family_core["shared_email"],
                "id_type": id_type, "id_number": id_number,
                "id_issue_date": id_issue_date, "id_issue_country": id_issue_country,
                "timestamp": timestamp_member
            }
            data_rows.append(record)

    # --- NEW SCENARIO 6: Application Velocity / Rapid Account Creation ---
    print("  - Generating Application Velocity scenarios...")
    for burst_profile in VELOCITY_BURST_PROFILES:
        num_burst_records = random.randint(5, 15)
        
        base_burst_time = burst_profile["base_time"]
        burst_timestamps = []
        for i in range(num_burst_records):
            ts = base_burst_time + timedelta(minutes=random.randint(0, 30))
            burst_timestamps.append(ts.isoformat())

        for member_idx in range(num_burst_records):
            country_iso3, country_data = random.choice(list(FAKER_LOCALES.items()))
            current_faker = country_data["faker"]

            first_name = get_fuzzy_name(current_faker.first_name(), is_first_name=True)
            last_name = get_fuzzy_name(current_faker.last_name())
            dob = generate_dob(current_faker)
            id_type = random.choice(['DL', 'PP', 'TIN'])
            id_number = "'" + generate_alphanumeric_id(id_type, country_iso3, current_faker)
            id_issue_date = generate_id_issue_date(current_faker, dob)
            id_issue_country = country_iso3

            record = {
                "first_name": first_name, "last_name": last_name, "dob": dob,
                "device_id": burst_profile["device_id"],
                "face_id": burst_profile["face_id"],
                "phone_country_code": country_data["phone_code"],
                "phone_number": "'" + "".join(random.choices('0123456789', k=10)),
                "email_id": generate_email(current_faker, first_name, last_name),
                "id_type": id_type, "id_number": id_number,
                "id_issue_date": id_issue_date, "id_issue_country": id_issue_country,
                "timestamp": burst_timestamps[member_idx]
            }
            data_rows.append(record)


    # Shuffle to mix legitimate and fraudulent records
    print("Phase 3: Shuffling and writing to CSV...")
    random.shuffle(data_rows)
    return headers, data_rows

# --- Execution ---
if __name__ == "__main__":
    # Robustly check Faker version (for user info)
    try:
        faker_version = importlib.metadata.version('Faker')
    except importlib.metadata.PackageNotFoundError:
        faker_version = "Unknown (Faker package not found)"
    except Exception as e:
        faker_version = f"Error getting version: {e}"
    print(f"Generating data using Faker version: {faker_version}")

    headers, data = generate_fraud_test_data(NUM_RECORDS)

    with open(OUTPUT_FILENAME, 'w', newline='', encoding='utf-8') as csvfile:
        writer = csv.DictWriter(csvfile, fieldnames=headers)
        writer.writeheader()
        writer.writerows(data)

    print(f"Test data successfully generated and saved to '{OUTPUT_FILENAME}'")
    print(f"Total rows generated: {len(data)}")