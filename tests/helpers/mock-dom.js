/**
 * Lekki helper Mock DOM do testów jednostkowych JavaScript w Node.js (Raricart)
 * Nie wymaga zewnętrznych bibliotek (zero npm bloat), działa błyskawicznie w node:test.
 */

class MockClassList {
  constructor() {
    this.classes = new Set();
  }

  add(cls) {
    this.classes.add(cls);
  }

  remove(cls) {
    this.classes.delete(cls);
  }

  contains(cls) {
    return this.classes.has(cls);
  }

  toggle(cls, force) {
    if (typeof force === 'boolean') {
      if (force) {
        this.classes.add(cls);
        return true;
      } else {
        this.classes.delete(cls);
        return false;
      }
    }
    if (this.classes.has(cls)) {
      this.classes.delete(cls);
      return false;
    } else {
      this.classes.add(cls);
      return true;
    }
  }
}

class MockLocalStorage {
  constructor() {
    this.store = new Map();
  }

  getItem(key) {
    return this.store.has(key) ? this.store.get(key) : null;
  }

  setItem(key, value) {
    this.store.set(key, String(value));
  }

  removeItem(key) {
    this.store.delete(key);
  }

  clear() {
    this.store.clear();
  }
}

class MockElement {
  constructor(id = '', tagName = 'div', classNames = []) {
    this._id = id;
    this._src = '';
    this.tagName = tagName.toUpperCase();
    this.classList = new MockClassList();
    classNames.forEach(c => this.classList.add(c));
    this.attributes = new Map();
    if (id) this.attributes.set('id', id);
    this.listeners = new Map();
    this.textContent = '';
    this.innerHTML = '';
    this.value = '';
    this.placeholder = '';
    this.children = [];
    this.dataset = {};
    this.style = {};
  }

  get id() {
    return this._id || this.attributes.get('id') || '';
  }

  set id(val) {
    this._id = val;
    this.attributes.set('id', String(val));
  }

  get src() {
    return this._src || this.attributes.get('src') || '';
  }

  set src(val) {
    this._src = String(val);
    this.attributes.set('src', String(val));
  }

  setAttribute(name, value) {
    this.attributes.set(name, String(value));
    if (name.startsWith('data-')) {
      const key = name.slice(5).replace(/-([a-z])/g, (_, c) => c.toUpperCase());
      this.dataset[key] = String(value);
    }
  }

  getAttribute(name) {
    return this.attributes.get(name) || null;
  }

  hasAttribute(name) {
    return this.attributes.has(name);
  }

  focus() {
    this.isFocused = true;
  }

  closest(selector) {
    if (selector.toUpperCase() === this.tagName) return this;
    if (selector.startsWith('.') && this.classList.contains(selector.slice(1))) return this;
    return null;
  }

  addEventListener(event, callback) {
    if (!this.listeners.has(event)) {
      this.listeners.set(event, []);
    }
    this.listeners.get(event).push(callback);
  }

  click() {
    const event = { target: this, preventDefault: () => {} };
    const handlers = this.listeners.get('click') || [];
    for (const fn of handlers) {
      fn(event);
    }
  }

  contains(target) {
    if (this === target) return true;
    return this.children.some(child => child === target || (child.contains && child.contains(target)));
  }

  appendChild(child) {
    this.children.push(child);
    return child;
  }
}

class MockDocument {
  constructor() {
    this.elementsById = new Map();
    this.elements = [];
    this.docListeners = new Map();
    this.head = new MockElement('head', 'head');
    this.body = new MockElement('body', 'body');
    this.registerElement(this.head);
    this.registerElement(this.body);
  }

  registerElement(el) {
    if (el.id) {
      this.elementsById.set(el.id, el);
    }
    this.elements.push(el);
    return el;
  }

  getElementById(id) {
    if (this.elementsById.has(id)) {
      const el = this.elementsById.get(id);
      if (el.id === id) return el;
    }
    const found = this.elements.find(el => el.id === id);
    if (found) {
      this.elementsById.set(id, found);
      return found;
    }
    return null;
  }

  createElement(tagName) {
    const el = new MockElement('', tagName);
    return this.registerElement(el);
  }

