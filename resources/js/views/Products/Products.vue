<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const products = ref([])

const getProducts = async () => {
    const response = await axios.get('/api/products')
    products.value = response.data
}

onMounted(() => {
    getProducts()
})
</script>

<template>
    <div>
        <h1>Products</h1>

        <div v-for="product in products" :key="product.id">
            <h2>{{ product.name }}</h2>
            <p>{{ product.description }}</p>
            <p>Price: {{ product.price }}</p>
            <p>Stock: {{ product.stock }}</p>
        </div>
    </div>
</template>