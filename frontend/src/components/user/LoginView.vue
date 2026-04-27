<template>
    <div class="login-container">
        <form @submit.prevent="handleLogin" class="login-form">
            <h2>DigitalTolk Login</h2>
            <input v-model="form.email" type="email" placeholder="Email" required />
            <input v-model="form.password" type="password" placeholder="Password" required />
            <button type="submit" :disabled="loading">
                {{ loading ? 'Logging in...' : 'Login' }}
            </button>
            <p v-if="error" class="error">{{ error }}</p>
        </form>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import AuthApi from '@/plugins/authApi'
import { LocalStorageService } from '@/services/LocalStorageService'

const router = useRouter()
const loading = ref(false)
const error = ref('')
const form = reactive({ email: '', password: '' })

const handleLogin = async () => {
    loading.value = true
    error.value = ''
    try {
        const response = await AuthApi.login(form)

        // update LocalStorageService
        LocalStorageService.setToken(response.data.token)
        LocalStorageService.setUser(response.data.user)

        router.push('/')
    } catch (err) {
        error.value = 'Login failed. Please check your credentials.'
    } finally {
        loading.value = false
    }
}
</script>

<style>
.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background: var(--dark);
}

.login-form {
    background: white;
    padding: 2.5rem;
    border-radius: 12px;
    width: 100%;
    max-width: 400px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.login-form h2 {
    margin-bottom: 1.5rem;
    text-align: center;
    color: var(--dark);
}

.login-form input {
    width: 100%;
    padding: 12px;
    margin-bottom: 1rem;
    border: 1px solid var(--border);
    border-radius: 6px;
    box-sizing: border-box;
}

.login-form button {
    width: 100%;
    padding: 12px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
    transition: opacity 0.2s;
}

.login-form button:hover {
    opacity: 0.9;
}
</style>