| count, asort | index.php |
# Gatepass - Event Ticketing (beginner version)

Copy all files into MAMP/htdocs/<your folder>/ then open http://localhost:8888/<your folder>/
Needs PHP 8+ (the `match` expression). The uploads/ folder must be writable.

## Where each rubric item is
| Topic | Where |
|---|---|
| Sticky POST form | index.php (top variables), templates/page.php (value="...") |
| Validation empty/trim/filter_var | index.php, inside `if POST` |
| XSS htmlspecialchars | includes/functions.php `e()`, used on every echo in page.php |
| File upload + move_uploaded_file | index.php ("File upload checks" and "save everything") |
| PRG header + exit | index.php, after add_registration() |
| Multidimensional arrays | includes/data.php ($events, $tiers) |
| 2+ typed functions | includes/functions.php (all of them) |
| Pass by reference / static | add_registration(array &$list), next_ticket_id() |
| count, asort | index.php |
| foreach + endforeach / endif | templates/page.php |
| if-elseif-else / match | get_level(), group_type() |
| Math | calculate_ticket_total() |
| ?? , <=> , .= , === | index.php ??, sort_by_total() <=>, $summary .=, === in several places |

## Live challenge quick edits
- Name length: MIN_NAME_LENGTH in includes/data.php
- Sort order: change 'desc' in `$_GET['sort'] ?? 'desc'` in index.php
