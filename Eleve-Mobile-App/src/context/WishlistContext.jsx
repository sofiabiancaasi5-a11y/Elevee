import React, { createContext, useContext, useState, useEffect, useCallback } from 'react';
import { api } from '../services/api';
import { useAuth } from './AuthContext';

const WishlistContext = createContext({
  wishlist: [],
  isFavorite: () => false,
  toggleWishlist: async () => {},
  refreshWishlist: async () => {},
});

export const WishlistProvider = ({ children }) => {
  const { isAuthenticated } = useAuth();
  const [wishlist, setWishlist] = useState([]);

  const refreshWishlist = useCallback(async () => {
    if (!isAuthenticated) {
      setWishlist([]);
      return;
    }
    try {
      const res = await api.getWishlist();
      if (res.data) {
        setWishlist(res.data);
      }
    } catch (e) {
      console.warn('Failed to fetch wishlist:', e.message);
    }
  }, [isAuthenticated]);

  useEffect(() => {
    refreshWishlist();
  }, [refreshWishlist]);

  const isFavorite = useCallback((productId) => {
    return wishlist.some(item => item.id === productId);
  }, [wishlist]);

  const toggleWishlist = async (product) => {
    if (!isAuthenticated) {
      return false;
    }

    const alreadyFav = isFavorite(product.id);
    
    // Optimistic update
    if (alreadyFav) {
      setWishlist(prev => prev.filter(item => item.id !== product.id));
    } else {
      setWishlist(prev => [...prev, product]);
    }

    try {
      await api.toggleWishlist(product.id);
      return !alreadyFav;
    } catch (e) {
      // Rollback on error
      if (alreadyFav) {
        setWishlist(prev => [...prev, product]);
      } else {
        setWishlist(prev => prev.filter(item => item.id !== product.id));
      }
      throw e;
    }
  };

  return (
    <WishlistContext.Provider
      value={{
        wishlist,
        isFavorite,
        toggleWishlist,
        refreshWishlist,
      }}
    >
      {children}
    </WishlistContext.Provider>
  );
};

export const useWishlist = () => useContext(WishlistContext);
