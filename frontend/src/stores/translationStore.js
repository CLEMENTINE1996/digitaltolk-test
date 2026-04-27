import { defineStore } from 'pinia'
import { Group as TranslationApi } from '@/api/translationApi'

export const useTranslationStore = defineStore('translation', {
  state: () => ({
    translations: {},
    currentLocale: 'en',
    isLoading: false
  }),

  getters: {
    // get a translation by key
    t: (state) => (key) => {
      return state.translations[state.currentLocale]?.[key] || `[${key}]`
    }
  },

  actions: {
    async setLocale(locale) {
      this.currentLocale = locale
      if (!this.translations[locale]) {
        await this.fetchTranslations()
      }
    },

    async fetchTranslations() {
      this.isLoading = true
      try {
        const response = await TranslationApi.export() 
        this.translations = response.data
      } catch (error) {
        console.error('Failed to load translations', error)
      } finally {
        this.isLoading = false
      }
    }
  }
})