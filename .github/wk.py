from pathlib import Path
import requests
import os
import sys
import time


# ============================================================
# CONFIG
# ============================================================

TARGET_PATH = "/wk/index.php"

FOUND_FILE = "found.txt"
NOT_FOUND_FILE = "not_found.txt"

TIMEOUT = 10

HEADERS = {
    "User-Agent": "Mozilla/5.0"
}


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
# SCREEN
# ============================================================

def clear_screen():
    os.system("cls" if os.name == "nt" else "clear")


# ============================================================
# BANNER
# ============================================================

def banner():

    clear_screen()

    print(f"{CYAN}{BOLD}")
    print("╔══════════════════════════════════════════════╗")
    print("║             WK URL CROSSCHECK                ║")
    print("╠══════════════════════════════════════════════╣")
    print("║ Input  : TXT bebas                           ║")
    print("║ Target : /wk/index.php                       ║")
    print("║ Output : found.txt / not_found.txt           ║")
    print("╚══════════════════════════════════════════════╝")
    print(f"{RESET}")


# ============================================================
# FIND TXT FILE
# ============================================================

def find_txt_files():

    excluded = {
        FOUND_FILE.lower(),
        NOT_FOUND_FILE.lower()
    }

    return sorted(
        [
            file
            for file in Path(".").glob("*.txt")
            if file.name.lower() not in excluded
        ],
        key=lambda x: x.name.lower()
    )


# ============================================================
# SELECT TXT
# ============================================================

def select_input():

    files = find_txt_files()

    if not files:

        print(
            f"{RED}[!] Tidak ada file TXT ditemukan.{RESET}"
        )

        return None

    print(
        f"{CYAN}{BOLD}"
        "File TXT tersedia:"
        f"{RESET}\n"
    )

    for number, file in enumerate(files, 1):

        size = file.stat().st_size

        print(
            f"{GREEN}[{number}]{RESET} "
            f"{file.name} "
            f"{WHITE}({size} bytes){RESET}"
        )

    print(
        f"\n{YELLOW}[0] Keluar{RESET}\n"
    )

    while True:

        choice = input(
            f"{MAGENTA}Pilih file: {RESET}"
        ).strip()

        if choice == "0":
            return None

        if choice.isdigit():

            number = int(choice)

            if 1 <= number <= len(files):

                selected = files[number - 1]

                print(
                    f"\n{GREEN}[✓] Input: "
                    f"{selected.name}{RESET}\n"
                )

                return selected

        print(
            f"{RED}[!] Pilihan tidak valid.{RESET}"
        )


# ============================================================
# LOAD DOMAINS
# ============================================================

def load_domains(file):

    domains = []
    seen = set()

    try:

        with open(
            file,
            "r",
            encoding="utf-8",
            errors="ignore"
        ) as f:

            for line in f:

                value = line.strip()

                if not value:
                    continue

                # Lewati komentar
                if value.startswith("#"):
                    continue

                # Tambahkan scheme jika belum ada
                if not value.startswith(
                    ("http://", "https://")
                ):
                    value = "https://" + value

                # Bersihkan slash akhir
                value = value.rstrip("/")

                if value not in seen:

                    seen.add(value)
                    domains.append(value)

    except Exception as error:

        print(
            f"{RED}[!] Gagal membaca file: "
            f"{error}{RESET}"
        )

    return domains


# ============================================================
# CHECK URL
# ============================================================

def check_url(domain):

    url = domain + TARGET_PATH

    try:

        response = requests.get(
            url,
            headers=HEADERS,
            timeout=TIMEOUT,
            allow_redirects=True
        )

        return {
            "url": url,
            "status": response.status_code,
            "final_url": response.url,
            "error": None
        }

    except requests.exceptions.Timeout:

        return {
            "url": url,
            "status": None,
            "final_url": None,
            "error": "TIMEOUT"
        }

    except requests.exceptions.SSLError:

        return {
            "url": url,
            "status": None,
            "final_url": None,
            "error": "SSL ERROR"
        }

    except requests.exceptions.ConnectionError:

        return {
            "url": url,
            "status": None,
            "final_url": None,
            "error": "CONNECTION ERROR"
        }

    except requests.exceptions.RequestException as error:

        return {
            "url": url,
            "status": None,
            "final_url": None,
            "error": str(error)
        }


# ============================================================
# DISPLAY
# ============================================================

