import React, { useState, useEffect, useCallback, useMemo } from 'react';
import {
  View,
  Text,
  FlatList,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  ActivityIndicator,
  RefreshControl,
  ScrollView,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useLocalSearchParams } from 'expo-router';
import Header from '../../components/Header';
import ProductCard from '../../components/ProductCard';
import CategoryPill from '../../components/CategoryPill';
import EmptyState from '../../components/EmptyState';
import { api } from '../../services/api';
import { useTheme } from '../../context/ThemeContext';
import { COLORS } from '../../constants/theme';

const SIZES = ['5', '6', '7', '8', '9', '10', '11', '12'];
const COLORS_LIST = ['Obsidian', 'Ivory', 'Sand', 'Espresso'];
const SORTS = [
  { id: 'newest', label: 'Newest Arrivals' },
  { id: 'popular', label: 'Bestselling' },
  { id: 'price_asc', label: 'Price: Low to High' },
  { id: 'price_desc', label: 'Price: High to Low' },
];

export default function ShopScreen() {
  const params = useLocalSearchParams();
  const { colors, isDark } = useTheme();
  const [categories, setCategories] = useState([]);
  const [products, setProducts] = useState([]);
  const [search, setSearch] = useState('');
  const [selectedCategory, setSelectedCategory] = useState(params.category || null);
  const [selectedSize, setSelectedSize] = useState(null);
  const [selectedColor, setSelectedColor] = useState(null);
  const [selectedSort, setSelectedSort] = useState('newest');
  const [showFilters, setShowFilters] = useState(false);
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  // Sync category from route params if navigated from Home or Drawer
  useEffect(() => {
    if (params.category) {
      if (params.category === 'all') {
        setSelectedCategory(null);
      } else {
        setSelectedCategory(params.category);
      }
    }
  }, [params.category]);

  // Load Categories once
  useEffect(() => {
    api.getCategories()
      .then(res => res.data && setCategories(res.data))
      .catch(() => {});
  }, []);

  // Fetch filtered products
  const fetchProducts = useCallback(async () => {
    try {
      setIsLoading(true);
      const queryParts = [];
      if (search.trim()) queryParts.push(`search=${encodeURIComponent(search.trim())}`);
      if (selectedCategory) queryParts.push(`category=${encodeURIComponent(selectedCategory)}`);
      if (selectedSize) queryParts.push(`size=${encodeURIComponent(selectedSize)}`);
      if (selectedColor) queryParts.push(`color=${encodeURIComponent(selectedColor)}`);
      if (selectedSort) queryParts.push(`sort=${encodeURIComponent(selectedSort)}`);

      const queryString = queryParts.join('&');
      const res = await api.getProducts(queryString);
      if (res.data) {
        setProducts(res.data);
      }
    } catch (e) {
      console.warn('Failed to fetch catalog:', e);
    } finally {
      setIsLoading(false);
      setRefreshing(false);
    }
  }, [search, selectedCategory, selectedSize, selectedColor, selectedSort]);

  // Debounced search effect
  useEffect(() => {
    const timer = setTimeout(() => {
      fetchProducts();
    }, 280);
    return () => clearTimeout(timer);
  }, [fetchProducts]);

  const onRefresh = () => {
    setRefreshing(true);
    fetchProducts();
  };

  const clearFilters = () => {
    setSearch('');
    setSelectedCategory(null);
    setSelectedSize(null);
    setSelectedColor(null);
    setSelectedSort('newest');
  };

  const hasActiveFilters = !!(selectedCategory || selectedSize || selectedColor || selectedSort !== 'newest' || search);

  const renderProductItem = useCallback(({ item }) => (
    <ProductCard product={item} />
  ), []);

  const keyExtractor = useCallback((item) => String(item.id), []);

  return (
    <SafeAreaView style={[styles.safeArea, { backgroundColor: colors.background }]} edges={['top']}>
      <Header title="FOOTWEAR" />

      {/* Search Bar */}
      <View style={styles.searchContainer}>
        <View style={[styles.searchBar, { backgroundColor: colors.surface, borderColor: colors.border }]}>
          <Ionicons name="search-outline" size={18} color={colors.textMuted} />
          <TextInput
            style={[styles.searchInput, { color: colors.inputText }]}
            placeholder="Search footwear by model or leather..."
            placeholderTextColor={colors.textMuted}
            value={search}
            onChangeText={setSearch}
            returnKeyType="search"
          />
          {search ? (
            <TouchableOpacity onPress={() => setSearch('')} hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}>
              <Ionicons name="close-circle" size={16} color={colors.textMuted} />
            </TouchableOpacity>
          ) : null}
        </View>

        <TouchableOpacity 
          style={[
            styles.filterToggleBtn,
            {
              backgroundColor: (showFilters || hasActiveFilters) ? colors.textPrimary : colors.surface,
              borderColor: (showFilters || hasActiveFilters) ? colors.textPrimary : colors.border,
            },
          ]} 
          onPress={() => setShowFilters(prev => !prev)}
          activeOpacity={0.8}
        >
          <Ionicons
            name="options-outline"
            size={18}
            color={(showFilters || hasActiveFilters) ? colors.background : colors.textPrimary}
          />
        </TouchableOpacity>
      </View>

      {/* Horizontal Categories Bar */}
      <View style={styles.categoryBar}>
        <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.categoryScroll}>
          <CategoryPill
            label="All Shoes"
            isSelected={selectedCategory === null}
            onPress={() => setSelectedCategory(null)}
          />
          {categories.map((cat) => (
            <CategoryPill
              key={cat.id}
              label={cat.name}
              isSelected={selectedCategory === cat.slug}
              onPress={() => setSelectedCategory(prev => prev === cat.slug ? null : cat.slug)}
            />
          ))}
        </ScrollView>
      </View>

      {/* Expandable Filter Drawer */}
      {showFilters && (
        <View style={[styles.filtersDrawer, { backgroundColor: colors.backgroundSubtle, borderColor: colors.border }]}>
          {/* Size Selector */}
          <View style={styles.filterSection}>
            <Text style={[styles.filterLabel, { color: colors.accent }]}>SIZE (US)</Text>
            <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.filterChipsRow}>
              {SIZES.map(sz => {
                const isSelected = selectedSize === sz;
                return (
                  <TouchableOpacity
                    key={sz}
                    style={[
                      styles.chip,
                      {
                        backgroundColor: isSelected ? colors.textPrimary : colors.surface,
                        borderColor: isSelected ? colors.textPrimary : colors.border,
                      },
                    ]}
                    onPress={() => setSelectedSize(prev => prev === sz ? null : sz)}
                  >
                    <Text
                      style={[
                        styles.chipText,
                        { color: isSelected ? colors.background : colors.textSecondary },
                      ]}
                    >
                      {sz}
                    </Text>
                  </TouchableOpacity>
                );
              })}
            </ScrollView>
          </View>

          {/* Color Selector */}
          <View style={styles.filterSection}>
            <Text style={[styles.filterLabel, { color: colors.accent }]}>SHADE</Text>
            <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.filterChipsRow}>
              {COLORS_LIST.map(col => {
                const isSelected = selectedColor === col;
                return (
                  <TouchableOpacity
                    key={col}
                    style={[
                      styles.chip,
                      {
                        backgroundColor: isSelected ? colors.textPrimary : colors.surface,
                        borderColor: isSelected ? colors.textPrimary : colors.border,
                      },
                    ]}
                    onPress={() => setSelectedColor(prev => prev === col ? null : col)}
                  >
                    <Text
                      style={[
                        styles.chipText,
                        { color: isSelected ? colors.background : colors.textSecondary },
                      ]}
                    >
                      {col}
                    </Text>
                  </TouchableOpacity>
                );
              })}
            </ScrollView>
          </View>

          {/* Sort Selector */}
          <View style={styles.filterSection}>
            <Text style={[styles.filterLabel, { color: colors.accent }]}>SORT ORDER</Text>
            <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.filterChipsRow}>
              {SORTS.map(srt => {
                const isSelected = selectedSort === srt.id;
                return (
                  <TouchableOpacity
                    key={srt.id}
                    style={[
                      styles.chip,
                      {
                        backgroundColor: isSelected ? colors.textPrimary : colors.surface,
                        borderColor: isSelected ? colors.textPrimary : colors.border,
                      },
                    ]}
                    onPress={() => setSelectedSort(srt.id)}
                  >
                    <Text
                      style={[
                        styles.chipText,
                        { color: isSelected ? colors.background : colors.textSecondary },
                      ]}
                    >
                      {srt.label}
                    </Text>
                  </TouchableOpacity>
                );
              })}
            </ScrollView>
          </View>

          {hasActiveFilters && (
            <TouchableOpacity style={styles.clearBtn} onPress={clearFilters}>
              <Text style={[styles.clearBtnText, { color: colors.accent }]}>Reset All Filters</Text>
            </TouchableOpacity>
          )}
        </View>
      )}

      {/* Products Grid */}
      {isLoading && products.length === 0 ? (
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color={colors.textPrimary} />
          <Text style={[styles.loadingText, { color: colors.textMuted }]}>Curating footwear selection...</Text>
        </View>
      ) : (
        <FlatList
          data={products}
          renderItem={renderProductItem}
          keyExtractor={keyExtractor}
          numColumns={2}
          columnWrapperStyle={styles.columnWrapper}
          contentContainerStyle={styles.listContent}
          showsVerticalScrollIndicator={false}
          initialNumToRender={6}
          maxToRenderPerBatch={8}
          windowSize={5}
          removeClippedSubviews={true}
          refreshControl={
            <RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={colors.textPrimary} />
          }
          ListEmptyComponent={
            <EmptyState
              icon="search-outline"
              title="No Silhouettes Found"
              description="We could not find any shoes matching your selected filters. Try broadening your criteria."
              buttonText="Reset Filters"
              onButtonPress={clearFilters}
            />
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
  searchContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingVertical: 10,
    gap: 10,
  },
  searchBar: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
    borderWidth: 1,
    borderColor: COLORS.border,
    borderRadius: 8,
    paddingHorizontal: 12,
    height: 42,
    gap: 8,
  },
  searchInput: {
    flex: 1,
    fontSize: 13,
    color: COLORS.obsidian,
  },
  filterToggleBtn: {
    width: 42,
    height: 42,
    borderRadius: 8,
    backgroundColor: '#FFFFFF',
    borderWidth: 1,
    borderColor: COLORS.border,
    alignItems: 'center',
    justifyContent: 'center',
  },
  filterToggleActive: {
    backgroundColor: COLORS.obsidian,
    borderColor: COLORS.obsidian,
  },
  categoryBar: {
    marginBottom: 8,
  },
  categoryScroll: {
    paddingHorizontal: 16,
  },
  filtersDrawer: {
    backgroundColor: '#ECE8E1',
    marginHorizontal: 16,
    marginBottom: 12,
    padding: 14,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: '#D8D0C4',
    gap: 12,
  },
  filterSection: {
    gap: 6,
  },
  filterLabel: {
    fontSize: 9,
    fontWeight: '700',
    color: COLORS.textMuted,
    letterSpacing: 1.5,
  },
  filterChipsRow: {
    gap: 6,
  },
  chip: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 6,
    backgroundColor: '#FFFFFF',
    borderWidth: 1,
    borderColor: '#D8D0C4',
  },
  chipActive: {
    backgroundColor: COLORS.obsidian,
    borderColor: COLORS.obsidian,
  },
  chipText: {
    fontSize: 11,
    fontWeight: '600',
    color: COLORS.textSecondary,
  },
  chipTextActive: {
    color: COLORS.ivory,
  },
  clearBtn: {
    alignSelf: 'center',
    paddingTop: 4,
  },
  clearBtnText: {
    fontSize: 11,
    color: COLORS.accent,
    fontWeight: '600',
    textDecorationLine: 'underline',
  },
  listContent: {
    paddingHorizontal: 16,
    paddingBottom: 40,
    paddingTop: 6,
  },
  columnWrapper: {
    justifyContent: 'space-between',
  },
  loadingContainer: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    gap: 12,
  },
  loadingText: {
    fontSize: 12,
    color: COLORS.textMuted,
  },
});
