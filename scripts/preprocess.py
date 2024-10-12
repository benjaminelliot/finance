import csv
import os
from pathlib import Path
import glob
import re
import sqlite3

dirname = os.path.dirname(__file__)


def preprocess(csv_file):
    file_path = os.path.join(
        dirname, f'../in/{csv_file["institution"]}/{csv_file["file_name"]}'
    )
    path = Path(file_path)
    if path.exists():
        open_file(file_path, csv_file)


def open_file(file_path, csv_file):
    with open(file_path, "r") as file:
        handle_csv(file, csv_file)


def validate_csv(file):
    reader = csv.reader(file)
    header = next(reader)
    header_length = len(header)

    for row in reader:
        if len(row) != header_length:
            raise ValueError(
                f"Invalid row: {row}, length: {len(row)}, expected: {header_length}"
            )


def get_date_header_for_institution(institution, file_name):
    if institution == "chase" and "checking" in file_name:
        return "Posting Date"
    elif institution == "chase":
        return "Post Date"
    elif institution == "discover":
        return "Post Date"
    elif institution == "isabellabank":
        return "Processed Date"
    else:
        raise ValueError(f"Unknown institution: {institution}")


def handle_csv(file, csv_file):
    validate_csv(file)

    # Reset the reader
    file.seek(0)

    reader = csv.reader(file)
    header = next(reader)
    header.append("Override")
    rows = [header]

    db_path = os.path.join(dirname, "overrides.db")
    con = sqlite3.connect(db_path)

    for row in reader:
        date_index = header.index(
            get_date_header_for_institution(
                csv_file["institution"], csv_file["file_name"]
            )
        )
        value_index = header.index("Amount")
        description_index = header.index("Description")

        date = row[date_index]
        value = float(row[value_index])
        description = row[description_index]
        found_override = False

        cur = con.cursor()
        res = cur.execute(
            "SELECT * FROM overrides WHERE transaction_date = ? AND amount = ?",
            [date, value],
        )
        override_row = res.fetchone()
        if override_row is not None:
            row.append(override_row[4])
        else:
            row.append("N/A")

        rows.append(row)

    out_dir = os.path.join(dirname, f'../out/{csv_file["institution"]}')
    if not os.path.exists(out_dir):
        os.makedirs(out_dir)

    file_name = os.path.basename(file.name)
    out_path = os.path.join(
        dirname, f'../out/{csv_file["institution"]}/{csv_file["file_name"]}'
    )
    with open(out_path, "w", newline="") as out_file:
        writer = csv.writer(out_file)
        writer.writerows(rows)


def locate_csv_files():
    csv_files = []
    files = glob.glob(os.path.join(dirname, "../in/*/*.csv"))
    for file in files:
        match = re.match(f"^{dirname}/../in/(\w*)/([^%]*)$", file)
        if match:
            [institution, csv_file_name] = match.groups()
            csv_files.append({"institution": institution, "file_name": csv_file_name})

    return csv_files


csv_files = locate_csv_files()
for csv_file in csv_files:
    print(f'Preprocessing {csv_file["institution"]}/{csv_file["file_name"]}')
    preprocess(csv_file)
