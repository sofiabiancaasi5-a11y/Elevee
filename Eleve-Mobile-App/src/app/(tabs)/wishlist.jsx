import React from 'react';
import {
  View,
  Text,
  FlatList,
  StyleSheet,
  TouchableOpacity,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useRouter } from 'expo-router';
import Header from '../../components/Header';
import ProductCard from '../../components/ProductCard';
import EmptyState from '../../components/EmptyState';
import { useWishlist } from '../../context/WishlistContext';
import { useAuth } from '../../context/AuthContext';
import { useTheme } from '../../context/ThemeContext';
import { COLORS } from '../../constants/theme';

export default function WishlistScreen() {
  const router = useRouter();
  const { wishlist } = useWishlist();
  const { isAuthenticated } = useAuth();
  const { colors } = useTheme();

  return (
    <SafeAreaView style={[styles.safeArea, { backgroundColor: colors.background }]} edges={['top']}>
      <Header title="WISHLIST" />

      {!isAuthenticated ? (
        <EmptyState
          icon="heart-outline"
          title="Curate Your Favorites"
          description="Sign in to save your preferred silhouettes, track availability, and sync across devices."
          buttonText="Sign In / Register"
          onButtonPress={() => router.push('/auth/login')}
        />
      ) : wishlist.length === 0 ? (
        <EmptyState
          icon="heart-outline"
          title="Your Wishlist is Empty"
          description="Tap the heart icon on any footwear silhouette in our catalog to save it for future consideration."
          buttonText="Discover Collection"
          onButtonPress={() => router.push('/(tabs)/shop')}
        />
      ) : (
        <FlatList
          data={wishlist}
          renderItem={({ item }) => <ProductCard product={item} />}
          keyExtractor={(item) => String(item.id)}
          numColumns={2}
          columnWrapperStyle={styles.columnWrapper}
          contentContainerStyle={styles.listContent}
          showsVerticalScrollIndicator={false}
          ListHeaderComponent={
            <View style={styles.headerInfo}>
              <Text style={[styles.countText, { color: colors.accent }]}>{wishlist.length} SAVED SILHOUETTES</Text>
            </View>
          }
        />
      )}
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: {
    flex: 1,
    backgroundColor: COLORS.ivory,
  },
  listContent: {
    paddingHorizontal: 16,
    paddingTop: 12,
    paddingBottom: 40,
  },
  columnWrapper: {
    justifyContent: 'space-between',
  },
  headerInfo: {
    marginBottom: 14,
  },
  countText: {
    fontSize: 10,
    fontWeight: '700',
    color: COLORS.textMuted,
    letterSpacing: 1.5,
  },
});
