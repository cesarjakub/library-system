# Knihovna
![PHP](https://img.shields.io/badge/PHP-8.2-blue)
![Nette Framework](https://img.shields.io/badge/Nette-3.2-red)

## Název
Knihovna – systém pro správu knih a výpůjček s REST API a administrátorskými funkcemi.

## Popis
Tato aplikace umožňuje správu knih, uživatelů a výpůjček. Uživatelé se mohou registrovat, přihlašovat, prohlížet knihy a filtrovat je podle autora a roku vydání. Administrátoři mají možnost přidávat, upravovat a mazat knihy, nahrávat obálky, spravovat výpůjčky a přiřazovat role uživatelům.

**Funkce:**
- Registrace a přihlášení uživatelů
- Přehled knih s filtrováním a stránkováním
- Detail knihy s obálkou
- Administrátorské funkce (CRUD knih, nahrávání obálek)
- Správa výpůjček (vytvoření, označení jako vráceno)
- REST API pro knihy a výpůjčky, chráněné API klíčem

## Instalace

1. Naklonujte repozitář:
```bash
git clone https://github.com/cesarjakub/library-system.git
cd knihovna
```

2. Nainstalujte závislosti přes Composer:
```bash
composer install
```

3. Vytvořte .env soubor s konfigurací databáze a API klíčů:

```ini
API_KEY_KNIHY=vas_klic_pro_knihy
API_KEY_VYPUJCKY=vas_klic_pro_vypujcky
```

4. Spusťte lokální server:
```bash
Zkopírovat kód
php -S localhost:8000 -t www
```

5. Otevřete http://localhost:8000 v prohlížeči.

## Požadavky
- PHP 8.0+
- Composer
- MySQL

## Použití
- Přihlaste se jako uživatel nebo administrátor
- Prohlížejte knihy, filtrujte podle autora nebo roku
- Administrátor může přidávat knihy a obálky
- REST API dostupné na /api/knihy a /api/vypujcky


## Autor
Jakub Cesar – hlavní vývojář
