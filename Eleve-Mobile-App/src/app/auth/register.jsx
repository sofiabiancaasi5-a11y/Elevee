import React, { useState } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  ActivityIndicator,
  Alert,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useRouter, useLocalSearchParams } from 'expo-router';
import { useAuth } from '../../context/AuthContext';
import { useTheme } from '../../context/ThemeContext';
import { COLORS } from '../../constants/theme';

export default function RegisterScreen() {
  const router = useRouter();
  const params = useLocalSearchParams();
  const { register } = useAuth();
  const { colors, isDark } = useTheme();

  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [phone, setPhone] = useState('');
  const [password, setPassword] = useState('');
  const [passwordConfirmation, setPasswordConfirmation] = useState('');
  const [isLoading, setIsLoading] = useState(false);

  const handleRegister = async () => {
    if (!name.trim() || !email.trim() || !password.trim()) {
      Alert.alert('Required Fields', 'Please provide your full name, email, and password.');
      return;
    }

    if (password !== passwordConfirmation) {
      Alert.alert('Password Mismatch', 'The passwords entered do not match.');
      return;
    }

    try {
      setIsLoading(true);
      await register(name.trim(), email.trim(), password, passwordConfirmation, phone.trim());

      // Seamless post-registration redirect (e.g. back to /checkout)
      if (params.redirect) {
        router.replace(params.redirect);
      } else {
        router.back();
      }
    } catch (err) {
      Alert.alert('Registration Failed', err.message || 'Could not complete registration.');
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <SafeAreaView style={[styles.safeArea, { backgroundColor: colors.background }]}>
      <KeyboardAvoidingView
        behavior={Platform.OS === 'ios' ? 'padding' : undefined}
        style={styles.container}
      >
        <ScrollView contentContainerStyle={styles.content} showsVerticalScrollIndicator={false}>
          <TouchableOpacity style={styles.closeBtn} onPress={() => router.back()}>
            <Ionicons name="close" size={24} color={colors.textPrimary} />
          </TouchableOpacity>

          <View style={styles.header}>
            <View style={[styles.monogram, { backgroundColor: colors.monogramBg }]}>
              <Text style={[styles.monogramText, { color: colors.monogramText }]}>É</Text>
            </View>
            <Text style={[styles.title, { color: colors.textPrimary }]}>Join ÉLEVÉ</Text>
            <Text style={[styles.subtitle, { color: colors.textSecondary }]}>
              {params.redirect === '/checkout'
                ? 'Create your atelier client profile to complete your footwear order.'
                : 'Create your personal luxury footwear account.'}
            </Text>
          </View>

          <View style={styles.form}>
            <View style={styles.formGroup}>
              <Text style={[styles.label, { color: colors.textMuted }]}>FULL NAME *</Text>
              <TextInput
                style={[
                  styles.input,
                  {
                    backgroundColor: colors.surfaceSubtle,
                    borderColor: colors.border,
                    color: colors.textPrimary,
                  },
                ]}
                value={name}
                onChangeText={setName}
                placeholder="Alexander Vance"
                placeholderTextColor={colors.textMuted}
              />
            </View>

            <View style={styles.formGroup}>
              <Text style={[styles.label, { color: colors.textMuted }]}>EMAIL ADDRESS *</Text>
              <TextInput
                style={[
                  styles.input,
                  {
                    backgroundColor: colors.surfaceSubtle,
                    borderColor: colors.border,
                    color: colors.textPrimary,
                  },
                ]}
                value={email}
                onChangeText={setEmail}
                keyboardType="email-address"
                autoCapitalize="none"
                placeholder="alexander@example.com"
                placeholderTextColor={colors.textMuted}
              />
            </View>

            <View style={styles.formGroup}>
              <Text style={[styles.label, { color: colors.textMuted }]}>MOBILE NUMBER (FOR COURIER)</Text>
              <TextInput
                style={[
                  styles.input,
                  {
                    backgroundColor: colors.surfaceSubtle,
                    borderColor: colors.border,
                    color: colors.textPrimary,
                  },
                ]}
                value={phone}
                onChangeText={setPhone}
                keyboardType="phone-pad"
                placeholder="+63 917 000 0000"
                placeholderTextColor={colors.textMuted}
              />
            </View>

            <View style={styles.formGroup}>
              <Text style={[styles.label, { color: colors.textMuted }]}>PASSWORD (MIN 8 CHARACTERS) *</Text>
              <TextInput
                style={[
                  styles.input,
                  {
                    backgroundColor: colors.surfaceSubtle,
                    borderColor: colors.border,
                    color: colors.textPrimary,
                  },
                ]}
                value={password}
                onChangeText={setPassword}
                secureTextEntry
                placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                placeholderTextColor={colors.textMuted}
              />
            </View>

            <View style={styles.formGroup}>
              <Text style={[styles.label, { color: colors.textMuted }]}>CONFIRM PASSWORD *</Text>
              <TextInput
                style={[
                  styles.input,
                  {
                    backgroundColor: colors.surfaceSubtle,
                    borderColor: colors.border,
                    color: colors.textPrimary,
                  },
                ]}
                value={passwordConfirmation}
                onChangeText={setPasswordConfirmation}
                secureTextEntry
                placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                placeholderTextColor={colors.textMuted}
              />
            </View>

            <TouchableOpacity
              style={[styles.submitBtn, { backgroundColor: colors.textPrimary }]}
              onPress={handleRegister}
              disabled={isLoading}
              activeOpacity={0.85}
            >
              {isLoading ? (
                <ActivityIndicator size="small" color={colors.background} />
              ) : (
                <Text style={[styles.submitBtnText, { color: colors.background }]}>Create Account & Continue</Text>
              )}
            </TouchableOpacity>

            <View style={styles.footerLinkRow}>
              <Text style={[styles.footerText, { color: colors.textMuted }]}>Already have an account?</Text>
              <TouchableOpacity
                onPress={() =>
                  router.replace({
                    pathname: '/auth/login',
                    params: params.redirect ? { redirect: params.redirect } : {},
                  })
                }
              >
                <Text style={[styles.footerLink, { color: colors.textPrimary }]}>Sign In</Text>
              </TouchableOpacity>
            </View>
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: {
    flex: 1,
  },
  container: {
    flex: 1,
  },
  content: {
    padding: 24,
    paddingTop: 40,
    paddingBottom: 40,
  },
  closeBtn: {
    position: 'absolute',
    top: 10,
    right: 16,
    padding: 8,
    zIndex: 10,
  },
  header: {
    alignItems: 'center',
    marginBottom: 28,
  },
  monogram: {
    width: 56,
    height: 56,
    borderRadius: 28,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 16,
  },
  monogramText: {
    fontFamily: 'serif',
    fontSize: 28,
    fontWeight: 'bold',
  },
  title: {
    fontFamily: 'serif',
    fontSize: 24,
    fontWeight: '700',
    marginBottom: 6,
  },
  subtitle: {
    fontSize: 12,
    textAlign: 'center',
    maxWidth: 280,
    lineHeight: 18,
  },
  form: {
    gap: 14,
  },
  formGroup: {
    gap: 5,
  },
  label: {
    fontSize: 10,
    fontWeight: '700',
    letterSpacing: 1.5,
  },
  input: {
    borderWidth: 1,
    borderRadius: 8,
    paddingHorizontal: 14,
    paddingVertical: 11,
    fontSize: 13,
  },
  submitBtn: {
    height: 50,
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 10,
  },
  submitBtnText: {
    fontSize: 11,
    fontWeight: '700',
    letterSpacing: 1.5,
    textTransform: 'uppercase',
  },
  footerLinkRow: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    gap: 6,
    marginTop: 10,
  },
  footerText: {
    fontSize: 12,
  },
  footerLink: {
    fontSize: 12,
    fontWeight: '700',
    textDecorationLine: 'underline',
  },
});