  querySelector(selector) {
    const all = this.querySelectorAll(selector);
    return all.length > 0 ? all[0] : null;
  }

  querySelectorAll(selector) {
    const selectors = selector.split(',').map(s => s.trim());
    return this.elements.filter(el => {
      return selectors.some(sel => {
        if (sel.startsWith('#')) {
          return el.id === sel.slice(1);
        }
        if (sel.startsWith('.')) {
          return el.classList.contains(sel.slice(1));
        }
        if (sel.startsWith('[') && sel.endsWith(']')) {
          const attr = sel.slice(1, -1);
          return el.hasAttribute(attr);
        }
        return el.tagName.toLowerCase() === sel.toLowerCase();
      });
    });
  }

  addEventListener(event, callback) {
    if (!this.docListeners.has(event)) {
      this.docListeners.set(event, []);
    }
    this.docListeners.get(event).push(callback);
  }

  dispatchDocumentClick(target) {
    const event = { target, preventDefault: () => {} };
    const handlers = this.docListeners.get('click') || [];
    for (const fn of handlers) {
      fn(event);
    }
  }

  dispatchDocumentKeyDown(keyEvent) {
    const handlers = this.docListeners.get('keydown') || [];
    for (const fn of handlers) {
      fn(keyEvent);
    }
  }
}

class MockWindow {
  constructor() {
    this.listeners = new Map();
    this.localStorage = new MockLocalStorage();
    this.location = {
      hash: '',
      pathname: '/',
      search: '',
      href: 'https://raricart.pl/'
    };
    this.history = {
      state: null,
      pushState: (state, title, url) => {
        this.history.state = state;
        if (url && url.startsWith('#')) {
          this.location.hash = url;
        }
      },
      replaceState: (state, title, url) => {
        this.history.state = state;
      },
      back: () => {
        this.history.state = null;
        this.dispatchPopState();
      }
    };
  }

  addEventListener(event, callback) {
    if (!this.listeners.has(event)) {
      this.listeners.set(event, []);
    }
    this.listeners.get(event).push(callback);
  }

  dispatchPopState() {
    const event = { state: this.history.state };
    const handlers = this.listeners.get('popstate') || [];
    for (const fn of handlers) {
      fn(event);
    }
  }
}

export {
  MockClassList,
  MockLocalStorage,
  MockElement,
  MockDocument,
  MockWindow,
};

