from pathlib import Path
import os
import time


# ============================================================
# CONFIG
# ============================================================

TARGET_PATH = "/wk/index.php"
OUTPUT_FILE = "hasil.txt"


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
# CLEAR SCREEN
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
    print("║              URL FIND BY AZKA                ║")
    print("╠══════════════════════════════════════════════╣")
    print("║  Input  : File TXT bebas                     ║")
    print("║  Target : /wk/index.php                      ║")
    print("║  Output : hasil.txt                          ║")
    print("╚══════════════════════════════════════════════╝")
    print(f"{RESET}")


# ============================================================
# FIND TXT FILES
# ============================================================

def find_txt_files():
    current_folder = Path(".")

    files = [
        file for file in current_folder.glob("*.txt")
        if file.name.lower() != OUTPUT_FILE.lower()
    ]

    return sorted(files, key=lambda x: x.name.lower())


# ============================================================
# SELECT INPUT FILE
# ============================================================

def select_input_file():
    txt_files = find_txt_files()

    if not txt_files:
        print(f"{RED}[!] Tidak ada file .txt ditemukan.{RESET}")
        return None

    print(f"{CYAN}{BOLD}File TXT yang tersedia:{RESET}\n")

    for number, file in enumerate(txt_files, start=1):
        size = file.stat().st_size

        print(
            f"{GREEN}[{number}]{RESET} "
            f"{file.name} "
            f"{WHITE}({size} bytes){RESET}"
        )

    print()
    print(f"{YELLOW}Ketik nomor file yang ingin digunakan.{RESET}")
    print(f"{YELLOW}Ketik 0 untuk keluar.{RESET}")
    print()

    while True:
        choice = input(f"{MAGENTA}Pilih file: {RESET}").strip()

        if not choice.isdigit():
            print(f"{RED}[!] Masukkan nomor yang valid.{RESET}")
            continue

        choice = int(choice)

        if choice == 0:
            return None

        if 1 <= choice <= len(txt_files):
            selected = txt_files[choice - 1]

            print(
                f"\n{GREEN}[✓] File dipilih: "
                f"{selected.name}{RESET}\n"
            )

            return selected

        print(f"{RED}[!] Nomor tidak tersedia.{RESET}")


# ============================================================
# LOAD URL FROM TXT
# ============================================================

def load_urls(input_file):
    urls = []

    try:
        with open(input_file, "r", encoding="utf-8", errors="ignore") as file:
            for line in file:
                url = line.strip()

                if not url:
                    continue

                # Hilangkan slash di akhir
                url = url.rstrip("/")

                # Hindari duplikat
                if url not in urls:
                    urls.append(url)

    except Exception as error:
        print(f"{RED}[!] Gagal membaca file: {error}{RESET}")
        return []

    return urls


# ============================================================
# BUILD URL
# ============================================================

def build_urls(urls):
    results = []

    print(f"{CYAN}{BOLD}")
    print("══════════════════════════════════════════════")
    print("              MEMPROSES URL")
    print("══════════════════════════════════════════════")
    print(f"{RESET}")

    for number, base_url in enumerate(urls, start=1):

        final_url = base_url + TARGET_PATH

        results.append(final_url)

        print(
            f"{GREEN}[{number:04}] ✓{RESET} "
            f"{final_url}"
        )

        time.sleep(0.01)

    return results


# ============================================================
# SAVE RESULT
# ============================================================

def save_results(results):
    try:
        with open(OUTPUT_FILE, "w", encoding="utf-8") as file:
            for url in results:
                file.write(url + "\n")

        return True

    except Exception as error:
        print(f"{RED}[!] Gagal menyimpan hasil: {error}{RESET}")
        return False


# ============================================================
# SUMMARY
# ============================================================

def show_summary(input_file, total_input, total_output):
    print()
    print(f"{CYAN}{BOLD}")
    print("╔══════════════════════════════════════════════╗")
    print("║                 SELESAI                      ║")
    print("╠══════════════════════════════════════════════╣")
    print(f"║ Input  : {input_file.name:<33} ║")
    print(f"║ Target : {TARGET_PATH:<33} ║")
    print(f"║ Jumlah : {total_input:<33} ║")
    print(f"║ Hasil  : {total_output:<33} ║")
    print(f"║ File   : {OUTPUT_FILE:<33} ║")
    print("╚══════════════════════════════════════════════╝")
    print(f"{RESET}")


# ============================================================
# MAIN
# ============================================================

def main():
    banner()

    input_file = select_input_file()

    if input_file is None:
        print(f"{YELLOW}[!] Program dihentikan.{RESET}")
        return

    urls = load_urls(input_file)

    if not urls:
        print(f"{RED}[!] Tidak ada URL di dalam file tersebut.{RESET}")
        input(f"\n{YELLOW}Tekan ENTER untuk keluar...{RESET}")
        return

    print(
        f"{WHITE}[+] URL ditemukan : "
        f"{len(urls)}{RESET}"
    )

    print()

    results = build_urls(urls)

    if save_results(results):
        show_summary(
            input_file,
            len(urls),
            len(results)
        )

    input(f"{YELLOW}Tekan ENTER untuk keluar...{RESET}")


# ============================================================
# RUN
# ============================================================

if __name__ == "__main__":
    try:
        main()

    except KeyboardInterrupt:
        print(f"\n\n{YELLOW}[!] Program dihentikan.{RESET}")

    except Exception as error:
        print(f"\n{RED}[!] Error: {error}{RESET}")
