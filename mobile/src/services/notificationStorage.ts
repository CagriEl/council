import * as FileSystem from 'expo-file-system/legacy';

const STORAGE_FILE = `${FileSystem.documentDirectory}notification-read-ids.json`;

type StoredState = {
  readIds: number[];
  pushUnreadIds: number[];
};

const defaultState = (): StoredState => ({ readIds: [], pushUnreadIds: [] });

/**
 * Okundu / okunmadı durumunu cihazda kalıcı tutar.
 */
async function readState(): Promise<StoredState> {
  try {
    const info = await FileSystem.getInfoAsync(STORAGE_FILE);
    if (!info.exists) return defaultState();

    const raw = await FileSystem.readAsStringAsync(STORAGE_FILE);
    const parsed = JSON.parse(raw) as Partial<StoredState>;
    return {
      readIds: Array.isArray(parsed.readIds) ? parsed.readIds.map(Number) : [],
      pushUnreadIds: Array.isArray(parsed.pushUnreadIds)
        ? parsed.pushUnreadIds.map(Number)
        : [],
    };
  } catch {
    return defaultState();
  }
}

async function writeState(state: StoredState): Promise<void> {
  await FileSystem.writeAsStringAsync(STORAGE_FILE, JSON.stringify(state));
}

export async function getReadNotificationIds(): Promise<Set<number>> {
  const state = await readState();
  return new Set(state.readIds);
}

export async function getPushUnreadIds(): Promise<Set<number>> {
  const state = await readState();
  return new Set(state.pushUnreadIds);
}

export async function markNotificationRead(id: number): Promise<void> {
  const state = await readState();
  const readIds = new Set(state.readIds);
  readIds.add(id);
  const pushUnreadIds = state.pushUnreadIds.filter((item) => item !== id);
  await writeState({
    readIds: [...readIds],
    pushUnreadIds,
  });
}

export async function markAllNotificationsRead(ids: number[]): Promise<void> {
  const state = await readState();
  const readIds = new Set([...state.readIds, ...ids]);
  await writeState({
    readIds: [...readIds],
    pushUnreadIds: [],
  });
}

export async function registerPushNotification(id: number): Promise<void> {
  if (!id) return;
  const state = await readState();
  if (state.readIds.includes(id)) return;

  const pushUnreadIds = new Set(state.pushUnreadIds);
  pushUnreadIds.add(id);
  await writeState({
    ...state,
    pushUnreadIds: [...pushUnreadIds],
  });
}