def display_result(number, result):

    url = result["url"]
    status = result["status"]
    error = result["error"]

    # 2xx = berhasil diakses
    if status is not None and 200 <= status < 300:

        print(
            f"{GREEN}[{number:04}] "
            f"FOUND [{status}] "
            f"{url}{RESET}"
        )

        return "found"

    # 3xx = redirect
    if status is not None and 300 <= status < 400:

        print(
            f"{YELLOW}[{number:04}] "
            f"REDIRECT [{status}] "
            f"{url}{RESET}"
        )

        return "redirect"

    # 403 bukan bukti file tidak ada
    if status == 403:

        print(
            f"{YELLOW}[{number:04}] "
            f"FORBIDDEN [403] "
            f"{url}{RESET}"
        )

        return "not_found"

    # 404
    if status == 404:

        print(
            f"{RED}[{number:04}] "
            f"NOT FOUND [404] "
            f"{url}{RESET}"
        )

        return "not_found"

    # Error koneksi
    if error:

        print(
            f"{RED}[{number:04}] "
            f"{error} "
            f"{url}{RESET}"
        )

        return "not_found"

    # Status HTTP lainnya
    print(
        f"{YELLOW}[{number:04}] "
        f"HTTP [{status}] "
        f"{url}{RESET}"
    )

    return "not_found"


# ============================================================
# SAVE
# ============================================================

def save_file(filename, data):

    with open(
        filename,
        "w",
        encoding="utf-8"
    ) as f:

        for line in data:
            f.write(line + "\n")


# ============================================================
# SCAN
# ============================================================

def scan(domains):

    found = []
    not_found = []

    total = len(domains)

    print(
        f"{CYAN}[*] Total target: "
        f"{total}{RESET}\n"
    )

    for number, domain in enumerate(
        domains,
        start=1
    ):

        result = check_url(domain)

        category = display_result(
            number,
            result
        )

        url = result["url"]
        status = result["status"]
        error = result["error"]

        # ----------------------------------------------
        # FOUND
        # ----------------------------------------------

        if category == "found":

            found.append(
                f"[{status}] {url}"
            )

        # ----------------------------------------------
        # REDIRECT
        # ----------------------------------------------

        elif category == "redirect":

            final_url = result["final_url"]

            found.append(
                f"[{status}] {url} -> {final_url}"
            )

        # ----------------------------------------------
        # NOT FOUND / ERROR
        # ----------------------------------------------

        else:

            if status is not None:

                not_found.append(
                    f"[{status}] {url}"
                )

            else:

                not_found.append(
                    f"[{error}] {url}"
                )

        time.sleep(0.05)

    # Simpan
    save_file(
        FOUND_FILE,
        found
    )

    save_file(
        NOT_FOUND_FILE,
        not_found
    )

    # ========================================================
    # SUMMARY
    # ========================================================

    print()

    print(f"{CYAN}{BOLD}")
    print("══════════════════════════════════════════════")
    print("                    SELESAI")
    print("══════════════════════════════════════════════")
    print(f"{RESET}")

    print(
        f"{GREEN}FOUND       : "
        f"{len(found)}{RESET}"
    )

    print(
        f"{RED}OTHER/ERROR : "
        f"{len(not_found)}{RESET}"
    )

    print()

    print(
        f"{WHITE}Found file     : "
        f"{FOUND_FILE}{RESET}"
    )

    print(
        f"{WHITE}Other results  : "
        f"{NOT_FOUND_FILE}{RESET}"
    )


# ============================================================
# MAIN
# ============================================================

def main():

    banner()

    input_file = select_input()

    if input_file is None:

        print(
            f"{YELLOW}[!] Program dihentikan.{RESET}"
        )

        return

    domains = load_domains(input_file)

    if not domains:

        print(
            f"{RED}[!] Tidak ada domain yang valid "
            f"di dalam file.{RESET}"
        )

        return

    scan(domains)

    print()

    input(
        f"{YELLOW}Tekan ENTER untuk keluar...{RESET}"
    )


# ============================================================
# RUN
# ============================================================

if __name__ == "__main__":

    try:

        main()

    except KeyboardInterrupt:

        print(
            f"\n\n{YELLOW}"
            "[!] Scan dihentikan."
            f"{RESET}"
        )

    except Exception as error:

        print(
            f"\n{RED}"
            f"[!] Error: {error}"
            f"{RESET}"
        )
