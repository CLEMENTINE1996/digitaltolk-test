<template>
    <div class="container translation-mgmt-page">
        <div class="mgmt-header">
            <div class="header-info">
                <h1>{{ translationHelper('nav_dashboard') }}</h1>
                <p>{{ translationHelper('label_footer_info') }}</p>
            </div>
            <div class="header-actions">
                <button @click="openModal()" class="btn btn-add">
                    + Add Translation
                </button>
            </div>
        </div>
        <div class="locale-export-zone flex items-center gap-2 p-4 bg-blue-50 rounded-lg border border-blue-100">
            <span class="text-sm font-medium text-blue-700">Quick Export:</span>
            <button v-for="lang in languages" :key="lang.id" @click="handleLocaleExport(lang.code)"
                :disabled="exportingLocale === lang.code" class="btn-locale-zip">
                {{ exportingLocale === lang.code ? '...' : lang.code.toUpperCase() }}
            </button>
        </div>

        <div class="filter-bar">
            <input v-model="filters.key" @input="debounceSearch" placeholder="Filter by key..." class="search-input" />
            <select v-model="filters.tag" @change="fetchTranslations" class="filter-select">
                <option value="">All Tags</option>
                <option value="web">Web</option>
                <option value="mobile">Mobile</option>
            </select>
        </div>

        <div class="container translation-mgmt-page">
            <div v-if="errorMessage" class="error-toast" @click="errorMessage = ''">
                {{ errorMessage }}
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Key</th>
                            <th>Language</th>
                            <th>Content</th>
                            <th>Tags</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in translations" :key="item.id">
                            <td>{{ item.key }}</td>
                            <td>{{ item.language_code }}</td>
                            <td>{{ item.content }}</td>
                            <td>
                                <span v-for="tag in item.tags" :key="tag" class="tag-pill">{{ tag }}</span>
                            </td>
                            <td>
                                <button @click="openModal(item)" class="btn btn-edit">Edit</button>
                                <button @click="handleDelete(item.id)" class="btn btn-delete">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="pagination.total > 0" class="pagination-container">
                <div class="pagination-info">
                    Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} entries
                </div>
                <div class="pagination-buttons">
                    <button :disabled="pagination.current_page === 1" @click="changePage(pagination.current_page - 1)"
                        class="btn-nav">
                        Previous
                    </button>

                    <span class="page-indicator">
                        Page {{ pagination.current_page }} of {{ pagination.last_page }}
                    </span>

                    <button :disabled="pagination.current_page === pagination.last_page"
                        @click="changePage(pagination.current_page + 1)" class="btn-nav">
                        Next
                    </button>
                </div>
            </div>
        </div>

        <div v-if="showModal" class="modal-overlay">
            <div class="modal-content">
                <h3>{{ editingId ? 'Update Translation' : 'New Translation' }}</h3>
                <form @submit.prevent="saveTranslation">
                    <div class="form-group">
                        <label>Language</label>
                        <select v-model="form.translation_language_id" required>
                            <option v-for="l in languages" :key="l.id" :value="l.id">{{ l.name }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Key</label>
                        <input v-model="form.key" placeholder="e.g. btn_save" required />
                    </div>
                    <div class="form-group">
                        <label>Content</label>
                        <textarea v-model="form.content" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Tags (Comma separated)</label>
                        <input v-model="tagInput" placeholder="web, mobile" required />
                    </div>
                    <div class="modal-actions">
                        <button type="button" @click="showModal = false" class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import translationApi from '@/plugins/translationApi'

const translations = ref([])
const loading = ref(false)
const isSaving = ref(false)
const isExporting = ref(false)
const errorMessage = ref('')
const filters = reactive({ key: '', tag: '', page: 1 })
const form = reactive({ translation_language_id: null, key: '', content: '', tags: [] })
const languages = ref([])
const editingId = ref(null)
const tagInput = ref('')
const showModal = ref(false)
const pagination = ref({
    current_page: 1,
    last_page: 1,
    total: 0,
    from: 0,
    to: 0
})
const exportingLocale = ref(null)

const handleLocaleExport = async (code) => {
    exportingLocale.value = code
    try {
        const { data } = await translationApi.exportLocale(code)

        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' })
        const url = URL.createObjectURL(blob)
        const a = document.createElement('a')
        a.href = url
        a.download = `translations_${code}.json`
        a.click();
        URL.revokeObjectURL(url)
    } catch (error) {
        handleError(error, `Exporting ${code}`)
    } finally {
        exportingLocale.value = null
    }
}

const handleError = (error, context) => {
    console.error(`Error during ${context}:`, error)

    if (error.response?.status === 422) {
        const firstError = Object.values(error.response.data.errors)[0][0]
        errorMessage.value = `${context} failed: ${firstError}`
    } else if (error.response?.data?.message) {
        errorMessage.value = error.response.data.message
    } else {
        errorMessage.value = `Unexpected error during ${context}. Please check connection.`
    }

    setTimeout(() => {
        errorMessage.value = ''
    }, 5000)
}

const fetchTranslations = async () => {
    loading.value = true
    try {
        const { data } = await translationApi.get({ params: filters })
        translations.value = data.data

        if (data.meta) {
            pagination.value = {
                current_page: data.meta.current_page,
                last_page: data.meta.last_page,
                total: data.meta.total,
                from: data.meta.from,
                to: data.meta.to
            }
        }
    } catch (error) {
        handleError(error, 'Fetching')
    } finally {
        loading.value = false
    }
}

const changePage = (page) => {
    filters.page = page
    fetchTranslations()
}

const saveTranslation = async () => {
    isSaving.value = true
    form.tags = tagInput.value ? tagInput.value.split(',').filter(t => t !== '') : []

    try {
        if (editingId.value) {
            await translationApi.update(editingId.value, form)
        } else {
            await translationApi.create(form)
        }
        showModal.value = false
        fetchTranslations()
    } catch (error) {
        handleError(error, 'Saving translation')
    } finally {
        isSaving.value = false
    }
}

const handleDelete = async (id) => {
    if (confirm('Are you sure?')) {
        await translationApi.delete(id)
        fetchTranslations()
    }
}

const openModal = (item = null) => {
    if (item) {
        editingId.value = item.id
        Object.assign(form, { ...item })
        tagInput.value = item.tags.join(',')
    } else {
        editingId.value = null
        Object.assign(form, { translation_language_id: null, key: '', content: '', tags: [] })
        tagInput.value = ''
    }
    showModal.value = true
}


const handleExport = async () => {
    isExporting.value = true
    const { data } = await translationApi.export()
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = 'translations.json'
    a.click()
    isExporting.value = false
}

let debounceTimeout
const debounceSearch = () => {
    filters.page = 1
    clearTimeout(debounceTimeout)
    debounceTimeout = setTimeout(fetchTranslations, 300)
}

const isMobile = ref(window.innerWidth <= 768)
const translationHelper = (key) => {
    const platform = isMobile.value ? 'mobile' : 'web'
    const item = translations.value.find(t => t.key === key)
    return item && item.tags.includes(platform) ? item.content : null
}

onMounted(async () => {
    const langRes = await translationApi.languages()
    languages.value = langRes.data
    fetchTranslations()
})
</script>


<style scoped>
.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: #f9fafb;
    border-top: 1px solid #e5e7eb;
}

