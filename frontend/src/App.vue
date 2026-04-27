<script setup>
import { RouterView, useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const logout = () => {
  localStorage.clear()
  router.push('/login')
}
</script>

<template>
  <div class="app-wrapper">
    <header v-if="route.name !== 'login'" class="main-header">
      <nav>
        <RouterLink to="/">Home</RouterLink> |
        <RouterLink to="/management">Management</RouterLink> |
        <a href="#" @click.prevent="logout">Logout</a>
      </nav>
    </header>

    <main :class="{ 'auth-page': route.name === 'login' }">
      <RouterView />
    </main>
  </div>
</template>

<style>
#app {
  max-width: 100% !important;
  margin: 0 !important;
  padding: 0 !important;
  display: block !important;
}

.main-header {
  padding: 1rem;
  background: #2c3e50;
  color: white;
  text-align: center;
}

.main-header a {
  color: white;
  margin: 0 10px;
  text-decoration: none;
}

.auth-page {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f5f5f5;
}

.main-wrapper {
  width: 100%;
  min-height: 100vh;
}
</style>