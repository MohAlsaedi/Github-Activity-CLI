GitHub Activity CLI (PHP)

A simple command-line PHP tool that fetches and displays a user’s recent public activity from GitHub using the GitHub Events API.
- The project idea from roadmap.sh: https://roadmap.sh/projects/github-user-activity

---

FEATURES

* Command-line interface (CLI)
* Validates GitHub username format
* Handles common HTTP errors (404, 403, 500, etc.)
* Displays activity in a readable table
* Human-friendly date formatting
* Lightweight — no external dependencies

---

REQUIREMENTS

* PHP 8.0 or higher (CLI enabled)
* Internet connection

---

USAGE

php github-activity.php <username>

Example:

php github-activity.php Potato

---

OUTPUT EXAMPLE

## Event Type                    Repository                               Date

PushEvent                     torvalds/linux                           2026-02-24 12:10 UTC
IssuesEvent                   torvalds/subsurface                      2026-02-23 09:45 UTC

---

HOW IT WORKS

1. Validates CLI arguments
2. Sends a GET request to:
   https://api.github.com/users/{username}/events
3. Parses the HTTP status code
4. Decodes JSON response
5. Formats and prints results

---

ERROR HANDLING

The script handles:

* Invalid username format
* User not found (404)
* Rate limit exceeded (403)
* API/server issues (5xx)
* Network errors
* JSON parsing errors

---

FILE STRUCTURE

github-activity.php   # Main CLI script
README.md             # Documentation

---

AUTHOR

Mohammed Alsaedi

---

LICENSE

MIT License — feel free to use and modify.
