import React, { createContext, useContext, useState, useEffect } from 'react';
import storage from '../services/storage';
import { api, TOKEN_KEY, USER_KEY } from '../services/api';

const AuthContext = createContext({
  user: null,
  token: null,
  isLoading: true,
  isAuthenticated: false,
  login: async () => {},
  register: async () => {},
  logout: async () => {},
  refreshProfile: async () => {},
});

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [token, setToken] = useState(null);
  const [isLoading, setIsLoading] = useState(true);

  // Initialize auth state on mount
  useEffect(() => {
    let isMounted = true;
    const bootstrapAuth = async () => {
      try {
        const storedToken = await storage.getItem(TOKEN_KEY);
        const storedUser = await storage.getItem(USER_KEY);

        if (storedToken && storedUser && isMounted) {
          setToken(storedToken);
          try {
            setUser(JSON.parse(storedUser));
          } catch {
            setUser(null);
          }
          
          // Optionally fetch fresh profile in background
          api.getProfile()
            .then(res => {
              if (res.data && isMounted) {
                setUser(res.data);
                storage.setItem(USER_KEY, JSON.stringify(res.data));
              }
            })
            .catch(() => {});
        }
      } catch (e) {
        console.warn('Failed to load auth state:', e);
      } finally {
        if (isMounted) {
          setIsLoading(false);
        }
      }
    };

    bootstrapAuth();

    return () => {
      isMounted = false;
    };
  }, []);

  const login = async (email, password) => {
    const res = await api.login({ email, password });
    if (res.data && res.data.token) {
      const { user: userData, token: authToken } = res.data;
      setToken(authToken);
      setUser(userData);
      await storage.setItem(TOKEN_KEY, authToken);
      await storage.setItem(USER_KEY, JSON.stringify(userData));
      return userData;
    }
    throw new Error(res.message || 'Login failed');
  };

  const register = async (name, email, password, password_confirmation, phone) => {
    const res = await api.register({
      name,
      email,
      password,
      password_confirmation,
      phone,
    });
    if (res.data && res.data.token) {
      const { user: userData, token: authToken } = res.data;
      setToken(authToken);
      setUser(userData);
      await storage.setItem(TOKEN_KEY, authToken);
      await storage.setItem(USER_KEY, JSON.stringify(userData));
      return userData;
    }
    throw new Error(res.message || 'Registration failed');
  };

  const logout = async () => {
    try {
      await api.logout();
    } catch {
      // ignore
    } finally {
      setToken(null);
      setUser(null);
      await storage.removeItem(TOKEN_KEY);
      await storage.removeItem(USER_KEY);
    }
  };

  const refreshProfile = async () => {
    try {
      const res = await api.getProfile();
      if (res.data) {
        setUser(res.data);
        await storage.setItem(USER_KEY, JSON.stringify(res.data));
      }
    } catch (e) {
      console.warn('Failed to refresh profile:', e);
    }
  };

  const updateUserProfile = async (profileData) => {
    const res = await api.updateProfile(profileData);
    if (res.data) {
      setUser(res.data);
      await storage.setItem(USER_KEY, JSON.stringify(res.data));
      return res.data;
    }
    throw new Error(res.message || 'Profile update failed');
  };

  return (
    <AuthContext.Provider
      value={{
        user,
        token,
        isLoading,
        isAuthenticated: !!token,
        login,
        register,
        logout,
        refreshProfile,
        updateUserProfile,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => useContext(AuthContext);
