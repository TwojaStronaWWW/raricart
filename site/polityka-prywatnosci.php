<?php
// polityka-prywatnosci.php
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Load dynamic content for consistency
$content_file = __DIR__ . '/assets/data/content.json';
$content = [];
if (file_exists($content_file)) {
    $content = json_decode(file_get_contents($content_file), true);
}

function get_val($key, $default) {
    global $content;
    return $content[$key] ?? $default;
}

include 'parts/head.php';
?>

<main class="content" style="padding-top: 15vh;">
    <section class="section">
        <div class="container" style="max-width: 800px; margin: 0 auto; line-height: 1.6;">
            <h1>Polityka Prywatności</h1>
            <p>Data aktualizacji: <?php echo date('d.m.Y'); ?></p>

            <h3>1. Informacje ogólne</h3>
            <p>Niniejsza Polityka Prywatności określa zasady przetwarzania i ochrony danych osobowych przekazywanych przez Użytkowników w związku z korzystaniem z usług Raricart za pośrednictwem serwisu raricart.pl.</p>

            <h3>2. Administrator Danych</h3>
            <p>Administratorem danych osobowych zawartych w serwisie jest Raricart Live Food Station.</p>

            <h3>3. Gromadzone dane</h3>
            <p>Przetwarzamy dane osobowe, które dobrowolnie podajesz w formularzu kontaktowym:
                <ul>
                    <li>Imię i nazwisko</li>
                    <li>Adres e-mail</li>
                    <li>Numer telefonu</li>
                    <li>Informacje o planowanym evencie</li>
                </ul>
            </p>

            <h3>4. Cel przetwarzania danych</h3>
            <p>Dane osobowe są przetwarzane w celu:
                <ul>
                    <li>Przygotowania i przedstawienia oferty handlowej</li>
                    <li>Realizacji kontaktu telefonicznego lub mailowego</li>
                    <li>Realizacji usług gastronomicznych</li>
                </ul>
            </p>

            <h3>5. Pliki Cookies</h3>
            <p>Serwis używa plików cookies w celu zapewnienia poprawnego działania strony oraz analizy ruchu. Użytkownik może w każdej chwili zmienić ustawienia plików cookies w swojej przeglądarce.</p>

            <h3>6. Prawa Użytkownika</h3>
            <p>Każdy Użytkownik ma prawo do:
                <ul>
                    <li>Dostępu do swoich danych</li>
                    <li>Sprostowania danych</li>
                    <li>Usunięcia danych ("prawo do bycia zapomnianym")</li>
                    <li>Ograniczenia przetwarzania</li>
                </ul>
            </p>

            <div style="margin-top: 3rem;">
                <a href="index.php" class="cta-secondary">Wróć do strony głównej</a>
            </div>
        </div>
    </section>
</main>

<?php include 'parts/footer.php'; ?>
