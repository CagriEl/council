type Listener = () => void;

const listeners = new Set<Listener>();

/** Bildirim durumu değişince abone ekranları yeniler. */
export function subscribeNotificationChanges(listener: Listener): () => void {
  listeners.add(listener);
  return () => listeners.delete(listener);
}

export function emitNotificationChanges(): void {
  listeners.forEach((listener) => listener());
}
