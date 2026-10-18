import React, { useState, useEffect } from 'react';
import {
  Modal,
  View,
  Text,
  TextInput,
  TouchableOpacity,
  ScrollView,
  StyleSheet,
  ActivityIndicator,
  Alert,
  KeyboardAvoidingView,
  Platform,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../context/AuthContext';
import { useNotifications } from '../context/NotificationContext';
import { useTheme } from '../context/ThemeContext';
import { COLORS } from '../constants/theme';

export default function EditProfileModal({ visible, onClose }) {
  const { user, updateUserProfile } = useAuth();
  const { showToast } = useNotifications();
  const { colors, isDark } = useTheme();

  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [phone, setPhone] = useState('');
  const [currentPassword, setCurrentPassword] = useState('');
  const [newPassword, setNewPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');

  // Destination address
  const [streetAddress, setStreetAddress] = useState('');
  const [apartment, setApartment] = useState('');
  const [city, setCity] = useState('');
  const [provinceState, setProvinceState] = useState('');
  const [postalCode, setPostalCode] = useState('');

  const [activeTab, setActiveTab] = useState('identity'); // 'identity' | 'security' | 'destination'
  const [isSubmitting, setIsSubmitting] = useState(false);

  useEffect(() => {
    if (user && visible) {
      setName(user.name || '');
      setEmail(user.email || '');
      setPhone(user.phone || '');
      setCurrentPassword('');
      setNewPassword('');
      setConfirmPassword('');

      const addr = user.addresses?.find((a) => a.is_default) || user.addresses?.[0];
      setStreetAddress(addr?.street_address || '');
      setApartment(addr?.apartment || '');
      setCity(addr?.city || 'Makati City');
      setProvinceState(addr?.province_state || 'Metro Manila');
      setPostalCode(addr?.postal_code || '1229');
    }
  }, [user, visible]);

  const handleSave = async () => {
    if (!name.trim()) {
      Alert.alert('Required Field', 'Please provide your full name.');
      return;
    }
    if (!email.trim()) {
      Alert.alert('Required Field', 'Please provide a valid email address.');
      return;
    }
    if (newPassword) {
      if (!currentPassword) {
        Alert.alert('Security Check', 'Please enter your current password to authorize this password change.');
        return;
      }
      if (newPassword.length < 6) {
        Alert.alert('Password Length', 'Your new password must be at least 6 characters.');
        return;
      }
      if (newPassword !== confirmPassword) {
        Alert.alert('Password Mismatch', 'The new passwords entered do not match.');
        return;
      }
    }

    try {
      setIsSubmitting(true);
      const payload = {
        name: name.trim(),
        email: email.trim(),
        phone: phone.trim(),
      };

      if (newPassword) {
        payload.current_password = currentPassword;
        payload.password = newPassword;
      }

      if (streetAddress.trim() && city.trim()) {
        payload.address = {
          street_address: streetAddress.trim(),
          apartment: apartment.trim(),
          city: city.trim(),
          province_state: provinceState.trim() || 'Metro Manila',
          postal_code: postalCode.trim() || '1229',
        };
      }

      await updateUserProfile(payload);
      showToast('Profile Updated', 'Your client account details have been securely synchronized.');
      onClose();
    } catch (err) {
      Alert.alert('Update Failed', err.message || 'Could not update your client account.');
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <Modal visible={visible} animationType="slide" transparent={true} onRequestClose={onClose}>
      <KeyboardAvoidingView
        behavior={Platform.OS === 'ios' ? 'padding' : undefined}
        style={styles.overlay}
      >
        <View style={[styles.sheetContainer, { backgroundColor: colors.surface }]}>
          {/* Header */}
          <View style={[styles.header, { borderBottomColor: colors.borderSubtle }]}>
            <View>
              <Text style={[styles.headerSubtitle, { color: colors.accent }]}>ATELIER CLIENT RECORD</Text>
              <Text style={[styles.headerTitle, { color: colors.textPrimary }]}>Edit Client Account</Text>
            </View>
            <TouchableOpacity onPress={onClose} hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}>
              <Ionicons name="close" size={22} color={colors.textPrimary} />
            </TouchableOpacity>
          </View>

          {/* Tab Selector */}
          <View style={[styles.tabBar, { backgroundColor: colors.backgroundSubtle }]}>
            <TouchableOpacity
              style={[
                styles.tabItem,
                activeTab === 'identity' && [styles.tabItemActive, { backgroundColor: colors.surface }],
              ]}
              onPress={() => setActiveTab('identity')}
            >
              <Ionicons
                name="person-outline"
                size={14}
                color={activeTab === 'identity' ? colors.textPrimary : colors.textMuted}
              />
              <Text
                style={[
                  styles.tabText,
                  { color: activeTab === 'identity' ? colors.textPrimary : colors.textMuted },
                  activeTab === 'identity' && styles.tabTextActive,
                ]}
              >
                Identity
              </Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={[
                styles.tabItem,
                activeTab === 'security' && [styles.tabItemActive, { backgroundColor: colors.surface }],
              ]}
              onPress={() => setActiveTab('security')}
            >
              <Ionicons
                name="shield-checkmark-outline"
                size={14}
                color={activeTab === 'security' ? colors.textPrimary : colors.textMuted}
              />
              <Text
                style={[
                  styles.tabText,
                  { color: activeTab === 'security' ? colors.textPrimary : colors.textMuted },
                  activeTab === 'security' && styles.tabTextActive,
                ]}
              >
                Security
              </Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={[
                styles.tabItem,
                activeTab === 'destination' && [styles.tabItemActive, { backgroundColor: colors.surface }],
              ]}
              onPress={() => setActiveTab('destination')}
            >
              <Ionicons
                name="location-outline"
                size={14}
                color={activeTab === 'destination' ? colors.textPrimary : colors.textMuted}
              />
              <Text
                style={[
                  styles.tabText,
                  { color: activeTab === 'destination' ? colors.textPrimary : colors.textMuted },
                  activeTab === 'destination' && styles.tabTextActive,
                ]}
              >
                Destination
              </Text>
            </TouchableOpacity>
          </View>

          {/* Form Content */}
          <ScrollView
            style={styles.body}
            contentContainerStyle={styles.bodyContent}
            showsVerticalScrollIndicator={false}
          >
            {activeTab === 'identity' && (
              <View style={styles.section}>
                <View style={styles.formGroup}>
                  <Text style={[styles.label, { color: colors.textSecondary }]}>FULL NAME / CLIENT USERNAME *</Text>
                  <TextInput
                    style={[styles.input, { backgroundColor: colors.inputBg, borderColor: colors.inputBorder, color: colors.inputText }]}
                    value={name}
                    onChangeText={setName}
                    placeholder="e.g. Sophia Laurent"
                    placeholderTextColor={colors.textMuted}
                  />
                </View>

                <View style={styles.formGroup}>
                  <Text style={[styles.label, { color: colors.textSecondary }]}>EMAIL ADDRESS *</Text>
                  <TextInput
                    style={[styles.input, { backgroundColor: colors.inputBg, borderColor: colors.inputBorder, color: colors.inputText }]}
                    value={email}
                    onChangeText={setEmail}
                    placeholder="client@eleve.com"
                    placeholderTextColor={colors.textMuted}
                    keyboardType="email-address"
                    autoCapitalize="none"
                  />
                </View>

                <View style={styles.formGroup}>
                  <Text style={[styles.label, { color: colors.textSecondary }]}>CONTACT PHONE NUMBER</Text>
                  <TextInput
                    style={[styles.input, { backgroundColor: colors.inputBg, borderColor: colors.inputBorder, color: colors.inputText }]}
                    value={phone}
                    onChangeText={setPhone}
                    placeholder="+63 917 000 0000"
                    placeholderTextColor={colors.textMuted}
                    keyboardType="phone-pad"
                  />
                  <Text style={[styles.fieldHint, { color: colors.textMuted }]}>
                    Used for courier white-glove arrival and delivery SMS alerts.
                  </Text>
                </View>
              </View>
            )}

            {activeTab === 'security' && (
              <View style={styles.section}>
                <View style={[styles.noticeBox, { backgroundColor: colors.backgroundSubtle, borderColor: colors.border }]}>
                  <Ionicons name="lock-closed-outline" size={16} color={colors.textPrimary} />
                  <Text style={[styles.noticeText, { color: colors.textSecondary }]}>
                    To update your password, verify your current password first. Leave blank to preserve existing credentials.
                  </Text>
                </View>

                <View style={styles.formGroup}>
                  <Text style={[styles.label, { color: colors.textSecondary }]}>CURRENT PASSWORD</Text>
                  <TextInput
                    style={[styles.input, { backgroundColor: colors.inputBg, borderColor: colors.inputBorder, color: colors.inputText }]}
                    value={currentPassword}
                    onChangeText={setCurrentPassword}
                    placeholder="Enter current password"
                    placeholderTextColor={colors.textMuted}
                    secureTextEntry
                  />
                </View>

                <View style={styles.formGroup}>
                  <Text style={[styles.label, { color: colors.textSecondary }]}>NEW PASSWORD</Text>
                  <TextInput
                    style={[styles.input, { backgroundColor: colors.inputBg, borderColor: colors.inputBorder, color: colors.inputText }]}
                    value={newPassword}
                    onChangeText={setNewPassword}
                    placeholder="Minimum 6 characters"
                    placeholderTextColor={colors.textMuted}
                    secureTextEntry
                  />
                </View>

                <View style={styles.formGroup}>
                  <Text style={[styles.label, { color: colors.textSecondary }]}>CONFIRM NEW PASSWORD</Text>
                  <TextInput
                    style={[styles.input, { backgroundColor: colors.inputBg, borderColor: colors.inputBorder, color: colors.inputText }]}
                    value={confirmPassword}
                    onChangeText={setConfirmPassword}
                    placeholder="Repeat new password"
                    placeholderTextColor={colors.textMuted}
                    secureTextEntry
                  />
                </View>
              </View>
            )}

            {activeTab === 'destination' && (
              <View style={styles.section}>
                <View style={styles.formGroup}>
                  <Text style={[styles.label, { color: colors.textSecondary }]}>STREET ADDRESS *</Text>
                  <TextInput
                    style={[styles.input, { backgroundColor: colors.inputBg, borderColor: colors.inputBorder, color: colors.inputText }]}
                    value={streetAddress}
                    onChangeText={setStreetAddress}
                    placeholder="e.g. 128 Paseo de Roxas"
                    placeholderTextColor={colors.textMuted}
                  />
                </View>

                <View style={styles.formGroup}>
                  <Text style={[styles.label, { color: colors.textSecondary }]}>SUITE / PENTHOUSE / BUILDING (OPTIONAL)</Text>
                  <TextInput
                    style={[styles.input, { backgroundColor: colors.inputBg, borderColor: colors.inputBorder, color: colors.inputText }]}
                    value={apartment}
                    onChangeText={setApartment}
                    placeholder="e.g. Penthouse 3A, The Residences"
                    placeholderTextColor={colors.textMuted}
                  />
                </View>

                <View style={styles.row}>
                  <View style={[styles.formGroup, { flex: 1 }]}>
                    <Text style={[styles.label, { color: colors.textSecondary }]}>CITY *</Text>
                    <TextInput
                      style={[styles.input, { backgroundColor: colors.inputBg, borderColor: colors.inputBorder, color: colors.inputText }]}
                      value={city}
                      onChangeText={setCity}
                      placeholder="Makati City"
                      placeholderTextColor={colors.textMuted}
                    />
                  </View>

                  <View style={[styles.formGroup, { flex: 1 }]}>
                    <Text style={[styles.label, { color: colors.textSecondary }]}>POSTAL CODE *</Text>
                    <TextInput
                      style={[styles.input, { backgroundColor: colors.inputBg, borderColor: colors.inputBorder, color: colors.inputText }]}
                      value={postalCode}
                      onChangeText={setPostalCode}
                      placeholder="1229"
                      placeholderTextColor={colors.textMuted}
                      keyboardType="numeric"
                    />
                  </View>
                </View>

                <View style={styles.formGroup}>
                  <Text style={[styles.label, { color: colors.textSecondary }]}>PROVINCE / REGION</Text>
                  <TextInput
                    style={[styles.input, { backgroundColor: colors.inputBg, borderColor: colors.inputBorder, color: colors.inputText }]}
                    value={provinceState}
                    onChangeText={setProvinceState}
                    placeholder="Metro Manila"
                    placeholderTextColor={colors.textMuted}
                  />
                </View>
              </View>
            )}
          </ScrollView>

          {/* Footer CTAs */}
          <View style={[styles.footer, { borderTopColor: colors.borderSubtle }]}>
            <TouchableOpacity
              style={[styles.cancelBtn, { borderColor: colors.border }]}
              onPress={onClose}
              disabled={isSubmitting}
            >
              <Text style={[styles.cancelBtnText, { color: colors.textSecondary }]}>Discard</Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={[styles.saveBtn, { backgroundColor: colors.textPrimary }]}
              onPress={handleSave}
              disabled={isSubmitting}
              activeOpacity={0.85}
            >
              {isSubmitting ? (
                <ActivityIndicator size="small" color={colors.background} />
              ) : (
                <Text style={[styles.saveBtnText, { color: colors.background }]}>Save Atelier Record</Text>
              )}
            </TouchableOpacity>
          </View>
        </View>
      </KeyboardAvoidingView>
    </Modal>
  );
}

const styles = StyleSheet.create({
  overlay: {
    flex: 1,
    backgroundColor: 'rgba(23, 23, 23, 0.65)',
    justifyContent: 'flex-end',
  },
  sheetContainer: {
    backgroundColor: '#FFFFFF',
    borderTopLeftRadius: 24,
    borderTopRightRadius: 24,
    maxHeight: '90%',
    paddingBottom: 24,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingTop: 20,
    paddingBottom: 14,
    borderBottomWidth: 1,
    borderBottomColor: '#F0ECE4',
  },
  headerSubtitle: {
    fontSize: 9,
    fontWeight: '700',
    color: '#A67C52',
    letterSpacing: 1.5,
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: '700',
    color: COLORS.obsidian,
    marginTop: 2,
    fontFamily: 'serif',
  },
  tabBar: {
    flexDirection: 'row',
    backgroundColor: '#F7F5F0',
    padding: 4,
    marginHorizontal: 16,
    marginTop: 12,
    borderRadius: 10,
    gap: 4,
  },
  tabItem: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 8,
    borderRadius: 8,
    gap: 6,
  },
  tabItemActive: {
    backgroundColor: '#FFFFFF',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.08,
    shadowRadius: 2,
    elevation: 2,
  },
  tabText: {
    fontSize: 11,
    fontWeight: '600',
    color: COLORS.textMuted,
  },
  tabTextActive: {
    color: COLORS.obsidian,
  },
  body: {
    maxHeight: 380,
  },
  bodyContent: {
    padding: 20,
  },
  section: {
    gap: 14,
  },
  formGroup: {
    gap: 6,
  },
  row: {
    flexDirection: 'row',
    gap: 12,
  },
  label: {
    fontSize: 10,
    fontWeight: '700',
    color: COLORS.textSecondary,
    letterSpacing: 1,
  },
  input: {
    backgroundColor: '#FBF9F6',
    borderWidth: 1,
    borderColor: '#E2DCD5',
    borderRadius: 8,
    paddingHorizontal: 12,
    paddingVertical: 10,
    fontSize: 13,
    color: COLORS.obsidian,
  },
  fieldHint: {
    fontSize: 10,
    color: COLORS.textMuted,
    marginTop: 2,
  },
  noticeBox: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#F3EDE4',
    padding: 10,
    borderRadius: 8,
    gap: 8,
    marginBottom: 6,
  },
  noticeText: {
    flex: 1,
    fontSize: 11,
    color: '#66615B',
    lineHeight: 15,
  },
  footer: {
    flexDirection: 'row',
    paddingHorizontal: 20,
    paddingTop: 14,
    borderTopWidth: 1,
    borderTopColor: '#F0ECE4',
    gap: 12,
  },
  cancelBtn: {
    paddingVertical: 12,
    paddingHorizontal: 18,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: '#D8D0C4',
    alignItems: 'center',
    justifyContent: 'center',
  },
  cancelBtnText: {
    fontSize: 12,
    fontWeight: '600',
    color: COLORS.textSecondary,
  },
  saveBtn: {
    flex: 1,
    backgroundColor: COLORS.obsidian,
    paddingVertical: 12,
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
  },
  saveBtnText: {
    fontSize: 12,
    fontWeight: '700',
    color: COLORS.ivory,
    letterSpacing: 0.5,
  },
});
