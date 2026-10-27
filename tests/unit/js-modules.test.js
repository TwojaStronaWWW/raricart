import { describe, it, beforeEach } from 'node:test';
import assert from 'node:assert/strict';
import { MockDocument, MockElement, MockWindow, MockLocalStorage } from '../helpers/mock-dom.js';
import { toggleBodyScroll } from '../../assets/js/modules/utils.js';
import { initI18n, getCurrentLang, getTranslation, updateLanguage, onLanguageChange } from '../../assets/js/modules/i18n.js';
import { initCookies } from '../../assets/js/modules/cookies.js';
import { initStationModals } from '../../assets/js/modules/modals.js';
import { initContactForm } from '../../assets/js/modules/contact.js';
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

  describe('Moduł contact.js (Formularz kontaktowy, walidacja, autosave i ochrona przed wyścigiem)', () => {
    let mockDoc;
    let mockWin;
    let fetchCalls;
    let form;
    let progressBar;
    let progressText;
    let nameInput;
    let emailInput;
    let phoneInput;
    let dateInput;
    let locationInput;
    let guestsInput;
    let eventTypeSelect;
    let cbGroup;
    let cbPancakes;
    let cbCheese;
    let messageInput;
    let submitBtn;
    let submitSpan;
    let submitSvg;

    function createField(id, tag, type, name, required = false) {
      const group = new MockElement(`group-${id}`, 'div', ['form-group']);
      const el = new MockElement(id, tag);
      el.type = type;
      el.name = name;
      if (required) el.setAttribute('required', 'required');
      el.parentNode = group;
      group.appendChild(el);
      group.querySelector = (sel) => {
        if (sel === '.error-message') return group.children.find(c => c.classList?.contains('error-message')) || null;
        if (sel === '.phone-error') return group.children.find(c => c.classList?.contains('phone-error')) || null;
        return null;
      };
      el.remove = () => {
        if (el.parentNode) {
          el.parentNode.children = el.parentNode.children.filter(c => c !== el);
        }
      };
      el.scrollIntoView = () => {};
      el.dispatch = (evtName, extra = {}) => {
        const handlers = el.listeners.get(evtName) || [];
        const evt = { type: evtName, target: el, preventDefault: () => {}, ...extra };
        handlers.forEach(fn => fn.call(el, evt));
      };
      mockDoc.registerElement(group);
      mockDoc.registerElement(el);
      return el;
    }

    beforeEach(() => {
      mockDoc = new MockDocument();
      mockWin = new MockWindow();
      mockDoc.visibilityState = 'visible';
      globalThis.document = mockDoc;
      globalThis.window = mockWin;
      initI18n(translations);

      const origCreateElement = mockDoc.createElement.bind(mockDoc);
      mockDoc.createElement = (tag) => {
        const el = origCreateElement(tag);
        el.remove = () => {
          if (el.parentNode) {
            el.parentNode.children = el.parentNode.children.filter(c => c !== el);
          }
          mockDoc.elements = mockDoc.elements.filter(c => c !== el);
        };
        el.querySelector = (sel) => {
          if (sel === '.toast-close') {
            const btn = origCreateElement('button');
            btn.classList.add('toast-close');
            return btn;
          }
          return null;
        };
        return el;
      };

      fetchCalls = [];
      globalThis.fetch = async (url, options = {}) => {
        const parsedBody = options.body ? JSON.parse(options.body) : {};
        fetchCalls.push({ url, options, body: parsedBody });
        return {
          ok: true,
          status: 200,
          json: async () => ({ status: 'success', message: 'Wiadomość wysłana!' }),
        };
      };

      form = new MockElement('form', 'form', ['contact-form']);
      progressBar = new MockElement('form-progress', 'div');
      progressText = new MockElement('progress-text', 'p');
      mockDoc.registerElement(form);
      mockDoc.registerElement(progressBar);
      mockDoc.registerElement(progressText);

      nameInput = createField('name', 'input', 'text', 'name', true);
      emailInput = createField('email', 'input', 'email', 'email', true);
      phoneInput = createField('phone', 'input', 'tel', 'phone', false);
      dateInput = createField('date', 'input', 'date', 'date', true);
      locationInput = createField('location', 'input', 'text', 'location', true);
      guestsInput = createField('guests', 'input', 'number', 'guests', true);
      eventTypeSelect = createField('event_type', 'select', 'select-one', 'event_type', true);
      messageInput = createField('message', 'textarea', 'textarea', 'message', false);

      const stationsWrapper = new MockElement('stations-wrapper', 'div', ['form-group']);
      cbGroup = new MockElement('cb-group', 'div', ['checkbox-group']);
      cbGroup.parentNode = stationsWrapper;
      stationsWrapper.appendChild(cbGroup);
      stationsWrapper.querySelector = (sel) => {
        if (sel === '.error-message') {
          return stationsWrapper.children.find(c => c.classList?.contains('error-message')) || null;
        }
        return null;
      };
      mockDoc.registerElement(stationsWrapper);
      mockDoc.registerElement(cbGroup);

      cbPancakes = new MockElement('p', 'input');
      cbPancakes.type = 'checkbox';
      cbPancakes.name = 'stations';
      cbPancakes.value = 'pancakes';
      cbPancakes.checked = false;
      cbPancakes.nextElementSibling = { textContent: 'Mini Pancakes' };
      cbPancakes.dispatch = (evtName) => {
        (cbPancakes.listeners.get(evtName) || []).forEach(fn => fn.call(cbPancakes, { target: cbPancakes }));
      };

      cbCheese = new MockElement('s', 'input');
      cbCheese.type = 'checkbox';
      cbCheese.name = 'stations';
      cbCheese.value = 'sery';
      cbCheese.checked = false;
      cbCheese.nextElementSibling = { textContent: 'Deska Serów' };
      cbCheese.dispatch = (evtName) => {
        (cbCheese.listeners.get(evtName) || []).forEach(fn => fn.call(cbCheese, { target: cbCheese }));
      };

      mockDoc.registerElement(cbPancakes);
      mockDoc.registerElement(cbCheese);

      submitBtn = new MockElement('submit-btn', 'button', ['cta-primary', 'form-submit-btn']);
      submitSpan = new MockElement('submit-span', 'span');
      submitSpan.textContent = 'SPRAWDŹ DOSTĘPNOŚĆ I OTRZYMAJ WYCENĘ';
      submitSvg = new MockElement('submit-svg', 'svg');
      submitBtn.appendChild(submitSpan);
      submitBtn.appendChild(submitSvg);
      submitBtn.querySelector = (sel) => (sel === 'span' ? submitSpan : null);
      submitBtn.dispatch = (evtName) => {
        (submitBtn.listeners.get(evtName) || []).forEach(fn => fn.call(submitBtn, { target: submitBtn }));
      };
      mockDoc.registerElement(submitBtn);

      const allFields = [nameInput, emailInput, phoneInput, dateInput, locationInput, guestsInput, eventTypeSelect, cbPancakes, cbCheese, messageInput];
      form.querySelectorAll = (sel) => {
        if (sel === 'input, select, textarea') return allFields;
        if (sel === 'input[name="stations"]') return [cbPancakes, cbCheese];
        if (sel === 'input[name="stations"]:checked') return [cbPancakes, cbCheese].filter(c => c.checked);
        if (sel === '.input-error') return mockDoc.elements.filter(el => el.classList?.contains('input-error'));
        if (sel === '.error-message') return mockDoc.elements.filter(el => el.classList?.contains('error-message'));
        return [];
      };
      form.querySelector = (sel) => {
        if (sel === '.cta-primary') return submitBtn;
        if (sel === 'input[name="phone"]') return phoneInput;
        if (sel === '.checkbox-group') return cbGroup;
        if (sel === '.input-error, .error-message') {
          return mockDoc.elements.find(el => el.classList?.contains('input-error') || el.classList?.contains('error-message')) || null;
        }
        return null;
      };
      form.reset = () => {
        allFields.forEach(f => {
          if (f.type === 'checkbox') f.checked = false;
          else f.value = '';
        });
      };
      form.submitForm = () => {
        (form.listeners.get('submit') || []).forEach(fn => fn.call(form, { preventDefault: () => {} }));
      };
    });

    it('próba wysłania pustego formularza powinna oznaczyć pola wymagane i grupę stacji klasą .input-error bez wywołania fetch', () => {
      initContactForm();
      form.submitForm();

      assert.strictEqual(fetchCalls.length, 0, 'Nie powinno być wywołania sieciowego przy błędnej walidacji');
      assert.strictEqual(nameInput.classList.contains('input-error'), true);
      assert.strictEqual(emailInput.classList.contains('input-error'), true);
      assert.strictEqual(cbGroup.classList.contains('input-error'), true);
    });

    it('krótka wiadomość (1-2 znaki) w opcjonalnym polu message NIE powinna blokować wysyłki formularza', async () => {
      initContactForm();

      nameInput.value = 'Gabriela Momot';
      emailInput.value = 'g.momot@eveline.com.pl';
      phoneInput.value = '+48575754222';
      dateInput.value = '2026-12-04';
      locationInput.value = 'Łódź';
      guestsInput.value = '140';
      eventTypeSelect.value = 'corporate';
      cbPancakes.checked = true;
      cbCheese.checked = true;
      messageInput.value = '-';

      form.submitForm();
      await new Promise(r => setTimeout(r, 20));

      assert.strictEqual(fetchCalls.length, 1);
      assert.strictEqual(fetchCalls[0].body.name, 'Gabriela Momot');
      assert.strictEqual(fetchCalls[0].body.stations, 'Mini Pancakes, Deska Serów');
      assert.strictEqual(fetchCalls[0].body.message, '-');
      assert.strictEqual(fetchCalls[0].body.is_partial, undefined);
      assert.ok(submitBtn.children.includes(submitSvg), 'Ikona SVG w przycisku submit musi pozostać nienaruszona');
    });

    it('kliknięcie przycisku submit po edycji ostatniego pola anuluje oczekujący autosave (brak wyścigu blur vs submit) i blokuje porzucony szkic po sukcesie', async () => {
      initContactForm();

      nameInput.value = 'Aleksandra Matusik';
      emailInput.value = 'ola.matusik@gmail.com';
      phoneInput.value = '+48512610613';
      dateInput.value = '2028-09-02';
      locationInput.value = 'Mysłowice';
      guestsInput.value = '150';
      eventTypeSelect.value = 'wedding';
      cbPancakes.checked = true;
      messageInput.value = 'Wesele w plenerze';

      // Symulacja sekwencji przeglądarki: blur na message -> pointerdown na submitBtn -> submit formularza
      messageInput.dispatch('blur');
      submitBtn.dispatch('pointerdown');
      form.submitForm();

      await new Promise(r => setTimeout(r, 350));

      // Powinien wyjść wyłącznie 1 request (finalny submit), a timer szkicu z blur został anulowany
      assert.strictEqual(fetchCalls.length, 1);
      assert.strictEqual(fetchCalls[0].body.is_partial, undefined);

      // Symulacja zamknięcia karty po wysłaniu (visibilitychange -> hidden)
      mockDoc.visibilityState = 'hidden';
      (mockDoc.docListeners.get('visibilitychange') || []).forEach(fn => fn());

      assert.strictEqual(fetchCalls.length, 1, 'Po wysłaniu formularza visibilitychange nie może wysyłać porzuconego szkicu');
    });

    it('niepełny numer telefonu (< 9 cyfr) powinien blokować wysyłkę, a poprawienie pola powinno natychmiast czyścić błąd', () => {
      initContactForm();

      nameInput.value = 'Jan Testowy';
      emailInput.value = 'jan@test.pl';
      phoneInput.value = '12345';
      dateInput.value = '2026-11-10';
      locationInput.value = 'Katowice';
      guestsInput.value = '80';
      eventTypeSelect.value = 'private';
      cbPancakes.checked = true;

      form.submitForm();
      assert.strictEqual(fetchCalls.length, 0);
      assert.strictEqual(phoneInput.classList.contains('input-error'), true);

      // Użytkownik dopisuje cyfry w polu telefonu
      phoneInput.value = '500600700';
      phoneInput.dispatch('input', { target: phoneInput });
      assert.strictEqual(phoneInput.classList.contains('input-error'), false);
    });

    it('autosave szkicu (blur/change), deduplikacja sygnatury, sendAbandonedLead, walidacja email/phone blur, obsługa błędu sieci i preCtaCheckBtn powinny działać niezawodnie', async () => {
      const contactSec = new MockElement('kontakt', 'section');
      contactSec.scrollIntoView = () => { contactSec.scrolled = true; };
      const preCtaBtn = new MockElement('preCtaCheckBtn', 'a');
      mockDoc.registerElement(contactSec);
      mockDoc.registerElement(preCtaBtn);

      initContactForm();

      // 1. Trigger window load ping
      mockWin.requestIdleCallback = (cb) => cb();
      (mockWin.listeners.get('load') || []).forEach(fn => fn());
      await new Promise(r => setTimeout(r, 10));
      assert.strictEqual(fetchCalls.length, 1);
      assert.strictEqual(fetchCalls[0].body.action, 'ping');

      // 2. Walidacja telefonu na blur (< 9 cyfr i >= 9 cyfr)
      phoneInput.value = '1234';
      phoneInput.dispatch('blur');
      assert.strictEqual(phoneInput.classList.contains('input-error'), true);
      phoneInput.value = '+48 500 600 700';
      phoneInput.dispatch('blur');
      assert.strictEqual(phoneInput.classList.contains('input-error'), false);

      // 3. Walidacja niepoprawnego emaila oraz czyszczenie błędu stacji po zaznaczeniu checkboxa
      emailInput.value = 'niepoprawny-email';
      form.submitForm();
      assert.strictEqual(emailInput.classList.contains('input-error'), true);
      assert.strictEqual(cbGroup.classList.contains('input-error'), true);

      cbPancakes.checked = true;
      cbPancakes.dispatch('change');
      assert.strictEqual(cbGroup.classList.contains('input-error'), false);

      // 4. Autosave po 300ms i deduplikacja identycznych szkiców
      await new Promise(r => setTimeout(r, 340));
      const draftCallsCount = fetchCalls.filter(c => c.body.is_partial === true).length;
      assert.strictEqual(draftCallsCount, 1);

      // Ponowny blur bez zmiany danych nie powinien wysłać duplikatu
      emailInput.dispatch('blur');
      await new Promise(r => setTimeout(r, 340));
      assert.strictEqual(fetchCalls.filter(c => c.body.is_partial === true).length, 1);

      // 5. Zmiana danych i ukrycie karty (sendAbandonedLead)
      emailInput.value = 'poprawny@raricart.pl';
      mockDoc.visibilityState = 'hidden';
      (mockDoc.docListeners.get('visibilitychange') || []).forEach(fn => fn());
      const abandonedCalls = fetchCalls.filter(c => c.body.is_abandoned === true);
      assert.strictEqual(abandonedCalls.length, 1);

      // 6. Obsługa błędu serwera i błędu sieciowego przy submicie
      nameInput.value = 'Test Klient';
      dateInput.value = '2026-12-12';
      locationInput.value = 'Kraków';
      guestsInput.value = '100';
      eventTypeSelect.value = 'wedding';

      globalThis.fetch = async () => ({
        ok: true,
        status: 400,
        json: async () => ({ status: 'error', message: 'Błąd walidacji backendu' }),
      });
      form.submitForm();
      await new Promise(r => setTimeout(r, 20));
      assert.strictEqual(submitBtn.disabled, false);

      globalThis.fetch = async () => {
        throw new Error('Network error');
      };
      const origError = console.error;
      console.error = () => {};
      form.submitForm();
      await new Promise(r => setTimeout(r, 20));
      console.error = origError;
      assert.strictEqual(submitBtn.disabled, false);

      // 7. Kliknięcie preCtaCheckBtn
      preCtaBtn.click();
      assert.strictEqual(contactSec.scrolled, true);
    });
  });
});