.pagination-buttons {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.btn-nav {
    padding: 0.5rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    background: white;
    cursor: pointer;
}

.btn-nav:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.page-indicator {
    font-size: 0.875rem;
    color: #4b5563;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 600;
    border-radius: 0.375rem;
    transition: all 0.2s ease-in-out;
    cursor: pointer;
    border: 1px solid transparent;
    gap: 0.5rem;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-primary,
.btn-add {
    background-color: #16a34a;
    color: #ffffff;
}

.btn-add:hover:not(:disabled) {
    background-color: #15803d;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-edit {
    background-color: #2563eb;
    color: #ffffff;
}

.btn-edit:hover:not(:disabled) {
    background-color: #1d4ed8;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-delete {
    background-color: #dc2626;
    color: #ffffff;
}

.btn-delete:hover:not(:disabled) {
    background-color: #b91c1c;
}

.btn-export {
    background-color: #059669;
    color: #ffffff;
    border: 1px solid #047857;
}

.btn-export:hover:not(:disabled) {
    background-color: #047857;
    transform: translateY(-1px);
}

.btn-export:active {
    transform: translateY(0);
}

.btn-locale-zip {
    padding: 0.25rem 0.75rem;
    background: white;
    border: 1px solid #bfdbfe;
    color: #2563eb;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
    transition: all 0.2s;
}

.btn-locale-zip:hover:not(:disabled) {
    background: #2563eb;
    color: white;
    border-color: #2563eb;
}

.btn-locale-zip:disabled {
    cursor: wait;
    opacity: 0.5;
}

.locale-export-zone {
    margin-bottom: 20px;
}
</style>
