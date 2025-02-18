#!/usr/bin/python3

import re
import sys
import json
import pickle

# Argumen check
if len(sys.argv) != 4:
    print("\n\nPenggunaan\n\tquery.py [index.txt] [n] [query]..\n")
    sys.exit(1)

query = sys.argv[3].split(" ")
n = int(sys.argv[2])

# Membaca indexdb
try:
    with open(sys.argv[1], 'rb') as indexdb:
        indexFile = pickle.load(indexdb)
        print(f"Index loaded with {len(indexFile)} entries.")
except Exception as e:
    print(f"Error loading index: {e}")
    sys.exit(1)

# Query
list_doc = {}
for q in query:
    print(f"Processing query word: {q}")
    if q in indexFile:
        for doc in indexFile[q]:
            if doc['url'] in list_doc:
                list_doc[doc['url']]['score'] += doc['score']
            else:
                list_doc[doc['url']] = doc
    else:
        print(f"Query word '{q}' not found in index.")

# Convert to list
list_data = []
for data in list_doc:
    list_data.append(list_doc[data])

# Sorting list by score descending
count = 1
print(f"Sorting documents by score and returning top {n} results.")
for data in sorted(list_data, key=lambda k: k['score'], reverse=True):
    y = json.dumps(data)
    print(y)

    if count == n:
        break
    count += 1
