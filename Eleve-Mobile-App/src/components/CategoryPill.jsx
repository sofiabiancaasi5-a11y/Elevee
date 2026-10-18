import React, { memo } from 'react';
import { TouchableOpacity, Text, StyleSheet } from 'react-native';
import { useTheme } from '../context/ThemeContext';

const CategoryPill = ({ label, isSelected, onPress }) => {
  const { colors } = useTheme();

  return (
    <TouchableOpacity
      style={[
        styles.pill,
        {
          backgroundColor: isSelected ? colors.textPrimary : colors.surface,
          borderColor: isSelected ? colors.textPrimary : colors.border,
        },
      ]}
      onPress={onPress}
      activeOpacity={0.8}
    >
      <Text
        style={[
          styles.text,
          {
            color: isSelected ? colors.background : colors.textSecondary,
            fontWeight: isSelected ? '700' : '500',
          },
        ]}
      >
        {label}
      </Text>
    </TouchableOpacity>
  );
};

const styles = StyleSheet.create({
  pill: {
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 20,
    borderWidth: 1,
    marginRight: 8,
  },
  text: {
    fontSize: 12,
    letterSpacing: 0.5,
  },
});

export default memo(CategoryPill);