export function setupMockDom() {
  const elements = new Map();
  const listeners = new Map();

  class SetupMockElement {
    constructor(tagName, id = '', className = '') {
      this.tagName = tagName.toUpperCase();
      this.id = id;
      this._classes = new Set(className ? className.split(' ').filter(Boolean) : []);
      const self = this;
      this.classList = {
        get _classes() { return self._classes; },
        add(...cls) { cls.forEach(c => self._classes.add(c)); },
        remove(...cls) { cls.forEach(c => self._classes.delete(c)); },
        toggle(cls, force) {
          if (typeof force === 'boolean') {
            if (force) self._classes.add(cls);
            else self._classes.delete(cls);
            return force;
          }
          if (self._classes.has(cls)) {
            self._classes.delete(cls);
            return false;
          }
          self._classes.add(cls);
          return true;
        },
        contains(cls) { return self._classes.has(cls); }
      };
      this.attributes = new Map();
      this._listeners = new Map();
      this.style = {};
      this.textContent = '';
      this._innerHTML = '';
      this.children = [];
      this.value = '';
      this.disabled = false;
      this.scrollTop = 0;
      this.scrollHeight = 100;
      this.parentNode = null;
    }

    get innerHTML() { return this._innerHTML; }
    set innerHTML(val) {
      this._innerHTML = String(val || '');
      if (this._innerHTML === '') {
        this.children = [];
        return;
      }
      const tagRegex = /<([a-zA-Z0-9]+)\b([^>]*)>/g;
      let match;
      while ((match = tagRegex.exec(this._innerHTML)) !== null) {
        const [, tag, attrs] = match;
        const idMatch = attrs.match(/\bid=["']([^"']+)["']/);
        const classMatch = attrs.match(/\bclass=["']([^"']+)["']/);
        if (idMatch) {
          const child = new SetupMockElement(tag, idMatch[1], classMatch ? classMatch[1] : '');
          const ariaExpanded = attrs.match(/\baria-expanded=["']([^"']+)["']/);
          if (ariaExpanded) child.setAttribute('aria-expanded', ariaExpanded[1]);
          const ariaLabel = attrs.match(/\baria-label=["']([^"']+)["']/);
          if (ariaLabel) child.setAttribute('aria-label', ariaLabel[1]);
          child.parentNode = this;
          this.children.push(child);
          elements.set(child.id, child);
        }
      }
    }

    get className() { return Array.from(this._classes).join(' '); }
    set className(val) { this._classes = new Set(val ? String(val).split(' ').filter(Boolean) : []); }
    appendChild(child) {
      if (!child) return null;
      child.parentNode = this;
      this.children.push(child);
      if (child.id) elements.set(child.id, child);
      return child;
    }
    remove() {
      if (this.parentNode && Array.isArray(this.parentNode.children)) {
        this.parentNode.children = this.parentNode.children.filter(c => c !== this);
      }
      if (this.id) elements.delete(this.id);
    }
    setAttribute(k, v) {
      this.attributes.set(k, String(v));
      if (k === 'class') this.className = v;
      if (k === 'id') { this.id = v; elements.set(v, this); }
    }
    getAttribute(k) { return this.attributes.get(k) || null; }
    removeAttribute(k) { this.attributes.delete(k); }
    hasAttribute(k) { return this.attributes.has(k); }
    addEventListener(evt, fn) {
      if (!this._listeners.has(evt)) this._listeners.set(evt, []);
      this._listeners.get(evt).push(fn);
    }
    dispatchEvent(evt) {
      const fns = this._listeners.get(evt.type) || [];
      fns.forEach(fn => fn.call(this, evt));
      return true;
    }
    focus() { this._focused = true; }
    reset() { this.value = ''; }
    querySelector(sel) {
      for (const el of elements.values()) {
        if (matchSelector(el, sel)) return el;
      }
      return null;
    }
  }

  const matchSelector = (el, rawSel) => {
    const sel = rawSel.trim();
    if (!sel) return false;
    if (sel.startsWith('#')) return el.id === sel.slice(1);
    if (sel.startsWith('.')) return el.classList.contains(sel.slice(1));
    if (sel.includes('[data-site-id]')) return el.hasAttribute('data-site-id');
    return el.tagName.toLowerCase() === sel.toLowerCase();
  };

  const mockDocument = {
    readyState: 'complete',
    head: new SetupMockElement('head'),
    body: new SetupMockElement('body'),
    documentElement: new SetupMockElement('html'),
    getElementById: (id) => elements.get(id) || null,
    querySelector: (sel) => {
      for (const p of sel.split(',')) {
        for (const el of elements.values()) {
          if (matchSelector(el, p)) return el;
        }
      }
      return null;
    },
    querySelectorAll: (sel) => {
      const res = [];
      for (const el of elements.values()) {
        if (sel.split(',').some(p => matchSelector(el, p))) res.push(el);
      }
      return res;
    },
    createElement: (tag) => new SetupMockElement(tag),
    addEventListener: (evt, fn) => {
      if (!listeners.has(evt)) listeners.set(evt, []);
      listeners.get(evt).push(fn);
    },
    dispatchEvent: (evt) => {
      const fns = listeners.get(evt.type) || [];
      fns.forEach(fn => fn(evt));
      return true;
    }
  };

  const defaultForbiddenFetch = async (url) => {
    throw new Error(`NETWORK_FORBIDDEN: Zablokowano niezamockowane wywołanie sieciowe fetch(${String(url)}) w teście jednostkowym`);
  };

  const mockWindow = {
    document: mockDocument,
    innerWidth: 1024,
    innerHeight: 768,
    sessionStorage: new MockLocalStorage(),
    localStorage: new MockLocalStorage(),
    performance: { now: () => Date.now() },
    fetch: defaultForbiddenFetch,
    addEventListener: (evt, fn) => mockDocument.addEventListener(evt, fn)
  };

  globalThis.fetch = defaultForbiddenFetch;

  return { mockWindow, mockDocument, MockElement: SetupMockElement, elements, listeners };
}
