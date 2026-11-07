# Persistent Design Rules & Post-Mortem Corrections (`design-rules.md`)

Zgodnie z meta-zasadą Anshu Chimala (*„Every time you have to correct the AI, add that correction to a persistent `design-rules.md` file so it never makes the same mistake twice”*), ten plik przechowuje twarde lekcje z audytów wizualnych:

## [DR-01] Zakaz „Martwego Ziarna” (Entropy Theater)
- Ziarno kryptograficzne z `generate-seed.js` **nigdy** nie może być wklejane do promptu równocześnie z gotowym przepisem CSS (np. *„zrób tabelę 1px solid i kolumny 65/35”*).
- Zanim powstanie pierwsza linijka CSS, projektant musi zapisać **1 zdanie ślepego skojarzenia przestrzennego** wyprowadzonego z ziarna (metafora spoza świata szablonów WWW).

## [DR-02] Zakaz Samodzielnego Oceniania Zrzutów przez Buildera (`invoke_subagent = 0` = BŁĄD KRYTYCZNY)
- Agent piszący kod (*Builder*) **ma bezwzględny zakaz** samodzielnego oceniania własnych zrzutów ekranu.
- Zawsze wymagane jest wywołanie osobnego subagenta `design-critic` (`invoke_subagent`), któremu przekazuje się ścieżki do `desktop-top.png`, `desktop-mid.png`, `desktop-bottom.png`, `mobile-full.png` oraz (jeśli istnieje) `reference-moodboard.png`.
- Obowiązuje **Stopping Rule**: minimum 1 pełna runda poprawek po werdykcie `design-critic`, maksimum 3 rundy (ochrona przed przepalaniem tokenów).

## [DR-03] Prawdziwy Viewport `100vh = 900px` i Kontrola Dolnej Połowy Strony
- Nigdy nie używaj `--window-size=1440,5800` z wiersza poleceń przeglądarki, ponieważ ustawia to `100vh = 5800px` i ucina strony poniżej `5800px`.
- Zawsze używaj `capture-screenshots.js` opartego o CDP (`Emulation.setDeviceMetricsOverride` z `1440x900` na Desktopie i `390x844` na Mobile), który przewija stronę do samego końca i zapisuje osobno `desktop-bottom.png` (Opinie, O nas, FAQ, Formularz, Stopka).

## [DR-04] Zakaz „2026 Editorial Hairline Slop” (Pokratkowanych Pustych Tabel `1px solid`)
- Usunięcie kart z cieniami (`box-shadow`) **nie oznacza** zamknięcia każdego elementu w klatce `1px solid`!
- **Zakaz pustych komórek siatki:** Jeśli w dwukolumnowym układzie (np. nagłówek po lewej, 5 kroków po prawej) lewa kolumna kończy się po `200px`, a prawa ciągnie się przez `900px`, lewa kolumna musi być `position: sticky; top: 110px;` albo układ musi przejść w pełną szerokość / oś poziomą — nigdy nie zostawiaj pustej klatki tabeli o wysokości `700px`!

## [DR-05] Ochrona Kadrowania i Kontrastu Zdjęć Produktowych / Kulinarnych
- **Zakaz miniaturyzacji zdjęć („efekt znaczka pocztowego”):** Główne zdjęcia oferty i Hero nigdy nie mogą być mniejsze niż `38%` szerokości kontenera na desktopie.
- **Zakaz ślepego rozciągania panoram (`21:9` / `100%` szerokości) na zdjęciach pionowych/kwadratowych:** Prowadzi to do ucięcia przedmiotu zdjęcia i gigantycznego powiększenia fragmentu napisu (np. rozciągnięty napis `RARICART` na dole wózka zamiast widoku jedzenia).
- **Zakaz „mlecznej mgły” na zdjęciach jedzenia:** Nigdy nie nakładaj jasnego, wyblakłego overlayu (`rgba(245, 241, 235, 0.78)`) bezpośrednio na zdjęcia kulinarne pod ciemny tekst — jedzenie staje się sine i nieapetyczne, a tekst traci kontrast.

## [DR-06] Reguła Coco Chanel („1 Sekcja = Maksymalnie 1 Zabieg Dekoracyjny”)
- W jednej sekcji nie wolno jednocześnie łączyć: numeracji `01 / 02`, ikonek SVG w ramkach, nad-etykiet (*badges*), linii podziału i list z ptaszkami `✓`.
- W sekcji „Proces” (`01–05`) zostaw wyłącznie dużą, architektoniczną cyfrę `01`–`05` i czysty tekst — ukryj redundantne ikonki SVG obok numerów.
- W sekcji „Dla kogo” zastąp zielone ptaszki `✓` czystymi liniami katalogowymi lub subtelnym myślnikiem / punktem typograficznym w kolorze akcentu marki.
- W sekcji „Opinie” zachowaj autentyczną treść recenzji, ale stonuj krzykliwe badge'e i nadmiar ramek wokół każdej karty.
- W interaktywnych scenach Hero (np. `twojastronawww`) obowiązuje zasada **1 głównego punktu skupienia (Focal Point)**: nie nakładaj jednocześnie siatki HUD, telemetrii w 4 rogach, wykresów i suwaków na tę samą warstwę.
