import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  ScrollView,
  StyleSheet,
  TouchableOpacity,
  RefreshControl,
  ActivityIndicator,
  Dimensions,
} from 'react-native';
import { Image } from 'expo-image';
import { useRouter } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import Header from '../../components/Header';
import ProductCard from '../../components/ProductCard';
import CategoryPill from '../../components/CategoryPill';
import { api } from '../../services/api';
import { useTheme } from '../../context/ThemeContext';
import { COLORS } from '../../constants/theme';

const { width } = Dimensions.get('window');

export default function HomeScreen() {
  const router = useRouter();
  const { colors, isDark } = useTheme();
  const [categories, setCategories] = useState([]);
  const [featuredProducts, setFeaturedProducts] = useState([]);
  const [bestsellers, setBestsellers] = useState([]);
  const [selectedCategory, setSelectedCategory] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const loadHomeData = useCallback(async () => {
    try {
      const [catsRes, featRes, bestRes] = await Promise.allSettled([
        api.getCategories(),
        api.getFeaturedProducts(),
        api.getBestsellers(),
      ]);

      if (catsRes.status === 'fulfilled' && catsRes.value?.data) {
        setCategories(catsRes.value.data);
      }
      if (featRes.status === 'fulfilled' && featRes.value?.data) {
        setFeaturedProducts(featRes.value.data);
      }
      if (bestRes.status === 'fulfilled' && bestRes.value?.data) {
        setBestsellers(bestRes.value.data);
      }
    } catch (e) {
      console.warn('Home data load error:', e);
    } finally {
      setIsLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => {
    loadHomeData();
  }, [loadHomeData]);

  const onRefresh = () => {
    setRefreshing(true);
    loadHomeData();
  };

  const handleCategoryPress = (slug) => {
    router.push({
      pathname: '/(tabs)/shop',
      params: { category: slug },
    });
  };

  return (
    <SafeAreaView style={[styles.safeArea, { backgroundColor: colors.background }]} edges={['top']}>
      <Header />

      <ScrollView
        style={styles.container}
        contentContainerStyle={styles.content}
        showsVerticalScrollIndicator={false}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={colors.textPrimary} />
        }
      >
        {/* Hero Editorial Banner */}
        <View style={styles.heroContainer}>
          <Image
            source={{ uri: 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?auto=format&fit=crop&w=1200&q=85' }}
            style={styles.heroImage}
            contentFit="cover"
            transition={300}
          />
          <View style={styles.heroOverlay}>
            <Text style={styles.heroTag}>AUTUMN / WINTER 2026</Text>
            <Text style={styles.heroTitle}>Every Step, Elevated.</Text>
            <Text style={styles.heroSubtitle}>
              Sculptural footwear engineered with uncompromising minimalism and glove-soft Italian leather.
            </Text>
            
            <TouchableOpacity
              style={[styles.heroCta, { backgroundColor: colors.surface }]}
              onPress={() => router.push('/(tabs)/shop')}
              activeOpacity={0.85}
            >
              <Text style={[styles.heroCtaText, { color: colors.textPrimary }]}>Explore Collection</Text>
            </TouchableOpacity>
          </View>
        </View>

        {/* Categories Bar */}
        <View style={styles.categoriesSection}>
          <View style={styles.sectionHeader}>
            <Text style={[styles.sectionTitle, { color: colors.textPrimary }]}>Curated Categories</Text>
            <TouchableOpacity onPress={() => router.push('/(tabs)/shop')}>
              <Text style={[styles.seeAll, { color: colors.accent }]}>View All</Text>
            </TouchableOpacity>
          </View>

          <ScrollView
            horizontal
            showsHorizontalScrollIndicator={false}
            contentContainerStyle={styles.categoriesScroll}
          >
            <CategoryPill
              label="All Footwear"
              isSelected={selectedCategory === null}
              onPress={() => setSelectedCategory(null)}
            />
            {categories.map((cat) => (
              <CategoryPill
                key={cat.id}
                label={cat.name}
                isSelected={selectedCategory === cat.slug}
                onPress={() => handleCategoryPress(cat.slug)}
              />
            ))}
          </ScrollView>
        </View>

        {/* Featured Carousel (New Arrivals) */}
        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <Text style={[styles.sectionTitle, { color: colors.textPrimary }]}>New Arrivals</Text>
            <TouchableOpacity onPress={() => router.push('/(tabs)/shop')}>
              <Text style={[styles.seeAll, { color: colors.accent }]}>Shop New</Text>
            </TouchableOpacity>
          </View>

          {isLoading ? (
            <ActivityIndicator size="small" color={colors.textPrimary} style={{ padding: 30 }} />
          ) : (
            <ScrollView
              horizontal
              showsHorizontalScrollIndicator={false}
              contentContainerStyle={styles.horizontalProducts}
            >
              {featuredProducts.map((product) => (
                <View key={product.id} style={{ marginRight: 16 }}>
                  <ProductCard product={product} />
                </View>
              ))}
            </ScrollView>
          )}
        </View>

        {/* Brand Craftsmanship Banner */}
        <View style={[styles.editorialCard, { backgroundColor: colors.backgroundSubtle, borderColor: colors.border }]}>
          <Text style={[styles.editorialEyebrow, { color: colors.accent }]}>ATELIER PHILOSOPHY</Text>
          <Text style={[styles.editorialTitle, { color: colors.textPrimary }]}>Pure Geometry. Timeless Cadence.</Text>
          <Text style={[styles.editorialText, { color: colors.textSecondary }]}>
            Each pair begins with full-grain calfskin, contoured memory footbeds, and architectural proportions made to move seamlessly from dawn to midnight.
          </Text>
        </View>

        {/* Bestsellers Grid */}
        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <Text style={[styles.sectionTitle, { color: colors.textPrimary }]}>Iconic Bestsellers</Text>
            <TouchableOpacity onPress={() => router.push('/(tabs)/shop')}>
              <Text style={[styles.seeAll, { color: colors.accent }]}>Browse Catalog</Text>
            </TouchableOpacity>
          </View>

          {isLoading ? (
            <ActivityIndicator size="small" color={colors.textPrimary} style={{ padding: 30 }} />
          ) : (
            <View style={styles.productsGrid}>
              {bestsellers.map((product) => (
                <ProductCard key={product.id} product={product} />
              ))}
            </View>
          )}
        </View>

        {/* Footer Accent */}
        <View style={[styles.footerNote, { borderTopColor: colors.borderSubtle }]}>
          <Text style={[styles.footerBrand, { color: colors.textPrimary }]}>ÉLEVÉ PARIS</Text>
          <Text style={[styles.footerSub, { color: colors.textMuted }]}>Complimentary White-Glove Shipping & Returns</Text>
        </View>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: {
    flex: 1,
    backgroundColor: COLORS.ivory,
  },
  container: {
    flex: 1,
  },
  content: {
    paddingBottom: 40,
  },
  heroContainer: {
    width: width,
    height: 440,
    position: 'relative',
    backgroundColor: COLORS.obsidian,
  },
  heroImage: {
    width: '100%',
    height: '100%',
    opacity: 0.75,
  },
  heroOverlay: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    padding: 24,
    backgroundColor: 'rgba(23, 23, 23, 0.45)',
  },
  heroTag: {
    color: COLORS.champagne,
    fontSize: 10,
    fontWeight: '700',
    letterSpacing: 2,
    marginBottom: 6,
  },
  heroTitle: {
    fontFamily: 'serif',
    fontSize: 32,
    fontWeight: '700',
    color: '#FFFFFF',
    lineHeight: 38,
    marginBottom: 8,
  },
  heroSubtitle: {
    fontSize: 13,
    color: '#E8E2D9',
    lineHeight: 18,
    marginBottom: 20,
    maxWidth: 320,
  },
  heroCta: {
    alignSelf: 'flex-start',
    backgroundColor: '#FFFFFF',
    paddingHorizontal: 22,
    paddingVertical: 12,
    borderRadius: 8,
  },
  heroCtaText: {
    color: COLORS.obsidian,
    fontSize: 11,
    fontWeight: '700',
    letterSpacing: 1.5,
    textTransform: 'uppercase',
  },
  categoriesSection: {
    marginTop: 24,
    marginBottom: 10,
  },
  categoriesScroll: {
    paddingHorizontal: 16,
  },
  section: {
    marginTop: 28,
  },
  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'baseline',
    paddingHorizontal: 16,
    marginBottom: 14,
  },
  sectionTitle: {
    fontFamily: 'serif',
    fontSize: 20,
    fontWeight: '700',
    color: COLORS.obsidian,
  },
  seeAll: {
    fontSize: 12,
    color: COLORS.textMuted,
    textDecorationLine: 'underline',
  },
  horizontalProducts: {
    paddingLeft: 16,
  },
  editorialCard: {
    marginHorizontal: 16,
    marginTop: 20,
    backgroundColor: '#ECE8E1',
    borderRadius: 12,
    padding: 24,
    borderWidth: 1,
    borderColor: '#D8D0C4',
  },
  editorialEyebrow: {
    fontSize: 10,
    fontWeight: '700',
    letterSpacing: 2,
    color: COLORS.accent,
    marginBottom: 6,
  },
  editorialTitle: {
    fontFamily: 'serif',
    fontSize: 22,
    fontWeight: '700',
    color: COLORS.obsidian,
    marginBottom: 8,
  },
  editorialText: {
    fontSize: 12.5,
    color: COLORS.textSecondary,
    lineHeight: 20,
  },
  productsGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-between',
    paddingHorizontal: 16,
  },
  footerNote: {
    marginTop: 36,
    alignItems: 'center',
    paddingVertical: 20,
    borderTopWidth: 1,
    borderTopColor: COLORS.border,
  },
  footerBrand: {
    fontFamily: 'serif',
    fontSize: 13,
    letterSpacing: 4,
    fontWeight: '700',
    color: COLORS.obsidian,
  },
  footerSub: {
    fontSize: 10,
    color: COLORS.textMuted,
    marginTop: 4,
  },
});
