const observers = new Set<MockIntersectionObserver>()

class MockIntersectionObserver implements IntersectionObserver {
  readonly root = null
  readonly rootMargin = ''
  readonly scrollMargin = ''
  readonly thresholds = []
  private readonly elements = new Set<Element>()
  private readonly callback: IntersectionObserverCallback

  constructor(callback: IntersectionObserverCallback) {
    this.callback = callback
  }

  observe(element: Element) {
    this.elements.add(element)
    observers.add(this)
  }

  unobserve(element: Element) {
    this.elements.delete(element)
  }

  disconnect() {
    this.elements.clear()
    observers.delete(this)
  }

  takeRecords(): IntersectionObserverEntry[] {
    return []
  }

  intersect() {
    const entries = [...this.elements].map(
      (target) => ({ target, isIntersecting: true }) as IntersectionObserverEntry,
    )

    this.callback(entries, this)
  }
}

export function installIntersectionObserver() {
  globalThis.IntersectionObserver = MockIntersectionObserver
}

export function scrollToSentinel() {
  observers.forEach((observer) => observer.intersect())
}
