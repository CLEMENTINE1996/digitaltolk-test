<template>

    <div class="container translation-test-page">
        <h3>In this page, you can test the translations. There are also translations specific for mobile or web sizes
            only.
        </h3>

        <div class="platform-status">
            Current View: <strong>{{ isMobile ? 'Mobile' : 'Web' }}</strong>
        </div>

        <div class="test-header">
            <div class="header-content">
                <h1 v-if="translationHelper('nav_dashboard')">{{ translationHelper('nav_dashboard') }}</h1>
                <p v-if="translationHelper('welcome_message')">{{ translationHelper('welcome_message') }}</p>
            </div>

            <div class="language-picker">
                <select v-model="selectedLanguageId" @change="onLanguageChange" class="lang-select">
                    <option v-for="lang in activeLanguages" :key="lang.id" :value="lang.id">
                        {{ lang.name }}
                    </option>
                </select>
            </div>
        </div>

        <div v-if="loading" class="loader-container">
            <div class="spinner"></div>
            <p>Updating UI translations...</p>
        </div>

        <div v-else class="test-grid">

            <aside v-if="translationHelper('label_navigation')" class="test-card">
                <h3>{{ translationHelper('label_navigation') }}</h3>
                <nav class="test-nav">
                    <div v-if="translationHelper('nav_home')">{{ translationHelper('nav_home') }}</div>
                    <div v-if="translationHelper('nav_settings')">{{ translationHelper('nav_settings') }}</div>
                    <div v-if="translationHelper('nav_profile')">{{ translationHelper('nav_profile') }}</div>
                </nav>
            </aside>

            <section v-if="translationHelper('label_user_details')" class="test-card">
                <div class="card-header">
                    <span class="icon">👤</span>
                    <h3>{{ translationHelper('label_user_details') }}</h3>
                </div>

                <div class="form-group">
                    <label v-if="translationHelper('label_username')">{{ translationHelper('label_username') }}</label>
                    <input type="text" :placeholder="translationHelper('placeholder_enter_username')"
                        class="test-input" />
                </div>

                <div class="form-group">
                    <label v-if="translationHelper('label_email')">{{ translationHelper('label_email') }}</label>
                    <input type="email" :placeholder="translationHelper('placeholder_enter_email')"
                        class="test-input" />
                    <span v-if="translationHelper('error_required_field')" class="error-msg">
                        {{ translationHelper('error_required_field') }}
                    </span>
                </div>
            </section>

            <section v-if="translationHelper('label_system_feedback')" class="test-card">
                <div class="card-header">
                    <span class="icon">🔔</span>
                    <h3>{{ translationHelper('label_system_feedback') }}</h3>
                </div>

                <div class="alert-stack">
                    <div v-if="translationHelper('msg_success_save')" class="alert success">
                        {{ translationHelper('msg_success_save') }}
                    </div>
                    <div v-if="translationHelper('msg_unsaved_changes')" class="alert warning">
                        {{ translationHelper('msg_unsaved_changes') }}
                    </div>
                    <div v-if="translationHelper('msg_connection_error')" class="alert danger">
                        {{ translationHelper('msg_connection_error') }}
                    </div>
                </div>

                <div class="btn-group">
                    <button v-if="translationHelper('btn_save_changes')" class="btn btn-primary">
                        {{ translationHelper('btn_save_changes') }}
                    </button>
                    <button v-if="translationHelper('btn_cancel')" class="btn btn-secondary">
                        {{ translationHelper('btn_cancel') }}
                    </button>
                </div>
            </section>
        </div>

        <footer v-if="translationHelper('label_footer_info')" class="test-footer">
            <p>{{ translationHelper('label_footer_info') }}</p>
        </footer>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import translationApi from '@/plugins/translationApi'

const rawTranslations = ref([])
const activeLanguages = ref([])
const selectedLanguageId = ref('')
const isMobile = ref(window.innerWidth <= 768)

const handleResize = () => {
    isMobile.value = window.innerWidth <= 768
}

const translationHelper = (key) => {
    const platform = isMobile.value ? 'mobile' : 'web'
    const item = rawTranslations.value.find(t => t.key === key)

    if (!item) return null

    const tags = item.tags || []
    return tags.includes(platform) ? item.content : null
}

const fetchTranslations = async (langId) => {
    loading.value = true
    try {
        const { data } = await translationApi.get({ params: { language_id: langId } })
        rawTranslations.value = data.data
    } finally {
        loading.value = false
    }
}

const onLanguageChange = () => {
    fetchTranslations(selectedLanguageId.value)
}

const loading = ref(false)

onMounted(async () => {
    window.addEventListener('resize', handleResize)
    const { data } = await translationApi.languages()
    activeLanguages.value = data
    if (data[0]) {
        selectedLanguageId.value = data[0].id
        fetchTranslations(data[0].id)
    }
})

onUnmounted(() => {
    window.removeEventListener('resize', handleResize)
})
</script>

<style scoped>
.translation-test-page {
    padding: 40px;
    max-width: 1000px;
}

.test-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 40px;
    border-bottom: 1px solid #eee;
    padding-bottom: 20px;
}

.lang-select {
    padding: 10px 15px;
    border-radius: 8px;
    border: 1px solid #ddd;
    background: white;
    min-width: 200px;
    font-size: 1rem;
}

.test-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 30px;
}

.test-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    border: 1px solid #f0f0f0;
}

.card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.card-header h3 {
    margin: 0;
    color: #333;
}

.test-input {
    width: 100%;
    padding: 12px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-sizing: border-box;
}

.error-msg {
    color: #e74c3c;
    font-size: 12px;
    margin-top: 5px;
    display: block;
}

.alert-stack {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.alert {
    padding: 12px;
    border-radius: 6px;
    font-size: 14px;
}

.success {
    background: #e6fffa;
    color: #2c7a7b;
    border: 1px solid #b2f5ea;
}

.warning {
    background: #fffaf0;
    color: #9c4221;
    border: 1px solid #feebc8;
}

.danger {
    background: #fff5f5;
    color: #c53030;
    border: 1px solid #feb2b2;
}

.btn-group {
    display: flex;
    gap: 15px;
    margin-top: 25px;
}

.btn {
    padding: 12px 24px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    border: none;
}

.btn-primary {
    background: #42b883;
    color: white;
}

.btn-secondary {
    background: #edf2f7;
    color: #4a5568;
}

.loader-container {
    text-align: center;
    padding: 100px;
    color: #666;
}
</style>