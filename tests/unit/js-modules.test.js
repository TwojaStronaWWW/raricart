import { describe, it, beforeEach } from 'node:test';
import assert from 'node:assert/strict';
import { MockDocument, MockElement, MockWindow, MockLocalStorage } from '../helpers/mock-dom.js';
import { toggleBodyScroll } from '../../assets/js/modules/utils.js';
import { initI18n, getCurrentLang, getTranslation, updateLanguage, onLanguageChange } from '../../assets/js/modules/i18n.js';
import { initCookies } from '../../assets/js/modules/cookies.js';
import { initStationModals } from '../../assets/js/modules/modals.js';
import { translations } from '../../assets/js/modules/translations.js';

describe('Moduły Vanilla JavaScript (ES6+)', () => {

  describe('Moduł utils.js (toggleBodyScroll)', () => {
    let mockDoc;

    beforeEach(() => {
      mockDoc = new MockDocument();
      globalThis.document = mockDoc;
    });

    it('toggleBodyScroll(true) powinien dodać klasę lightbox-open do body', () => {
      toggleBodyScroll(true);
      assert.strictEqual(mockDoc.body.classList.contains('lightbox-open'), true);
    });

    it('toggleBodyScroll(false) powinien usunąć klasę lightbox-open i ustawić overflow auto', () => {
      toggleBodyScroll(true);
      assert.strictEqual(mockDoc.body.classList.contains('lightbox-open'), true);

      toggleBodyScroll(false);
      assert.strictEqual(mockDoc.body.classList.contains('lightbox-open'), false);
      assert.strictEqual(mockDoc.body.style.overflow, 'auto');
    });
  });

  describe('Moduł i18n.js (Wielojęzyczność)', () => {
    let mockDoc;

    beforeEach(() => {
      mockDoc = new MockDocument();
      globalThis.document = mockDoc;
      initI18n(translations);
    });

    it('domyślny język powinien być ustawiony na "pl"', () => {
      assert.strictEqual(getCurrentLang(), 'pl');
    });

    it('getTranslation powinien zwracać poprawny tekst dla zagnieżdżonego klucza', () => {
      const textPl = getTranslation('nav.about');
      assert.strictEqual(textPl, 'O Nas');
    });

    it('getTranslation powinien zwracać fallback w przypadku braku klucza', () => {
      const fallback = getTranslation('brak.takiego.klucza', 'Wartość domyślna');
      assert.strictEqual(fallback, 'Wartość domyślna');
    });

    it('updateLanguage powinien podmieniać zawartość [data-i18n] i wywoływać zarejestrowany callback', () => {
      const labelEl = new MockElement('label-about', 'span');
      labelEl.setAttribute('data-i18n', 'nav.about');
      labelEl.innerHTML = 'O Nas';
      mockDoc.registerElement(labelEl);

      let callbackLang = null;
      onLanguageChange((lang) => {
        callbackLang = lang;
      });

      updateLanguage('en');

      assert.strictEqual(getCurrentLang(), 'en');
      assert.strictEqual(labelEl.innerHTML, 'About Us');
      assert.strictEqual(callbackLang, 'en');

      // Powrót do PL dla stabilności innych testów
      updateLanguage('pl');
    });
  });

  describe('Moduł cookies.js (Zgoda RODO & Analityka)', () => {
    let mockDoc;
    let mockWin;
    let banner;
    let acceptBtn;
    let rejectBtn;

    beforeEach(() => {
      mockDoc = new MockDocument();
      mockWin = new MockWindow();
      globalThis.document = mockDoc;
      globalThis.window = mockWin;
      globalThis.localStorage = mockWin.localStorage;

      banner = new MockElement('cookie-banner', 'div');
      acceptBtn = new MockElement('accept-cookies', 'button');
      rejectBtn = new MockElement('reject-cookies', 'button');

      mockDoc.registerElement(banner);
      mockDoc.registerElement(acceptBtn);
      mockDoc.registerElement(rejectBtn);
    });

    it('brak wcześniejszej zgody powinien wyświetlić banner cookies', () => {
      initCookies();
      assert.strictEqual(banner.style.display, 'block');
    });

    it('kliknięcie Akceptuję powinno zapisać granted w localStorage i dołączyć skrypt Google Analytics', () => {
      initCookies();
      acceptBtn.click();

      assert.strictEqual(localStorage.getItem('raricart_cookies_consent'), 'granted');
      const gaScript = mockDoc.getElementById('ga-script');
      assert.ok(gaScript, 'Skrypt Google Analytics powinien zostać utworzony w DOM');
      assert.ok(gaScript.getAttribute('src')?.includes('googletagmanager.com'));
    });

    it('kliknięcie Odrzuć powinno zapisać denied w localStorage i NIE ładować analityki', () => {
      initCookies();
      rejectBtn.click();

      assert.strictEqual(localStorage.getItem('raricart_cookies_consent'), 'denied');
      const gaScript = mockDoc.getElementById('ga-script');
      assert.strictEqual(gaScript, null, 'Skrypt analityki nie powinien zostać załadowany');
    });

    it('wcześniej udzielona zgoda (granted) powinna załadować analitykę od razu bez pokazywania banera', () => {
      localStorage.setItem('raricart_cookies_consent', 'granted');
      initCookies();

      assert.notStrictEqual(banner.style.display, 'block');
      const gaScript = mockDoc.getElementById('ga-script');
      assert.ok(gaScript);
    });
  });

  describe('Moduł modals.js (Okna stacji kulinarnych)', () => {
    let mockDoc;
    let mockWin;
    let modal;
    let modalTitle;
    let modalText;
    let modalImg;
    let cardPancakes;

    beforeEach(() => {
      mockDoc = new MockDocument();
      mockWin = new MockWindow();
      globalThis.document = mockDoc;
      globalThis.window = mockWin;
      globalThis.history = mockWin.history;
      globalThis.location = mockWin.location;
      globalThis.Image = class {
        constructor() {
          this.src = '';
        }
      };

      modal = new MockElement('modal', 'div');
      modalTitle = new MockElement('modalTitle', 'h3');
      modalText = new MockElement('modalText', 'div');
      modalImg = new MockElement('modalImg', 'img');

      cardPancakes = new MockElement('card-pancakes', 'div', ['offer-card']);
      cardPancakes.setAttribute('data-offer', 'pancakes');

      mockDoc.registerElement(modal);
      mockDoc.registerElement(modalTitle);
      mockDoc.registerElement(modalText);
      mockDoc.registerElement(modalImg);
      mockDoc.registerElement(cardPancakes);
    });

    it('kliknięcie w kartę stacji powinno otworzyć modal, ustawić treść i zablokować scroll', () => {
      initStationModals(translations);

      cardPancakes.click();

      assert.strictEqual(modal.classList.contains('active'), true);
      assert.strictEqual(modalTitle.textContent, 'Mini Pancakes');
      assert.ok(modalText.innerHTML.length > 50);
      assert.strictEqual(mockDoc.body.classList.contains('lightbox-open'), true);
      assert.strictEqual(history.state?.modal, 'offer');
    });

    it('wciśnięcie klawisza Escape powinno zamknąć aktywny modal', () => {
      initStationModals(translations);

      // Otwórz
      cardPancakes.click();
      assert.strictEqual(modal.classList.contains('active'), true);

      // Symulacja Escape
      mockDoc.dispatchDocumentKeyDown({ key: 'Escape' });

      assert.strictEqual(modal.classList.contains('active'), false);
      assert.strictEqual(mockDoc.body.classList.contains('lightbox-open'), false);
    });
  });
});
