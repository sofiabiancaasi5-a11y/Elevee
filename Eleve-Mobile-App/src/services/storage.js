import AsyncStorage from '@react-native-async-storage/async-storage';
import { Platform } from 'react-native';

// Resilient memory cache fallback for Expo Go and environments where native storage is initializing
const memoryStorage = new Map();

export const storage = {
  async getItem(key) {
    try {
      if (Platform.OS === 'web' && typeof window !== 'undefined' && window.localStorage) {
        const item = window.localStorage.getItem(key);
        if (item !== null) return item;
      }
      const val = await AsyncStorage.getItem(key);
      return val !== null ? val : (memoryStorage.get(key) || null);
    } catch {
      return memoryStorage.get(key) || null;
    }
  },

  async setItem(key, value) {
    const stringVal = typeof value === 'string' ? value : JSON.stringify(value);
    memoryStorage.set(key, stringVal);
    try {
      if (Platform.OS === 'web' && typeof window !== 'undefined' && window.localStorage) {
        window.localStorage.setItem(key, stringVal);
      }
      await AsyncStorage.setItem(key, stringVal);
    } catch {
      // In-memory fallback already saved
    }
  },

  async removeItem(key) {
    memoryStorage.delete(key);
    try {
      if (Platform.OS === 'web' && typeof window !== 'undefined' && window.localStorage) {
        window.localStorage.removeItem(key);
      }
      await AsyncStorage.removeItem(key);
    } catch {
      // In-memory fallback already removed
    }
  },

  async clear() {
    memoryStorage.clear();
    try {
      if (Platform.OS === 'web' && typeof window !== 'undefined' && window.localStorage) {
        window.localStorage.clear();
      }
      await AsyncStorage.clear();
    } catch {
      // In-memory fallback cleared
    }
  }
};

export default storage;
