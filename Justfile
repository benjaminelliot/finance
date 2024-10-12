# Set a variable that gets the current year
current_year := `date +%Y`

default:
    just --list

import:
    @just preprocess
    @just parse-rules

parse-rules:
    @echo 'Parsing rules...'
    echo "\ninclude {{current_year}}.chasesapphire.journal\ninclude {{current_year}}.chasechecking.journal\ninclude {{current_year}}.discover.journal\ninclude {{current_year}}.mortgage.journal" > {{current_year}}.journal

    hledger -f ./rules/chasesapphire.rules print > {{current_year}}.chasesapphire.journal
    hledger -f ./rules/chasechecking.rules print > {{current_year}}.chasechecking.journal
    hledger -f ./rules/discover.rules print > {{current_year}}.discover.journal
    hledger -f ./rules/mortgage.rules print > {{current_year}}.mortgage.journal

    @just check-unknown

preprocess:
    @echo 'Pre-processing CSVs...'
    python3 ./scripts/preprocess.py

check-unknown:
    @echo 'Checking for unknown expenses...'
    grep -B 2 'expenses:unknown' *.journal | { grep -v grep || true; } 

report-mi:
    @echo 'Monthly Income Statement'
    hledger -f ./journals/all.journal is --monthly --depth=3 --sort-amount

report-bs:
    @echo 'Balance Sheet'
    hledger -f ./journals/all.journal balance --monthly --depth=2

start-override:
    @echo 'Starting the app...'
    php tempest serve
