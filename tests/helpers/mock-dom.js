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
