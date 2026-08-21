import requests
from pathlib import Path
from urllib.parse import urlparse
import os
import time


# ============================================================
# CONFIG
# ============================================================

TARGET_PATH = "/wk/index.php"

OUTPUT_FOUND = "found.txt"
OUTPUT_NOT_FOUND = "not_found.txt"

TIMEOUT = 10


# ============================================================
# COLORS
# ============================================================

RESET = "\033[0m"
BOLD = "\033[1m"

RED = "\033[91m"
GREEN = "\033[92m"
YELLOW = "\033[93m"
CYAN = "\033[96m"
WHITE = "\033[97m"
MAGENTA = "\033[95m"


# ============================================================
# CLEAR
# ============================================================

def clear():
    os.system("cls" if os.name == "nt" else "clear")


# ============================================================
# BANNER
# ============================================================

def banner():
    clear()

    print(f"{CYAN}{BOLD}")
    print("╔══════════════════════════════════════════════╗")
    print("║      URL CROSSCHECK BY AZKA                  ║")
    print("╠══════════════════════════════════════════════╣")
    print("║  Target : /wk/index.php                      ║")
    print("║  Status : HTTP response                      ║")
    print("╚══════════════════════════════════════════════╝")
    print(f"{RESET}")


# ============================================================
# FIND TXT
# ============================================================

def find_txt_files():
    return sorted(
        [
            f for f in Path(".").glob("*.txt")
            if f.name.lower() not in {
                OUTPUT_FOUND.lower(),
                OUTPUT_NOT_FOUND.lower()
            }
        ],
        key=lambda x: x.name.lower()
    )


# ============================================================
# SELECT TXT
# ============================================================

def select_file():

    files = find_txt_files()

    if not files:
        print(f"{RED}[!] Tidak ada file .txt ditemukan.{RESET}")
        return None

    print(f"{CYAN}{BOLD}File TXT:{RESET}\n")

    for i, file in enumerate(files, 1):
        print(f"{GREEN}[{i}]{RESET} {file.name}")

    print(f"{YELLOW}[0]{RESET} Keluar\n")

    while True:
        choice = input(
            f"{MAGENTA}Pilih file: {RESET}"
        ).strip()

        if choice == "0":
            return None

        if choice.isdigit():
            number = int(choice)

            if 1 <= number <= len(files):
                return files[number - 1]

        print(f"{RED}[!] Pilihan tidak valid.{RESET}")


# ============================================================
# LOAD DOMAINS
# ============================================================

def load_urls(file):

    urls = []
    seen = set()

    with open(
        file,
        "r",
        encoding="utf-8",
        errors="ignore"
    ) as f:

        for line in f:
            url = line.strip()

            if not url:
                continue

            # Tambahkan scheme kalau belum ada
            if not url.startswith(("http://", "https://")):
                url = "https://" + url

            url = url.rstrip("/")

            if url not in seen:
                seen.add(url)
                urls.append(url)

    return urls


# ============================================================
# CHECK URL
# ============================================================

def check_url(base_url):

    url = base_url + TARGET_PATH

    try:

        response = requests.get(
            url,
            timeout=TIMEOUT,
            allow_redirects=True,
            headers={
                "User-Agent": "Mozilla/5.0"
            }
        )

        status = response.status_code

        return url, status, response.url, None

    except requests.exceptions.Timeout:

        return url, None, None, "TIMEOUT"

    except requests.exceptions.ConnectionError:

        return url, None, None, "CONNECTION ERROR"

    except requests.exceptions.RequestException as error:

        return url, None, None, str(error)


# ============================================================
# MAIN SCANNER
# ============================================================

def scan(file):

    urls = load_urls(file)

    if not urls:
        print(f"{RED}[!] File kosong.{RESET}")
        return

    found = []
    not_found = []

    print()
    print(
        f"{CYAN}[*] Mengecek {len(urls)} URL...{RESET}\n"
    )

    for number, base_url in enumerate(urls, 1):

        url, status, final_url, error = check_url(base_url)

        # ----------------------------------------------------
        # SUCCESS / REDIRECT
        # ----------------------------------------------------

        if status is not None:

            if 200 <= status < 300:

                print(
                    f"{GREEN}[{number:03}] "
                    f"FOUND  {status}  {url}{RESET}"
                )

                found.append(
                    f"{url} | HTTP {status}"
                )

            elif 300 <= status < 400:

                print(
                    f"{YELLOW}[{number:03}] "
                    f"REDIRECT  {status}  {url}{RESET}"
                )

                found.append(
                    f"{url} | HTTP {status} | REDIRECT {final_url}"
                )

            elif status == 403:

                print(
                    f"{YELLOW}[{number:03}] "
                    f"FORBIDDEN 403  {url}{RESET}"
                )

                not_found.append(
                    f"{url} | HTTP 403"
                )

            elif status == 404:

                print(
                    f"{RED}[{number:03}] "
                    f"NOT FOUND 404  {url}{RESET}"
                )

                not_found.append(
                    f"{url} | HTTP 404"
                )

            else:

                print(
                    f"{YELLOW}[{number:03}] "
                    f"HTTP {status}  {url}{RESET}"
                )

                not_found.append(
                    f"{url} | HTTP {status}"
                )

        # ----------------------------------------------------
        # ERROR
        # ----------------------------------------------------

        else:

            print(
                f"{RED}[{number:03}] "
                f"{error}  {url}{RESET}"
            )

            not_found.append(
                f"{url} | {error}"
            )

        time.sleep(0.05)

    # ========================================================
    # SAVE FOUND
    # ========================================================

    with open(
        OUTPUT_FOUND,
        "w",
        encoding="utf-8"
    ) as f:

        for item in found:
            f.write(item + "\n")

    # ========================================================
    # SAVE NOT FOUND
    # ========================================================

    with open(
        OUTPUT_NOT_FOUND,
        "w",
        encoding="utf-8"
    ) as f:

        for item in not_found:
            f.write(item + "\n")

    # ========================================================
    # SUMMARY
    # ========================================================

    print()
    print(f"{CYAN}{BOLD}")
    print("══════════════════════════════════════════════")
    print("                 SELESAI")
    print("══════════════════════════════════════════════")
    print(f"{RESET}")

    print(f"{GREEN}FOUND      : {len(found)}{RESET}")
    print(f"{RED}NOT FOUND  : {len(not_found)}{RESET}")

    print()
    print(f"{WHITE}Hasil ditemukan : {OUTPUT_FOUND}{RESET}")
    print(f"{WHITE}Hasil lainnya   : {OUTPUT_NOT_FOUND}{RESET}")


# ============================================================
# RUN
# ============================================================

def main():

    banner()

    input_file = select_file()

    if input_file is None:
        print(f"{YELLOW}[!] Keluar.{RESET}")
        return

    print(
        f"\n{GREEN}[✓] Input: "
        f"{input_file.name}{RESET}"
    )

    scan(input_file)

    print()
    input(
        f"{YELLOW}Tekan ENTER untuk keluar...{RESET}"
    )


if __name__ == "__main__":

    try:
        main()

    except KeyboardInterrupt:
        print(
            f"\n\n{YELLOW}[!] Dihentikan.{RESET}"
        )

    except Exception as error:
        print(
            f"\n{RED}[!] Error: {error}{RESET}"
        )
