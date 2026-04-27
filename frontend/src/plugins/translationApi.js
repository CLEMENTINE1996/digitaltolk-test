import api from './api'

const TRANSLATION_API = '/api/translations'
const LANGUAGE_API = '/api/languages'

export default {
  /**
   * Fetch paginated translations with filters
   * @param {Object} params - Query filters {key, tag, page}
   */
  get: function(params) {
    return api.get(TRANSLATION_API, params)
  },

  /**
   * Create a new translation record
   * @param {Object} params - {translation_language_id, key, content, tags}
   */
  create: function(params) {
      return api.create(TRANSLATION_API, params)
  },

  /**
   * Update an existing translation
   * @param {number} id
   * @param {Object} params
   */
  update: function(id, params) {
      return api.update(TRANSLATION_API + '/' + id, params)
  },

  /**
   * Delete a translation
   * @param {number} id
   */
  delete: function(id) {
    return api.delete(TRANSLATION_API + '/' + id)
  },

  /**
   * Export all translations grouped by language
   */
  export: function(params) {
    return api.get(TRANSLATION_API + '/export', params)
  },

  /**
   * Export key-value pairs for a specific language code
   * @param {string} code - e.g. 'en', 'sv'
   */
  exportLocale: function(code) {
    return api.get(TRANSLATION_API + '/export/' + code)
  },

  /**
   * Fetch active languages from DB
   */
  languages: function(params) {
    return api.get(LANGUAGE_API, params)
  },
}